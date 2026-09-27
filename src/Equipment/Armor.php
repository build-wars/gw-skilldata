<?php
/**
 * Class Armor
 *
 * @created      10.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\DamageType;
use Buildwars\GWSkillData\Common\DataObjectAbstract;
use Buildwars\GWSkillData\Common\Effect;
use Buildwars\GWSkillData\Common\EffectCondition;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\Profession;
use function array_key_exists;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/Armor
 * @see https://www.guildwiki.de/wiki/R%C3%BCstung
 */
final class Armor extends DataObjectAbstract{

	public const int NONE   = 0x7000;
	public const int LIGHT  = 0x7001;
	public const int MEDIUM = 0x7002;
	public const int HEAVY  = 0x7003;

	public const int ENERGY_BONUS = 5;
	public const int ENERGY_PIPS  = 1;

	public const int AR_NONE   = 0;
	public const int AR_LIGHT  = 60;
	public const int AR_MEDIUM = 70;
	public const int AR_HEAVY  = 80;

	public const array NAME = [
		self::NONE   => [Lang::DE => 'Keine Rüstung',    Lang::EN => 'No Armor',     Lang::ES => 'Sin Armadura',    Lang::FR => 'Pas d\'Armure',  Lang::IT => 'Nessuna Armatura', Lang::XX => 'Nu Aermur',      ],
		self::LIGHT  => [Lang::DE => 'Leichte Rüstung',  Lang::EN => 'Light Armor',  Lang::ES => 'Armadura ligera', Lang::FR => 'Armure légère',  Lang::IT => 'Armatura leggera', Lang::XX => 'Leeght Aermur',  ],
		self::MEDIUM => [Lang::DE => 'Mittlere Rüstung', Lang::EN => 'Medium Armor', Lang::ES => 'Armadura media',  Lang::FR => 'Armure moyenne', Lang::IT => 'Armatura media',   Lang::XX => 'Medeeoom Aermur',],
		self::HEAVY  => [Lang::DE => 'Schwere Rüstung',  Lang::EN => 'Heavy Armor',  Lang::ES => 'Armadura pesada', Lang::FR => 'Armure lourde',  Lang::IT => 'Armatura pesante', Lang::XX => 'Heaefy Aermur',  ],
	];

	private const array BY_PROFESSION = [
		Profession::NONE         => self::NONE,
		Profession::WARRIOR      => self::HEAVY,
		Profession::RANGER       => self::MEDIUM,
		Profession::MONK         => self::LIGHT,
		Profession::NECROMANCER  => self::LIGHT,
		Profession::MESMER       => self::LIGHT,
		Profession::ELEMENTALIST => self::LIGHT,
		Profession::ASSASSIN     => self::MEDIUM,
		Profession::RITUALIST    => self::LIGHT,
		Profession::PARAGON      => self::HEAVY,
		Profession::DERVISH      => self::MEDIUM,
	];

	private const array AR = [
		self::NONE   => self::AR_NONE,
		self::LIGHT  => self::AR_LIGHT,
		self::MEDIUM => self::AR_MEDIUM,
		self::HEAVY  => self::AR_HEAVY,
	];

	private const array AR_BONUS = [
		Profession::WARRIOR => [20, DamageType::PHYSICAL],
		Profession::RANGER  => [30, DamageType::ELEMENTAL],
	];

	private(set) ItemPosition $position;

	/**
	 * @param int $id - may be an armor or profession ID.
	 */
	public function __construct(int $id, ItemPosition|int $position, string|Lang $lang = Lang::EN){

		if(array_key_exists($id, self::BY_PROFESSION)){
			$id = self::BY_PROFESSION[$id];
		}

		parent::__construct($id, $lang);

		if(!$position instanceof ItemPosition){
			$position = new ItemPosition($position, $this->lang);
		}

		$this->position = $position;
	}

	public static function getType(Profession $profession):int{
		return self::BY_PROFESSION[$profession->id];
	}

	public static function getArmorRating(Profession $profession):int{
		return self::AR[self::getType($profession)];
	}

	public function getAffix(Profession $profession, Attribute $attribute, Lang|string|null $lang = null):array{

		if($profession->is(Profession::NONE) || !$this->is(self::BY_PROFESSION[$profession->id])){
			return [];
		}

		$lang = $this->getLang($lang);

		$armor    = new Effect(Effect::ARMOR, $lang);
		$energy   = new Effect(Effect::ENERGY, $lang)->getAffix('+'.self::ENERGY_BONUS);
		$recovery = new Effect(Effect::ENERGY_RECOVERY, $lang)->getAffix('+'.self::ENERGY_PIPS);

		$affix = [];

		// energy on chest piece
		if($this->position->is(ItemPosition::CHEST) && !$profession->in([Profession::WARRIOR, Profession::DERVISH])){
			$affix[] = $energy;
		}
		// energy on hands (same as above, just written separate for clarity)
		if($this->position->is(ItemPosition::HANDS) && !$profession->in([Profession::WARRIOR, Profession::RANGER, Profession::ASSASSIN])){ // phpcs:ignore
			$affix[] = $energy;
		}

		// fixed armor rating
		$affix[] = $armor->getAffix($this::getArmorRating($profession));

		// headpiece attribute bonus
		if($this->position->is(ItemPosition::HEAD) && !$attribute->is(Attribute::NONE)){
			$stacking = new EffectCondition(EffectCondition::STACKING, $lang)->getAffix();

			$affix[] = sprintf('<blue>%s +1</blue> %s', $attribute->getName(), $stacking);
		}
		// dervish health bonus on chest, after fixed AR
		if($this->position->is(ItemPosition::CHEST) && $profession->is(Profession::DERVISH)){
			$affix[] = $this->blue(new Effect(Effect::HEALTH)->getAffix('+25'));
		}
		// energy recovery on legs
		if($this->position->is(ItemPosition::LEGS) && !$profession->in([Profession::WARRIOR, Profession::PARAGON])){
			$affix[] = $this->blue($recovery);
		}
		// energy recovery on feet (same as above, just written separate for clarity)
		if($this->position->is(ItemPosition::FEET) && !$profession->in([Profession::WARRIOR, Profession::RANGER, Profession::PARAGON])){ // phpcs:ignore
			$affix[] = $this->blue($recovery);
		}
		// profession specific bonus for all pieces
		if($profession->inKeys(self::AR_BONUS)){
			[$value, $dmg_type] = self::AR_BONUS[$profession->id];
			$dmg = new DamageType($dmg_type, $lang);

			$affix[] = sprintf(
				'<blue>%s</blue> %s',
				$armor->getAffix('+'.$value),
				new EffectCondition(EffectCondition::VS_DAMAGE_TYPE, $lang)->getAffix($dmg->getName()),
			);
		}

		return $affix;
	}

}
