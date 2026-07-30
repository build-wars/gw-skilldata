<?php
/**
 * Class Profession
 *
 * @created      27.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 *
 * @codeCoverageIgnore
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use function sprintf;
use function strtolower;

/**
 * Encapsulates all profession related static data
 */
final class Profession extends DataObjectAbstract{

	public const CSS_CLASS = 'profession';

	public const NONE         = 0;
	public const WARRIOR      = 1;
	public const RANGER       = 2;
	public const MONK         = 3;
	public const NECROMANCER  = 4;
	public const MESMER       = 5;
	public const ELEMENTALIST = 6;
	public const ASSASSIN     = 7;
	public const RITUALIST    = 8;
	public const PARAGON      = 9;
	public const DERVISH      = 10;

	public const NAME = [
		self::NONE         => [SkillLang::DE => 'keine',           SkillLang::EN => 'none',        ],
		self::WARRIOR      => [SkillLang::DE => 'Krieger',         SkillLang::EN => 'Warrior',     ],
		self::RANGER       => [SkillLang::DE => 'Waldläufer',      SkillLang::EN => 'Ranger',      ],
		self::MONK         => [SkillLang::DE => 'Mönch',           SkillLang::EN => 'Monk',        ],
		self::NECROMANCER  => [SkillLang::DE => 'Nekromant',       SkillLang::EN => 'Necromancer', ],
		self::MESMER       => [SkillLang::DE => 'Mesmer',          SkillLang::EN => 'Mesmer',      ],
		self::ELEMENTALIST => [SkillLang::DE => 'Elementarmagier', SkillLang::EN => 'Elementalist',],
		self::ASSASSIN     => [SkillLang::DE => 'Assassine',       SkillLang::EN => 'Assassin',    ],
		self::RITUALIST    => [SkillLang::DE => 'Ritualist',       SkillLang::EN => 'Ritualist',   ],
		self::PARAGON      => [SkillLang::DE => 'Paragon',         SkillLang::EN => 'Paragon',     ],
		self::DERVISH      => [SkillLang::DE => 'Derwisch',        SkillLang::EN => 'Dervish',     ],
	];

	/** @var array<int, array{de: string, en: string}> */
	public const NAME_ABBR = [
		self::NONE         => [SkillLang::DE => 'X',  SkillLang::EN => 'X', ],
		self::WARRIOR      => [SkillLang::DE => 'K',  SkillLang::EN => 'W', ],
		self::RANGER       => [SkillLang::DE => 'W',  SkillLang::EN => 'R', ],
		self::MONK         => [SkillLang::DE => 'Mö', SkillLang::EN => 'Mo',],
		self::NECROMANCER  => [SkillLang::DE => 'N',  SkillLang::EN => 'N', ],
		self::MESMER       => [SkillLang::DE => 'Me', SkillLang::EN => 'Me',],
		self::ELEMENTALIST => [SkillLang::DE => 'E',  SkillLang::EN => 'E', ],
		self::ASSASSIN     => [SkillLang::DE => 'A',  SkillLang::EN => 'A', ],
		self::RITUALIST    => [SkillLang::DE => 'R',  SkillLang::EN => 'Rt',],
		self::PARAGON      => [SkillLang::DE => 'P',  SkillLang::EN => 'P', ],
		self::DERVISH      => [SkillLang::DE => 'D',  SkillLang::EN => 'D', ],
	];

	/** @var array<int, int> */
	public const PRIMARY_ATTRIBUTE = [
		self::NONE         => Attribute::NONE,
		self::WARRIOR      => Attribute::STRENGTH,
		self::RANGER       => Attribute::EXPERTISE,
		self::MONK         => Attribute::DIVINE_FAVOR,
		self::NECROMANCER  => Attribute::SOUL_REAPING,
		self::MESMER       => Attribute::FAST_CASTING,
		self::ELEMENTALIST => Attribute::ENERGY_STORAGE,
		self::ASSASSIN     => Attribute::CRITICAL_STRIKES,
		self::RITUALIST    => Attribute::SPAWNING_POWER,
		self::PARAGON      => Attribute::LEADERSHIP,
		self::DERVISH      => Attribute::MYSTICISM,
	];

	public function getAbbr(SkillLang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return self::NAME_ABBR[$this->id][$lang->id];
	}

	public function getPrimaryAttribute(int $level = 0):Attribute{
		return new Attribute(self::PRIMARY_ATTRIBUTE[$this->id], $this->lang->id, $level);
	}

	public function getPrimaryAttributeID():int{
		return self::PRIMARY_ATTRIBUTE[$this->id];
	}

	public function getAttributes():array{
		return Attribute::getByProfession($this);
	}

	public function toHTML(SkillLang|string|null $lang = null, bool $short = false):string{
		$lang = $this->getLang($lang);
		$name = $this->getName($lang);

		if($short){
			$name = $this->getAbbr($lang);
		}

		return sprintf('<span class="%s %s">%s</span>', self::CSS_CLASS, strtolower($this->getName(SkillLang::EN)), $name);
	}

}
