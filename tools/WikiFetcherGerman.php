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

	protected const REDIRECTS = [
		316  => 'Bis ans Limit!',
		333  => 'Ich werde Euch rächen!',
		343  => 'Für höhere Gerechtigkeit!',
		348  => 'Passt auf Euch auf!',
		364  => 'Angriff!',
		365  => 'Der Sieg ist mein!',
		366  => 'Fürchtet mich!',
		367  => 'Schilde hoch!',
		368  => 'Ich überlebe!',
		1599 => 'Es ist nur eine Fleischwunde.',
		1948 => 'Schattenzuflucht (Rollenspiel-Fertigkeit)', // Luxon
		1954 => '"Rettet Euch selbst!"', // Luxon
		2091 => 'Schattenzuflucht (Rollenspiel-Fertigkeit)', // Kurzick
		2097 => '"Rettet Euch selbst!"', // Kurzick
		2355 => 'Ich bin am stärksten!',
		2858 => 'Passt auf Euch auf! (PvP)',
		2883 => 'Für höhere Gerechtigkeit! (PvP)',
		3007 => 'Schmerzen (PvP)',
		3035 => '"Gebt nicht auf!" (PvP)',
		3037 => '"Zieht Euch zurück!" (PvP)',
		3375 => 'Wiederherstellungs-Aura (PvP)',
		3437 => 'Sense des Bauern (PvP)',
	];

	protected function parseResponse(array $data, int $id):array|null{

		if($id === 0){
			return ['Keine Fertigkeit', 'Leerer Fertigkeiten-Slot', 'Leerer Slot'];
		}

		if(!isset($data['revisions'][0]['slots']['main']['*'])){
			return null;
		}

		$data = $data['revisions'][0]['slots']['main']['*'];

		// remove some templates first
		$data = str_ireplace(
			['{{pipe}}}', '{{{pipe}}', '{{pipe}}', '{{!-}}', "'''", '{{sic}}'],
			['', '', '', '', '', '<sic/>]'],
			$data,
		);

		$infobox = $this->getInfobox($data, 'infobox fertigkeit');

		if($infobox === null){
			$this->logger->warning(sprintf('could not parse infobox for skill %s', $id));

			return null;
		}

		return $this->parseInfobox($infobox, $id);
	}

	protected function parseInfobox(string $infobox, int $id):array{
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

		// fix some things
		$infobox = str_ireplace(
			['&#45;', '[s]', '&nbsp;', '  '],
			['+', '(s)', ' ', ' '],
			$infobox,
		);

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

		// fix +/- for re-/degeneration
		$infobox = preg_replace(
			[
				'/(?:regeneration von ((?:\d+)([.]+(?:\d+))?))/',
				'/(?:degeneration von \+?((?:\d+)(?:[.]+(?:\d+))?))/',
				'/(?:[^+]((?:\d+)(?:[.]+(?:\d+))\s+(?:Lebens|Energie)regeneration))/i',
			],
			[
				'regeneration von +$1',
				'degeneration von -$1',
				' +$1',
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
