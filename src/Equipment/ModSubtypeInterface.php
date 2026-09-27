<?php
/**
 * Class ModSubtype
 *
 * @created      19.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\DataObjectInterface;
use Buildwars\GWSkillData\Common\Lang;

interface ModSubtypeInterface extends DataObjectInterface{

	public function setFor(ItemType $for):static;
	public function setModID(int $modID):static;
	public function setModAttribute(Attribute|null $modAttribute):static;
	public function setModEffects(array|null $effects):static;

	/**
	 * Gets the current Affix(es)
	 */
	public function getAffix(Lang|string|null $lang = null):array;

	/**
	 * Returns the name of the mod item (rather than the mod name)
	 */
	public function getItemName(Lang|string|null $lang = null):string;

}
