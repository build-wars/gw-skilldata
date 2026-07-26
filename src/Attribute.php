<?php
/**
 * Class Attribute
 *
 * @created      27.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use Closure;
use function array_map;
use function floor;
use function intval;
use function max;
use function min;
use function range;
use function round;

/**
 * Encapsulates all skill attribute related static data
 */
final class Attribute extends DataObjectAbstract{

	public const FAST_CASTING        = 0;
	public const ILLUSION_MAGIC      = 1;
	public const DOMINATION_MAGIC    = 2;
	public const INSPIRATION_MAGIC   = 3;
	public const BLOOD_MAGIC         = 4;
	public const DEATH_MAGIC         = 5;
	public const SOUL_REAPING        = 6;
	public const CURSES              = 7;
	public const AIR_MAGIC           = 8;
	public const EARTH_MAGIC         = 9;
	public const FIRE_MAGIC          = 10;
	public const WATER_MAGIC         = 11;
	public const ENERGY_STORAGE      = 12;
	public const HEALING_PRAYERS     = 13;
	public const SMITING_PRAYERS     = 14;
	public const PROTECTION_PRAYERS  = 15;
	public const DIVINE_FAVOR        = 16;
	public const STRENGTH            = 17;
	public const AXE_MASTERY         = 18;
	public const HAMMER_MASTERY      = 19;
	public const SWORDMANSHIP        = 20;
	public const TACTICS             = 21;
	public const BEAST_MASTERY       = 22;
	public const EXPERTISE           = 23;
	public const WILDERNESS_SURVIVAL = 24;
	public const MARKMANSHIP         = 25;
	public const DAGGER_MASTERY      = 29;
	public const DEADLY_ARTS         = 30;
	public const SHADOW_ARTS         = 31;
	public const COMMUNING           = 32;
	public const RESTORATION_MAGIC   = 33;
	public const CHANNELING_MAGIC    = 34;
	public const CRITICAL_STRIKES    = 35;
	public const SPAWNING_POWER      = 36;
	public const SPEAR_MASTERY       = 37;
	public const COMMAND             = 38;
	public const MOTIVATION          = 39;
	public const LEADERSHIP          = 40;
	public const SCYTHE_MASTERY      = 41;
	public const WIND_PRAYERS        = 42;
	public const EARTH_PRAYERS       = 43;
	public const MYSTICISM           = 44;
	// not exactly sure what to do with the "no attribute" - technically we could move it to -1
	public const NONE                = 101;
	// PvE titles are technically attributes - WTB "official" internal IDs
	public const TITLE_SUNSPEAR      = 102;
	public const TITLE_LIGHTBRINGER  = 103;
	public const TITLE_LUXON         = 104;
	public const TITLE_KURZICK       = 105;
	public const TITLE_ASURA         = 106;
	public const TITLE_DELDRIMOR     = 107;
	public const TITLE_VANGUARD      = 108;
	public const TITLE_NORN          = 109;

	public const NAME = [
		self::FAST_CASTING        => [SkillLang::DE => 'Schnellwirkung',           SkillLang::EN => 'Fast Casting',                      ],
		self::ILLUSION_MAGIC      => [SkillLang::DE => 'Illusionsmagie',           SkillLang::EN => 'Illusion Magic',                    ],
		self::DOMINATION_MAGIC    => [SkillLang::DE => 'Beherrschungsmagie',       SkillLang::EN => 'Domination Magic',                  ],
		self::INSPIRATION_MAGIC   => [SkillLang::DE => 'Inspirationsmagie',        SkillLang::EN => 'Inspiration Magic',                 ],
		self::BLOOD_MAGIC         => [SkillLang::DE => 'Blutmagie',                SkillLang::EN => 'Blood Magic',                       ],
		self::DEATH_MAGIC         => [SkillLang::DE => 'Todesmagie',               SkillLang::EN => 'Death Magic',                       ],
		self::SOUL_REAPING        => [SkillLang::DE => 'Seelensammlung',           SkillLang::EN => 'Soul Reaping',                      ],
		self::CURSES              => [SkillLang::DE => 'Flüche',                   SkillLang::EN => 'Curses',                            ],
		self::AIR_MAGIC           => [SkillLang::DE => 'Luftmagie',                SkillLang::EN => 'Air Magic',                         ],
		self::EARTH_MAGIC         => [SkillLang::DE => 'Erdmagie',                 SkillLang::EN => 'Earth Magic',                       ],
		self::FIRE_MAGIC          => [SkillLang::DE => 'Feuermagie',               SkillLang::EN => 'Fire Magic',                        ],
		self::WATER_MAGIC         => [SkillLang::DE => 'Wassermagie',              SkillLang::EN => 'Water Magic',                       ],
		self::ENERGY_STORAGE      => [SkillLang::DE => 'Energiespeicherung',       SkillLang::EN => 'Energy Storage',                    ],
		self::HEALING_PRAYERS     => [SkillLang::DE => 'Heilgebete',               SkillLang::EN => 'Healing Prayers',                   ],
		self::SMITING_PRAYERS     => [SkillLang::DE => 'Peinigungsgebete',         SkillLang::EN => 'Smiting Prayers',                   ],
		self::PROTECTION_PRAYERS  => [SkillLang::DE => 'Schutzgebete',             SkillLang::EN => 'Protection Prayers',                ],
		self::DIVINE_FAVOR        => [SkillLang::DE => 'Gunst der Götter',         SkillLang::EN => 'Divine Favor',                      ],
		self::STRENGTH            => [SkillLang::DE => 'Stärke',                   SkillLang::EN => 'Strength',                          ],
		self::AXE_MASTERY         => [SkillLang::DE => 'Axtbeherrschung',          SkillLang::EN => 'Axe Mastery',                       ],
		self::HAMMER_MASTERY      => [SkillLang::DE => 'Hammerbeherrschung',       SkillLang::EN => 'Hammer Mastery',                    ],
		self::SWORDMANSHIP        => [SkillLang::DE => 'Schwertkunst',             SkillLang::EN => 'Swordsmanship',                     ],
		self::TACTICS             => [SkillLang::DE => 'Taktik',                   SkillLang::EN => 'Tactics',                           ],
		self::BEAST_MASTERY       => [SkillLang::DE => 'Tierbeherrschung',         SkillLang::EN => 'Beast Mastery',                     ],
		self::EXPERTISE           => [SkillLang::DE => 'Fachkenntnis',             SkillLang::EN => 'Expertise',                         ],
		self::WILDERNESS_SURVIVAL => [SkillLang::DE => 'Überleben in der Wildnis', SkillLang::EN => 'Wilderness Survival',               ],
		self::MARKMANSHIP         => [SkillLang::DE => 'Treffsicherheit',          SkillLang::EN => 'Marksmanship',                      ],
		self::DAGGER_MASTERY      => [SkillLang::DE => 'Dolchbeherrschung',        SkillLang::EN => 'Dagger Mastery',                    ],
		self::DEADLY_ARTS         => [SkillLang::DE => 'Tödliche Künste',          SkillLang::EN => 'Deadly Arts',                       ],
		self::SHADOW_ARTS         => [SkillLang::DE => 'Schattenkünste',           SkillLang::EN => 'Shadow Arts',                       ],
		self::COMMUNING           => [SkillLang::DE => 'Zwiesprache',              SkillLang::EN => 'Communing',                         ],
		self::RESTORATION_MAGIC   => [SkillLang::DE => 'Wiederherstellungsmagie',  SkillLang::EN => 'Restoration Magic',                 ],
		self::CHANNELING_MAGIC    => [SkillLang::DE => 'Kanalisierungsmagie',      SkillLang::EN => 'Channeling Magic',                  ],
		self::CRITICAL_STRIKES    => [SkillLang::DE => 'Kritische Stöße',          SkillLang::EN => 'Critical Strikes',                  ],
		self::SPAWNING_POWER      => [SkillLang::DE => 'Macht des Herbeirufens',   SkillLang::EN => 'Spawning Power',                    ],
		self::SPEAR_MASTERY       => [SkillLang::DE => 'Speerbeherrschung',        SkillLang::EN => 'Spear Mastery',                     ],
		self::COMMAND             => [SkillLang::DE => 'Befehlsgewalt',            SkillLang::EN => 'Command',                           ],
		self::MOTIVATION          => [SkillLang::DE => 'Motivation',               SkillLang::EN => 'Motivation',                        ],
		self::LEADERSHIP          => [SkillLang::DE => 'Führung',                  SkillLang::EN => 'Leadership',                        ],
		self::SCYTHE_MASTERY      => [SkillLang::DE => 'Sensenbeherrschung',       SkillLang::EN => 'Scythe Mastery',                    ],
		self::WIND_PRAYERS        => [SkillLang::DE => 'Windgebete',               SkillLang::EN => 'Wind Prayers',                      ],
		self::EARTH_PRAYERS       => [SkillLang::DE => 'Erdgebete',                SkillLang::EN => 'Earth Prayers',                     ],
		self::MYSTICISM           => [SkillLang::DE => 'Mystik',                   SkillLang::EN => 'Mysticism',                         ],
		self::NONE                => [SkillLang::DE => 'Kein Attribut',            SkillLang::EN => 'No Attribute',                      ],
		self::TITLE_SUNSPEAR      => [SkillLang::DE => 'Sonnenspeertitel',         SkillLang::EN => 'Sunspear Title Track',              ],
		self::TITLE_LIGHTBRINGER  => [SkillLang::DE => 'Lichtbringertitel',        SkillLang::EN => 'Lightbringer Title Track',          ],
		self::TITLE_LUXON         => [SkillLang::DE => 'Freund der Luxon',         SkillLang::EN => 'Friend of the Luxons Title Track',  ],
		self::TITLE_KURZICK       => [SkillLang::DE => 'Freund der Kurzick',       SkillLang::EN => 'Friend of the Kurzicks Title Track',],
		self::TITLE_ASURA         => [SkillLang::DE => 'Asuratitel',               SkillLang::EN => 'Asura Title Track',                 ],
		self::TITLE_DELDRIMOR     => [SkillLang::DE => 'Deldrimortitel',           SkillLang::EN => 'Deldrimor Title Track',             ],
		self::TITLE_VANGUARD      => [SkillLang::DE => 'Ebon-Vorhut-Titel',        SkillLang::EN => 'Ebon Vanguard Title Track',         ],
		self::TITLE_NORN          => [SkillLang::DE => 'Norntitel',                SkillLang::EN => 'Norn Title Track',                  ],
	];

	public const PROFESSION = [
		self::FAST_CASTING        => Profession::MESMER,
		self::ILLUSION_MAGIC      => Profession::MESMER,
		self::DOMINATION_MAGIC    => Profession::MESMER,
		self::INSPIRATION_MAGIC   => Profession::MESMER,
		self::BLOOD_MAGIC         => Profession::NECROMANCER,
		self::DEATH_MAGIC         => Profession::NECROMANCER,
		self::SOUL_REAPING        => Profession::NECROMANCER,
		self::CURSES              => Profession::NECROMANCER,
		self::AIR_MAGIC           => Profession::ELEMENTALIST,
		self::EARTH_MAGIC         => Profession::ELEMENTALIST,
		self::FIRE_MAGIC          => Profession::ELEMENTALIST,
		self::WATER_MAGIC         => Profession::ELEMENTALIST,
		self::ENERGY_STORAGE      => Profession::ELEMENTALIST,
		self::HEALING_PRAYERS     => Profession::MONK,
		self::SMITING_PRAYERS     => Profession::MONK,
		self::PROTECTION_PRAYERS  => Profession::MONK,
		self::DIVINE_FAVOR        => Profession::MONK,
		self::STRENGTH            => Profession::WARRIOR,
		self::AXE_MASTERY         => Profession::WARRIOR,
		self::HAMMER_MASTERY      => Profession::WARRIOR,
		self::SWORDMANSHIP        => Profession::WARRIOR,
		self::TACTICS             => Profession::WARRIOR,
		self::BEAST_MASTERY       => Profession::RANGER,
		self::EXPERTISE           => Profession::RANGER,
		self::WILDERNESS_SURVIVAL => Profession::RANGER,
		self::MARKMANSHIP         => Profession::RANGER,
		self::DAGGER_MASTERY      => Profession::ASSASSIN,
		self::DEADLY_ARTS         => Profession::ASSASSIN,
		self::SHADOW_ARTS         => Profession::ASSASSIN,
		self::COMMUNING           => Profession::RITUALIST,
		self::RESTORATION_MAGIC   => Profession::RITUALIST,
		self::CHANNELING_MAGIC    => Profession::RITUALIST,
		self::CRITICAL_STRIKES    => Profession::ASSASSIN,
		self::SPAWNING_POWER      => Profession::RITUALIST,
		self::SPEAR_MASTERY       => Profession::PARAGON,
		self::COMMAND             => Profession::PARAGON,
		self::MOTIVATION          => Profession::PARAGON,
		self::LEADERSHIP          => Profession::PARAGON,
		self::SCYTHE_MASTERY      => Profession::DERVISH,
		self::WIND_PRAYERS        => Profession::DERVISH,
		self::EARTH_PRAYERS       => Profession::DERVISH,
		self::MYSTICISM           => Profession::DERVISH,
		self::NONE                => Profession::NONE,
		self::TITLE_SUNSPEAR      => Profession::NONE,
		self::TITLE_LIGHTBRINGER  => Profession::NONE,
		self::TITLE_LUXON         => Profession::NONE,
		self::TITLE_KURZICK       => Profession::NONE,
		self::TITLE_ASURA         => Profession::NONE,
		self::TITLE_DELDRIMOR     => Profession::NONE,
		self::TITLE_VANGUARD      => Profession::NONE,
		self::TITLE_NORN          => Profession::NONE,
	];

	public const MAX_VALUE = [
		self::FAST_CASTING        => 21,
		self::ILLUSION_MAGIC      => 21,
		self::DOMINATION_MAGIC    => 21,
		self::INSPIRATION_MAGIC   => 21,
		self::BLOOD_MAGIC         => 21,
		self::DEATH_MAGIC         => 21,
		self::SOUL_REAPING        => 21,
		self::CURSES              => 21,
		self::AIR_MAGIC           => 21,
		self::EARTH_MAGIC         => 21,
		self::FIRE_MAGIC          => 21,
		self::WATER_MAGIC         => 21,
		self::ENERGY_STORAGE      => 21,
		self::HEALING_PRAYERS     => 21,
		self::SMITING_PRAYERS     => 21,
		self::PROTECTION_PRAYERS  => 21,
		self::DIVINE_FAVOR        => 21,
		self::STRENGTH            => 21,
		self::AXE_MASTERY         => 21,
		self::HAMMER_MASTERY      => 21,
		self::SWORDMANSHIP        => 21,
		self::TACTICS             => 21,
		self::BEAST_MASTERY       => 20,
		self::EXPERTISE           => 20,
		self::WILDERNESS_SURVIVAL => 20,
		self::MARKMANSHIP         => 21,
		self::DAGGER_MASTERY      => 21,
		self::DEADLY_ARTS         => 20,
		self::SHADOW_ARTS         => 20,
		self::COMMUNING           => 21,
		self::RESTORATION_MAGIC   => 21,
		self::CHANNELING_MAGIC    => 21,
		self::CRITICAL_STRIKES    => 20,
		self::SPAWNING_POWER      => 21,
		self::SPEAR_MASTERY       => 21,
		self::COMMAND             => 21,
		self::MOTIVATION          => 21,
		self::LEADERSHIP          => 20,
		self::SCYTHE_MASTERY      => 21,
		self::WIND_PRAYERS        => 20,
		self::EARTH_PRAYERS       => 20,
		self::MYSTICISM           => 21,
		self::NONE                => 0,
		self::TITLE_SUNSPEAR      => 10,
		self::TITLE_LIGHTBRINGER  => 8,
		self::TITLE_LUXON         => 12,
		self::TITLE_KURZICK       => 12,
		self::TITLE_ASURA         => 10,
		self::TITLE_DELDRIMOR     => 10,
		self::TITLE_VANGUARD      => 10,
		self::TITLE_NORN          => 10,
	];

	public readonly int $level;

	public function __construct(int $id, SkillLang|string $lang = SkillLang::EN, int $level = 0){
		parent::__construct($id, $lang);

		$this->level = $this->clamp($level);
	}

	public function getProfession():Profession{
		return new Profession(self::PROFESSION[$this->id], $this->lang->id);
	}

	public function getProfessionID():int{
		return self::PROFESSION[$this->id];
	}

	public function getMaxValue():int{
		return self::MAX_VALUE[$this->id];
	}

	public static function getByProfession(Profession|int $profession):array{

		if(!$profession instanceof Profession){
			$profession = new Profession($profession);
		}

		$attributes = [];

		foreach(self::PROFESSION as $attr => $prof){
			if($prof === $profession->id){
				$attributes[] = $attr;
			}
		}

		return $attributes;
	}

	public function isPrimary():bool{
		return $this->in(Profession::PRIMARY_ATTRIBUTE);
	}

	public function clamp(int|null $level = null):int{
		return max(0, min(($level ?? $this->level), self::MAX_VALUE[$this->id]));
	}

	/**
	 * Returns the progression function for the given title rank or attribute
	 */
	public function getProgressionFunction():Closure{
		return match(self::MAX_VALUE[$this->id]){
			// lightbringer
			8       => $this->progression8(...),
			// sunspear and eotn titles
			10      => $this->progression10(...),
			// luxon/kurzick
			12      => $this->progression12(...),
			// regular skill progression
			default => $this->progression15(...),
		};
	}

	protected function progression8(int $level, int $val0, int $val15):int{
		return (int)round(min(($level * 4), 15) * (($val15 - $val0) / 15) + $val0);
	}

	protected function progression10(int $level, int $val0, int $val15):int{
		return (int)round(min(($level * 3), 15) * (($val15 - $val0) / 15) + $val0);
	}

	protected function progression12(int $level, int $val0, int $val15):int{
		return (int)round(min(floor($level * 2.5), 15) * (($val15 - $val0) / 15) + $val0);
	}

	protected function progression15(int $level, int $val0, int $val15):int{
		return (int)round($level * (($val15 - $val0) / 15) + $val0);
	}

	/**
	 * Calculates the value for the given val0-val15 progression for the given attribute and level.
	 *
	 * Creates an optional table in the `progressions` array.
	 */
	public function getProgressionValue(int|string $val0, int|string $val15, int|null $level = null):int{

		if($level !== null){
			$level = $this->clamp($level);
		}

		$fn = $this->getProgressionFunction();

		// values might come in as strings from preg_match()
		return $fn(($level ?? $this->level), intval($val0), intval($val15));
	}

	/**
	 * Creates a progression table for the values 0 to attribute-max of the given val0 and val15
	 */
	public function getProgressionTable(int $val0, int $val15, int|null $max = null):array{
		// the internal maximum attribute level for player characters is 20-21, monsters are capped at 30
		// fast cast levels > 33 result in negative activation & recharge for mesmer - THE CHRONOMANCER IS REAL
		$max = min(($max ?? self::MAX_VALUE[$this->id]), 30);

		// we'll clamp the PvE attributes to their respectime max title ranks
		if($this->id > 100){
			$max = self::MAX_VALUE[$this->id];
		}

		$fn = $this->getProgressionFunction();

		return array_map(fn(int $i):int => $fn($i, $val0, $val15), range(0, $max));
	}

}
