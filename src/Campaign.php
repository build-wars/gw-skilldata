<?php
/**
 * Class Campaign
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

/**
 * Encapsulates all campaign related static data
 */
final class Campaign extends DataObjectAbstract{

	public const CORE             = 0;
	public const PROPHECIES       = 1;
	public const FACTIONS         = 2;
	public const NIGHTFALL        = 3;
	public const EYE_OF_THE_NORTH = 4;

	public const NAME = [
		self::CORE             => [SkillLang::DE => 'Basis',            SkillLang::EN => 'Core',           ],
		self::PROPHECIES       => [SkillLang::DE => 'Prophecies',       SkillLang::EN => 'Prophecies',     ],
		self::FACTIONS         => [SkillLang::DE => 'Factions',         SkillLang::EN => 'Factions',       ],
		self::NIGHTFALL        => [SkillLang::DE => 'Nightfall',        SkillLang::EN => 'Nightfall',      ],
		self::EYE_OF_THE_NORTH => [SkillLang::DE => 'Eye of the North', SkillLang::EN => 'Eye of the North'],
	];

	public const CONTINENT_NAME = [
		self::CORE             => [SkillLang::DE => 'Die Nebel', SkillLang::EN => 'The Mists',],
		self::PROPHECIES       => [SkillLang::DE => 'Tyria',     SkillLang::EN => 'Tyria',    ],
		self::FACTIONS         => [SkillLang::DE => 'Cantha',    SkillLang::EN => 'Cantha',   ],
		self::NIGHTFALL        => [SkillLang::DE => 'Elona',     SkillLang::EN => 'Elona',    ],
		self::EYE_OF_THE_NORTH => [SkillLang::DE => 'Tyria',     SkillLang::EN => 'Tyria',    ],
	];

	public function getContinentName(string|null $lang = null):string{

		if($lang !== null){
			$lang = new SkillLang($lang);
		}

		return self::CONTINENT_NAME[$this->id][($lang->id ?? $this->lang->id)];
	}

}
