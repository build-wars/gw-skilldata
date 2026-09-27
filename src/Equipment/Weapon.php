<?php
/**
 * Class Weapon
 *
 * @created      24.08.2026
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
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/Weapon
 * @see https://www.guildwiki.de/wiki/Waffe
 */
final class Weapon extends DataObjectAbstract{

	public const string CSS_CLASS = 'weapon';

	public const int AXE     = 0x4001;
	public const int BOW     = 0x4002;
	public const int DAGGERS = 0x4003;
	public const int HAMMER  = 0x4004;
	public const int SWORD   = 0x4005;
	public const int SCYTHE  = 0x4006;
	public const int SPEAR   = 0x4007;
	public const int STAFF   = 0x4008;
	public const int WAND    = 0x4009;
	public const int FOCUS   = 0x400a;
	public const int SHIELD  = 0x400b;
	// virtual types
	public const int PRIMARY = 0x4101;
	public const int OFFHAND = 0x4102;

	public const array NAME = [
		self::AXE     => [Lang::DE => 'Axt',     Lang::EN => 'Axe',     Lang::ES => 'Hacha',    Lang::FR => 'Hache',    Lang::IT => 'Ascia',     Lang::XX => 'Aexe-a',  ],
		self::BOW     => [Lang::DE => 'Bogen',   Lang::EN => 'Bow',     Lang::ES => 'Arco',     Lang::FR => 'Arc',      Lang::IT => 'Arco',      Lang::XX => 'Boo',     ],
		self::DAGGERS => [Lang::DE => 'Dolche',  Lang::EN => 'Daggers', Lang::ES => 'Dagas',    Lang::FR => 'Dagues',   Lang::IT => 'Pugnale',   Lang::XX => 'Daegger', ],
		self::HAMMER  => [Lang::DE => 'Hammer',  Lang::EN => 'Hammer',  Lang::ES => 'Martillo', Lang::FR => 'Marteau',  Lang::IT => 'Martello',  Lang::XX => 'Haemmer', ],
		self::SWORD   => [Lang::DE => 'Schwert', Lang::EN => 'Sword',   Lang::ES => 'Espada',   Lang::FR => 'Epée',     Lang::IT => 'Spada',     Lang::XX => 'Svurd',   ],
		self::SCYTHE  => [Lang::DE => 'Sense',   Lang::EN => 'Scythe',  Lang::ES => 'Guadaña',  Lang::FR => 'Faux',     Lang::IT => 'Falce',     Lang::XX => 'Scyzee',  ],
		self::SPEAR   => [Lang::DE => 'Speer',   Lang::EN => 'Spear',   Lang::ES => 'Lanza',    Lang::FR => 'Lance',    Lang::IT => 'Lancia',    Lang::XX => 'Speaer',  ],
		self::STAFF   => [Lang::DE => 'Stab',    Lang::EN => 'Staff',   Lang::ES => 'Báculo',   Lang::FR => 'Bâton',    Lang::IT => 'Bastone',   Lang::XX => 'Staeffff',],
		self::WAND    => [Lang::DE => 'Stecken', Lang::EN => 'Wand',    Lang::ES => 'Varita',   Lang::FR => 'Baguette', Lang::IT => 'Bacchetta', Lang::XX => 'Vund',    ],
		self::FOCUS   => [Lang::DE => 'Fokus',   Lang::EN => 'Focus',   Lang::ES => 'Foco',     Lang::FR => 'Focus',    Lang::IT => 'Focus',     Lang::XX => 'Fucoos',  ],
		self::SHIELD  => [Lang::DE => 'Schild',  Lang::EN => 'Shield',  Lang::ES => 'Escudo',   Lang::FR => 'Bouclier', Lang::IT => 'Scudo',     Lang::XX => 'Sheeeld', ],
	];

	public const array BASE_VALUE = [
		self::AXE     => '6-28',
		self::BOW     => '15-28',
		self::DAGGERS => '7-17',
		self::HAMMER  => '19-35',
		self::SWORD   => '15-22',
		self::SCYTHE  => '9-41',
		self::SPEAR   => '14-27',
		self::STAFF   => '11-22',
		self::WAND    => '11-22',
		self::FOCUS   => '12',
		self::SHIELD  => '16',
	];

	public const int ENERGY_STAFF = 10;
	public const int ENERGY_FOCUS = 12;
	public const int ARMOR_SHIELD = 16;

	public const array DEFAULT_DAMAGE_TYPE = [
		self::AXE     => [DamageType::SLASHING],
		self::BOW     => [DamageType::PIERCING],
		self::DAGGERS => [DamageType::PIERCING, DamageType::SLASHING],
		self::HAMMER  => [DamageType::BLUNT],
		self::SWORD   => [DamageType::SLASHING],
		self::SCYTHE  => [DamageType::SLASHING],
		self::SPEAR   => [DamageType::PIERCING],
		self::STAFF   => [DamageType::COLD, DamageType::EARTH, DamageType::FIRE, DamageType::LIGHTNING, DamageType::CHAOS, DamageType::DARK, DamageType::HOLY],
		self::WAND    => [DamageType::COLD, DamageType::EARTH, DamageType::FIRE, DamageType::LIGHTNING, DamageType::CHAOS, DamageType::DARK, DamageType::HOLY],
		self::FOCUS   => null,
		self::SHIELD  => null,
	];

	public const array MARTIAL = [self::AXE, self::BOW, self::DAGGERS, self::HAMMER, self::SCYTHE, self::SPEAR, self::SWORD];
	public const array CASTER  = [self::STAFF, self::WAND];

	public const array TYPE = [
		self::AXE     => self::PRIMARY,
		self::BOW     => self::PRIMARY,
		self::DAGGERS => self::PRIMARY,
		self::HAMMER  => self::PRIMARY,
		self::SWORD   => self::PRIMARY,
		self::SCYTHE  => self::PRIMARY,
		self::SPEAR   => self::PRIMARY,
		self::STAFF   => self::PRIMARY,
		self::WAND    => self::PRIMARY,
		self::FOCUS   => self::OFFHAND,
		self::SHIELD  => self::OFFHAND,
	];

	public function getAffix(Attribute $attribute, DamageType|null $dmgType = null, Lang|string|null $lang = null):array{
		$lang  = $this->getLang($lang);
		$dmg   = ($dmgType ?? self::DEFAULT_DAMAGE_TYPE[$this->id][0] ?? null);
		$req   = $attribute->getReq(9, $lang);
		$affix = [];

		$energy = new Effect(Effect::ENERGY, $lang);

		// staff effect: energy bonus
		if($this->is(self::STAFF)){
			$affix[] = $energy->getAffix('+'.self::ENERGY_STAFF);
		}
		// damage type & req
		if($dmg !== null){

			if(!$dmg instanceof DamageType){
				$dmg = new DamageType($dmg, $lang);
			}

			if(!$dmg->is(DamageType::NONE)){
				$format = '%s: %s %s'; // @todo: localized format
				// color for modified damage type
				/** @phan-suppress-next-line PhanTypeMismatchArgumentNullable */
				if(!$dmg->in(self::DEFAULT_DAMAGE_TYPE[$this->id])){
					$format = '<blue>%s: %s</blue> %s';
				}

				$affix[] = sprintf($format, $dmg->getName(), self::BASE_VALUE[$this->id], $req);
			}
		}
		// fixed 2h-staff effect, after energy & dmg
		if($this->is(self::STAFF)){
			$effect    = new Effect(Effect::HSR, $lang);
			$condition = new EffectCondition(EffectCondition::CHANCE, $lang);

			$affix[] = sprintf('%s %s', $effect->getName(), $condition->getAffix(20));
		}

		if($this->is(self::SHIELD)){
			$effect = new Effect(Effect::ARMOR, $lang);

			$affix[] = sprintf('%s %s', $effect->getAffix('+'.self::ARMOR_SHIELD), $req);
		}

		if($this->is(self::FOCUS)){
			$affix[] = sprintf('%s %s', $energy->getAffix('+'.self::ENERGY_FOCUS), $req);
		}

		return $affix;
	}

}
