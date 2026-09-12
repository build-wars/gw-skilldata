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

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Campaign;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\Profession;
use Buildwars\GWSkillData\Common\Type;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use RuntimeException;
use function array_flip;
use function define;
use function ini_set;
use function mb_internal_encoding;

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
define(__NAMESPACE__.'\\BUILDDIR', File::realpath($builddir));
define(__NAMESPACE__.'\\DATADIR', File::realpath(__DIR__.'/../data'));
define(__NAMESPACE__.'\\PUBLICDIR', File::realpath(__DIR__.'/../public'));
define(__NAMESPACE__.'\\SRCDIR', File::realpath(__DIR__.'/../src'));

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
define(__NAMESPACE__.'\\PVP_SPLIT_FLIP', array_flip(PVP_SPLIT));

/*
 * maps of Lang => constant name for the class builder
 */

const CONST_LANG = [
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

const CONST_CAMPAIGN = [
	Campaign::CORE             => 'C::CORE',
	Campaign::PROPHECIES       => 'C::PROPHECIES',
	Campaign::FACTIONS         => 'C::FACTIONS',
	Campaign::NIGHTFALL        => 'C::NIGHTFALL',
	Campaign::EYE_OF_THE_NORTH => 'C::EYE_OF_THE_NORTH',
];

const CONST_PROFESSION = [
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

const CONST_ATTRIBUTE = [
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

const CONST_TYPE = [
	Type::NONE            => 'T::NONE',
	Type::SKILL           => 'T::SKILL',
	Type::BOW_ATK         => 'T::BOW_ATK',
	Type::MELEE_ATK       => 'T::MELEE_ATK',
	Type::AXE_ATK         => 'T::AXE_ATK',
	Type::LEAD_ATK        => 'T::LEAD_ATK',
	Type::OFFHAND_ATK     => 'T::OFFHAND_ATK',
	Type::DUAL_ATK        => 'T::DUAL_ATK',
	Type::HAMMER_ATK      => 'T::HAMMER_ATK',
	Type::SCYTHE_ATK      => 'T::SCYTHE_ATK',
	Type::SWORD_ATK       => 'T::SWORD_ATK',
	Type::PET_ATK         => 'T::PET_ATK',
	Type::SPEAR_ATK       => 'T::SPEAR_ATK',
	Type::CHANT           => 'T::CHANT',
	Type::ECHO            => 'T::ECHO',
	Type::FORM            => 'T::FORM',
	Type::GLYPH           => 'T::GLYPH',
	Type::PREPARATION     => 'T::PREPARATION',
	Type::BINDING_RITUAL  => 'T::BINDING_RITUAL',
	Type::NATURE_RITUAL   => 'T::NATURE_RITUAL',
	Type::SHOUT           => 'T::SHOUT',
	Type::SIGNET          => 'T::SIGNET',
	Type::SPELL           => 'T::SPELL',
	Type::ENCH            => 'T::ENCH',
	Type::HEX             => 'T::HEX',
	Type::ITEM_SPELL      => 'T::ITEM_SPELL',
	Type::WARD_SPELL      => 'T::WARD_SPELL',
	Type::WEAPON_SPELL    => 'T::WEAPON_SPELL',
	Type::WELL            => 'T::WELL',
	Type::STANCE          => 'T::STANCE',
	Type::TRAP            => 'T::TRAP',
	Type::RANGED_ATK      => 'T::RANGED_ATK',
	Type::VANGUARD_RITUAL => 'T::VANGUARD_RITUAL',
	Type::FLASH_ENCH      => 'T::FLASH_ENCH',
	Type::ATK_SKILL       => 'T::ATK_SKILL',
	Type::DAGGER_ATK      => 'T::DAGGER_ATK',
	Type::RITUAL          => 'T::RITUAL',
	Type::DOUBLE_ENCH     => 'T::DOUBLE_ENCH',
	Type::TOUCH_SKILL     => 'T::TOUCH_SKILL',
	Type::TOUCH_SPELL     => 'T::TOUCH_SPELL',
	Type::TOUCH_ENCH      => 'T::TOUCH_ENCH',
	Type::TOUCH_HEX       => 'T::TOUCH_HEX',
	Type::TOUCH_SIGNET    => 'T::TOUCH_SIGNET',
];

