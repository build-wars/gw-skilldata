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

use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use function abs;
use function count;
use function ksort;
use function realpath;
use function sprintf;
use function str_replace;
use function trim;

/**
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

// the output arrays have some missing data already set

$skilldesc = [
	'de' => [
		0    => [
			'id'          => 0,
			'name'        => 'Keine Fertigkeit',
			'description' => 'Leerer Fertigkeiten-Slot',
			'concise'     => 'Leerer Slot',
		],
		3437 => [
			'id'          => 3437,
			'name'        => 'Sense des Bauern (PvP)',
			'description' => '',
			'concise'     => '',
		],
	],
	'en' => [
		0    => [
			'id'          => 0,
			'name'        => 'No Skill',
			'description' => 'Empty skill slot',
			'concise'     => 'Empty slot',
		],
		3437 => [
			'id'          => 3437,
			'name'        => 'Farmer\'s Scythe (PvP)',
			'description' => '',
			'concise'     => '',
		],
	],
];

$skilldata = [
	// skill number zero is the "unknown skill"
	0    => [
		'id'                 => 0,
		'campaign'           => 0,
		'profession'         => 0,
		'attribute'          => 101,
		'type'               => 0,
		'is_elite'           => false,
		'is_rp'              => false,
		'is_pvp'             => false,
		'pvp_split'          => false,
		'split_id'           => 0,
		'upkeep'             => 0,
		'energy'             => 0,
		'activation'         => 0,
		'recharge'           => 0,
		'adrenaline'         => 0,
		'adrenaline_precise' => 0,
		'sacrifice'          => 0,
		'overcast'           => 0,
	],
	// Farmer's Scythe (PvP)
	3437 => [
		'id'                 => 3437,
		'campaign'           => 3,
		'profession'         => 10,
		'attribute'          => 41,
		'type'               => 9,
		'is_elite'           => false,
		'is_rp'              => false,
		'is_pvp'             => true,
		'pvp_split'          => false,
		'split_id'           => 0,
		'upkeep'             => 0,
		'energy'             => 5,
		'activation'         => 0,
		'recharge'           => 20,
		'adrenaline'         => 0,
		'adrenaline_precise' => 0,
		'sacrifice'          => 0,
		'overcast'           => 0,
	],
];



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
foreach(PWND_CSV['pve'] as $lang => $file){
	$logger->info(sprintf('preparing skilldata pve-%s: %s', $lang, realpath($file)));

	foreach(load_pawned_file($file) as $skill){
		$id = (int)$skill[0];

		if($id === 0){
			continue;
		}

		$skilldesc[$lang][$id] = [
			'id'          => $id,
			'name'        => trim($skill[1]),
			'description' => str_replace('"', '', trim($skill[3])),
			'concise'     => '',
		];

		$attr = (int)$skill[5];

		if($attr < 0){
			$attr = PWND_ATTR_TRANSLATE[$attr];
		}

		$skilldata[$id] = [
			'id'                 => $id,
			'campaign'           => (int)$skill[4],
			'profession'         => (int)$skill[7],
			'attribute'          => $attr,
			'type'               => (int)$skill[6],
			'is_elite'           => (bool)$skill[14],
			'is_rp'              => (bool)$skill[15],
			'is_pvp'             => false,
			'pvp_split'          => isset(PVP_SPLIT[$id]),
			'split_id'           => (PVP_SPLIT[$id] ?? 0),
			'upkeep'             => (int)$skill[8],
			'energy'             => (int)$skill[9],
			'activation'         => abs((float)$skill[10]),
			'recharge'           => (int)$skill[11],
			'adrenaline'         => abs((float)$skill[12]),
			'adrenaline_precise' => abs((float)$skill[12]),
			'sacrifice'          => (int)$skill[13],
			'overcast'           => (int)$skill[16],
		];

	}

}

// now merge the pvp data
foreach(PWND_CSV['pvp'] as $lang => $file){
	$logger->info(sprintf('preparing skilldata pvp-%s: %s', $lang, realpath($file)));

	foreach(load_pawned_file($file) as $skill){
		$id = (int)$skill[0];

		if($id === 0 || !isset(PVP_SPLIT[$id])){
			continue;
		}

		$skilldesc[$lang][PVP_SPLIT[$id]] = [
			'id'          => PVP_SPLIT[$id],
			'name'        => trim($skill[1]),
			'description' => str_replace('"', '', trim($skill[3])),
			'concise'     => '',
		];

		$attr = (int)$skill[5];

		if($attr < 0){
			$attr = PWND_ATTR_TRANSLATE[$attr];
		}

		$skilldata[PVP_SPLIT[$id]] = [
			'id'                 => PVP_SPLIT[$id],
			'campaign'           => (int)$skill[4],
			'profession'         => (int)$skill[7],
			'attribute'          => $attr,
			'type'               => (int)$skill[6],
			'is_elite'           => (bool)$skill[14],
			'is_rp'              => false,
			'is_pvp'             => true,
			'pvp_split'          => false,
			'split_id'           => 0,
			'upkeep'             => (int)$skill[8],
			'energy'             => (int)$skill[9],
			'activation'         => abs((float)$skill[10]),
			'recharge'           => (int)$skill[11],
			'adrenaline'         => abs((float)$skill[12]),
			'adrenaline_precise' => abs((float)$skill[12]),
			'sacrifice'          => (int)$skill[13],
			'overcast'           => (int)$skill[16],
		];

	}

}

// save skill data
$logger->info(sprintf('skilldata: %s skills', count($skilldata)));

ksort($skilldata);

$jsonData = [
	'$schema'   => 'https://build-wars.github.io/gw-skilldata/schemas/skilldata.schema.json',
	'skilldata' => $skilldata,
];

File::save(DATA_FILE, str_replace('    ', "\t", Str::jsonEncode($jsonData)));

// save skill descriptions
foreach(LANG_FILES as [$lang, $file]){
	$logger->info(sprintf('lang "%s": %s skills', $lang, count($skilldesc[$lang])));

	ksort($skilldesc[$lang]);

	$jsonData = [
		'$schema'   => 'https://build-wars.github.io/gw-skilldata/schemas/skilldesc.schema.json',
		'lang'      => $lang,
		'skilldesc' => $skilldesc[$lang],
	];

	File::save($file, str_replace('    ', "\t", Str::jsonEncode($jsonData)));
}
