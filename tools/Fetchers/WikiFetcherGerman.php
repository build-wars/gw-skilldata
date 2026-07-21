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

namespace Buildwars\GWSkillDataTools\Fetchers;

use Buildwars\GWSkillData\SkillDataInterface;
use function array_column;
use function array_combine;
use function array_filter;
use function array_map;
use function explode;
use function floatval;
use function in_array;
use function intval;
use function preg_replace;
use function str_ireplace;
use function str_replace;
use function trim;

/**
 * Fetches from the german Guild Wars wiki (guildwiki.de)
 */
final class WikiFetcherGerman extends WikiFetcherAbstract{

	protected const LANG          = SkillDataInterface::LANG_DE;
	protected const MEDIAWIKI_API = 'https://www.guildwiki.de/gwiki/api.php';
	protected const CACHEDIR      = BUILDDIR.'/guildwiki';
	protected const INFOBOX_NAME  = 'infobox fertigkeit';

	public const USE_FIELDS = ['upkeep', 'energy', 'activation', 'recharge', 'adrenaline', 'adrenaline_precise', 'sacrifice', 'overcast'];

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
		3442 => 'Mächtiger Wurf (PvP)', // missing "PvP" suffix
	];

	protected const EMPTY_SKILL = [
		'name'               => 'Keine Fertigkeit',
		'description'        => 'Leerer Fertigkeiten-Slot',
		'concise'            => 'Leerer Slot',
		'upkeep'             => 0,
		'energy'             => 0,
		'activation'         => 0,
		'recharge'           => 0,
		'adrenaline'         => 0,
		'adrenaline_precise' => 0,
		'sacrifice'          => 0,
		'overcast'           => 0,
	];

	protected const PRE_PARSE_REPLACE = [
		'{{pipe}}}' => '',
		'{{{pipe}}' => '',
		'{{pipe}}'  => '',
		'{{!-}}'    => '',
		"'''"       => '',
		'{{sic}}'   => '<sic/>',
		'{{1/2}}'   => ' 1/2',
		'{{1/4}}'   => ' 1/4',
		'{{3/4}}'   => ' 3/4',
		'[s]'       => '(s)',
		'&#45;'     => '-',
		'&#x2d;'    => '-',
		'&nbsp;'    => ' ',
		'  '        => ' ',
	];

	protected function parseInfobox(string $infobox, int $id):array{
		// replace some templates (progression, links, colored text)
		$s = [
			// progression
			'/\{\{[p1-2]+\|([\+\-]*\d+)\|([\d%]+)(?:\|(?:[^\}]+))?\}\}/i',
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

		// fix +/- for re-/degeneration
		$infobox = preg_replace(
			[
				'/(?:regeneration von ((?:\d+)([.]+(?:\d+))?))/',
				'/(?:degeneration von \+?((?:\d+)(?:[.]+(?:\d+))?))/',
				'/(?:[^+]((?:\d+)(?:[.]+(?:\d+))\s+(?:Lebenspunktere|Lebenspunktre|Lebensre|Energiere)generation))/i',
				'/(?:[^-]((?:\d+)(?:[.]+(?:\d+))\s+(?:Lebenspunktede|Lebenspunktde|Lebensde|Energiede)generation))/i',
				'/(\d+)\s+%/',
			],
			[
				'regeneration von +$1',
				'degeneration von -$1',
				' +$1',
				' -$1',
				'$1%',
			],
			$infobox,
		);

		// split into key=value pairs
		$infobox = array_map($this->splitKV(...), array_filter(explode('|', trim($infobox))));
		// combine keys and values
		$infobox = array_combine(array_column($infobox, 0), array_column($infobox, 1));

		$infobox['name'] .= match(true){
			in_array($id, self::Luxon, true)   => ' (Luxon)',
			in_array($id, self::Kurzick, true) => ' (Kurzick)',
			default                            => '',
		};

		$adrenaline_precise = str_replace(',', '.', ($infobox['adrenalingenau'] ?? $infobox['adrenalin'] ?? '0')); // seriously???

		return [
			'name'               => $infobox['name'],
			'description'        => $infobox['beschreibung'],
			'concise'            => $infobox['kurzbeschreibung'],
			'upkeep'             => intval(($infobox['energieregeneration'] ?? 0)),
			'energy'             => intval(($infobox['energie'] ?? 0)),
			'activation'         => $this->calcFraction(($infobox['aktivierung'] ?? '0')),
			'recharge'           => intval(($infobox['wiederaufladung'] ?? 0)),
			'adrenaline'         => intval(($infobox['adrenalin'] ?? 0)),
			'adrenaline_precise' => floatval($adrenaline_precise),
			'sacrifice'          => intval(str_replace('%', '', ($infobox['lebenspunkteopfer'] ?? '0'))),
			'overcast'           => intval(($infobox['erschöpfung'] ?? 0)),
		];
	}

}
