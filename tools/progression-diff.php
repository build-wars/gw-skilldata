<?php
/**
 * Matches the progressions in skill descriptions in order to find discrepancies.
 *
 * The several language descriptions are matched against english (GWW).
 * Results are saved as JSON.
 *
 * indexed by skill id > lang > description type > [en match, lang match],
 * where a match contains [name, description, progressions]
 *
 * @created      21.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillDataTools\Builder\Builder;
use chillerlan\Utilities\File;
use function array_map;
use function in_array;
use const BUILDDIR;

require_once __DIR__.'/common.php';

const RESULT_FILE = BUILDDIR.'/progression-diff.json';

const KNOWN_DISCREPANCIES = [
	Lang::DE => [
		27, 50, 57, 239, 242, 335, 775, 898, 979, 1213, 1335, 1345, 1551,
		1815, 2013, 2218, 2223, 2806, 3180, 3191, 3192, 3446, 3447,
	],
];

$use_known_discrepancies = true;

$databases = array_map(fn(string $fqcn):SkillDataInterface => new $fqcn, Builder::DATABASES);
$pvp_split = $databases[Lang::EN]->getIDs(true);
$diffs     = [];

foreach($databases[Lang::EN]->getIDs() as $id){
	$pvp   = in_array($id, $pvp_split, true);
	$lang1 = $databases[Lang::EN]->get($id, $pvp)->toArray();

	foreach(Lang::IDS as $lang){

		if($lang === Lang::EN){
			continue;
		}

		$lang2 = $databases[$lang]->get($id, $pvp)->toArray();

		foreach(Skill::KEYS_DESC as $key){

			if($key === Skill::DESC_NAME){
				continue;
			}

			$diff = diffProgressions($lang1[$key], $lang2[$key], true);

			if($diff === null || ($use_known_discrepancies && in_array($id, KNOWN_DISCREPANCIES[$lang], true))){
				continue;
			}

			[$match1, $match2] = $diff;

			$diffs[$id][$lang][$key] = [
				Lang::EN => ['name' => $lang1[Skill::DESC_NAME], 'text' => $lang1[$key], 'prog' => $match1],
				$lang    => ['name' => $lang2[Skill::DESC_NAME], 'text' => $lang2[$key], 'prog' => $match2],
			];
		}
	}
}

File::saveJSON(RESULT_FILE, $diffs);
