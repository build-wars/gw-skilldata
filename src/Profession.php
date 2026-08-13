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

use function implode;
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
		self::NONE         => [Lang::DE => 'keine',           Lang::EN => 'none',        ],
		self::WARRIOR      => [Lang::DE => 'Krieger',         Lang::EN => 'Warrior',     ],
		self::RANGER       => [Lang::DE => 'Waldläufer',      Lang::EN => 'Ranger',      ],
		self::MONK         => [Lang::DE => 'Mönch',           Lang::EN => 'Monk',        ],
		self::NECROMANCER  => [Lang::DE => 'Nekromant',       Lang::EN => 'Necromancer', ],
		self::MESMER       => [Lang::DE => 'Mesmer',          Lang::EN => 'Mesmer',      ],
		self::ELEMENTALIST => [Lang::DE => 'Elementarmagier', Lang::EN => 'Elementalist',],
		self::ASSASSIN     => [Lang::DE => 'Assassine',       Lang::EN => 'Assassin',    ],
		self::RITUALIST    => [Lang::DE => 'Ritualist',       Lang::EN => 'Ritualist',   ],
		self::PARAGON      => [Lang::DE => 'Paragon',         Lang::EN => 'Paragon',     ],
		self::DERVISH      => [Lang::DE => 'Derwisch',        Lang::EN => 'Dervish',     ],
	];

	/** @var array<int, array{de: string, en: string}> */
	public const NAME_ABBR = [
		self::NONE         => [Lang::DE => 'X',  Lang::EN => 'X', ],
		self::WARRIOR      => [Lang::DE => 'K',  Lang::EN => 'W', ],
		self::RANGER       => [Lang::DE => 'W',  Lang::EN => 'R', ],
		self::MONK         => [Lang::DE => 'Mö', Lang::EN => 'Mo',],
		self::NECROMANCER  => [Lang::DE => 'N',  Lang::EN => 'N', ],
		self::MESMER       => [Lang::DE => 'Me', Lang::EN => 'Me',],
		self::ELEMENTALIST => [Lang::DE => 'E',  Lang::EN => 'E', ],
		self::ASSASSIN     => [Lang::DE => 'A',  Lang::EN => 'A', ],
		self::RITUALIST    => [Lang::DE => 'R',  Lang::EN => 'Rt',],
		self::PARAGON      => [Lang::DE => 'P',  Lang::EN => 'P', ],
		self::DERVISH      => [Lang::DE => 'D',  Lang::EN => 'D', ],
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

	/**
	 * Returns the short name for the fiven profession ID
	 */
	public function getAbbr(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return self::NAME_ABBR[$this->id][$lang->id];
	}

	/**
	 * Returns the primary attribute of the current profession
	 */
	public function getPrimaryAttribute(int $level = 0):Attribute{
		return (new Attribute(self::PRIMARY_ATTRIBUTE[$this->id], $this->lang))->setLevel($level);
	}

	/**
	 * Returns the primary attribute ID of the current profession
	 */
	public function getPrimaryAttributeID():int{
		return self::PRIMARY_ATTRIBUTE[$this->id];
	}

	/**
	 * Returns all attributes for the current profession
	 *
	 * @return int[]
	 */
	public function getAttributes():array{
		return Attribute::getByProfession($this);
	}

	public function toHTML(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return sprintf(
			'<span class="%s" data-id="%d" data-lang="%s" data-abbr="%s">%s</span>',
			implode(' ', [self::CSS_CLASS, strtolower($this->getName(Lang::EN))]),
			$this->id,
			$lang->id,
			$this->getAbbr($lang),
			$this->getName($lang),
		);
	}

}
