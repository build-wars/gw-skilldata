<?php
/**
 * Class BuilderAbstract
 *
 * @created      02.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillDataTools\BuilderOptions;
use Buildwars\GWSkillDataTools\Fetchers\WikFetcherInterface;
use chillerlan\HTTP\CurlClient;
use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\HTTP\Utils\MessageUtil;
use chillerlan\Settings\SettingsContainerInterface;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use function array_map;
use function count;
use function implode;
use function is_bool;
use function ksort;
use function sha1;
use function sprintf;
use const BUILDDIR;
use const DATADIR;
use const SRCDIR;

abstract class BuilderAbstract implements BuilderInterface{

	/**
	 * We need an independent source of the known player skills, as the database files
	 * which are about to be written might break in the process and becone unusable.
	 *
	 * This file also contains the english and german wiki names for the respective fetchers,
	 * as well as the types from GWW, as assigning from the game data is quite messy.
	 */
	protected const string KNOWN_SKILLS_JSON       = __DIR__.'/known-skills.json';

	protected const string JSON_SKILLDATA_FILE     = DATADIR.'/json-full/skilldata.json';
	protected const string JSON_SKILLDATA_COMBINED = DATADIR.'/json-full/skilldata-combined.json';
	protected const string JSON_LANG_FILE          = DATADIR.'/json-full/skilldesc-%s.json';
	protected const string JSON_SKILL_DIR          = DATADIR.'/json-skills';

	// used for diffs
	protected const string WIKI_SKILLDATA_FILE     = BUILDDIR.'/skilldata-%s.json';

	protected const string PAWNED_CACHEDIR         = BUILDDIR.'/pawned';

	protected const string TOOLBOX_API_URL         = 'https://api.gwtoolbox.com/v1';
	protected const string TOOLBOX_SKILL_ENDPOINT  = self::TOOLBOX_API_URL.'/%s/skills.json';
	protected const string TOOLBOX_CACHEDIR        = BUILDDIR.'/toolbox';

	/**
	 * cache directories to create on startup
	 */
	protected const array CACHE_DIRS = [
		self::JSON_SKILL_DIR,
		self::PAWNED_CACHEDIR,
		self::TOOLBOX_CACHEDIR,
		WikFetcherInterface::WIKI_BULK_CACHE,
	];

	/**
	 * map of Lang => constant name for the class builder
	 */
	private const array LANG_CONST_NAME = [
		Lang::CN           => 'CN',
		Lang::DE           => 'DE',
		Lang::EN           => 'EN',
		Lang::ES           => 'ES',
		Lang::FR           => 'FR',
		Lang::IT           => 'IT',
		Lang::JA           => 'JA',
		Lang::KO           => 'KO',
		Lang::PL           => 'PL',
		Lang::RU           => 'RU',
		Lang::XX           => 'XX',
		Lang::ZH           => 'ZH',
		Lang::DE_GUILDWIKI => 'DE',
		Lang::EN_GWW       => 'EN',
		Lang::FR_GWIKI     => 'FR',
	];

	protected readonly SettingsContainerInterface|BuilderOptions $options;
	protected readonly LoggerInterface                           $logger;
	protected readonly ClientInterface                           $http;
	protected readonly RequestFactoryInterface                   $requestFactory;
	protected readonly ResponseFactoryInterface                  $responseFactory;
	protected readonly StreamFactoryInterface                    $streamFactory;

	protected readonly array $known;

	public function __construct(SettingsContainerInterface|BuilderOptions $options){
		$factory = new HTTPFactory;

		$this->requestFactory  = $factory;
		$this->responseFactory = $factory;
		$this->streamFactory   = $factory;

		$this->options         = $options;
		$this->logger          = $this->initLogger();
		$this->http            = new CurlClient($this->requestFactory, $this->options, $this->logger);

		$this->createCacheDirectories(self::CACHE_DIRS);
		$this->known = $this->loadKnownSkills();
	}

	protected function initLogger():LoggerInterface{
		$formatter  = new LineFormatter(null, 'Y-m-d H:i:s', true, true)->setJsonPrettyPrint(true);
		$logHandler = new StreamHandler('php://stdout', $this->options->logLevel)->setFormatter($formatter);

		return new Logger('log', [$logHandler]);
	}

	protected function createCacheDirectories(array $dirs):static{

		foreach($dirs as $dir){
			Directory::create($dir);

			if(!Directory::isWritable($dir) || !Directory::isReadable($dir)){
				throw new RuntimeException(sprintf('cannot read/write to cache dir [%s]', $dir));
			}
		}

		return $this;
	}

	protected function loadKnownSkills():array{
		$known = File::loadJSON(self::KNOWN_SKILLS_JSON, true);
		$data  = [];

		foreach($known as [$id, $type, $en, $de, $fr]){
			$data[$id] = [
				Skill::DATA_ID   => $id,
				Skill::DATA_TYPE => $type,
				Lang::EN         => $en,
				Lang::DE         => $de,
				Lang::FR         => $fr,
			];
		}

		return $data;
	}

	protected function saveFile(string $filepath, string $data):string{
		File::save($filepath, $data."\n");

		return File::realpath($filepath);
	}

	protected function saveJSON(string $filepath, array $data):string{
		return $this->saveFile($filepath, strtr(Str::jsonEncode($data), ['    ' => "\t"]));
	}

	protected function getJsonLangFile(string $langID):string{
		return sprintf(self::JSON_LANG_FILE, $langID);
	}

	protected function saveDataJSON(array $skillData, string $file = self::JSON_SKILLDATA_FILE):static{
		ksort($skillData);

		$jsonData = ['$schema' => self::SCHEMA_SKILLDATA, 'skilldata' => $skillData];
		$path     = $this->saveJSON($file, $jsonData);

		$this->logger->info(sprintf('JSON skilldata saved: %s skills to [%s]', count($skillData), $path));

		return $this;
	}

	protected function saveLangJSON(array $skillDesc, string $langID):static{
		ksort($skillDesc);

		$jsonData = ['$schema' => self::SCHEMA_SKILLDESC, 'lang' => new Lang($langID)->id, 'skilldesc' => $skillDesc];
		$path     = $this->saveJSON($this->getJsonLangFile($langID), $jsonData);

		$this->logger->info(sprintf('JSON skilldesc [%s] saved to [%s]', $langID, $path));

		return $this;
	}

	protected function createDataClass(array $skillData):static{

		$content = [
			'<?php // THERE BE DRAGONS',
			'declare(strict_types=1);',
			'namespace Buildwars\\GWSkillData;',
			'abstract class SkillData extends SkillDataAbstract{',
			'protected const array ID2DATA = [',
		];

		foreach($skillData as $skillID => $data){
			foreach($data as &$field){
				if(is_bool($field)){
					$field = ($field === true) ? 'true' : 'false';
				}
			}

			$content[] = sprintf('%d=>[%s],', $skillID, implode(',', $data));
		}

		$content[] = '];}';

		$savepath = $this->saveFile(sprintf('%s/SkillData.php', SRCDIR), implode("\n", $content));

		$this->logger->info(sprintf('class SkillData saved to: [%s]', $savepath));

		return $this;
	}

	protected function createLangClass(array $skillDesc, string $langID):static{
		$lang      = new Lang($langID);
		$className = $lang->getClassName($langID);

		$content = [
			'<?php // THERE BE DRAGONS',
			'declare(strict_types=1);',
			'namespace Buildwars\\GWSkillData;',
			sprintf('final class %s extends SkillData{', $className),
			sprintf('public const string LANG = Lang::%s;', self::LANG_CONST_NAME[$langID]),
			'protected const array ID2DESC = [',
		];

		foreach($skillDesc as $skillID => $data){
			unset($data[Skill::DATA_ID]);

			// escape single quotes
			$data = array_map(fn(string $str):string => strtr($str, ["'" => "\\'"]), $data);

			$content[] = sprintf("%d=>['%s'],", $skillID, implode("','", $data));
		}

		$content[] = '];}';

		$path = $this->saveFile(sprintf('%s/%s.php', SRCDIR, $className), implode("\n", $content));

		$this->logger->info(sprintf('class [%s] saved in [%s]', $className, $path));

		return $this;
	}

	protected function fetch(string $url, string $cachedir, bool $cached):ResponseInterface{

		$request = $this->requestFactory->createRequest('GET', $url);

		if($this->options->use_http_compression){
			// idk why the other compression methods (gzip, br, zstd) error out here on huge bulk requests,
			// might be a windows thing, i don't have the energy to check right now
			$request = $request->withHeader('Accept-Encoding', 'deflate;q=1.0, identity;q=0.8, *;q=0.1');
		}

		$cachefile = sprintf('%s/%s.json', $cachedir, sha1($url));

		if($cached === true && File::isReadable($cachefile)){
			$this->logger->info(sprintf('fetched: [%s] from cache [%s]', $url, File::realpath($cachefile)));

			$stream = $this->streamFactory->createStreamFromFile($cachefile);
			// using code 304 here to indicate a cache response
			return $this->responseFactory
				->createResponse(304)
				->withHeader('Content-Type', 'application/json')
				->withBody($stream);
		}

		$response = $this->http->sendRequest($request);
		$status   = $response->getStatusCode();

		if($status !== 200){
			throw new RuntimeException(sprintf('fetch error: http/%s at %s', $status, $request->getUri()->getHost()));
		}

		$path = $this->saveFile($cachefile, MessageUtil::decompress($response));

		$this->logger->info(sprintf('fetched: [%s] to cache [%s]', $url, $path));

		// we're replacing the possibly compressed body with the decompressed content so that we don't run into unexpected issues
		return $response->withBody($this->streamFactory->createStreamFromFile($cachefile));
	}

}
