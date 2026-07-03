<?php
/**
 * Class DBParser
 *
 * @created      01.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillData\SkillLangEnglish;
use Buildwars\GWSkillData\SkillLangGerman;
use Buildwars\GWSkillData\Skilltype;
use Buildwars\GWSkillDataTools\BuilderOptions;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcher;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherEnglish;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherGerman;
use chillerlan\HTTP\CurlClient;
use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\Settings\SettingsContainerInterface;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use function array_key_exists;
use function array_keys;
use function array_map;
use function count;
use function implode;
use function in_array;
use function is_bool;
use function ksort;
use function sprintf;
use function strtoupper;
use function strtr;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;
use const SRCDIR;

class Builder{

	public const REPO_URL = 'https://build-wars.github.io/gw-skilldata';

	public const SCHEMA_SKILL              = self::REPO_URL.'/schemas/skill.schema.json';
	public const SCHEMA_SKILLDATA          = self::REPO_URL.'/schemas/skilldata.schema.json';
	public const SCHEMA_SKILLDESC          = self::REPO_URL.'/schemas/skilldesc.schema.json';
	public const SCHEMA_SKILLDATA_COMBINED = self::REPO_URL.'/schemas/skilldata-combined.schema.json';

	protected const JSON_SKILL_DIR = DATA_DIR.'/json-skills';
	protected const JSON_DATA_FILE = DATA_DIR.'/json-full/skilldata.json';
	protected const JSON_COMBINED  = DATA_DIR.'/json-full/skilldata-combined.json';

	/** @var array<string, string>  */
	protected const JSON_LANG_FILES = [
		SkillDataInterface::LANG_DE => DATA_DIR.'/json-full/skilldesc-de.json',
		SkillDataInterface::LANG_EN => DATA_DIR.'/json-full/skilldesc-en.json',
	];

	/** @var array<string, string>  */
	public const DATABASES = [
		SkillDataInterface::LANG_DE => SkillLangGerman::class,
		SkillDataInterface::LANG_EN => SkillLangEnglish::class,
	];

	/** @var array<string, string>  */
	protected const WIKIFETCHERS = [
		SkillDataInterface::LANG_DE => WikiFetcherGerman::class,
		SkillDataInterface::LANG_EN => WikiFetcherEnglish::class,
	];

	protected readonly SettingsContainerInterface|BuilderOptions $options;
	protected readonly LoggerInterface                           $logger;
	protected readonly ClientInterface                           $http;
	protected readonly RequestFactoryInterface                   $requestFactory;

	protected WikiFetcher $wikiFetcher;

	/** @var array<string, \Buildwars\GWSkillData\SkillDataInterface> */
	protected readonly array $databases;

	protected array $skilldata = [];
	protected array $skilldesc = [];

	public function __construct(SettingsContainerInterface|BuilderOptions $options){
		$this->options   = $options;
		$this->databases = array_map(fn(string $fqcn):SkillDataInterface => new $fqcn, static::DATABASES);
		$this->logger    = $this->initLogger();

		$factory = new HTTPFactory;

		$this->requestFactory = $factory;
		$this->http           = new CurlClient($factory, $this->options);
	}

	protected function initLogger():LoggerInterface{
		$formatter  = (new LineFormatter(null, 'Y-m-d H:i:s', true, true))->setJsonPrettyPrint(true);
		$logHandler = (new StreamHandler('php://stdout', $this->options->logLevel))->setFormatter($formatter);

		return new Logger('log', [$logHandler]);
	}

	public function addSkill(int $id, int $campaign, int $profession, int $attribute, bool $is_elite, bool $is_rp):static{
		// required values: id, campaign, profession, attribute, type=0, is_elite, is_rp
		#$current_skilldata[9999] = [9999,0,5,2,0,true,true];
		return $this;
	}

	public function addSkillLang(int $id, string $lang, string $name):static{
		// required values: name
		#$current_skilldesc['de'][9999] = ['SKILL_NAME'];
		#$current_skilldesc['en'][9999] = ['SKILL_NAME'];
		return $this;
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

	public function create():static{
		// we're using the current skill database as basis
		// create the skill data rows
		foreach($this->databases[SkillDataInterface::LANG_EN]::ID2DATA as $id => $row){
			// create named fields
			foreach(SkillDataInterface::KEYS_DATA as $pos => $key){
				$this->skilldata[$id][$key] = null;
				// we'll keep these fields as they shouldn't change, and if so, a manual update is warranted
				if(in_array($key, ['id', 'campaign', 'profession', 'attribute', 'is_elite', 'is_rp'], true)){
					$this->skilldata[$id][$key] = $row[$pos];
				}
				// this skill *is* a pvp version
				if($key === 'is_pvp'){
					$this->skilldata[$id][$key] = in_array($id, PVP_SPLIT, true);
				}
				// the skill *has* a pvp version
				if($key === 'pvp_split'){
					$this->skilldata[$id][$key] = array_key_exists($id, PVP_SPLIT);
				}
				// the id of the pvp version of the current skill
				if($key === 'split_id'){
					// @todo: add pve version id to pvp skill
					$this->skilldata[$id][$key] = (PVP_SPLIT[$id] ?? 0);
				}
			}
		}

		// create the skill description rows
		foreach(array_keys(SkillDataInterface::LANGUAGES) as $lang){
			foreach($this->databases[$lang]::ID2DESC as $id => $row){
				// the ID field is not included in the php classes
				$this->skilldesc[$lang][$id]['id'] = $id;
				// create named fields
				foreach(SkillDataInterface::KEYS_DESC as $pos => $key){
					$this->skilldesc[$lang][$id][$key] = '';
					// add the name field as this is the article query for the wikis
					if($key === 'name'){
						$this->skilldesc[$lang][$id][$key] = $row[$pos];
					}
				}
			}
		}

		$this->saveSkilldata($this->skilldata);
		$this->saveSkillDescriptions($this->skilldesc);

		return $this;
	}

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
			$language = SkillDataInterface::LANGUAGES[$lang];

			// unset the "id" field here
			foreach($json['skilldesc'] as &$row){
				unset($row['id']);
			}

			$content = [
				'<?php // THERE BE DRAGONS',
				'declare(strict_types=1);',
				'namespace Buildwars\\GWSkillData;',
				sprintf('final class SkillLang%s extends SkillData{', $language),
				sprintf('public const LANG = self::LANG_%s;', strtoupper($lang)),
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

	public function buildJSON():static{
		Directory::create(static::JSON_SKILL_DIR);

		$jsonData = File::loadJSON(static::JSON_DATA_FILE, true);
		$jsonLang = [];

		foreach(static::JSON_LANG_FILES as $lang => $file){
			$jsonLang[$lang] = File::loadJSON($file, true);
		}

		foreach($jsonData['skilldata'] as $skillID => &$skillData){
			foreach(['de', 'en'] as $lang){
				$desc = $jsonLang[$lang]['skilldesc'][$skillID];
				$prof = new Profession($skillData['profession'], $lang);

				$skillData['lang'][$lang] = [
					'name'            => $desc['name'],
					'description'     => $desc['description'],
					'concise'         => $desc['concise'],
					'campaign'        => (new Campaign($skillData['campaign'], $lang))->getName(),
					'profession'      => $prof->getName(),
					'profession_abbr' => $prof->getAbbr(),
					'attribute'       => (new Attribute($skillData['attribute'], 0, $lang))->getName(),
					'type'            => (new Skilltype($skillData['type'], $lang))->getName(),
				];
			}

			$skill = [
				'$schema' => static::SCHEMA_SKILL,
				'skill'   => $skillData,
			];

			$this->saveJSON(sprintf('%s/%s.json', static::JSON_SKILL_DIR, $skillID), $skill);

			$this->logger->info(sprintf('JSON for [%-4s] %s', $skillID, $skillData['lang']['en']['name']));
		}

		$jsonData['$schema'] = static::SCHEMA_SKILLDATA_COMBINED;

		$savepath = $this->saveJSON(static::JSON_COMBINED, $jsonData);

		$this->logger->info(sprintf('saved JSON for combined skilldata to: %s', $savepath));

		return $this;
	}

	public function fetchSkilldesc():static{
		$jsonData = File::loadJSON(static::JSON_DATA_FILE, true);

		foreach(static::WIKIFETCHERS as $lang => $fqcn){
			$this->wikiFetcher = new $fqcn($this->options, $this->http, $this->logger);
			// load the previously created JSON
			$skilldesc = File::loadJSON(static::JSON_LANG_FILES[$lang], true);

			foreach($skilldesc['skilldesc'] as &$desc){
				[$localized_desc, $skilldata] = $this->wikiFetcher->fetch($desc['name'], $desc['id'], $this->options->from_cache);

				// update skill data from guildwiki
				if($this->options->update_skilldata && $lang === 'de' && $skilldata !== null){
					foreach(['upkeep', 'energy', 'activation', 'recharge', 'adrenaline_precise', 'sacrifice', 'overcast'] as $k){
						$jsonData['skilldata'][$desc['id']][$k] = $skilldata[$k];
					}
				}

				// update skill data from GWW
				if($this->options->update_skilldata && $lang === 'en' && $skilldata !== null){
					foreach(['type', 'adrenaline'] as $k){
						$jsonData['skilldata'][$desc['id']][$k] = $skilldata[$k];
					}
				}

				if($localized_desc === null){
					continue;
				}

				[$name, $desc['description'], $desc['concise']] = $localized_desc;

				if($name !== $desc['name']){
					$this->logger->info(sprintf('name fix: %s => %s', $desc['name'], $name));

					$desc['name'] = $name;
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
}
