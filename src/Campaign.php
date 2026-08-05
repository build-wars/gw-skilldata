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

	public const CSS_CLASS = 'campaign';

	public const CORE             = 0;
	public const PROPHECIES       = 1;
	public const FACTIONS         = 2;
	public const NIGHTFALL        = 3;
	public const EYE_OF_THE_NORTH = 4;

	public const NAME = [
		self::CORE             => [Lang::DE => 'Basis',            Lang::EN => 'Core',           ],
		self::PROPHECIES       => [Lang::DE => 'Prophecies',       Lang::EN => 'Prophecies',     ],
		self::FACTIONS         => [Lang::DE => 'Factions',         Lang::EN => 'Factions',       ],
		self::NIGHTFALL        => [Lang::DE => 'Nightfall',        Lang::EN => 'Nightfall',      ],
		self::EYE_OF_THE_NORTH => [Lang::DE => 'Eye of the North', Lang::EN => 'Eye of the North'],
	];

	public const CONTINENT_NAME = [
		self::CORE             => [Lang::DE => 'Die Nebel', Lang::EN => 'The Mists',],
		self::PROPHECIES       => [Lang::DE => 'Tyria',     Lang::EN => 'Tyria',    ],
		self::FACTIONS         => [Lang::DE => 'Cantha',    Lang::EN => 'Cantha',   ],
		self::NIGHTFALL        => [Lang::DE => 'Elona',     Lang::EN => 'Elona',    ],
		self::EYE_OF_THE_NORTH => [Lang::DE => 'Tyria',     Lang::EN => 'Tyria',    ],
	];

	/**
	 * Returns the readable name of the continent for the given campaign ID
	 */
	public function getContinentName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return self::CONTINENT_NAME[$this->id][$lang->id];
	}

}
