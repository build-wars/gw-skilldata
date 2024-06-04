<?php
/**
 * Class WikiFetcherGerman
 *
 * @created      26.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 *
 * @noinspection RegExpUnnecessaryNonCapturingGroup, RegExpRedundantEscape
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use function array_column;
use function array_combine;
use function array_filter;
use function array_map;
use function count;
use function explode;
use function in_array;
use function preg_replace;
use function sprintf;
use function str_ireplace;
use function trim;

/**
 * Fetches from the german Guild Wars wiki (guildwiki.de)
 */
final class WikiFetcherGerman extends WikiFetcher{

	protected const MEDIAWIKI_API = 'https://www.guildwiki.de/gwiki/api.php';
	protected const CACHEDIR      = __DIR__.'/../.build/guildwiki/';

	/**
	 * @inheritDoc
	 */
	protected function prepareSkillName(string $skillName, int $id):string{

		// fix for pve faction skills
		if($skillName === 'Schattenzuflucht (Kurzick)' || $skillName === 'Schattenzuflucht (Luxon)'){
			$skillName = 'Schattenzuflucht (Rollenspiel-Fertigkeit)';
		}

		$skillName = preg_replace('/(\s\((Kurzick|Luxon)\))/', '', $skillName);

		$redirect = [
			1780 => '"Darf nicht angerührt werden!"',
			2858 => 'Passt auf Euch auf! (PvP)',
			2883 => 'Für höhere Gerechtigkeit! (PvP)',
			3007 => 'Schmerzen (PvP)',
			3035 => '"Gebt nicht auf!" (PvP)',
			3037 => '"Zieht Euch zurück!" (PvP)',
		];

		if(isset($redirect[$id])){
			return $redirect[$id];
		}

		return $skillName;
	}

	/**
	 * @inheritDoc
	 */
	protected function getRequestParams(string $skillName):array{
		return [
			'format'  => 'json',
			'action'  => 'query',
			'prop'    => 'revisions',
			'rvprop'  => 'content',
			#			'rvslots' => '*',
			'titles'  => $skillName,
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function parseResponse(array $data, int $id):array|null{

		if($id === 0){
			return ['Keine Fertigkeit', 'Leerer Fertigkeiten-Slot', 'Leerer Slot'];
		}

		if(!isset($data['revisions'][0]['*'])){
			return null;
		}

		$data = $data['revisions'][0]['*'];

		// remove some templates first
		$data = str_ireplace(
			['{{pipe}}}', '{{{pipe}}', '{{pipe}}', '{{!-}}', "'''", '{{sic}}'],
			['', '', '', '', '', '<sic/>]'],
			$data
		);

		$infobox = $this->getInfobox($data, 'infobox fertigkeit');

		if($infobox === null){
			$this->logger->warning(sprintf('could not parse infobox for skill %s', $id));

			return null;
		}

		return $this->parseInfobox($infobox, $id);
	}

	/**
	 * @inheritDoc
	 */
	protected function parseInfobox(string $infobox, $id):array{
		// replace some templates (progression, links, colored text)
		$s = [
			// progression
			'/\{\{[p1-2]+\|([\+\-\d]+)\|([\d%]+)(?:\|(?:[^\}]+))?\}\}/i',
			// article links
			'/\[\[[^\[\|]+\|([^\[\|]+)\]\]/',
			// random templates
			'/\{\{[a-z]+\|([^\{\}]+)\}\}/i',
			// html comments
			'/<!--(.*)-->/',
		];

		$r = [
			'$1...$2',
			'$1',
			'<gray>$1</gray>',
			'',
		];

		$infobox = preg_replace($s, $r, $infobox);

		// clean out unwanted braces and stuff
		$infobox = str_ireplace(
			[
				'{', '}',
				'[', ']',
				'infobox fertigkeit',
				'kurzbeschreibungstyp',
				'(Rollenspiel-Fertigkeit)',
			],
			'',
			$infobox,
		);

		// fix some things
		$infobox = str_ireplace(
			['&#45;', '[s]', '&nbsp;', '  '],
			['+', '(s)', ' ', ' '],
			$infobox,
		);

		// fix +/- for re-/degeneration
		$infobox = preg_replace(
			[
				'/(?:regeneration von ((?:\d+)([.]+(?:\d+))?))/',
				'/(?:degeneration von \+?((?:\d+)(?:[.]+(?:\d+))?))/',
				'/(?:[^+]((?:\d+)(?:[.]+(?:\d+))\s+(?:Lebens|Energie)regeneration))/i'
			],
			[
				'regeneration von +$1',
				'degeneration von -$1',
				' +$1'
			],
			$infobox,
		);


		// split into key=value pairs
		$infobox = array_map($this->splitKV(...), array_filter(explode('|', trim($infobox))));

		// fix some empty parameters
		foreach($infobox as &$e){
			if(count($e) < 2){
				$e[] = '';
			}
		}

		// combine keys and values
		$infobox = array_combine(array_column($infobox, 0), array_column($infobox, 1));

		if(in_array($id, self::Luxon, true)){
			$infobox['name'] .= ' (Luxon)';
		}
		elseif(in_array($id, self::Kurzick, true)){
			$infobox['name'] .= ' (Kurzick)';
		}

		return [$infobox['name'], $infobox['beschreibung'], $infobox['kurzbeschreibung']];
	}

}
