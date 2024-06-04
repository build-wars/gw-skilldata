<?php
/**
 * Parses the CSV data from the paw-ned² data files, mixes in some missing data,
 * and saves everything as neatly formatted JSON
 *
 * @created      12.02.2015
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2015 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use InvalidArgumentException;
use function array_map;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function is_readable;
use function json_encode;
use function ksort;
use function mb_convert_encoding;
use function mb_detect_encoding;
use function realpath;
use function sprintf;
use function str_replace;
use function trim;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

// the skill databases for each language. more to come... probably never.
const skilldb = [
	'pve' => [
		'de' => __DIR__.'/../data/paw-ned/de_classic_pve.csv',
		'en' => __DIR__.'/../data/paw-ned/en_classic_pve.csv',
	],
	'pvp' => [
		'de' => __DIR__.'/../data/paw-ned/de_classic_pvp.csv',
		'en' => __DIR__.'/../data/paw-ned/en_classic_pvp.csv',
	],
];

// pawned uses negative numbers for the pve attributes which is ...
// since there are no new attributes to be expected we can safely use the 1xx range
const attr_translate = [
	-9 => 109,
	-8 => 108,
	-7 => 107,
	-6 => 106,
	-5 => 105,
	-4 => 104,
	-3 => 103,
	-2 => 102,
	-1 => 101,
];

// skills that have deviating PvP versions
// see: https://wiki.guildwars.com/wiki/List_of_PvP_versions_of_skills
// skill id => pvp skill id
const pvp_split = [
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

// the output arrays have some missing data already set

// the new PvE skills are not in the paw-ned CSV data for some reason (hardcoded into the binary eh?)
$skilldesc = [
	'de' => [
		0    => [
			'id'          => 0,
			'name'        => 'Keine Fertigkeit',
			'description' => 'Leerer Fertigkeiten-Slot',
			'concise'     => 'Leerer Slot',
		],
		3422 => [
			'id'          => 3422,
			'name'        => 'Zeitabwehr',
			'description' => 'Ihr erschafft eine Zeitabwehr an Eurem aktuellen Standort. Für 3...15 Sekunden wirken Verbündete Nicht-Geister in diesem Gebiet um 15...20% schneller und Fertigkeiten laden um 15...20% schneller auf.',
			'concise'     => '(3...15 Sekunden.) Verbündete in dieser Abwehr wirken Zauber um 15...20% schneller und Fertigkeiten laden um 15...20% schneller wieder auf. Betrifft nicht verbündete Geister.',
		],
		3423 => [
			'id'          => 3423,
			'name'        => 'Seelengreifer',
			'description' => 'Für 3...30 Sekunden opfern Eure Angriffe 15...20 Lebenspunkte und verursachen 15...20 Punkte mehr Schaden',
			'concise'     => '(3...30 Sekunden.) Angriffe verursachen +15...20 Punkte Schaden und opfern 15...20 Lebenspunkte.',
		],
		3424 => [
			'id'          => 3424,
			'name'        => 'Über die Grenze hinaus',
			'description' => 'Während dieser Verzauberung wirken Eure Zauber um 15...20% schneller und laden sich 15...50% schneller auf, aber verursachen zunehmend Überzaubern.',
			'concise'     => 'Zauber wirken um 15...20% schneller und laden sich um 15...50% schneller auf. Verursacht bei Aktivierung zunehmend Überzaubern.',
		],
		3425 => [
			'id'          => 3425,
			'name'        => 'Urteilsschlag',
			'description' => 'Attackiert das Ziel und umstehende Feinde. Jeder treffende Angriff verursacht +13...30 Punkte heiliger Schaden und wirft angreifende Feinde zu Boden.',
			'concise'     => 'Attackiert das Ziel und umstehende Feinde und verursacht +13...30 Punkte Schaden. Wirft angreifende Feinde zu Boden.',
		],
		3426 => [
			'id'          => 3426,
			'name'        => 'Sieben Waffen-Haltung',
			'description' => 'Für 3...20 Sekunden werden Eure Waffenwerte um 1...15 erhöht und Euer Angriff ist um 33% schneller.',
			'concise'     => '(3...20 Sekunden) Waffenwerte werden um 1...15 erhöht. Euer Angriff ist um 33% schneller.',
		],
		3427 => [
			'id'          => 3427,
			'name'        => '"Gemeinsam als Eines!"',
			'description' => 'Für 3...15 Sekunden verursachen alle Gruppenmitglieder in der Nähe von Euch oder Eurem Tiergefährten +5...15 Punkte zusätzlichen Schaden bei Angriffen und erhalten +1...7 Lebenspunkte-Wiederherstellung.',
			'concise'     => '(3...15 Sekunden.) Alle Gruppenmitglieder in der Nähe von Euch oder Eurem Tiergefährten verursachen +5...15 Punkte Schaden und erhalten +1...7 Lebenspunkte-Wiederherstellung.',
		],
		3428 => [
			'id'          => 3428,
			'name'        => 'Schattendiebstahl',
			'description' => 'Macht einen Schattenschritt zum feindlichen Ziel. Für 5...20 Sekunden sind die Werte dieses Feindes um 1...5 reduziert und Eure Werte um 1...5 erhöht. Diese Fertigkeit zählt als Leithandangriff.',
			'concise'     => 'Macht einen Schattenschritt zum feindlichen Ziel. Für 5...20 Sekunden sind die Werte dieses Feindes um 1...5 reduziert und Eure Werte um 1...5 erhöht. Zählt als Leithandangriff.',
		],
		3429 => [
			'id'          => 3429,
			'name'        => 'Waffen aus drei Schmieden',
			'description' => 'Für 3...20 Sekunden erhält jeder Verbündete Nicht-Geist in Hörweite den Effekt eines zufälligen Waffenzaubers.',
			'concise'     => '(3...20 Sekunden.) Verbündete in Hörweite erhalten den Effekt eines zufälligen Waffenzaubers. Betrifft nicht verbündete Geister.',
		],
		3430 => [
			'id'          => 3430,
			'name'        => 'Schwur der Revolution',
			'description' => 'Für 3...10 Sekunden erhaltet Ihr +1...5 Energieregeneration. Diese Fertigkeit erneuert sich jedes mal>, wenn Ihr keine Derwisch-Fertigkeit anwendet.',
			'concise'     => '(3...10 Sekunden.) Erhaltet +1...5 Energieregeneration. Erneuerung: immer wenn Ihr keine Derwisch-Fertigkeit anwendet.',
		],
		3431 => [
			'id'          => 3431,
			'name'        => 'Heroischer Refrain',
			'description' => 'Für 3...15 Sekunden erhält ein gezielter, verbündeter Nicht-Geist +1...3 auf alle Werte. Dieses Echo erneuert sich jedes Mal, wenn ein Anfeuerungsruf oder Schrei bei diesem Verbündeten endet.',
			'concise'     => '(3...15 Sekunden.) Gezielter Verbündeter erhält +1...3 auf alle Werte. Erneuerung: jedes Mal, wenn ein Anfeuerungsruf oder Schrei bei diesem Verbündeten endet. Geister können nicht anvisiert werden.',
		],
	],
	'en' => [
		0    => [
			'id'          => 0,
			'name'        => 'No Skill',
			'description' => 'Empty skill slot',
			'concise'     => 'Empty slot',
		],
		3422 => [
			'id'          => 3422,
			'name'        => 'Time Ward',
			'description' => 'You create a Time Ward at your location. For 3...15 seconds, non-spirit allies in this area cast spells 15...20% faster and recharge skills 15...20% faster.',
			'concise'     => '(3...15 seconds.) Allies in this ward cast spells 15...20% faster and recharge skills 15...20% faster.',
		],
		3423 => [
			'id'          => 3423,
			'name'        => 'Soul Taker',
			'description' => 'For 3...30 seconds, your attacks sacrifice 15...20 health and deal +15...20 more damage.',
			'concise'     => '(3...30 seconds.) Attacks deal +15...20 damage and sacrifice 15...20 health.',
		],
		3424 => [
			'id'          => 3424,
			'name'        => 'Over the Limit',
			'description' => 'While you maintain this enchantment, your spells cast 15...20% faster, and recharge 15...50% faster, but you continuously gain Overcast.',
			'concise'     => 'Spells cast 15...20% faster and recharge 15...50% faster. Continuously gain Overcast while active.',
		],
		3425 => [
			'id'          => 3425,
			'name'        => 'Judgment Strike',
			'description' => 'Attack target and adjacent foes. Each attack that hits deals +13...30 Holy damage and knocks down attacking foes.',
			'concise'     => 'Attacks target and adjacent foes for +13...30 Holy damage. Causes knock down on attacking foes.',
		],
		3426 => [
			'id'          => 3426,
			'name'        => 'Seven Weapons Stance',
			'description' => 'For 3...20 seconds, your weapon attributes are increased by +1...15 and you attack 33% faster.',
			'concise'     => '(3...20 seconds.) Weapon attributes are increased by +1...15. You attack 33% faster.',
		],
		3427 => [
			'id'          => 3427,
			'name'        => '"Together as One!"',
			'description' => 'For 3...15 seconds, all party members near you or near your pet deal +5...15 additional damage with attacks and gain +1...7 Health regeneration.',
			'concise'     => '(3...15 seconds.) All party members near you or your pet deal +5...15 damage and gain +1...7 Health regeneration.',
		],
		3428 => [
			'id'          => 3428,
			'name'        => 'Shadow Theft',
			'description' => 'Shadow Step to target foe. For 5...20 seconds that foe\'s attributes are reduced by 1...5 and your attributes are increased by 1...5. This skill counts as a Lead Attack.',
			'concise'     => 'Shadow Step to target foe. For 5...20 seconds that foe\'s attributes are reduced by 1...5 and your attributes are increased by 1...5. Counts as a Lead Attack.',
		],
		3429 => [
			'id'          => 3429,
			'name'        => 'Weapons of Three Forges',
			'description' => 'For 3...20 seconds, each non-spirit ally in earshot gains the effect of a random Weapon Spell.',
			'concise'     => '(3...20 seconds.) Allies in earshot gain the effect of a random Weapon Spell. Allied spirits are not affected.',
		],
		3430 => [
			'id'          => 3430,
			'name'        => 'Vow of Revolution',
			'description' => 'For 3...10 seconds, you have +1...5 energy regeneration. This skill reapplies itself every time you use a non-Dervish skill.',
			'concise'     => '(3...10 seconds.) Gain +1...5 energy regeneration. Renewal: whenever you use a non-Dervish skill.',
		],
		3431 => [
			'id'          => 3431,
			'name'        => 'Heroic Refrain',
			'description' => 'For 3...15 seconds, target non-spirit ally gains +1...3 to all attributes. This echo is reapplied every time a chant or shout ends on that ally.',
			'concise'     => '(3...13...15 seconds.) Target ally gains +1...3 to all attributes. Renewal: every time a chant or shout ends on this ally. Cannot target spirits.',
		],
	],
];

$skilldata = [
	// skill number zero is the "unknown skill"
	0    => [
		'id'         => 0,
		'campaign'   => 0,
		'profession' => 0,
		'attribute'  => 101,
		'type'       => 0,
		'is_elite'   => false,
		'is_rp'      => false,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 0,
		'activation' => 0,
		'recharge'   => 0,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Time Ward
	3422 => [
		'id'         => 3422,
		'campaign'   => 0,
		'profession' => 5,
		'attribute'  => 0,
		'type'       => 26,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 10,
		'activation' => 2,
		'recharge'   => 30,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Soul Taker
	3423 => [
		'id'         => 3423,
		'campaign'   => 0,
		'profession' => 4,
		'attribute'  => 6,
		'type'       => 23,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 5,
		'activation' => 1,
		'recharge'   => 15,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Over the Limit
	3424 => [
		'id'         => 3424,
		'campaign'   => 0,
		'profession' => 6,
		'attribute'  => 12,
		'type'       => 23,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => -1,
		'energy'     => 5,
		'activation' => 1,
		'recharge'   => 20,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Judgment Strike
	3425 => [
		'id'         => 3425,
		'campaign'   => 0,
		'profession' => 3,
		'attribute'  => 16,
		'type'       => 3,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 5,
		'activation' => 1,
		'recharge'   => 8,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Seven Weapons Stance
	3426 => [
		'id'         => 3426,
		'campaign'   => 0,
		'profession' => 1,
		'attribute'  => 17,
		'type'       => 29,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 5,
		'activation' => 0,
		'recharge'   => 20,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// "Together as One!"
	3427 => [
		'id'         => 3427,
		'campaign'   => 0,
		'profession' => 2,
		'attribute'  => 23,
		'type'       => 20,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 10,
		'activation' => 0,
		'recharge'   => 15,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Shadow Theft
	3428 => [
		'id'         => 3428,
		'campaign'   => 0,
		'profession' => 7,
		'attribute'  => 35,
		'type'       => 1,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 5,
		'activation' => 0.25,
		'recharge'   => 20,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Weapons of Three Forges
	3429 => [
		'id'         => 3429,
		'campaign'   => 0,
		'profession' => 8,
		'attribute'  => 36,
		'type'       => 27,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 10,
		'activation' => 2,
		'recharge'   => 15,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Vow of Revolution
	3430 => [
		'id'         => 3430,
		'campaign'   => 0,
		'profession' => 10,
		'attribute'  => 44,
		'type'       => 23,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 10,
		'activation' => 2,
		'recharge'   => 30,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
	// Heroic Refrain
	3431 => [
		'id'         => 3431,
		'campaign'   => 0,
		'profession' => 9,
		'attribute'  => 40,
		'type'       => 14,
		'is_elite'   => true,
		'is_rp'      => true,
		'is_pvp'     => false,
		'pvp_split'  => false,
		'split_id'   => 0,
		'upkeep'     => 0,
		'energy'     => 5,
		'activation' => 1,
		'recharge'   => 10,
		'adrenaline' => 0,
		'sacrifice'  => 0,
		'overcast'   => 0,
	],
];


function loadPwndFile(string $file):array{
	$file = realpath($file);

	if($file === false || !is_readable($file)){
		throw new InvalidArgumentException(sprintf('invalid file "%s"', $file));
	}

	$data = file_get_contents($file);

	// the original paw-ned² files are stored in Windows-1252
	if(mb_detect_encoding($data) !== 'UTF-8'){
		$data = mb_convert_encoding($data, 'UTF-8', 'Windows-1252');
	}

	// split the CSV into an array
	return array_map(fn(string $line):array => explode(';', trim($line), 20), explode("\n", trim($data)));
}


/*
 * paw-ned² skilldb schema
 *
 * 0 = id
 * 1 = name
 * 2 = name2 (de/en)
 * 3 = desc
 * 4 = campaign
 * 5 = attribute
 * 6 = type
 * 7 = profession
 * 8 = upkeep
 * 9 = energy
 * 10 = activation
 * 11 = recharge
 * 12 = adrenaline
 * 13 = sacrifice
 * 14 = elite
 * 15 = pve
 * 16 = overcast
 * 17 = ?
 * 18 = ?
 * 19 = empty
 */

// first, process pve data
foreach(skilldb['pve'] as $lang => $file){
	$logger->info(sprintf('preparing skilldata pve-%s: %s', $lang, realpath($file)));

	foreach(loadPwndFile($file) as $skill){
		$id = (int)$skill[0];

		if($id === 0){
			continue;
		}

		$skilldesc[$lang][$id] = [
			'id'          => $id,
			'name'        => trim($skill[1]),
			'description' => trim($skill[3]),
			'concise'     => '',
		];

		$attr = (int)$skill[5];

		if($attr < 0){
			$attr = attr_translate[$attr];
		}

		$skilldata[$id] = [
			'id'         => $id,
			'campaign'   => (int)$skill[4],
			'profession' => (int)$skill[7],
			'attribute'  => $attr,
			'type'       => (int)$skill[6],
			'is_elite'   => (bool)$skill[14],
			'is_rp'      => (bool)$skill[15],
			'is_pvp'     => false,
			'pvp_split'  => isset(pvp_split[$id]),
			'split_id'   => (pvp_split[$id] ?? 0),
			'upkeep'     => (int)$skill[8],
			'energy'     => (int)$skill[9],
			'activation' => (float)$skill[10],
			'recharge'   => (int)$skill[11],
			'adrenaline' => (int)$skill[12],
			'sacrifice'  => (int)$skill[13],
			'overcast'   => (int)$skill[16],
		];

	}

}

// now merge the pvp data
foreach(skilldb['pvp'] as $lang => $file){
	$logger->info(sprintf('preparing skilldata pvp-%s: %s', $lang, realpath($file)));

	foreach(loadPwndFile($file) as $skill){
		$id = (int)$skill[0];

		if($id === 0 || !isset(pvp_split[$id])){
			continue;
		}

		$skilldesc[$lang][pvp_split[$id]] = [
			'id'          => pvp_split[$id],
			'name'        => trim($skill[1]),
			'description' => trim($skill[3]),
			'concise'     => '',
		];

		$attr = (int)$skill[5];

		if($attr < 0){
			$attr = attr_translate[$attr];
		}

		$skilldata[pvp_split[$id]] = [
			'id'         => pvp_split[$id],
			'campaign'   => (int)$skill[4],
			'profession' => (int)$skill[7],
			'attribute'  => $attr,
			'type'       => (int)$skill[6],
			'is_elite'   => (bool)$skill[14],
			'is_rp'      => false,
			'is_pvp'     => true,
			'pvp_split'  => false,
			'split_id'   => 0,
			'upkeep'     => (int)$skill[8],
			'energy'     => (int)$skill[9],
			'activation' => (float)$skill[10],
			'recharge'   => (int)$skill[11],
			'adrenaline' => (int)$skill[12],
			'sacrifice'  => (int)$skill[13],
			'overcast'   => (int)$skill[16],
		];

	}

}


// save skill data
$logger->info(sprintf('skilldata: %s skills', count($skilldata)));

ksort($skilldata);

$jsonData = [
#	'$schema'   => './skilldata.schema.json',
	'$schema'   => 'https://raw.githubusercontent.com/build-wars/gw-skilldata/blob/main/data/json-full/skilldata.schema.json',
	'skilldata' => $skilldata,
];

$jsonData = json_encode($jsonData, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

file_put_contents(__DIR__.'/../data/json-full/skilldata.json', str_replace('    ', "\t", $jsonData));

// save skill descriptions
foreach(['de', 'en'] as $lang){
	$logger->info(sprintf('lang "%s": %s skills', $lang, count($skilldesc[$lang])));

	ksort($skilldesc[$lang]);

	$jsonData = [
#		'$schema'   => './skilldesc.schema.json',
		'$schema'   => 'https://raw.githubusercontent.com/build-wars/gw-skilldata/blob/main/data/json-full/skilldesc.schema.json',
		'lang'      => $lang,
		'skilldesc' => $skilldesc[$lang],
	];

	$jsonData = json_encode($jsonData, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

	file_put_contents(__DIR__.'/../data/json-full/skilldesc-'.$lang.'.json', $jsonData);
}
