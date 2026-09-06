<?php
/**
 * Matches skill data from the wikis against the game data in order to find discrepancies.
 *
 * The several wiki language descriptions are matched against their respective game languages.
 * Results are saved as JSON.
 *
 * Data diffs are indexed by skill id > data key > [game data, wiki match],
 * where a match contains the value for the current data key
 *
 * Description diffs are indexed by skill id > description type > [game data, wiki match],
 * where a match contains [name, description, progressions]
 *
 * @created      05.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataAwareInterface;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use Buildwars\GWSkillData\SkillDataInterface;
use chillerlan\Utilities\File;
use function count;
use function in_array;
use function preg_match_all;
use function sort;
use function sprintf;
use const BUILDDIR;
use const PREG_UNMATCHED_AS_NULL;
use const SORT_NATURAL;

class WikiDiff extends BuilderAbstract implements SkillDataAwareInterface{
	use SkillDataAwareTrait;

	private const array WIKIS = [
		Lang::EN => Lang::EN_GWW,
		Lang::DE => Lang::DE_GUILDWIKI,
	];

	private const array PROGRESSION_DISCREPANCIES = [
		Lang::DE_GUILDWIKI => [
			27, 50, 57, 239, 242, 335, 474, 898, 979, 983, 1195, 1213, 1335, 1345,
			1551, 1758, 2218, 2223, 2806, 3180, 3191, 3192, 3447, 3468,
		],
		Lang::EN_GWW       => [
			1815,
		],
	];

	private const string DATA_DIFF_RESULT_FILE        = BUILDDIR.'/data-diff-%s.json';
	private const string PROGRESSION_DIFF_RESULT_FILE = BUILDDIR.'/progression-diff-%s.json';

	// the game data to compare against
	private SkillDataInterface $gw;
	// the comparison candidate
	private SkillDataInterface $current;
	/** @var array<int, array<string, scalar>> */
	private array $wikiData;

	public function build():static{

		foreach(self::WIKIS as $gwLang => $wikiLang){
			$this->gw        = $this->getGWDB($gwLang);
			$this->current   = $this->getGWDB($wikiLang);
			$pvp_split       = $this->gw->getIDs(true);
			$this->wikiData  = File::loadJSON(sprintf(self::WIKI_SKILLDATA_FILE, $wikiLang), true)['skilldata'];

			$dataDiffs = [];
			$progDiffs = [];

			foreach($this->gw->getIDs() as $id){
				$pvp = in_array($id, $pvp_split, true);
				$gw  = $this->gw->get($id, $pvp);

				$dDiff = $this->dataDiff($gw, $gwLang, $wikiLang);
				$pDiff = $this->progressionDiff($gw, $gwLang, $wikiLang);

				if($dDiff !== []){
					$dataDiffs[$id] = $dDiff;
				}

				if($pDiff !== []){
					$progDiffs[$id] = $pDiff;
				}
			}

			File::saveJSON(sprintf(self::DATA_DIFF_RESULT_FILE, $wikiLang), $dataDiffs);
			File::saveJSON(sprintf(self::PROGRESSION_DIFF_RESULT_FILE, $wikiLang), $progDiffs);

			$this->logger->info(sprintf('created diff for [%s]', $wikiLang));
		}

		return $this;
	}

	private function dataDiff(Skill $gw, string $gwLang, string $wikiLang):array{
		$gwData  = $gw->toArray();
		$current = $this->wikiData[$gw->id];
		$dataDiff = [];

		foreach(Skill::KEYS_DATA as $key){

			if($gwData[$key] === $current[$key]){
				continue;
			}

			$dataDiff[$key] = [$gwLang => $gwData[$key], $wikiLang => $current[$key]];
		}

		return $dataDiff;
	}

	private function progressionDiff(Skill $gw, string $gwLang, string $wikiLang):array{
		$lang1 = $gw->toArray();
		$lang2 = $this->current->get($gw->id, $gw->is_pvp)->toArray();

		$progessionDiff = [];

		foreach(Skill::KEYS_DESC as $key){

			if($key === Skill::DESC_NAME){
				continue;
			}

			$diff = $this->diffProgressions($lang1[$key], $lang2[$key], true);

			if($diff === null || ($this->options->use_known_discrepancies && in_array($gw->id, self::PROGRESSION_DISCREPANCIES[$wikiLang], true))){ // phpcs:ignore
				continue;
			}

			[$match1, $match2] = $diff;

			$progessionDiff[$key] = [
				$gwLang    => ['name' => $lang1[Skill::DESC_NAME], 'text' => $lang1[$key], 'prog' => $match1],
				$wikiLang  => ['name' => $lang2[Skill::DESC_NAME], 'text' => $lang2[$key], 'prog' => $match2],
			];
		}

		return $progessionDiff;
	}

	/**
	 * Matches the progression values in the given skill description.
	 *
	 * Returns the matches as an array, returns null if no progression could be found.
	 */
	private function getProgressions(string $description, bool $sort = false):array|null{
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
	private function diffProgressions(string $desc1, string $desc2, bool $sort = false):array|null{
		$prog1 = $this->getProgressions($desc1, $sort);
		$prog2 = $this->getProgressions($desc2, $sort);

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

}
