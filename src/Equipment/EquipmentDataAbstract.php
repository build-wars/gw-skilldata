<?php
/**
 * Class EquipmentDataAbstract
 *
 * @created      19.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\Profession;
use function array_keys;

/**
 * @todo
 */
abstract class EquipmentDataAbstract implements EquipmentDataInterface{

	protected const array NAMES = [];
	protected const array DATA  = [];

	protected string|Lang $lang;

	public function __construct(Lang|string $lang = Lang::EN){

		if(!$lang instanceof Lang){
			$lang = new Lang($lang);
		}

		$this->lang = $lang;
	}

	/** @param int[] $IDs */
	public function getAll(array $IDs):array{
		return [];
	}

	public function getByProfession(Profession|int $profession):array{

		if(!$profession instanceof Profession){
			$profession = new Profession($profession);
		}

		return [];
	}

	public function getByAttribute(Attribute|int $attribute):array{

		if(!$attribute instanceof Attribute){
			$attribute = new Attribute($attribute);
		}

		return [];
	}

	public function getByType(int $type):array{
		return [];
	}

	public function getIDs():array{
		return array_keys(static::DATA);
	}

}
