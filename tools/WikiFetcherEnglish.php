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
declare(strict_types = 1);

namespace Buildwars\GWSkillDataTools;

use function array_column;
use function array_combine;
use function array_filter;
use function array_map;
use function explode;
use function in_array;
use function preg_replace;
use function sprintf;
use function str_ireplace;
use function trim;

/**
 * Fetches from the official Guild Wars wiki (wiki.guildwars.com)
 */
final class WikiFetcherEnglish extends WikiFetcher{

	protected const MEDIAWIKI_API = 'https://wiki.guildwars.com/api.php';
	protected const CACHEDIR      = __DIR__.'/../.build/gww/';

	/**
	 * @inheritDoc
	 */
	protected function prepareSkillName(string $skillName, int $id):string{
		// fix for pve faction skills
		$skillName = preg_replace('/(\s\((Kurzick|Luxon)\))/', '', $skillName);

		if(in_array($id, [316, 333, 343, 348, 364, 365, 366, 367, 368, 2858, 2883])){
			$skillName = preg_replace('/([^\(\)]+)(\s\(PvP\))?$/i', '"$1"$2', $skillName);
		}

		return $skillName;
	}

	/**
	 * @inheritDoc
	 */
	protected function parseResponse(array $data, int $id):array|null{

		if($id === 0){
			return ['No Skill', 'Empty skill slot', 'Empty slot'];
		}

		if(!isset($data['revisions'][0]['slots']['main']['*'])){
			return null;
		}

		$data    = $data['revisions'][0]['slots']['main']['*'];
		$infobox = $this->getInfobox($data, 'skill infobox');

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

		// clean out unwanted braces, double spaces, etc.
		$infobox = str_ireplace(['Skill infobox', '<gray>PvE Skill</gray>', '{', '}', '[', ']'], '', $infobox,);

		// fix some things
		$infobox = str_ireplace(
			['[does/do]', '[its/their]', '[s]', '&nbsp;', '  '],
			['(does/do)', '(its/their)', '(s)', ' ', ' '],
			$infobox,
		);

		// add a minus to degeneration
		$infobox = preg_replace('/((?:\d+)[.]+(?:\d+)\s+(?:Health|Energy)\s+degeneration)/i', '-$1', $infobox);

		// split into key=value pairs
		$infobox = array_map($this->splitKV(...), array_filter(explode('|', trim($infobox))));

		// combine keys and values
		$infobox = array_combine(array_column($infobox, 0), array_column($infobox, 1));

		// strip out the skill type
		foreach(['description', 'concise description'] as $k){
			// strip out the skill type
			$infobox[$k] = preg_replace('/^(Elite\s)?(Not a Skill|Skill|Bow Attack|Melee Attack|Axe Attack|Lead Attack|Off-Hand Attack|Dual Attack|Hammer Attack|Scythe Attack|Sword Attack|Pet Attack|Spear Attack|Chant|Echo|Form|Glyph|Preparation|Binding ritual|Nature ritual|Shout|Signet|Spell|Enchantment spell|Hex Spell|Item Spell|Ward Spell|Weapon Spell|Well Spell|Stance|Trap|Ranged attack|Ebon Vanguard Ritual|Flash Enchantment|Flash Enchantment Spell|Double Enchantment|Touch Skill)\.\s/i','', $infobox[$k]);
		}

		if(in_array($id, self::Luxon, true)){
			$infobox['name'] .= ' (Luxon)';
		}
		elseif(in_array($id, self::Kurzick, true)){
			$infobox['name'] .= ' (Kurzick)';
		}

		return [$infobox['name'], $infobox['description'], $infobox['concise description']];
	}

}
