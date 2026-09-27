<?php
/**
 * Class ModSubtypeAbstract
 *
 * @created      19.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Condition;
use Buildwars\GWSkillData\Common\DamageType;
use Buildwars\GWSkillData\Common\DataObjectAbstract;
use Buildwars\GWSkillData\Common\Effect;
use Buildwars\GWSkillData\Common\EffectCondition;
use Buildwars\GWSkillData\Common\Lang;
use RuntimeException;
use function count;
use function dechex;
use function implode;
use function in_array;
use function is_array;
use function is_int;
use function sprintf;
use function str_replace;

abstract class ModSubtypeAbstract extends DataObjectAbstract implements ModSubtypeInterface{

	protected(set) ItemType $for;
	protected(set) Attribute|null $attribute = null;
	protected(set) int $modID = -1;
	protected(set) array|null $effects = null;

	public function setFor(ItemType $for):static{
		$this->for = $for;

		return $this;
	}

	public function setModID(int $modID):static{
		$this->modID = $modID;

		return $this;
	}

	public function setModAttribute(Attribute|null $modAttribute):static{
		$this->attribute = $modAttribute;

		return $this;
	}

	public function setModEffects(array|null $effects):static{
		$this->effects = $effects;

		return $this;
	}

	public function getAffix(Lang|string|null $lang = null):array{

		if($this->effects === null){
			return [];
		}

		return $this->parseEffects($this->effects, $this->getLang($lang));
	}

	/**
	 * Parses the given array of effects, returns an array with human-readable item affixes
	 *
	 * @param int[][]|int[][][] $effects
	 *
	 * @return string[]
	 */
	protected function parseEffects(array $effects, Lang $lang):array{

		if(is_array($effects[0][0])){
			$e = [];
			// recursion for multiple effects
			foreach($effects as $effect){
				$e = array_merge($e, $this->parseEffects($effect, $lang));
			}

			return $e;
		}

		// a single effect without condition
		if(count($effects) === 1){
			/** @phan-suppress-next-line PhanTypeInvalidDimOffsetArrayDestructuring */
			[$effect, $effectValue] = $effects[0];

			return [$this->getEffect($effect, $effectValue, $lang)];
		}

		$affix = [];

		[$eff, $con] = $effects;
		/** @phan-suppress-next-line PhanTypeInvalidDimOffsetArrayDestructuring */
		[$effect, $effectValue] = $eff;

		$affix[] = $this->getEffect($effect, $effectValue, $lang);

		// multiple conditions
		if(is_array($con[0])){
			$v = [];

			foreach($con as [$c, $cv]){
				$v[] = $this->getEffectCondition($c, $cv, $lang);
			}

			// all conditions are of type EffectCondition
			if(EffectCondition::has($con[0][0])){
				// combine the affixes
				$affix[] = str_replace(')</gray><gray>(', ', ', implode('', $v));

				return [implode(' ', $affix)];
			}

			$affix[] = implode(' ', $v);

			return $affix;
		}

		[$condition, $conditionValue] = $con;
		$affix[] = $this->getEffectCondition($condition, $conditionValue, $lang);
		// the condition is also an effect
		if(Effect::has($condition)){
			return $affix;
		}

		return [implode(' ', $affix)];
	}

	protected function getEffect(int $effect, int|string|null $value, Lang $lang):string{

		if(Effect::has($effect)){
			$e = new Effect($effect, $lang);

			if($effect === Effect::ATTRIBUTE_BONUS || $effect === Effect::OF_PROFESSION){
				/** @phan-suppress-next-line PhanTypeMismatchArgumentNullable */
				return $e->getAffix($this->attribute->getName($lang), $value);
			}

			if(is_int($value) && Condition::has($value)){
				$value = new Condition($value, $lang)->getName();
			}

			// positive armor, energy and health values on affixes are always "+<value>"
			if(in_array($effect, [Effect::ARMOR, Effect::ENERGY, Effect::HEALTH], true) && is_int($value) &&  $value > 0){
				$value = '+'.$value;
			}

			return $e->getAffix(($value ?? ''));
		}

		if(DamageType::has($effect)){
			return new DamageType($effect, $lang)->getName();
		}

		throw new RuntimeException(sprintf('unknown effect: %s', dechex($effect)));
	}

	protected function getEffectCondition(int $effect, int|string|null $value, Lang $lang):string{

		if(EffectCondition::has($effect)){
			$e = new EffectCondition($effect, $lang);

			if($value !== null && DamageType::has($value)){
				$value = new DamageType($value, $lang)->getName();
			}

			if($effect === EffectCondition::ATTRIBUTE_REQ9 || $effect === EffectCondition::ATTRIBUTE_REQ13){
				/** @phan-suppress-next-line PhanTypeMismatchArgumentNullable */
				$value = new Attribute($value, $lang)->getName();
			}

			return $e->getAffix(($value ?? ''));
		}

		if(Effect::has($effect)){
			return $this->getEffect($effect, $value, $lang);
		}

		throw new RuntimeException(sprintf('unknown effect condition: %s', dechex($effect)));
	}

	public function getName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return (static::NAME[$this->id][$lang->id] ?? sprintf('[MOD_%s_%s]', $this->modID, $lang->id));
	}

	public function getItemName(Lang|string|null $lang = null):string{
		return $this->getName($lang);
	}

}
