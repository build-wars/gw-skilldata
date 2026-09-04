<?php
/**
 * Creates the description files for the several languages from the game data via GW Toolbox API.
 *
 * Please note that these language strings have tags and replacements for grammatical gender in them.
 * Unfortunately there's no easy way to strip out the Skill types and properly highlight grey text.
 *
 * Creates:
 *
 * - DATADIR/skilldesc-<lang>.json
 * - SRCDIR/SkillLang<lang>.php
 *
 * @created      03.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use chillerlan\HTTP\Utils\MessageUtil;
use function array_key_exists;
use function preg_replace;
use function sprintf;

final class BuildLangFromToolbox extends BuilderAbstract{

	private const array FIELD_MAP = [
		Skill::DESC_NAME         => 'name',
		Skill::DESC_DESCRIPTION  => 'description',
		Skill::DESC_CONCISE      => 'concise',
	];

	/**
	 * might change this to translated strings, but not today...
	 */
	private const array EMPTY_SKILL = [
		Skill::DATA_ID          => 0,
		Skill::DESC_NAME        => 'No Skill',
		Skill::DESC_DESCRIPTION => 'Empty skill slot',
		Skill::DESC_CONCISE     => 'Empty slot',
	];

	public function build():static{

		foreach(Lang::IDS as $langID){
			$url       = sprintf(self::TOOLBOX_SKILL_ENDPOINT, $langID);
			$response  = $this->fetch($url, self::TOOLBOX_CACHEDIR, $this->options->from_cache);
			$json      = MessageUtil::decodeJSON($response, true);
			$skillDesc = [];

			foreach($json as $skill){

				if(!array_key_exists($skill['id'], $this->known)){
					continue;
				}

				if($skill['id'] === 0){
					$skill = self::EMPTY_SKILL;
				}

				$skillDesc[$skill['id']] = $this->createDesc($skill);
			}

			$this->saveLangJSON($skillDesc, $langID)->createLangClass($skillDesc, $langID);
		}

		return $this;
	}

	private function createDesc(array $skill):array{
		$desc = $this->createLangFields($skill['id']);

		foreach(self::FIELD_MAP as $key => $tbkey){
			/** @noinspection RegExpRedundantEscape */
			$desc[$key] = match($key){
				Skill::DESC_NAME => preg_replace('/\[[^\]]+\]/i', '', ($skill[$tbkey] ?? '')),
				default          => preg_replace('/\s+/', ' ', ($skill[$tbkey] ?? '')),
			};
		}

		return $desc;
	}

	private function createLangFields(int $id):array{
		$fields = [Skill::DATA_ID => $id];

		foreach(Skill::KEYS_DESC as $key){
			$fields[$key] = null;
		}

		return $fields;
	}

}
