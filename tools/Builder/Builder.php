<?php
/**
 * Class DBParser
 *
 * @created      01.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 *
 * @noinspection PhpForeachNestedOuterKeyValueVariablesConflictInspection
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\SkillLangEnglish;
use Buildwars\GWSkillData\SkillLangGerman;
use Buildwars\GWSkillData\Type;
use Buildwars\GWSkillDataTools\BuilderOptions;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherAbstract;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherEnglish;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherGerman;
use chillerlan\HTTP\CurlClient;
use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\Settings\SettingsContainerInterface;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use function array_chunk;
use function array_column;
use function array_diff;
use function array_key_exists;
use function array_map;
use function count;
use function implode;
use function in_array;
use function is_bool;
use function ksort;
use function sprintf;
use function str_split;
use function strtoupper;
use function strtr;
use function trim;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;
use const SRCDIR;

class Builder{

	public const REPO_URL                  = 'https://build-wars.github.io/gw-skilldata';

	public const SCHEMA_SKILL              = self::REPO_URL.'/schemas/skill.schema.json';
	public const SCHEMA_SKILLDATA          = self::REPO_URL.'/schemas/skilldata.schema.json';
	public const SCHEMA_SKILLDESC          = self::REPO_URL.'/schemas/skilldesc.schema.json';
	public const SCHEMA_SKILLDATA_COMBINED = self::REPO_URL.'/schemas/skilldata-combined.schema.json';

	protected const JSON_SKILL_DIR = DATA_DIR.'/json-skills';
	protected const JSON_DATA_FILE = DATA_DIR.'/json-full/skilldata.json';
	protected const JSON_COMBINED  = DATA_DIR.'/json-full/skilldata-combined.json';

	/** @var array<string, string>  */
	protected const JSON_LANG_FILES = [
		Lang::DE => DATA_DIR.'/json-full/skilldesc-de.json',
		Lang::EN => DATA_DIR.'/json-full/skilldesc-en.json',
	];

	/** @var array<string, string>  */
	public const DATABASES = [
		Lang::DE => SkillLangGerman::class,
		Lang::EN => SkillLangEnglish::class,
	];

	/** @var array<string, string>  */
	protected const WIKIFETCHERS = [
		Lang::DE => WikiFetcherGerman::class,
		Lang::EN => WikiFetcherEnglish::class,
	];

	protected readonly SettingsContainerInterface|BuilderOptions $options;
	protected readonly LoggerInterface                           $logger;
	protected readonly ClientInterface                           $http;
	protected readonly RequestFactoryInterface                   $requestFactory;

	protected WikiFetcherAbstract $wikiFetcher;

	/** @var array<string, \Buildwars\GWSkillData\SkillDataInterface> */
	protected readonly array $databases;

	protected array $skilldata     = [];
	protected array $skilldesc     = [];
	protected array $new_skilldata = [];
	protected array $new_skilldesc = [];

	public function __construct(SettingsContainerInterface|BuilderOptions $options){
		$this->options   = $options;
		$this->databases = array_map(fn(string $fqcn):SkillDataInterface => new $fqcn, static::DATABASES);
		$this->logger    = $this->initLogger();

		$factory = new HTTPFactory;

		$this->requestFactory = $factory;
		$this->http           = new CurlClient($factory, $this->options);
	}

	/**
	 * Adds a new skill
	 *
	 * needs to be run before `create()`
	 */
	public function addSkill(
		int            $id,
		Campaign|int   $campaign,
		Profession|int $profession,
		Attribute|int  $attribute,
		bool           $is_elite,
		bool           $is_rp,
	):static{

		if(!$campaign instanceof Campaign){
			$campaign = new Campaign($campaign);
		}

		if(!$profession instanceof Profession){
			$profession = new Profession($profession);
		}

		if(!$attribute instanceof Attribute){
			$attribute = new Attribute($attribute);
		}

		// required values: id, campaign, profession, attribute, is_elite, is_rp
		$data = [
			Skill::DATA_ID         => $id,
			Skill::DATA_CAMPAIGN   => $campaign->id,
			Skill::DATA_PROFESSION => $profession->id,
			Skill::DATA_ATTRIBUTE  => $attribute->id,
			Skill::DATA_IS_ELITE   => $is_elite,
			Skill::DATA_IS_RP      => $is_rp,
			Skill::DATA_IS_PVP     => in_array($id, PVP_SPLIT, true),
			Skill::DATA_PVP_SPLIT  => array_key_exists($id, PVP_SPLIT),
			Skill::DATA_SPLIT_ID   => (PVP_SPLIT[$id] ?? 0),
		];

		$this->new_skilldata[$id] = $this->createDataFields($id);

		foreach($data as $k => $v){
			$this->new_skilldata[$id][$k] = $v;
		}

		return $this;
	}

	/**
	 * Adds the name for the given language and skill
	 *
	 * needs to be run before `create()`
	 */
	public function addSkillLang(int $id, Lang|string $lang, string $name):static{

		if(!$lang instanceof Lang){
			$lang = new Lang($lang);
		}

		$this->new_skilldesc[$lang->id][$id] = $this->createLangFields($id);
		// required values: name
		$this->new_skilldesc[$lang->id][$id][Skill::DESC_NAME] = trim($name);

		return $this;
	}

	/**
	 * Creates the JSON skeletons, filled with some basic, non-changing data based on previous builds
	 */
	public function create():static{
		// we're using the current skill database as basis
		// create the skill data skeleton rows for all *known* skills
		foreach($this->databases[Lang::EN]::ID2DATA as $id => $_){
			$this->skilldata[$id] = $this->createDataFields($id);
			// add a row for existing pvp redirects
			if(array_key_exists($id, PVP_SPLIT)){
				$this->skilldata[PVP_SPLIT[$id]] = $this->createDataFields(PVP_SPLIT[$id]);
			}
		}

		// create language skeletons for the list of known IDs
		foreach($this->skilldata as $id => $_){
			foreach(Lang::IDS as $lang){
				$this->skilldesc[$lang][$id] = $this->createLangFields($id);
			}
		}

		// now fill the skill data with the known values
		foreach($this->skilldata as $id => &$row){
			foreach(Lang::IDS as $lang){

				try{
					$current = $this->databases[$lang]->get($id);
				}
				// the skill might be a new pvp redirect, data is added later
				catch(InvalidArgumentException){
					$this->logger->warning(sprintf('invalid data for [%-4s][%s]', $id, $lang));

					continue;
				}

				// add the skill name
				$this->skilldesc[$lang][$id][Skill::DESC_NAME] = $current->name;

				// we only need to update the data once here
				if($lang !== Lang::EN){
					continue;
				}

				// update skill data
				foreach($current->toArray() as $key => $value){
					// we'll keep these fields as they shouldn't change, and if so, a manual update is warranted
					if(in_array($key, [
						Skill::DATA_ID, Skill::DATA_CAMPAIGN, Skill::DATA_PROFESSION,
						Skill::DATA_ATTRIBUTE, Skill::DATA_IS_ELITE, Skill::DATA_IS_RP,
					], true)){
						$row[$key] = $value;
					}
					// this skill *is* a pvp version
					if($key === Skill::DATA_IS_PVP){
						$row[$key] = in_array($id, PVP_SPLIT, true);
					}
					// the skill *has* a pvp version
					if($key === Skill::DATA_PVP_SPLIT){
						$row[$key] = array_key_exists($id, PVP_SPLIT);
					}
					// the id of the pvp version of the current skill
					if($key === Skill::DATA_SPLIT_ID){
						$row[$key] = (PVP_SPLIT[$id] ?? 0);
						// add the base id to pvp-split skills
						if($row[Skill::DATA_IS_PVP] === true){
							$row[$key] = (PVP_SPLIT_FLIP[$id] ?? 0);
						}
					}
				}

			}
		}

		// add new skill data
		foreach($this->new_skilldata as $id => $new_row){
			$this->skilldata[$id] = $new_row;

			foreach(Lang::IDS as $lang){

				if(!array_key_exists($id, $this->new_skilldesc[$lang])){
					throw new RuntimeException(sprintf('invalid skill descriptions for [%-4s][%s]', $id, $lang));
				}

				$this->skilldesc[$lang][$id] = $this->new_skilldesc[$lang][$id];
			}
		}

		$this->saveSkilldata($this->skilldata);
		$this->saveSkillDescriptions($this->skilldesc);

		return $this;
	}

	/**
	 * Creates the combined and per-skill JSON files from the previously created JSON data/lang files
	 */
	public function buildJSON():static{
		Directory::create(static::JSON_SKILL_DIR);

		$jsonData = File::loadJSON(static::JSON_DATA_FILE, true);
		$jsonLang = [];

		foreach(static::JSON_LANG_FILES as $lang => $file){
			$jsonLang[$lang] = File::loadJSON($file, true);
		}

		foreach($jsonData['skilldata'] as $skillID => &$skillData){
			foreach(Lang::IDS as $lang){
				$desc = $jsonLang[$lang]['skilldesc'][$skillID];
				$prof = new Profession($skillData[Skill::DATA_PROFESSION], $lang);

				$skillData['lang'][$lang] = [
					Skill::DESC_NAME        => $desc[Skill::DESC_NAME],
					Skill::DESC_DESCRIPTION => $desc[Skill::DESC_DESCRIPTION],
					Skill::DESC_CONCISE     => $desc[Skill::DESC_CONCISE],
					Skill::DATA_CAMPAIGN    => (new Campaign($skillData[Skill::DATA_CAMPAIGN], $lang))->getName(),
					Skill::DATA_PROFESSION  => $prof->getName(),
					'profession_abbr'       => $prof->getAbbr(),
					Skill::DATA_ATTRIBUTE   => (new Attribute($skillData[Skill::DATA_ATTRIBUTE], $lang))->getName(),
					Skill::DATA_TYPE        => (new Type($skillData[Skill::DATA_TYPE], $lang))->getName(),
				];
			}

			$skill = [
				'$schema' => static::SCHEMA_SKILL,
				'skill'   => $skillData,
			];

			$this->saveJSON(sprintf('%s/%s.json', static::JSON_SKILL_DIR, $skillID), $skill);

			$this->logger->info(sprintf('JSON for [%-4s] %s', $skillID, $skillData['lang'][Lang::EN][Skill::DESC_NAME]));
		}

		$jsonData['$schema'] = static::SCHEMA_SKILLDATA_COMBINED;

		$savepath = $this->saveJSON(static::JSON_COMBINED, $jsonData);

		$this->logger->info(sprintf('saved JSON for combined skilldata to: %s', $savepath));

		return $this;
	}

	/**
	 * Fetches multiple pages from the Wiki and saves them to cache
	 */
	public function fetchSkilldescToCache():static{

		foreach(static::WIKIFETCHERS as $lang => $fqcn){
			$this->wikiFetcher = new $fqcn($this->options, $this->http, $this->logger, $this->databases[$lang]);
			$skilldesc  = File::loadJSON(static::JSON_LANG_FILES[$lang], true);
			$skillnames = array_column($skilldesc['skilldesc'], Skill::DESC_NAME, Skill::DATA_ID);

			unset($skillnames[0]); // unset the "No Skill" for the fetch request

			// 50 articles is mediawiki limit
			foreach(array_chunk($skillnames, 50, true) as $chonk){
				$this->wikiFetcher->fetchMulti($chonk);
			}

		}

		return $this;
	}

	/**
	 * Fetches the wiki pages for the given skills and parses them for skill data and descriptions
	 *
	 * @phan-suppress PhanTypeArraySuspiciousNullable
	 */
	public function fetchSkilldesc():static{
		$jsonData = File::loadJSON(static::JSON_DATA_FILE, true);

		foreach(static::WIKIFETCHERS as $lang => $fqcn){
			$this->wikiFetcher = new $fqcn($this->options, $this->http, $this->logger, $this->databases[$lang]);
			// load the previously created JSON
			$skilldesc = File::loadJSON(static::JSON_LANG_FILES[$lang], true);

			foreach($skilldesc['skilldesc'] as &$desc){
				$current = $this->databases[$lang]->get($desc[Skill::DATA_ID]);
				$data    = $this->wikiFetcher->fetch($desc[Skill::DESC_NAME], $desc[Skill::DATA_ID], $this->options->from_cache);

				if($data === null){
					$message = sprintf('invalid wiki data for [%-4s] %s', $desc[Skill::DESC_NAME], $desc[Skill::DATA_ID]);

					$this->logger->warning($message);
				}

				// update skill descriptions
				foreach(SkillDataInterface::KEYS_DESC as $k){

					if($k === Skill::DESC_NAME && $data[Skill::DESC_NAME] !== $desc[Skill::DESC_NAME]){
						$this->logger->info(sprintf('name fix: %s => %s', $desc[Skill::DESC_NAME], $data[Skill::DESC_NAME]));
					}

					// on CI, diff descriptions against previous versions, fail if there's a certain amount of changes
					if($k !== Skill::DESC_NAME && $this->options->diff_descriptions){
						$this->diffDescription($current->{$k}, $data[$k]);
					}

					$desc[$k] = $data[$k];
				}
				// while we're at it: update skill data
				if($this->options->update_skilldata && $data !== null){
					// update skill data from guildwiki
					if($this->wikiFetcher instanceof WikiFetcherGerman){
						foreach(WikiFetcherGerman::USE_FIELDS as $k){
							$jsonData['skilldata'][$desc[Skill::DATA_ID]][$k] = $data[$k];
						}
					}

					// update skill data from GWW
					if($this->wikiFetcher instanceof WikiFetcherEnglish){
						foreach(WikiFetcherEnglish::USE_FIELDS as $k){
							$jsonData['skilldata'][$desc[Skill::DATA_ID]][$k] = $data[$k];
						}
					}

				}

			}

			// save updated JSON
			$this->saveJSON(static::JSON_LANG_FILES[$lang], $skilldesc);
		}

		if($this->options->update_skilldata){
			$this->saveJSON(static::JSON_DATA_FILE, $jsonData);
		}

		return $this;
	}

	/**
	 * Creates the final output
	 */
	public function build():static{
		$json = File::loadJSON(static::JSON_DATA_FILE, true);

		$content = [
			'<?php // THERE BE DRAGONS',
			'declare(strict_types=1);',
			'namespace Buildwars\\GWSkillData;',
			'abstract class SkillData extends SkillDataAbstract{',
			'public const ID2DATA = [',
		];

		foreach($json['skilldata'] as $skillID => $data){
			foreach($data as &$field){
				if(is_bool($field)){
					$field = ($field === true) ? 'true' : 'false';
				}
			}

			$content[] = sprintf('%d=>[%s],', $skillID, implode(',', $data));
		}

		$content[] = '];}';

		$savepath = $this->saveFile(sprintf('%s/SkillData.php', SRCDIR), implode("\n", $content));

		$this->logger->info(sprintf('class SkillData saved to: %s', $savepath));


		foreach(static::JSON_LANG_FILES as $lang => $file){
			$json     = File::loadJSON($file, true);
			$language = (new Lang($lang))->getName();

			// unset the "id" field here
			foreach($json['skilldesc'] as &$row){
				unset($row[Skill::DATA_ID]);
			}

			$content = [
				'<?php // THERE BE DRAGONS',
				'declare(strict_types=1);',
				'namespace Buildwars\\GWSkillData;',
				sprintf('final class SkillLang%s extends SkillData{', $language),
				sprintf('public const LANG = Lang::%s;', strtoupper($lang)),
				'public const ID2DESC = [',
			];

			foreach($json['skilldesc'] as $skillID => $data){
				// escape single quotes
				$data = array_map(fn(string $str):string => strtr($str, ["'" => "\\'"]), $data);

				$content[] = sprintf("%d=>['%s'],", $skillID, implode("','", $data));
			}

			$content[] = '];}';

			$savepath = $this->saveFile(sprintf('%s/SkillLang%s.php', SRCDIR, $language), implode("\n", $content));

			$this->logger->info(sprintf('class SkillLang%s saved in %s', $language, $savepath));
		}

		return $this;
	}

	protected function initLogger():LoggerInterface{
		$formatter  = (new LineFormatter(null, 'Y-m-d H:i:s', true, true))->setJsonPrettyPrint(true);
		$logHandler = (new StreamHandler('php://stdout', $this->options->logLevel))->setFormatter($formatter);

		return new Logger('log', [$logHandler]);
	}

	protected function saveJSON(string $filepath, array $data):string{
		return $this->saveFile($filepath,  strtr(Str::jsonEncode($data), ['    ' => "\t"]));
	}

	protected function saveFile(string $filepath, string $data):string{
		File::save($filepath, $data."\n");

		return File::realpath($filepath);
	}

	protected function saveSkilldata(array $skilldata):void{
		ksort($skilldata);

		$jsonData = [
			'$schema'   => static::SCHEMA_SKILLDATA,
			'skilldata' => $skilldata,
		];

		$this->saveJSON(static::JSON_DATA_FILE, $jsonData);

		$this->logger->info(sprintf('saved skilldata: %s skills', count($skilldata)));
	}

	protected function saveSkillDescriptions(array $skilldesc):void{

		foreach(static::JSON_LANG_FILES as $lang => $file){
			ksort($skilldesc[$lang]);

			$jsonData = [
				'$schema'   => static::SCHEMA_SKILLDESC,
				'lang'      => $lang,
				'skilldesc' => $skilldesc[$lang],
			];

			$this->saveJSON($file, $jsonData);

			$count = count($skilldesc[$lang]);

			$this->logger->info(sprintf('saved lang "%s": %s skills', $lang, $count));
		}

	}

	protected function createDataFields(int $id):array{
		$fields = [];

		foreach(SkillDataInterface::KEYS_DATA as $key){
			$fields[$key] = null;

			if($key === 'id'){
				$fields[$key] = $id;
			}
		}

		return $fields;
	}

	protected function createLangFields(int $id):array{
		$fields = ['id' => $id];

		foreach(SkillDataInterface::KEYS_DESC as $key){
			$fields[$key] = null;
		}

		return $fields;
	}

	protected function diffDescription(string $current, string $new):void{

		if($current === $new){
			return;
		}

		$a1 = str_split($current);
		$a2 = str_split($new);

		// simple diff, we'll allow a certain threshold
		if((count(array_diff($a1, $a2)) + count(array_diff($a2, $a1))) < $this->options->diff_threshold){
			return;
		}

		// fail the CI run, manual update and review required
		throw new RuntimeException('diff error');
	}

}
