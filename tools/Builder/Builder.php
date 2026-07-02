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
use Buildwars\GWSkillData\SkillData;
use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillData\SkillLangEnglish;
use Buildwars\GWSkillData\SkillLangGerman;
use Buildwars\GWSkillData\Skilltype;
use Buildwars\GWSkillDataTools\BuilderOptions;
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
use function array_map;
use function count;
use function implode;
use function in_array;
use function is_bool;
use function ksort;
use function sprintf;
use function str_replace;
use function strtoupper;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;

class Builder{

	public const REPO_URL = 'https://build-wars.github.io/gw-skilldata';

	public const SCHEMA_SKILL              = self::REPO_URL.'/schemas/skill.schema.json';
	public const SCHEMA_SKILLDATA          = self::REPO_URL.'/schemas/skilldata.schema.json';
	public const SCHEMA_SKILLDESC          = self::REPO_URL.'/schemas/skilldesc.schema.json';
	public const SCHEMA_SKILLDATA_COMBINED = self::REPO_URL.'/schemas/skilldata-combined.schema.json';

	public const DATABASES = [
		SkillDataInterface::LANG_DE => SkillLangGerman::class,
		SkillDataInterface::LANG_EN => SkillLangEnglish::class,
	];

	protected const JSON_SKILL_DIR     = DATA_DIR.'/json-skills';
	protected const DATA_FILE          = DATA_DIR.'/json-full/skilldata.json';
	protected const DATA_FILE_COMBINED = DATA_DIR.'/json-full/skilldata-combined.json';

	protected const LANG_FILES = [
		'English' => ['en', DATA_DIR.'/json-full/skilldesc-en.json'],
		'German'  => ['de', DATA_DIR.'/json-full/skilldesc-de.json'],
	];

	protected readonly SettingsContainerInterface|BuilderOptions $options;
	protected readonly LoggerInterface                           $logger;
	protected readonly ClientInterface                           $http;
	protected readonly RequestFactoryInterface                   $requestFactory;

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

	protected function save_skilldata(array $skilldata):void{
		ksort($skilldata);

		$jsonData = [
			'$schema'   => static::SCHEMA_SKILLDATA,
			'skilldata' => $skilldata,
		];

		File::save(static::DATA_FILE, str_replace('    ', "\t", Str::jsonEncode($jsonData)));

		$this->logger->info(sprintf('saved skilldata: %s skills to %s', count($skilldata), File::realpath(static::DATA_FILE)));
	}

	protected function save_skill_descriptions(array $skilldesc):void{

		foreach(static::LANG_FILES as [$lang, $file]){
			ksort($skilldesc[$lang]);

			$jsonData = [
				'$schema'   => static::SCHEMA_SKILLDESC,
				'lang'      => $lang,
				'skilldesc' => $skilldesc[$lang],
			];

			File::save($file, str_replace('    ', "\t", Str::jsonEncode($jsonData)));

			$this->logger->info(
				sprintf('saved lang "%s": %s skills to %s', $lang, count($skilldesc[$lang]), File::realpath($file)),
			);
		}

	}

	public function create():static{

		$current_skilldata = SkillData::ID2DATA;
		/** @var array<string, \Buildwars\GWSkillData\SkillDataInterface> $skilldb */
		$current_skilldesc = [
			SkillDataInterface::LANG_DE => SkillLangGerman::ID2DESC,
			SkillDataInterface::LANG_EN => SkillLangEnglish::ID2DESC,
		];

		$skilldesc = [];
		$skilldata = [];

		// we're using the current skill database as basis
		// create the skill data rows
		foreach($current_skilldata as $id => $row){
			// create named fields
			foreach(SkillDataInterface::KEYS_DATA as $pos => $key){
				$skilldata[$id][$key] = null;
				// we'll keep these fields as they shouldn't change, and if so, a manual update is warranted
				if(in_array($key, ['id', 'campaign', 'profession', 'attribute', 'is_elite', 'is_rp'], true)){
					$skilldata[$id][$key] = $row[$pos];
				}
				// this skill *is* a pvp version
				if($key === 'is_pvp'){
					$skilldata[$id][$key] = in_array($id, PVP_SPLIT, true);
				}
				// the skill *has* a pvp version
				if($key === 'pvp_split'){
					$skilldata[$id][$key] = array_key_exists($id, PVP_SPLIT);
				}
				// the id of the pvp version of the current skill
				if($key === 'split_id'){
					$skilldata[$id][$key] = (PVP_SPLIT[$id] ?? 0);
				}
			}
		}


		// create the skill description rows
		foreach(SkillDataInterface::LANGUAGES as $lang){
			foreach($current_skilldesc[$lang] as $id => $row){
				// the ID field is not included in the php classes
				$skilldesc[$lang][$id]['id'] = $id;
				// create named fields
				foreach(SkillDataInterface::KEYS_DESC as $pos => $key){
					$skilldesc[$lang][$id][$key] = '';
					// add the name field as this is the article query for the wikis
					if($key === 'name'){
						$skilldesc[$lang][$id][$key] = $row[$pos];
					}
				}
			}
		}

		$this->save_skilldata($skilldata);
		$this->save_skill_descriptions($skilldesc);

		return $this;
	}

	public function build():static{

		/*
		 * skill data
		 */

		$json = File::loadJSON(static::DATA_FILE, true);

		// dump the PHP class
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
					$field = $field === true ? 'true' : 'false';
				}
			}

			$content[] = sprintf('%d=>[%s],', $skillID, implode(',', $data));
		}

		$content[] = "];}\n";

		$classFile = __DIR__.'/../../src/SkillData.php';

		File::save($classFile, implode("\n", $content));

		$this->logger->info(sprintf('class SkillData saved in %s', File::realpath($classFile)));


		/*
		 * skill descriptions
		 */

		foreach(static::LANG_FILES as $lang => [$abbr, $file]){
			$json = File::loadJSON($file, true);

			// unset the "id" field here
			foreach($json['skilldesc'] as &$row){
				unset($row['id']);
			}

			// dump the PHP class
			$content = [
				'<?php // THERE BE DRAGONS',
				'declare(strict_types=1);',
				'namespace Buildwars\\GWSkillData;',
				sprintf('final class SkillLang%s extends SkillData{', $lang),
				sprintf('public const LANG = self::LANG_%s;', strtoupper($abbr)),
				'public const ID2DESC = [',
			];

			foreach($json['skilldesc'] as $skillID => $data){
				// escape single quotes
				$data = array_map(fn(string $str):string => str_replace("'", "\\'", $str), $data);

				$content[] = sprintf("%d=>['%s'],", $skillID, implode("','", $data));
			}

			$content[] = "];}\n";

			$classFile = __DIR__.'/../../src/SkillLang'.$lang.'.php';

			File::save($classFile, implode("\n", $content));

			$this->logger->info(sprintf('class SkillLang%s saved in %s', $lang, File::realpath($classFile)));
		}

		return $this;
	}


	public function buildJSON():static{
		Directory::create(static::JSON_SKILL_DIR);

		$jsonData = File::loadJSON(static::DATA_FILE, true);
		$jsonLang = [];

		foreach(static::LANG_FILES as [$abbr, $file]){
			$jsonLang[$abbr] = File::loadJSON($file, true);
		}

		foreach($jsonData['skilldata'] as $skillID => &$skillData){
			foreach(['de', 'en'] as $abbr){
				$lang = $jsonLang[$abbr]['skilldesc'][$skillID];
				$prof = new Profession($skillData['profession'], $abbr);

				$skillData['lang'][$abbr] = [
					'name'            => $lang['name'],
					'description'     => $lang['description'],
					'concise'         => $lang['concise'],
					'campaign'        => (new Campaign($skillData['campaign'], $abbr))->getName(),
					'profession'      => $prof->getName(),
					'profession_abbr' => $prof->getAbbr(),
					'attribute'       => (new Attribute($skillData['attribute'], 0, $abbr))->getName(),
					'type'            => (new Skilltype($skillData['type'], $abbr))->getName(),
				];

			}

			$skill = [
				'$schema' => static::SCHEMA_SKILL,
				'skill'   => $skillData,
			];

			$this->logger->info(sprintf('JSON for [%-4s] %s ', $skillID, $skillData['lang']['en']['name']));

			File::save(
				sprintf('%s/%s.json', static::JSON_SKILL_DIR, $skillID),
				str_replace('    ', "\t", Str::jsonEncode($skill)),
			);
		}

		$jsonData['$schema'] = static::SCHEMA_SKILLDATA_COMBINED;

		File::save(static::DATA_FILE_COMBINED, str_replace('    ', "\t", Str::jsonEncode($jsonData)));

		$this->logger->info(sprintf('JSON for combined skilldata: %s ', File::realpath(static::DATA_FILE_COMBINED)));

		return $this;
	}

	protected const WIKIFETCHERS = [
		'English' => WikiFetcherEnglish::class,
		'German'  => WikiFetcherGerman::class,
	];

	public function fetchSkilldesc():static{
		$jsonData = File::loadJSON(static::DATA_FILE, true);

		foreach(static::WIKIFETCHERS as $language => $fqcn){
			// invoke fetcher
			/** @var \Buildwars\GWSkillDataTools\Fetchers\WikiFetcher $fetcher */
			$fetcher = new $fqcn($this->options, $this->http, $this->logger);

			// load the previously created JSON (see parse-pwnd)
			[$lang, $skilldescJSON] = static::LANG_FILES[$language];

			$skilldesc = File::loadJSON($skilldescJSON, true);

			foreach($skilldesc['skilldesc'] as &$desc){
				[$localized_desc, $skilldata] = $fetcher->fetch($desc['name'], $desc['id'], $this->options->from_cache);

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
			File::save($skilldescJSON, str_replace('    ', "\t", Str::jsonEncode($skilldesc)));
		}

		if($this->options->update_skilldata){
			File::save(static::DATA_FILE, str_replace('    ', "\t", Str::jsonEncode($jsonData)));
		}

		return $this;
	}
}
