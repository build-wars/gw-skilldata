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

use Buildwars\GWSkillData\SkillLang;
use function array_column;
use function array_combine;
use function array_filter;
use function array_map;
use function explode;
use function implode;
use function in_array;
use function intval;
use function preg_replace;
use function str_ireplace;
use function str_replace;
use function strtr;
use function trim;
use function ucwords;

/**
 * Fetches from the official Guild Wars wiki (wiki.guildwars.com)
 */
final class WikiFetcherEnglish extends WikiFetcherAbstract{

	protected const LANG          = SkillLang::EN;
	protected const MEDIAWIKI_API = 'https://wiki.guildwars.com/api.php';
	protected const CACHEDIR      = BUILDDIR.'/gww';
	protected const INFOBOX_NAME  = 'skill infobox';

	public const USE_FIELDS = ['type'];

	protected const REDIRECTS = [
		1599 => '"It\'s Just a Flesh Wound."',
		1954 => '"Save Yourselves!"', // Luxon
		2097 => '"Save Yourselves!"', // Kurzick
	];

	protected const EMPTY_SKILL = [
		'name'        => 'No Skill',
		'description' => 'Empty skill slot',
		'concise'     => 'Empty slot',
		'type'        => 0,
		'upkeep'      => 0,
		'energy'      => 0,
		'activation'  => 0,
		'recharge'    => 0,
		'adrenaline'  => 0,
		'sacrifice'   => 0,
		'overcast'    => 0,
		'range'       => 0,
		'aoe'         => 0,
	];

	protected const PRE_PARSE_REPLACE = [
		'{{sic}}'     => '<sic/>',
		'{{1/2}}'     => ' 1/2',
		'{{1/4}}'     => ' 1/4',
		'{{3/4}}'     => ' 3/4',
		'&nbsp;'      => ' ',
		'&#20;'       => ' ',
		'&#45;'       => '-',
		'&#x2d;'      => '-',
		'[s]'         => '(s)',
		'[es]'        => '(es)',
		'[its/their]' => '(its/their)',
	];

	protected function parseInfobox(string $infobox, int $id):array{
		// replace some templates
		$s = [
			// progression
			'/\{\{gr2?\|(\d+)\|(\d+)(?:\|([+-])?)?(?:\|(%)?)?\}\}/i',
			// article links
			'/\[\[[^\[\|]+\|([^\[\|]+)\]\]/',
			// [sic]
			'/\{\{sic(?:\|[^\}]+)?\}\}/i',
			// colored text
			'/\{\{(?:gray|grey)\|([^\{\}]+)\}\}/i',
			'#<span[^>]*?>(.*)</span>#i',
			'/(\d+)\s+%/',
			'/\s+/',
		];

		$r = [
			'$3$1...$2$4',
			'$1',
			'<sic/>',
			'<gray>$1</gray>',
			'<gray>$1</gray>',
			'$1%',
			' ',
		];

		$infobox = preg_replace($s, $r, $infobox);

		// clean out unwanted braces etc.
		$infobox = str_ireplace(['Skill infobox', '<gray>PvE Skill</gray>', '{', '}', '[', ']', "'''"], '', $infobox);

		$infobox = strtr($infobox, [
			'(s)'         => '[s]',
			'(es)'        => '[es]',
			'(its/their)' => '[its/their]',
		]);

		// split into key=value pairs
		$infobox = array_map($this->splitKV(...), array_filter(explode('|', trim($infobox))));
		// combine keys and values
		$infobox = array_combine(array_column($infobox, 0), array_column($infobox, 1));

		// strip out the skill type
		foreach(['description', 'concise description'] as $k){
			$ex = explode('. ', $infobox[$k], 2);

			$infobox[$k] = $ex[1];

			// we'll leave the skill type in for some outlier concise descriptions
			if($k === 'concise description' && $this->strContainsAny($ex[0], ['Half Range', 'Touch', 'Spear Melee'], true)){
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
			'name'        => $infobox['name'],
			'description' => $infobox['description'],
			'concise'     => $infobox['concise description'],
			'type'        => ($this->skilltypes[ucwords($infobox['type'], ' ')] ?? 0),
			'upkeep'      => intval(($infobox['upkeep'] ?? 0)),
			'energy'      => intval(($infobox['energy'] ?? 0)),
			'activation'  => $this->calcFraction(($infobox['activation'] ?? '0')),
			'recharge'    => intval(($infobox['recharge'] ?? 0)),
			'adrenaline'  => intval(($infobox['adrenaline'] ?? 0)),
			'sacrifice'   => intval(str_replace('%', '', ($infobox['sacrifice'] ?? '0'))),
			'overcast'    => intval(($infobox['overcast'] ?? 0)),
			'range'       => ($infobox['range'] ?? '--'),
			'aoe'         => ($infobox['aoe'] ?? '--'),
		];
	}

}
