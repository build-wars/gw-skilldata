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

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Campaign;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\Profession;
use Buildwars\GWSkillData\Common\Type;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillDataTools\BuilderOptions;
use Buildwars\GWSkillDataTools\Fetchers\WikFetcherInterface;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherEnglish;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherFrench;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherGerman;
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

	protected const string JS_DIST_DIR             = BUILDDIR.'/js-dist';
	protected const string JSON_SKILL_DIR          = BUILDDIR.'/json-skills';
	protected const string PAWNED_CACHEDIR         = BUILDDIR.'/pawned';
	protected const string TOOLBOX_CACHEDIR        = BUILDDIR.'/toolbox';

	protected const string JSON_SKILLDATA_FILE     = DATADIR.'/json-full/skilldata.json';
	protected const string JSON_LANG_FILE          = DATADIR.'/json-full/skilldesc-%s.json';
	protected const string JSON_SKILLDATA_COMBINED = BUILDDIR.'/skilldata-combined.json';
	// used for diffs
	protected const string WIKI_SKILLDATA_FILE     = BUILDDIR.'/skilldata-%s.json';

	protected const string TOOLBOX_API_URL         = 'https://api.gwtoolbox.com/v1';
	protected const string TOOLBOX_SKILL_ENDPOINT  = self::TOOLBOX_API_URL.'/%s/skills.json';

	/**
	 * cache directories to create on startup
	 */
	protected const array CACHE_DIRS = [
		BUILDDIR, // this already exists, but added here for clearing
		self::JS_DIST_DIR,
		self::JSON_SKILL_DIR,
		self::PAWNED_CACHEDIR,
		self::TOOLBOX_CACHEDIR,
		WikFetcherInterface::WIKI_BULK_CACHE,
		WikiFetcherEnglish::CACHEDIR,
		WikiFetcherGerman::CACHEDIR,
		WikiFetcherFrench::CACHEDIR,
	];

	/*
	 * maps of Lang => constant name for the class builder
	 */

	private const array CONST_LANG = [
		Lang::CN           => 'Lang::CN',
		Lang::DE           => 'Lang::DE',
		Lang::EN           => 'Lang::EN',
		Lang::ES           => 'Lang::ES',
		Lang::FR           => 'Lang::FR',
		Lang::IT           => 'Lang::IT',
		Lang::JA           => 'Lang::JA',
		Lang::KO           => 'Lang::KO',
		Lang::PL           => 'Lang::PL',
		Lang::RU           => 'Lang::RU',
		Lang::XX           => 'Lang::XX',
		Lang::ZH           => 'Lang::ZH',
		Lang::DE_GUILDWIKI => 'Lang::DE',
		Lang::EN_GWW       => 'Lang::EN',
		Lang::FR_GWIKI     => 'Lang::FR',
	];

	private const array CONST_CAMPAIGN = [
		Campaign::CORE             => 'C::CORE',
		Campaign::PROPHECIES       => 'C::PROPHECIES',
		Campaign::FACTIONS         => 'C::FACTIONS',
		Campaign::NIGHTFALL        => 'C::NIGHTFALL',
		Campaign::EYE_OF_THE_NORTH => 'C::EYE_OF_THE_NORTH',
	];

	private const array CONST_PROFESSION = [
		Profession::NONE         => 'P::NONE',
		Profession::WARRIOR      => 'P::WARRIOR',
		Profession::RANGER       => 'P::RANGER',
		Profession::MONK         => 'P::MONK',
		Profession::NECROMANCER  => 'P::NECROMANCER',
		Profession::MESMER       => 'P::MESMER',
		Profession::ELEMENTALIST => 'P::ELEMENTALIST',
		Profession::ASSASSIN     => 'P::ASSASSIN',
		Profession::RITUALIST    => 'P::RITUALIST',
		Profession::PARAGON      => 'P::PARAGON',
		Profession::DERVISH      => 'P::DERVISH',
	];

	private const array CONST_ATTRIBUTE = [
		Attribute::FAST_CASTING        => 'A::FAST_CASTING',
		Attribute::ILLUSION_MAGIC      => 'A::ILLUSION_MAGIC',
		Attribute::DOMINATION_MAGIC    => 'A::DOMINATION_MAGIC',
		Attribute::INSPIRATION_MAGIC   => 'A::INSPIRATION_MAGIC',
		Attribute::BLOOD_MAGIC         => 'A::BLOOD_MAGIC',
		Attribute::DEATH_MAGIC         => 'A::DEATH_MAGIC',
		Attribute::SOUL_REAPING        => 'A::SOUL_REAPING',
		Attribute::CURSES              => 'A::CURSES',
		Attribute::AIR_MAGIC           => 'A::AIR_MAGIC',
		Attribute::EARTH_MAGIC         => 'A::EARTH_MAGIC',
		Attribute::FIRE_MAGIC          => 'A::FIRE_MAGIC',
		Attribute::WATER_MAGIC         => 'A::WATER_MAGIC',
		Attribute::ENERGY_STORAGE      => 'A::ENERGY_STORAGE',
		Attribute::HEALING_PRAYERS     => 'A::HEALING_PRAYERS',
		Attribute::SMITING_PRAYERS     => 'A::SMITING_PRAYERS',
		Attribute::PROTECTION_PRAYERS  => 'A::PROTECTION_PRAYERS',
		Attribute::DIVINE_FAVOR        => 'A::DIVINE_FAVOR',
		Attribute::STRENGTH            => 'A::STRENGTH',
		Attribute::AXE_MASTERY         => 'A::AXE_MASTERY',
		Attribute::HAMMER_MASTERY      => 'A::HAMMER_MASTERY',
		Attribute::SWORDMANSHIP        => 'A::SWORDMANSHIP',
		Attribute::TACTICS             => 'A::TACTICS',
		Attribute::BEAST_MASTERY       => 'A::BEAST_MASTERY',
		Attribute::EXPERTISE           => 'A::EXPERTISE',
		Attribute::WILDERNESS_SURVIVAL => 'A::WILDERNESS_SURVIVAL',
		Attribute::MARKMANSHIP         => 'A::MARKMANSHIP',
		Attribute::DAGGER_MASTERY      => 'A::DAGGER_MASTERY',
		Attribute::DEADLY_ARTS         => 'A::DEADLY_ARTS',
		Attribute::SHADOW_ARTS         => 'A::SHADOW_ARTS',
		Attribute::COMMUNING           => 'A::COMMUNING',
		Attribute::RESTORATION_MAGIC   => 'A::RESTORATION_MAGIC',
		Attribute::CHANNELING_MAGIC    => 'A::CHANNELING_MAGIC',
		Attribute::CRITICAL_STRIKES    => 'A::CRITICAL_STRIKES',
		Attribute::SPAWNING_POWER      => 'A::SPAWNING_POWER',
		Attribute::SPEAR_MASTERY       => 'A::SPEAR_MASTERY',
		Attribute::COMMAND             => 'A::COMMAND',
		Attribute::MOTIVATION          => 'A::MOTIVATION',
		Attribute::LEADERSHIP          => 'A::LEADERSHIP',
		Attribute::SCYTHE_MASTERY      => 'A::SCYTHE_MASTERY',
		Attribute::WIND_PRAYERS        => 'A::WIND_PRAYERS',
		Attribute::EARTH_PRAYERS       => 'A::EARTH_PRAYERS',
		Attribute::MYSTICISM           => 'A::MYSTICISM',
		Attribute::NONE                => 'A::NONE',
		Attribute::TITLE_SUNSPEAR      => 'A::TITLE_SUNSPEAR',
		Attribute::TITLE_LIGHTBRINGER  => 'A::TITLE_LIGHTBRINGER',
		Attribute::TITLE_LUXON         => 'A::TITLE_LUXON',
		Attribute::TITLE_KURZICK       => 'A::TITLE_KURZICK',
		Attribute::TITLE_ASURA         => 'A::TITLE_ASURA',
		Attribute::TITLE_DELDRIMOR     => 'A::TITLE_DELDRIMOR',
		Attribute::TITLE_VANGUARD      => 'A::TITLE_VANGUARD',
		Attribute::TITLE_NORN          => 'A::TITLE_NORN',
	];

	private const array CONST_TYPE = [
		Type::NONE                    => 'T::NONE',
		Type::SKILL                   => 'T::SKILL',
		Type::BOW_ATTACK              => 'T::BOW_ATTACK',
		Type::MELEE_ATTACK            => 'T::MELEE_ATTACK',
		Type::AXE_ATTACK              => 'T::AXE_ATTACK',
		Type::LEAD_ATTACK             => 'T::LEAD_ATTACK',
		Type::OFF_HAND_ATTACK         => 'T::OFF_HAND_ATTACK',
		Type::DUAL_ATTACK             => 'T::DUAL_ATTACK',
		Type::HAMMER_ATTACK           => 'T::HAMMER_ATTACK',
		Type::SCYTHE_ATTACK           => 'T::SCYTHE_ATTACK',
		Type::SWORD_ATTACK            => 'T::SWORD_ATTACK',
		Type::PET_ATTACK              => 'T::PET_ATTACK',
		Type::SPEAR_ATTACK            => 'T::SPEAR_ATTACK',
		Type::CHANT                   => 'T::CHANT',
		Type::ECHO                    => 'T::ECHO',
		Type::FORM                    => 'T::FORM',
		Type::GLYPH                   => 'T::GLYPH',
		Type::PREPARATION             => 'T::PREPARATION',
		Type::BINDING_RITUAL          => 'T::BINDING_RITUAL',
		Type::NATURE_RITUAL           => 'T::NATURE_RITUAL',
		Type::SHOUT                   => 'T::SHOUT',
		Type::SIGNET                  => 'T::SIGNET',
		Type::SPELL                   => 'T::SPELL',
		Type::ENCHANTMENT_SPELL       => 'T::ENCHANTMENT_SPELL',
		Type::HEX_SPELL               => 'T::HEX_SPELL',
		Type::ITEM_SPELL              => 'T::ITEM_SPELL',
		Type::WARD_SPELL              => 'T::WARD_SPELL',
		Type::WEAPON_SPELL            => 'T::WEAPON_SPELL',
		Type::WELL_SPELL              => 'T::WELL_SPELL',
		Type::STANCE                  => 'T::STANCE',
		Type::TRAP                    => 'T::TRAP',
		Type::RANGED_ATTACK           => 'T::RANGED_ATTACK',
		Type::EBON_VANGUARD_RITUAL    => 'T::EBON_VANGUARD_RITUAL',
		Type::FLASH_ENCHANTMENT_SPELL => 'T::FLASH_ENCHANTMENT_SPELL',
		Type::ATTACK_SKILL            => 'T::ATTACK_SKILL',
		Type::DAGGER_ATTACK           => 'T::DAGGER_ATTACK',
		Type::RITUAL                  => 'T::RITUAL',
		Type::DOUBLE_ENCHANTMENT      => 'T::DOUBLE_ENCHANTMENT',
		Type::TOUCH_SKILL             => 'T::TOUCH_SKILL',
		Type::TOUCH_SPELL             => 'T::TOUCH_SPELL',
		Type::TOUCH_ENCHANTMENT_SPELL => 'T::TOUCH_ENCHANTMENT_SPELL',
		Type::TOUCH_HEX_SPELL         => 'T::TOUCH_HEX_SPELL',
		Type::TOUCH_SIGNET            => 'T::TOUCH_SIGNET',
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

	/**
	 * @param string[] $dirs
	 * @throws \RuntimeException
	 */
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

	/**
	 * @param array<string, mixed> $data
	 */
	protected function saveJSON(string $filepath, array $data):string{
		return $this->saveFile($filepath, strtr(Str::jsonEncode($data), ['    ' => "\t"]));
	}

	protected function getJsonLangFile(string $langID):string{
		return sprintf(self::JSON_LANG_FILE, $langID);
	}

	/**
	 * @param array<int, array<string, scalar>> $skillData
	 */
	protected function saveDataJSON(array $skillData, string $file = self::JSON_SKILLDATA_FILE):static{
		ksort($skillData);

		$jsonData = ['$schema' => self::SCHEMA_SKILLDATA, 'skilldata' => $skillData];
		$path     = $this->saveJSON($file, $jsonData);

		$this->logger->info(sprintf('JSON skilldata saved: %s skills to [%s]', count($skillData), $path));

		return $this;
	}

	/**
	 * @param array<int, array<string, string|int>> $skillDesc
	 */
	protected function saveLangJSON(array $skillDesc, string $langID):static{
		ksort($skillDesc);

		$jsonData = ['$schema' => self::SCHEMA_SKILLDESC, 'lang' => new Lang($langID)->id, 'skilldesc' => $skillDesc];
		$path     = $this->saveJSON($this->getJsonLangFile($langID), $jsonData);

		$this->logger->info(sprintf('JSON skilldesc [%s] saved to [%s]', $langID, $path));

		return $this;
	}

	/**
	 * @param array<int, array<string, scalar>> $skillData
	 */
	protected function createDataClass(array $skillData):static{

		$content = [
			'<?php // THERE BE DRAGONS',
			'declare(strict_types=1);',
			'namespace Buildwars\\GWSkillData;',
			'use Buildwars\GWSkillData\Common\{Attribute as A, Campaign as C, Profession as P, Type as T};',
			'abstract class SkillData extends SkillDataAbstract{',
			'protected const array ID2DATA = [',
		];

		foreach($skillData as $skillID => $data){
			foreach($data as $key => &$field){

				$field = match($key){
					Skill::DATA_ATTRIBUTE  => self::CONST_ATTRIBUTE[$field],
					Skill::DATA_CAMPAIGN   => self::CONST_CAMPAIGN[$field],
					Skill::DATA_PROFESSION => self::CONST_PROFESSION[$field],
					Skill::DATA_TYPE       => self::CONST_TYPE[$field],
					Skill::DATA_IS_ELITE, Skill::DATA_IS_RP, Skill::DATA_IS_PVP, Skill::DATA_PVP_SPLIT
						=> ($field === true) ? 'true' : 'false',
					default => $field,
				};

			}

			$content[] = sprintf('%d=>[%s],', $skillID, implode(',', $data));
		}

		$content[] = '];}';

		$savepath = $this->saveFile(sprintf('%s/SkillData.php', SRCDIR), implode("\n", $content));

		$this->logger->info(sprintf('class SkillData saved to: [%s]', $savepath));

		return $this;
	}

	/**
	 * @param array<int, array<string, string|int>> $skillDesc
	 */
	protected function createLangClass(array $skillDesc, string $langID):static{
		$lang      = new Lang($langID);
		$className = $lang->getClassName($langID);

		$content = [
			'<?php // THERE BE DRAGONS',
			'declare(strict_types=1);',
			'namespace Buildwars\\GWSkillData;',
			'use Buildwars\\GWSkillData\\Common\\Lang;',
			sprintf('final class %s extends SkillData{', $className),
			sprintf('public const string LANG = %s;', self::CONST_LANG[$langID]),
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
