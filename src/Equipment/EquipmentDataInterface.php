<?php
/**
 * Interface EquipmentDataInterface
 *
 * @created      19.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Profession;
use Closure;

interface EquipmentDataInterface{

	public function map(Closure $callable):array;
	public function get(int $id):object;
	/** @param int[] $IDs */
	public function getAll(array $IDs):array;
	public function getByProfession(Profession|int $profession):array;
	public function getByAttribute(Attribute|int $attribute):array;
	public function getByType(int $type):array;
	public function getIDs():array;

}
