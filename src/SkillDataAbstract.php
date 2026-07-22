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

	/** @phan-suppress PhanTypeInvalidDimOffset, PhanTypeArraySuspiciousNullable, PhanTypeMismatchArgumentNullableInternal */
	private function combine(int $id):array{

		if(!array_key_exists($id, static::ID2DATA)){
			throw new InvalidArgumentException('invalid skill ID');
		}

		$skillData = array_combine(static::KEYS_DATA, static::ID2DATA[$id]);
		$skillDesc = array_combine(static::KEYS_DESC, static::ID2DESC[$id]);
		$prof      = new Profession($skillData['profession'], static::LANG);

		$names = array_combine(static::KEYS_NAMES, [
			(new Campaign($skillData['campaign'], static::LANG))->getName(),
			$prof->getName(),
			$prof->getAbbr(),
			(new Attribute($skillData['attribute'], static::LANG))->getName(),
			(new Skilltype($skillData['type'], static::LANG))->getName(),
		]);

		return array_merge($skillData, $skillDesc, $names);
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

	public function get(int $id, bool $pvp = false):array{
		$data = $this->combine($id);

		if($pvp === false || $data['pvp_split'] === false){
			return $data;
		}

		return $this->combine($data['split_id']);
	}

	public function getAll(array $IDs, bool $pvp = false):array{
		return array_map(fn(int $id):array => $this->get($id, $pvp), $IDs);
	}

	public function getByCampaign(int $campaign, bool $pvp = false):array{
		return $this->getByKey('campaign', (new Campaign($campaign))->id, $pvp);
	}

	public function getByProfession(int $profession, bool $pvp = false):array{
		return $this->getByKey('profession', (new Profession($profession))->id, $pvp);
	}

	public function getByAttribute(int $attribute, bool $pvp = false):array{
		return $this->getByKey('attribute', (new Attribute($attribute))->id, $pvp);
	}

	public function getByType(int $type, bool $pvp = false):array{
		return $this->getByKey('type', (new Skilltype($type))->id, $pvp);
	}

	public function getByTypeWithSubtypes(int $type, bool $pvp = false):array{
		$types  = (new Skilltype($type))->withSubtypes();
		$keyID  = array_search('type', static::KEYS_DATA, true);
		$skills = [];

		foreach(static::ID2DATA as $id => $data){
			if(in_array($data[$keyID], $types, true)){
				$skills[] = $this->get($id, $pvp);
			}
		}

		return $skills;
	}

	public function getElite(bool $pvp = false):array{
		return $this->getByKey('is_elite', true, $pvp);
	}

	public function getRoleplay():array{
		return $this->getByKey('is_rp', true, false);
	}

	public function getIDs(bool $pvp = false):array{
		$keyID = array_search('is_pvp', static::KEYS_DATA, true);
		$ids   = [];

		foreach(static::ID2DATA as $id => $data){
			if($data[$keyID] === $pvp){
				$ids[] = $id;
			}
		}

		return $ids;
	}

}
