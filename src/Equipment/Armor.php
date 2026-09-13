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
		self::NONE   => [Lang::DE => 'Keine %s',    Lang::EN => 'No %s',     Lang::ES => 'Sin %s',    Lang::FR => 'Pas d\'%s',  Lang::IT => 'Nessuna %s', Lang::XX => 'Nu %s',      ],
		self::LIGHT  => [Lang::DE => 'Leichte %s',  Lang::EN => 'Light %s',  Lang::ES => '%s ligera', Lang::FR => '%s légère',  Lang::IT => '%s leggera', Lang::XX => 'Leeght %s',  ],
		self::MEDIUM => [Lang::DE => 'Mittlere %s', Lang::EN => 'Medium %s', Lang::ES => '%s media',  Lang::FR => '%s moyenne', Lang::IT => '%s media',   Lang::XX => 'Medeeoom %s',],
		self::HEAVY  => [Lang::DE => 'Schwere %s',  Lang::EN => 'Heavy %s',  Lang::ES => '%s pesada', Lang::FR => '%s lourde',  Lang::IT => '%s pesante', Lang::XX => 'Heaefy %s',  ],
	];

	private const array ENERGY_RECOVERY = [
		Lang::DE => 'Energierückgewinnung +1',
		Lang::EN => 'Energy recovery +1',
		Lang::ES => 'Recuperación de energía +1',
		Lang::FR => 'Récupération d\'énergie +1',
		Lang::IT => 'Recupero energia +1',
		Lang::XX => 'Inergy recufery +1',
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

	public function getName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return sprintf(self::NAME[$this->id][$lang->id], $lang->string(Lang::STR_ARMOR));
	}

	public function getType(Profession $profession):int{
		return self::BY_PROFESSION[$profession->id];
	}

	public function getArmorRating(Profession $profession):int{
		return self::AR[$this->getType($profession)];
	}

	public function getAffix(Profession $profession, Attribute $attribute, Lang|string|null $lang = null):array{

		if($profession->is(Profession::NONE) || !$this->is(self::BY_PROFESSION[$profession->id])){
			return [];
		}

		$lang = $this->getLang($lang);

		$affix = [];

		// energy on chest piece
		if($this->position->is(ItemPosition::CHEST) && !$profession->in([Profession::WARRIOR, Profession::DERVISH])){
			$affix[] = $lang->string(Lang::STR_ENERGY, '+'.self::ENERGY_BONUS);
		}
		// energy on hands (same as above, just written separate for clarity)
		if($this->position->is(ItemPosition::HANDS) && !$profession->in([Profession::WARRIOR, Profession::RANGER, Profession::ASSASSIN])){ // phpcs:ignore
			$affix[] = $lang->string(Lang::STR_ENERGY, '+'.self::ENERGY_BONUS);
		}

		// fixed armor rating
		$format  = $lang->is(Lang::FR) ? '%s : %s' : '%s: %s'; // extra baguette for french
		$affix[] = sprintf($format, $lang->string(Lang::STR_ARMOR), $this->getArmorRating($profession));

		// headpiece attribute bonus
		if($this->position->is(ItemPosition::HEAD) && !$attribute->is(Attribute::NONE)){
			$affix[] = sprintf('<blue>%s +1</blue> <gray>(%s)</gray>', $attribute->getName(), $lang->stackable(true));
		}
		// dervish health bonus on chest, after fixed AR
		if($this->position->is(ItemPosition::CHEST) && $profession->is(Profession::DERVISH)){
			$affix[] = $this->blue($lang->string(Lang::STR_HEALTH, '+25'));
		}
		// energy recovery on legs
		if($this->position->is(ItemPosition::LEGS) && !$profession->in([Profession::WARRIOR, Profession::PARAGON])){
			$affix[] = $this->blue(self::ENERGY_RECOVERY[$lang->id]);
		}
		// energy recovery on feet (same as above, just written separate for clarity)
		if($this->position->is(ItemPosition::FEET) && !$profession->in([Profession::WARRIOR, Profession::RANGER, Profession::PARAGON])){ // phpcs:ignore
			$affix[] = $this->blue(self::ENERGY_RECOVERY[$lang->id]);
		}
		// profession specific bonus for all pieces
		if($profession->inKeys(self::AR_BONUS)){
			[$value, $dmg_type] = self::AR_BONUS[$profession->id];

			$affix[] = sprintf(
				'<blue>%s +%s</blue> <gray>(%s %s)</gray>',
				$lang->string(Lang::STR_ARMOR),
				$value,
				$lang->string(Lang::STR_VERSUS),
				new DamageType($dmg_type, $lang)->getAffixName(),
			);
		}

		return $affix;
	}

}
