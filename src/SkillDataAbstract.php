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

		$names = array_combine(static::KEYS_NAMES, [
			static::CAMPAIGNS[$skillData['campaign']]['name'][static::LANG],
			static::PROFESSIONS[$skillData['profession']]['name'][static::LANG],
			static::PROFESSIONS[$skillData['profession']]['abbr'][static::LANG],
			static::ATTRIBUTES[$skillData['attribute']]['name'][static::LANG],
			static::SKILLTYPES[$skillData['type']]['name'][static::LANG],
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

		if(!array_key_exists($campaign, static::CAMPAIGNS)){
			throw new InvalidArgumentException('invalid campaign ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('campaign', $campaign, $pvp);
	}

	public function getByProfession(int $profession, bool $pvp = false):array{

		if(!array_key_exists($profession, static::PROFESSIONS)){
			throw new InvalidArgumentException('invalid profession ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('profession', $profession, $pvp);
	}

	public function getByAttribute(int $attribute, bool $pvp = false):array{

		if(!array_key_exists($attribute, static::ATTRIBUTES)){
			throw new InvalidArgumentException('invalid attribute ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('attribute', $attribute, $pvp);
	}

	public function getByType(int $type, bool $pvp = false):array{

		if(!array_key_exists($type, static::SKILLTYPES)){
			throw new InvalidArgumentException('invalid skill type ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('type', $type, $pvp);
	}

	public function getElite(bool $pvp = false):array{
		return $this->getByKey('is_elite', true, $pvp);
	}

	public function getRoleplay():array{
		return $this->getByKey('is_rp', true, false);
	}

}
