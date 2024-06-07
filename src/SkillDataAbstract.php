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
use function array_merge;
use function array_search;

/**
 *
 */
abstract class SkillDataAbstract implements SkillDataInterface{

	public readonly array $keys;

	public function __construct(){
		$this->keys = array_merge($this::KEYS_DATA, $this::KEYS_DESC, $this::KEYS_NAMES);
	}

	/** @phan-suppress PhanTypeInvalidDimOffset, PhanTypeArraySuspiciousNullable, PhanTypeMismatchArgumentNullableInternal */
	private function combine(int $id):array{

		if(!isset($this::ID2DATA[$id])){
			throw new InvalidArgumentException('invalid skill ID');
		}

		$skillData = array_combine($this::KEYS_DATA, $this::ID2DATA[$id]);
		$skillDesc = array_combine($this::KEYS_DESC, $this::ID2DESC[$id]);

		$names = array_combine($this::KEYS_NAMES, [
			$this::CAMPAIGNS[$skillData['campaign']]['name'][$this::LANG],
			$this::PROFESSIONS[$skillData['profession']]['name'][$this::LANG],
			$this::PROFESSIONS[$skillData['profession']]['abbr'][$this::LANG],
			$this::ATTRIBUTES[$skillData['attribute']]['name'][$this::LANG],
			$this::SKILLTYPES[$skillData['type']]['name'][$this::LANG],
		]);

		return array_merge($skillData, $skillDesc, $names);
	}

	private function getByKey(string $key, int|bool $value, bool $pvp):array{
		$keyID  = array_search($key, $this::KEYS_DATA);
		$skills = [];

		foreach($this::ID2DATA as $id => $data){
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
		$skills = [];

		foreach($IDs as $k => $id){
			$skills[$k] = $this->get($id, $pvp);
		}

		return $skills;
	}

	public function getByCampaign(int $campaign, bool $pvp = false):array{

		if(!isset($this::CAMPAIGNS[$campaign])){
			throw new InvalidArgumentException('invalid campaign ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('campaign', $campaign, $pvp);
	}

	public function getByProfession(int $profession, bool $pvp = false):array{

		if(!isset($this::PROFESSIONS[$profession])){
			throw new InvalidArgumentException('invalid profession ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('profession', $profession, $pvp);
	}

	public function getByAttribute(int $attribute, bool $pvp = false):array{

		if(!isset($this::ATTRIBUTES[$attribute])){
			throw new InvalidArgumentException('invalid attribute ID'); // @codeCoverageIgnore
		}

		return $this->getByKey('attribute', $attribute, $pvp);
	}

	public function getByType(int $type, bool $pvp = false):array{

		if(!isset($this::SKILLTYPES[$type])){
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
