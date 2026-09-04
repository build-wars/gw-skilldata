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

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataInterface;
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
use const BUILDDIR;

/**
 * Fetches from the official Guild Wars wiki (wiki.guildwars.com)
 */
final class WikiFetcherEnglish extends WikiFetcherAbstract{

	public const string CACHEDIR         = BUILDDIR.'/gww';
	public const string MEDIAWIKI_API    = 'https://wiki.guildwars.com/api.php';

	protected const string LANG          = Lang::EN;
	protected const string INFOBOX_NAME  = 'skill infobox';

	protected const array REDIRECTS = [
		1599 => '"It\'s Just a Flesh Wound."',
		1954 => '"Save Yourselves!"', // Luxon
		2097 => '"Save Yourselves!"', // Kurzick
	];

	protected const array EMPTY_SKILL = [
		Skill::DESC_NAME        => 'No Skill',
		Skill::DESC_DESCRIPTION => 'Empty skill slot',
		Skill::DESC_CONCISE     => 'Empty slot',
		Skill::DATA_TYPE        => 0,
		Skill::DATA_UPKEEP      => 0,
		Skill::DATA_ENERGY      => 0,
		Skill::DATA_ACTIVATION  => 0,
		Skill::DATA_RECHARGE    => 0,
		Skill::DATA_ADRENALINE  => 0,
		Skill::DATA_SACRIFICE   => 0,
		Skill::DATA_EXHAUSTION  => 0,
		'range'                 => 0,
		'aoe'                   => 0,
	];

	protected const array PRE_PARSE_REPLACE = [
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
		$infobox = str_ireplace([self::INFOBOX_NAME, '<gray>PvE Skill</gray>', '{', '}', '[', ']', "'''"], '', $infobox);

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
			in_array($id, SkillDataInterface::SKILLS_LUXON, true)   => ' (Luxon)',
			in_array($id, SkillDataInterface::SKILLS_KURZICK, true) => ' (Kurzick)',
			default => '',
		};

		return [
			Skill::DESC_NAME        => $infobox['name'],
			Skill::DESC_DESCRIPTION => $infobox['description'],
			Skill::DESC_CONCISE     => $infobox['concise description'],
			Skill::DATA_TYPE        => ($this->skilltypes[ucwords($infobox['type'], ' ')] ?? 0),
			Skill::DATA_UPKEEP      => intval(($infobox['upkeep'] ?? 0)),
			Skill::DATA_ENERGY      => intval(($infobox['energy'] ?? 0)),
			Skill::DATA_ACTIVATION  => $this->calcFraction(($infobox['activation'] ?? '0')),
			Skill::DATA_RECHARGE    => intval(($infobox['recharge'] ?? 0)),
			Skill::DATA_ADRENALINE  => intval(($infobox['adrenaline'] ?? 0)),
			Skill::DATA_SACRIFICE   => intval(str_replace('%', '', ($infobox['sacrifice'] ?? '0'))),
			Skill::DATA_EXHAUSTION  => intval(($infobox['overcast'] ?? 0)),
			'range'                 => ($infobox['range'] ?? '--'),
			'aoe'                   => ($infobox['aoe'] ?? '--'),
		];
	}

}
