<?php
/**
 * common.php
 *
 * @created      25.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use Buildwars\GWSkillData\Attribute;
use chillerlan\HTTP\CurlClient;
use chillerlan\HTTP\HTTPOptions;
use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\Utilities\File;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use function array_map;
use function explode;
use function ini_set;
use function mb_strtolower;
use function str_contains;
use function trim;

require_once __DIR__.'/../vendor/autoload.php';

ini_set('date.timezone', 'UTC');
ini_set('memory_limit', -1);

const logLevel = LogLevel::DEBUG;
const cacert   = __DIR__.'/cacert.pem';

// init logger
$formatter  = (new LineFormatter(null, 'Y-m-d H:i:s', true, true))->setJsonPrettyPrint(true);
$logHandler = (new StreamHandler('php://stdout', logLevel))->setFormatter($formatter);
$logger     = new Logger('log', [$logHandler]); // phpcs:ignore
#$logger     = new \Psr\Log\NullLogger;

// init http
$httpOptions = new HTTPOptions(['ca_info' => cacert, 'timeout' => 30]);
$http        = new CurlClient(new HTTPFactory, $httpOptions); // phpcs:ignore

const DATA_FILE = __DIR__.'/../data/json-full/skilldata.json';

const LANG_FILES = [
	'English' => ['en', __DIR__.'/../data/json-full/skilldesc-en.json'],
	'German'  => ['de', __DIR__.'/../data/json-full/skilldesc-de.json'],
];

// the paw-ned skill databases for each language. more to come... probably never.
const PWND_CSV = [
	'pve' => [
		'de' => __DIR__.'/../data/paw-ned/de_classic_pve.csv',
		'en' => __DIR__.'/../data/paw-ned/en_classic_pve.csv',
	],
	'pvp' => [
		'de' => __DIR__.'/../data/paw-ned/de_classic_pvp.csv',
		'en' => __DIR__.'/../data/paw-ned/en_classic_pvp.csv',
	],
];

// skills that have deviating PvP versions
// see: https://wiki.guildwars.com/wiki/List_of_PvP_versions_of_skills
// skill id => pvp skill id
const PVP_SPLIT = [
	17   => 3063,
	18   => 3179,
	19   => 2998,
	26   => 3151,
	27   => 3180,
	33   => 3181,
	37   => 3373,
	49   => 2734,
	53   => 3183,
	54   => 3152,
	55   => 3289,
	110  => 3058,
	117  => 2859,
	118  => 2885,
	145  => 3059,
	180  => 3375,
	181  => 2860,
	209  => 2803,
	219  => 2809,
	226  => 2804,
	236  => 2805,
	239  => 2806,
	243  => 2999,
	257  => 2857,
	268  => 2891,
	287  => 3232,
	294  => 2887,
	318  => 3204,
	343  => 2883,
	348  => 2858,
	374  => 3002,
	398  => 2861,
	415  => 2657,
	432  => 2969,
	436  => 3045,
	441  => 3047,
	448  => 3060,
	453  => 3141,
	775  => 3061,
	780  => 3251,
	791  => 2866,
	792  => 2868,
	793  => 2893,
	817  => 2863,
	826  => 2862,
	836  => 2807,
	865  => 3396,
	871  => 3006,
	878  => 3234,
	879  => 3374,
	880  => 3187,
	900  => 3186,
	911  => 3005,
	920  => 3008,
	921  => 3014,
	923  => 3017,
	934  => 3188,
	963  => 3018,
	979  => 3191,
	981  => 3013,
	982  => 3016,
	993  => 2808,
	994  => 3143,
	1022 => 3252,
	1031 => 3048,
	1041 => 3049,
	1052 => 3184,
	1057 => 3185,
	1066 => 3233,
	1114 => 2892,
	1191 => 2864,
	1194 => 3050,
	1195 => 3144,
	1199 => 3145,
	1202 => 3051,
	1232 => 3003,
	1239 => 2965,
	1246 => 2867,
	1247 => 3007,
	1249 => 3010,
	1250 => 3011,
	1251 => 3012,
	1252 => 3015,
	1253 => 3019,
	1255 => 3020,
	1266 => 3009,
	1336 => 3189,
	1341 => 3190,
	1344 => 3386,
	1345 => 3192,
	1349 => 3194,
	1380 => 3021,
	1397 => 2871,
	1481 => 2872,
	1483 => 3263,
	1487 => 3264,
	1489 => 3265,
	1490 => 3266,
	1495 => 3346,
	1497 => 3347,
	1512 => 3348,
	1513 => 3269,
	1516 => 2884,
	1519 => 3270,
	1522 => 3271,
	1527 => 3272,
	1536 => 3367,
	1549 => 2875,
	1555 => 2876,
	1558 => 3026,
	1559 => 3148,
	1564 => 2877,
	1572 => 3027,
	1575 => 3028,
	1577 => 3062,
	1578 => 3149,
	1580 => 3029,
	1589 => 3032,
	1592 => 3033,
	1593 => 3035,
	1594 => 3036,
	1595 => 3037,
	1596 => 2879,
	1598 => 2880,
	1639 => 2869,
	1657 => 3193,
	1663 => 3397,
	1698 => 3156,
	1720 => 3147,
	1724 => 2959,
	1732 => 3157,
	1734 => 3022,
	1742 => 2966,
	1745 => 3023,
	1747 => 3024,
	1748 => 3025,
	1754 => 3365,
	1762 => 3366,
	1771 => 2878,
	1778 => 3030,
	1780 => 3031,
	1781 => 3034,
	2005 => 2895,
	2014 => 3273,
	2015 => 3437,
	2018 => 3040,
	2053 => 3196,
	2056 => 3195,
	2069 => 2925,
	2139 => 3054,
	2146 => 3368,
	2186 => 3053,
	2191 => 3398,
	2204 => 3039,
	2205 => 3038,
];

// pawned uses negative numbers for the pve attributes
const PWND_ATTR_TRANSLATE = [
	-9 => Attribute::TITLE_NORN,
	-8 => Attribute::TITLE_VANGUARD,
	-7 => Attribute::TITLE_DELDRIMOR,
	-6 => Attribute::TITLE_ASURA,
	-5 => Attribute::TITLE_KURZICK,
	-4 => Attribute::TITLE_LUXON,
	-3 => Attribute::TITLE_LIGHTBRINGER,
	-2 => Attribute::TITLE_SUNSPEAR,
	-1 => Attribute::NONE,
];


function load_pawned_file(string $file):array{
	$data = File::load($file);

	// the original paw-ned² files are stored in Windows-1252
	if(mb_detect_encoding($data, ['Windows-1252', 'UTF-8']) !== 'UTF-8'){
		$data = mb_convert_encoding($data, 'UTF-8', 'Windows-1252');
	}

	// split the CSV into an array
	return array_map(fn(string $line):array => explode(';', trim($line), 20), explode("\n", trim($data)));
}

function str_contains_any(string $haystack, array $needles, bool $case_insensitive = false):bool{

	if($case_insensitive){
		$haystack = mb_strtolower($haystack);
	}

	foreach($needles as $needle){

		if($case_insensitive){
			$needle = mb_strtolower($needle);
		}

		if(str_contains($haystack, $needle)){
			return true;
		}
	}

	return false;
}
