<?php
/**
 * Class AttributeRune
 *
 * @created      23.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Effect;
use Buildwars\GWSkillData\Common\EffectCondition;
use Buildwars\GWSkillData\Common\Lang;
use RuntimeException;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/Rune
 * @see https://www.guildwiki.de/wiki/Rune
 */
final class AttributeRune extends ModSubtypeAbstract{

	public const int MINOR    = 0x5001;
	public const int MAJOR    = 0x5002;
	public const int SUPERIOR = 0x5003;

	public const array NAME = [
		self::MINOR    => [
			Lang::DE => '%1$s-Rune d. kleineren %2$s',
			Lang::EN => '%1$s Rune of Minor %2$s',
			Lang::ES => 'Runa %1$s (%2$s de grado menor)',
			Lang::FR => 'Rune %1$s (%2$s : bonus mineur)',
			Lang::IT => 'Runa %1$s %2$s di grado minore',
			Lang::XX => '%1$s Roone-a ooff Meenur %2$s',
		],
		self::MAJOR    => [
			Lang::DE => '%1$s-Rune d. hohen %2$s',
			Lang::EN => '%1$s Rune of Major %2$s',
			Lang::ES => 'Runa %1$s (%2$s de grado mayor)',
			Lang::FR => 'Rune %1$s (%2$s : bonus majeur)',
			Lang::IT => 'Runa %1$s %2$s di grado maggiore',
			Lang::XX => '%1$s Roone-a ooff Maejur %2$s',
		],
		self::SUPERIOR => [
			Lang::DE => '%1$s-Rune d. überlegenen %2$s',
			Lang::EN => '%1$s Rune of Superior %2$s',
			Lang::ES => 'Runa %1$s (%2$s de grado excepcional)',
			Lang::FR => 'Rune %1$s (%2$s : bonus supérieur)',
			Lang::IT => 'Runa %1$s %2$s di grado supremo',
			Lang::XX => '%1$s Roone-a ooff Soopereeur %2$s',
		],
	];

	private const array BONUS = [
		self::MINOR    => 1,
		self::MAJOR    => 2,
		self::SUPERIOR => 3,
	];

	private const array MALUS = [
		self::MAJOR    => -35,
		self::SUPERIOR => -75,
	];

	public function getName(Lang|string|null $lang = null):string{

		if($this->attribute === null || $this->attribute->is(Attribute::NONE) || $this->attribute->in(Attribute::PVE_TITLES)){
			throw new RuntimeException('profession attribute required');
		}

		$lang = $this->getLang($lang);

		// @todo: language fix
		if(!isset(self::NAME[$this->id][$this->lang->id])){
			return sprintf('[ATTRIBUTE_RUNE_%s_%s]', $this->id, $lang->id);
		}

		return sprintf(
			self::NAME[$this->id][$lang->id],
			$this->attribute->getProfession()->getAffixName(),
			$this->attribute->getName(),
		);
	}

	public function getAffix(Lang|string|null $lang = null):array{

		if($this->attribute === null || $this->attribute->is(Attribute::NONE) || $this->attribute->in(Attribute::PVE_TITLES)){
			throw new RuntimeException('profession attribute required');
		}

		$lang   = $this->getLang($lang);
		$effect = new Effect(Effect::ATTRIBUTE_BONUS, $lang);
		$stack  = new EffectCondition(EffectCondition::NONSTACKING, $lang);
		$bonus  = sprintf('%s %s', $effect->getAffix($this->attribute->getName(), self::BONUS[$this->id]), $stack->getAffix());

		if($this->id === self::MINOR){
			return [$bonus];
		}

		return [$bonus, new Effect(Effect::HEALTH, $lang)->getAffix(self::MALUS[$this->id])];
	}

}
