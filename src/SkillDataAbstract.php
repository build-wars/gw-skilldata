<?php
/**
 * Class SkillDataAbstract
 *
 * @created      01.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use InvalidArgumentException;
use function array_combine;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_search;
use function in_array;

/**
 * Utility methods for the static skill data and description classes
 */
abstract class SkillDataAbstract implements SkillDataInterface{

	/** @phan-suppress PhanTypeMismatchArgumentNullableInternal */
	public function get(int $id, bool $pvp = false):Skill{

		if(!array_key_exists($id, static::ID2DATA)){
			throw new InvalidArgumentException('invalid skill ID');
		}

		$skillData = array_combine(static::KEYS_DATA, static::ID2DATA[$id]);

		if(
			// pvp mode, the skill has a pvp redirect but pve version was given
			($pvp && !$skillData[Skill::DATA_IS_PVP] && $skillData[Skill::DATA_PVP_SPLIT])
			// pve mode, the pvp skill was given, redirect to pve
			|| (!$pvp && $skillData[Skill::DATA_IS_PVP] && $skillData[Skill::DATA_SPLIT_ID] !== 0)
		){
			$id = $skillData[Skill::DATA_SPLIT_ID];

			$skillData = array_combine(static::KEYS_DATA, static::ID2DATA[$id]);
		}

		$skillDesc = array_combine(static::KEYS_DESC, static::ID2DESC[$id]);

		return new Skill(array_merge($skillData, $skillDesc), static::LANG);
	}

	private function getByKey(string $key, int|bool $value, bool $pvp):array{
		$keyID  = array_search($key, static::KEYS_DATA, true);
		$skills = [];

		foreach(static::ID2DATA as $id => $data){
			if($data[$keyID] === $value){
				$skills[] = $this->get($id, $pvp);
			}
		}

		return $skills;
	}

	public function getAll(array $IDs, bool $pvp = false):array{
		return array_map(fn(int $id):Skill => $this->get($id, $pvp), $IDs);
	}

	public function getByCampaign(Campaign|int $campaign, bool $pvp = false):array{

		if(!$campaign instanceof Campaign){
			$campaign = new Campaign($campaign);
		}

		return $this->getByKey(Skill::DATA_CAMPAIGN, $campaign->id, $pvp);
	}

	public function getByProfession(Profession|int $profession, bool $pvp = false):array{

		if(!$profession instanceof Profession){
			$profession = new Profession($profession);
		}

		return $this->getByKey(Skill::DATA_PROFESSION, $profession->id, $pvp);
	}

	public function getByAttribute(Attribute|int $attribute, bool $pvp = false):array{

		if(!$attribute instanceof Attribute){
			$attribute = new Attribute($attribute);
		}

		return $this->getByKey(Skill::DATA_ATTRIBUTE, $attribute->id, $pvp);
	}

	public function getByType(Skilltype|int $type, bool $pvp = false):array{

		if(!$type instanceof Skilltype){
			$type = new Skilltype($type);
		}

		return $this->getByKey(Skill::DATA_TYPE, $type->id, $pvp);
	}

	public function getByTypeWithSubtypes(Skilltype|int $type, bool $pvp = false):array{

		if(!$type instanceof Skilltype){
			$type = new Skilltype($type);
		}

		$types  = $type->withSubtypes();
		$keyID  = array_search(Skill::DATA_TYPE, static::KEYS_DATA, true);
		$skills = [];

		foreach(static::ID2DATA as $id => $data){
			if(in_array($data[$keyID], $types, true)){
				$skills[] = $this->get($id, $pvp);
			}
		}

		return $skills;
	}

	public function getElite(bool $pvp = false):array{
		return $this->getByKey(Skill::DATA_IS_ELITE, true, $pvp);
	}

	public function getRoleplay():array{
		return $this->getByKey(Skill::DATA_IS_RP, true, false);
	}

	public function getIDs(bool $pvp = false):array{
		$keyID = array_search(Skill::DATA_IS_PVP, static::KEYS_DATA, true);
		$ids   = [];

		foreach(static::ID2DATA as $id => $data){
			if($data[$keyID] === $pvp){
				$ids[] = $id;
			}
		}

		return $ids;
	}

}
