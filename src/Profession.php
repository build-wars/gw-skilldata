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

use InvalidArgumentException;
use function array_key_exists;

/**
 * Encapsulates all profession related static data
 */
final class Profession{

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

	/** @var array<int, array{de: string, en: string}> */
	public const NAME = [
		self::NONE         => ['de' => 'keine',           'en' => 'none',        ],
		self::WARRIOR      => ['de' => 'Krieger',         'en' => 'Warrior',     ],
		self::RANGER       => ['de' => 'Waldläufer',      'en' => 'Ranger',      ],
		self::MONK         => ['de' => 'Mönch',           'en' => 'Monk',        ],
		self::NECROMANCER  => ['de' => 'Nekromant',       'en' => 'Necromancer', ],
		self::MESMER       => ['de' => 'Mesmer',          'en' => 'Mesmer',      ],
		self::ELEMENTALIST => ['de' => 'Elementarmagier', 'en' => 'Elementalist',],
		self::ASSASSIN     => ['de' => 'Assassine',       'en' => 'Assassin',    ],
		self::RITUALIST    => ['de' => 'Ritualist',       'en' => 'Ritualist',   ],
		self::PARAGON      => ['de' => 'Paragon',         'en' => 'Paragon',     ],
		self::DERVISH      => ['de' => 'Derwisch',        'en' => 'Dervish',     ],
	];

	/** @var array<int, array{de: string, en: string}> */
	public const NAME_ABBR = [
		self::NONE         => ['de' => 'X', 'en' => 'X', ],
		self::WARRIOR      => ['de' => 'K', 'en' => 'W', ],
		self::RANGER       => ['de' => 'W', 'en' => 'R', ],
		self::MONK         => ['de' => 'Mö','en' => 'Mo',],
		self::NECROMANCER  => ['de' => 'N', 'en' => 'N', ],
		self::MESMER       => ['de' => 'Me','en' => 'Me',],
		self::ELEMENTALIST => ['de' => 'E', 'en' => 'E', ],
		self::ASSASSIN     => ['de' => 'A', 'en' => 'A', ],
		self::RITUALIST    => ['de' => 'R', 'en' => 'Rt',],
		self::PARAGON      => ['de' => 'P', 'en' => 'P', ],
		self::DERVISH      => ['de' => 'D', 'en' => 'D', ],
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

	public function __construct(
		public readonly int       $id,
		protected readonly string $lang = SkillDataInterface::LANG_EN,
	){
		if(!array_key_exists($this->id, self::PRIMARY_ATTRIBUTE)){
			throw new InvalidArgumentException('invalid profession ID');
		}
	}

	public function getName():string{
		return self::NAME[$this->id][$this->lang];
	}

	public function getAbbr():string{
		return self::NAME_ABBR[$this->id][$this->lang];
	}

	public function getPrimaryAttribute():int{
		/** @phan-suppress-next-line PhanTypeArraySuspicious */
		return self::PRIMARY_ATTRIBUTE[$this->id][$this->lang];
	}

}
