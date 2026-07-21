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

use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillDataTools\Builder\Builder;
use chillerlan\Utilities\File;
use function array_keys;
use function array_map;
use function count;
use function in_array;
use function preg_match_all;
use function sort;
use const BUILDDIR;
use const PREG_UNMATCHED_AS_NULL;
use const SORT_NUMERIC;

require_once __DIR__.'/common.php';

const RESULT_FILE = BUILDDIR.'/progression-diff.json';

const KNOWN_DISCREPANCIES = [
	SkillDataInterface::LANG_DE => [
		27, 50, 57, 239, 242, 335, 775, 898, 979, 1335, 1345, 1758, 2013, 2218, 2223, 2806, 3180, 3191, 3192,
	],
];

$use_known_discrepancies = true;

$databases = array_map(fn(string $fqcn):SkillDataInterface => new $fqcn, Builder::DATABASES);
$diffs     = [];

foreach($databases[SkillDataInterface::LANG_EN]::ID2DESC as $id => $lang1){
	foreach(array_keys(SkillDataInterface::LANGUAGES) as $lang){

		if($lang === SkillDataInterface::LANG_EN){
			continue;
		}

		$lang2 = $databases[$lang]::ID2DESC[$id];

		foreach(SkillDataInterface::KEYS_DESC as $pos => $key){

			if($key === 'name'){
				continue;
			}

			$diff = diffProgressions($lang1[$pos], $lang2[$pos]);

			if($diff === null || ($use_known_discrepancies && in_array($id, KNOWN_DISCREPANCIES[$lang]))){
				continue;
			}

			[$match1, $match2] = $diff;

			$diffs[$id][$lang][$key] = [
				[$lang1[0], $lang1[$pos], $match1],
				[$lang2[0], $lang2[$pos], $match2],
			];
		}
	}
}

File::saveJSON(RESULT_FILE, $diffs);

exit;

/**
 * Matches the progression values in the given skill description.
 *
 * Returns the matches as an array, returns null if no progression could be found.
 */
function getProgressions(string $description):array|null{
	$r = preg_match_all('/(?<progression>\d+\.+\d+)/', $description, $matches, PREG_UNMATCHED_AS_NULL);

	if($r === false || $r < 1){
		return null;
	}

	sort($matches['progression'], SORT_NUMERIC);

	return $matches['progression'];
}

/**
 * Compares the progression values of the given skill descriptions.
 *
 * Returnd null if there are no differences, returns an array of progression matches if there were differences.
 */
function diffProgressions(string $desc1, string $desc2):array|null{
	$prog1 = getProgressions($desc1);
	$prog2 = getProgressions($desc2);

	// description does not contain progressions
	if($prog1 === null && $prog2 === null){
		return null;
	}
	// general discrepancy: either of the descriptions does not have a progression
	// or count of progressions does not match (???)
	if($prog1 === null || $prog2 === null || count($prog1) !== count($prog2)){
		return [$prog1, $prog2];
	}

	$diff = [];

	foreach($prog1 as $i => $value){
		if($prog2[$i] !== $value){
			$diff[] = $value;
		}
	}

	if($diff !== []){
		return [$prog1, $prog2];
	}

	// no differences
	return null;
}
