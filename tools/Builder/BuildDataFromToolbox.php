<?php
/**
 * Creates the main data JSON and the PHP class from the game data via GW Toolbox API
 *
 * Creates:
 *
 *   - DATADIR/skilldata.json
 *   - SRCDIR/SkillData.php
 *
 * @created      02.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataInterface;
use chillerlan\HTTP\Utils\MessageUtil;
use function array_key_exists;
use function ceil;
use function in_array;
use function round;
use function sprintf;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;

final class BuildDataFromToolbox extends BuilderAbstract{

	private const array FIELD_MAP = [
		Skill::DATA_CAMPAIGN           => 'campaign',
		Skill::DATA_PROFESSION         => 'profession',
		Skill::DATA_ATTRIBUTE          => 'attribute',
		Skill::DATA_IS_ELITE           => 'elite',
		Skill::DATA_IS_RP              => 'pve_only',
		Skill::DATA_IS_PVP             => 'pvp_only',
		Skill::DATA_SPLIT_ID           => 'pvp_skill_id',
		Skill::DATA_ACTIVATION         => 'activation',
		Skill::DATA_AFTERCAST          => 'aftercast',
		Skill::DATA_RECHARGE           => 'recharge',
		Skill::DATA_UPKEEP             => 'duration0',
		Skill::DATA_ENERGY             => 'energy_cost',
		Skill::DATA_ADRENALINE         => 'adrenaline',
		Skill::DATA_ADRENALINE_PRECISE => 'adrenaline',
		Skill::DATA_SACRIFICE          => 'health_cost',
		Skill::DATA_EXHAUSTION         => 'overcast',
	];

	private const array TITLE_MAP = [
		0  => Attribute::NONE,
		5  => Attribute::TITLE_KURZICK,
		6  => Attribute::TITLE_LUXON,
		17 => Attribute::TITLE_SUNSPEAR,
		20 => Attribute::TITLE_LIGHTBRINGER,
		38 => Attribute::TITLE_ASURA,
		39 => Attribute::TITLE_DELDRIMOR,
		40 => Attribute::TITLE_VANGUARD,
		41 => Attribute::TITLE_NORN,
	];

	public function build():static{
		$url       = sprintf(self::TOOLBOX_SKILL_ENDPOINT, Lang::EN);
		$response  = $this->fetch($url, self::TOOLBOX_CACHEDIR, $this->options->from_cache);
		$json      = MessageUtil::decodeJSON($response, true);
		$skillData = [];

		foreach($json as $skill){

			if(!array_key_exists($skill['id'], $this->known)){
				continue;
			}

			$skillData[$skill['id']] = $this->createSkill($skill);
		}

		return $this->saveDataJSON($skillData)->createDataClass($skillData);
	}

	/**
	 * @param array<string, scalar> $skill
	 *
	 * @return array<string, scalar>
	 */
	private function createSkill(array $skill):array{
		$newSkill = $this->createDataFields($skill['id']);

		foreach(self::FIELD_MAP as $key => $tbkey){

			$newSkill[$key] = match($key){
				Skill::DATA_AFTERCAST
					=> ((isset($skill[$tbkey]) && $skill[$tbkey] > 0) ? round($skill[$tbkey], 2) : 0),
				Skill::DATA_ATTRIBUTE
					=> ($skill[$tbkey] ?? self::TITLE_MAP[($skill['title'] ?? 0)]),
				Skill::DATA_IS_ELITE, Skill::DATA_IS_RP, Skill::DATA_IS_PVP
					=> (bool)($skill[$tbkey] ?? 0),
				Skill::DATA_ADRENALINE
					=> (int)ceil(($skill[$tbkey] ?? 0) / 25),
				Skill::DATA_ADRENALINE_PRECISE
					=> (($skill[$tbkey] ?? 0) / 25),
				// the "duration0" field is probably a bitmask (big endian) and bit 2 might be the upkeep flag
				Skill::DATA_UPKEEP
					=> ((isset($skill[$tbkey]) && $skill[$tbkey] === 131072) ? -1 : 0),
				default => ($skill[$tbkey] ?? 0),
			};

			// for some reason some of the PvE skills have the wrong campaign assigned
			if($key === Skill::DATA_CAMPAIGN){
				$newSkill[$key] = match(true){
					in_array($skill['id'], SkillDataInterface::SKILLS_KURZICK, true),
					in_array($skill['id'], SkillDataInterface::SKILLS_LUXON, true)
						=> Campaign::FACTIONS,
					in_array($skill['id'], SkillDataInterface::SKILLS_SUNSPEAR, true),
					in_array($skill['id'], SkillDataInterface::SKILLS_LIGHTBRINGER, true)
						=> Campaign::NIGHTFALL,
					default => ($skill[$tbkey] ?? 0),
				};
			}

		}

		return $newSkill;
	}

	/**
	 * @return array<string, scalar>
	 */
	private function createDataFields(int $id):array{
		$fields = [];

		foreach(Skill::KEYS_DATA as $key){

			$fields[$key] = match($key){
				Skill::DATA_ID        => $id,
				Skill::DATA_TYPE      => $this->known[$id][Skill::DATA_TYPE],
				Skill::DATA_PVP_SPLIT => array_key_exists($id, PVP_SPLIT),
				default               => null,
			};

#			if($key === Skill::DATA_SPLIT_ID){
#
#				$fields[$key] = match(true){
#					array_key_exists($id, PVP_SPLIT)      => PVP_SPLIT[$id],
#					array_key_exists($id, PVP_SPLIT_FLIP) => PVP_SPLIT_FLIP[$id],
#					default                               => 0,
#				};
#
#			}

		}

		return $fields;
	}

}
