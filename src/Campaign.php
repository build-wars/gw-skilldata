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

use InvalidArgumentException;
use function array_key_exists;

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
		self::CORE             => ['de' => 'Basis',            'en' => 'Core',           ],
		self::PROPHECIES       => ['de' => 'Prophecies',       'en' => 'Prophecies',     ],
		self::FACTIONS         => ['de' => 'Factions',         'en' => 'Factions',       ],
		self::NIGHTFALL        => ['de' => 'Nightfall',        'en' => 'Nightfall',      ],
		self::EYE_OF_THE_NORTH => ['de' => 'Eye of the North', 'en' => 'Eye of the North'],
	];

	public const CONTINENT_NAME = [
		self::CORE             => ['de' => 'Die Nebel', 'en' => 'The Mists',],
		self::PROPHECIES       => ['de' => 'Tyria',     'en' => 'Tyria',    ],
		self::FACTIONS         => ['de' => 'Cantha',    'en' => 'Cantha',   ],
		self::NIGHTFALL        => ['de' => 'Elona',     'en' => 'Elona',    ],
		self::EYE_OF_THE_NORTH => ['de' => 'Tyria',     'en' => 'Tyria',    ],
	];

	public function getContinentName(string|null $lang = null):string{

		if($lang !== null && !array_key_exists($lang, SkillDataInterface::LANGUAGES)){
			throw new InvalidArgumentException('invalid language');
		}

		return self::CONTINENT_NAME[$this->id][($lang ?? $this->lang)];
	}

}
