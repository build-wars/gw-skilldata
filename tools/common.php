<?php
/**
 * Common settings, includes and functions used during build
 *
 * @created      25.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use RuntimeException;
use function array_flip;
use function count;
use function define;
use function ini_set;
use function mb_internal_encoding;
use function preg_match_all;
use function sort;
use const PREG_UNMATCHED_AS_NULL;
use const SORT_NATURAL;

require_once __DIR__.'/../vendor/autoload.php';

ini_set('date.timezone', 'UTC');
ini_set('memory_limit', -1);
mb_internal_encoding('UTF-8');

$builddir = __DIR__.'/../.build';

Directory::create($builddir);

if(!Directory::isWritable($builddir) || !Directory::isReadable($builddir)){
	throw new RuntimeException('cannot read/write build dir');
}

#define('IS_CI', isset($_SERVER['GITHUB_ACTIONS']));
define('BUILDDIR', File::realpath($builddir));
define('DATA_DIR', File::realpath(__DIR__.'/../data'));
define('SRCDIR', File::realpath(__DIR__.'/../src'));

/**
 * Skills that have deviating PvP versions
 *
 * This list is hardcoded as it rarely changes.
 *
 * skill id => pvp skill id
 *
 * @see https://wiki.guildwars.com/wiki/List_of_PvP_versions_of_skills
 * @var array<int, int>
 */
const PVP_SPLIT = [
	17   => 3063,
	18   => 3179,
	19   => 2998,
	26   => 3151,
	27   => 3180,
	33   => 3181,
	37   => 3373,
	49   => 2734,
	50   => 3447,
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
	266  => 3448,
	268  => 2891,
	287  => 3232,
	294  => 2887,
	318  => 3204,
	326  => 3449,
	343  => 2883,
	346  => 3443,
	348  => 2858,
	374  => 3002,
	381  => 3444,
	393  => 3450,
	398  => 2861,
	415  => 2657,
	432  => 2969,
	436  => 3045,
	441  => 3047,
	447  => 3451,
	448  => 3060,
	453  => 3141,
	476  => 3445,
	775  => 3061,
	780  => 3251,
	791  => 2866,
	792  => 2868,
	793  => 2893,
	810  => 3457,
	817  => 2863,
	826  => 2862,
	831  => 3458,
	836  => 2807,
	853  => 3459,
	865  => 3396,
	869  => 3456,
	871  => 3006,
	878  => 3234,
	879  => 3374,
	880  => 3187,
	885  => 3454,
	900  => 3186,
	905  => 3455,
	911  => 3005,
	920  => 3008,
	921  => 3014,
	923  => 3017,
	928  => 3452,
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
	1043 => 3453,
	1052 => 3184,
	1057 => 3185,
	1066 => 3233,
	1114 => 2892,
	1191 => 2864,
	1194 => 3050,
	1195 => 3144,
	1199 => 3145,
	1202 => 3051,
	1213 => 3460,
	1232 => 3003,
	1239 => 2965,
	1240 => 3461,
	1246 => 2867,
	1247 => 3007,
	1249 => 3010,
	1250 => 3011,
	1251 => 3012,
	1252 => 3015,
	1253 => 3019,
	1255 => 3020,
	1259 => 3462,
	1266 => 3009,
	1336 => 3189,
	1341 => 3190,
	1342 => 3463,
	1344 => 3386,
	1345 => 3192,
	1349 => 3194,
	1380 => 3021,
	1397 => 2871,
	1404 => 3464,
	1405 => 3465,
	1481 => 2872,
	1483 => 3263,
	1487 => 3264,
	1489 => 3265,
	1490 => 3266,
	1495 => 3346,
	1497 => 3347,
	1500 => 3469,
	1512 => 3348,
	1513 => 3269,
	1514 => 3470,
	1516 => 2884,
	1519 => 3270,
	1522 => 3271,
	1527 => 3272,
	1529 => 3471,
	1536 => 3367,
	1544 => 3472,
	1547 => 3442,
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
	1730 => 3466,
	1732 => 3157,
	1734 => 3022,
	1742 => 2966,
	1745 => 3023,
	1747 => 3024,
	1748 => 3025,
	1754 => 3365,
	1755 => 3467,
	1758 => 3468,
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
	2070 => 3473,
	2139 => 3054,
	2146 => 3368,
	2186 => 3053,
	2191 => 3398,
	2204 => 3039,
	2205 => 3038,
];

// convenience
define('PVP_SPLIT_FLIP', array_flip(PVP_SPLIT));

/**
 * Matches the progression values in the given skill description.
 *
 * Returns the matches as an array, returns null if no progression could be found.
 */
function getProgressions(string $description, bool $sort = false):array|null{
	$r = preg_match_all('/(?<progression>\d+\.+\d+)/', $description, $matches, PREG_UNMATCHED_AS_NULL);

	if($r === false || $r < 1){
		return null;
	}

	if($sort){
		sort($matches['progression'], SORT_NATURAL);
	}

	return $matches['progression'];
}

/**
 * Compares the progression values of the given skill descriptions.
 *
 * Returns null if there are no differences, returns an array of progression matches if there were differences.
 */
function diffProgressions(string $desc1, string $desc2, bool $sort = false):array|null{
	$prog1 = getProgressions($desc1, $sort);
	$prog2 = getProgressions($desc2, $sort);

	// description does not contain progressions
	if($prog1 === null && $prog2 === null){
		return null;
	}
	// general discrepancy: either of the descriptions does not have a progression
	// or count of progressions does not match (???)
	if($prog1 === null || $prog2 === null || count($prog1) !== count($prog2)){
		return [$prog1, $prog2];
	}

	$diff = 0;

	foreach($prog1 as $i => $value){
		if($prog2[$i] !== $value){
			$diff++;
		}
	}

	if($diff > 0){
		return [$prog1, $prog2];
	}

	// no differences
	return null;
}
