<?php
/**
 * Class WikiFetcherEnglish
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

use function array_column;
use function array_combine;
use function array_filter;
use function array_map;
use function Buildwars\GWSkillDataTools\str_contains_any;
use function explode;
use function implode;
use function in_array;
use function intval;
use function preg_replace;
use function sprintf;
use function str_ireplace;
use function str_replace;
use function strtr;
use function trim;
use function ucwords;

/**
 * Fetches from the official Guild Wars wiki (wiki.guildwars.com)
 */
final class WikiFetcherEnglish extends WikiFetcher{

	protected const MEDIAWIKI_API = 'https://wiki.guildwars.com/api.php';
	protected const CACHEDIR      = __DIR__.'/../../.build/gww/';

	protected const REDIRECTS = [
		1599 => '"It\'s Just a Flesh Wound."',
		1954 => '"Save Yourselves!"', // Luxon
		2097 => '"Save Yourselves!"', // Kurzick
	];

	protected function parseResponse(array $data, int $id):array|null{

		if($id === 0){
			return [['No Skill', 'Empty skill slot', 'Empty slot'], null];
		}

		if(!isset($data['revisions'][0]['slots']['main']['*'])){
			return null;
		}

		$data = $data['revisions'][0]['slots']['main']['*'];

		// remove/fix some templates first
		$data = strtr($data, [
			'{{1/2}}' => ' 1/2',
			'{{1/4}}' => ' 1/4',
			'{{3/4}}' => ' 3/4',
			'&nbsp;'  => ' ',
			'  '      => ' ',
		]);

		$infobox = $this->getInfobox($data, 'skill infobox');

		if($infobox === null){
			$this->logger->warning(sprintf('could not parse infobox for skill %s', $id));

			return null;
		}

		return $this->parseInfobox($infobox, $id);
	}

	protected function parseInfobox(string $infobox, int $id):array{
		// replace some templates
		$s = [
			// progression
			'/\{\{gr(?:2)?\|([\+\-\d]+)\|([\d%]+)(?:\|(?:[^\}]+)?)?\}\}/i',
			// article links
			'/\[\[[^\[\|]+\|([^\[\|]+)\]\]/',
			// [sic]
			'/\{\{sic(?:\|[^\}]+)?\}\}/i',
			// colored text
			'/\{\{(?:gray|grey)\|([^\{\}]+)\}\}/i',
			'#<span[^>]*?>(.*)</span>#i',
		];

		$r = [
			'$1...$2',
			'$1',
			'<sic/>',
			'<gray>$1</gray>',
			'<gray>$1</gray>',
		];

		$infobox = preg_replace($s, $r, $infobox);

		// fix some things
		$infobox = str_ireplace(
			['[its/their]', '[s]'], // '[does/do]',
			['their', 's'], // '(does/do)',
			$infobox,
		);

		// clean out unwanted braces etc.
		$infobox = str_ireplace(['Skill infobox', '<gray>PvE Skill</gray>', '{', '}', '[', ']', "'''"], '', $infobox);
		// add a minus to degeneration
		$infobox = preg_replace('/((?:\d+)[.]+(?:\d+)\s+(?:Health|Energy)\s+degeneration)/i', '-$1', $infobox);
		// split into key=value pairs
		$infobox = array_map($this->splitKV(...), array_filter(explode('|', trim($infobox))));
		// combine keys and values
		$infobox = array_combine(array_column($infobox, 0), array_column($infobox, 1));

		// strip out the skill type
		foreach(['description', 'concise description'] as $k){
			$ex = explode('. ', $infobox[$k], 2);

			$infobox[$k] = $ex[1];

			// we'll leave the skill type in for some outlier concise descriptions
			if($k === 'concise description' && str_contains_any($ex[0], ['Half Range', 'Touch', 'Spear Melee'], true)){
				$ex[0]       = ucwords($ex[0], ' ');
				$infobox[$k] = implode('. ', $ex);
			}
		}

		$infobox['name'] .= match(true){
			in_array($id, self::Luxon, true)   => ' (Luxon)',
			in_array($id, self::Kurzick, true) => ' (Kurzick)',
			default                            => '',
		};

		return [
			[
				$infobox['name'],
				$infobox['description'],
				$infobox['concise description'],
			],
			[
				'type_name'  => ucwords($infobox['type'], ' '),
				'upkeep'     => intval(($infobox['upkeep'] ?? 0)),
				'energy'     => intval(($infobox['energy'] ?? 0)),
				'activation' => $this->calcFraction(($infobox['activation'] ?? '0')),
				'recharge'   => intval(($infobox['recharge'] ?? 0)),
				'adrenaline' => intval(($infobox['adrenaline'] ?? 0)),
				'sacrifice'  => intval(str_replace('%', '', ($infobox['sacrifice'] ?? '0'))),
				'overcast'   => intval(($infobox['overcast'] ?? 0)),
			],
		];
	}

}
