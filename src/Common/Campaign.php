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

namespace Buildwars\GWSkillData\Common;

/**
 * Encapsulates all campaign related static data
 */
final class Campaign extends DataObjectAbstract{

	public const string CSS_CLASS = 'campaign';

	public const int CORE             = 0;
	public const int PROPHECIES       = 1;
	public const int FACTIONS         = 2;
	public const int NIGHTFALL        = 3;
	public const int EYE_OF_THE_NORTH = 4;

	public const array NAME = [
		self::CORE             => [Lang::DE => 'Basis',            Lang::EN => 'Core',             Lang::ES => '', Lang::FR => 'Core',             Lang::IT => '', Lang::XX => 'Cure-a',             ],
		self::PROPHECIES       => [Lang::DE => 'Prophecies',       Lang::EN => 'Prophecies',       Lang::ES => '', Lang::FR => 'Prophecies',       Lang::IT => '', Lang::XX => 'Prupheceees',        ],
		self::FACTIONS         => [Lang::DE => 'Factions',         Lang::EN => 'Factions',         Lang::ES => '', Lang::FR => 'Factions',         Lang::IT => '', Lang::XX => 'Faecshuns',          ],
		self::NIGHTFALL        => [Lang::DE => 'Nightfall',        Lang::EN => 'Nightfall',        Lang::ES => '', Lang::FR => 'Nightfall',        Lang::IT => '', Lang::XX => 'Neeghtffaell',       ],
		self::EYE_OF_THE_NORTH => [Lang::DE => 'Eye of the North', Lang::EN => 'Eye of the North', Lang::ES => '', Lang::FR => 'Eye of the North', Lang::IT => '', Lang::XX => 'Iye-a ooff zee Nurt',],
	];

	public const array CONTINENT_NAME = [
		self::CORE             => [Lang::DE => 'Die Nebel', Lang::EN => 'The Mists', Lang::ES => '', Lang::FR => 'Les Brumes', Lang::IT => '', Lang::XX => '',      ],
		self::PROPHECIES       => [Lang::DE => 'Tyria',     Lang::EN => 'Tyria',     Lang::ES => '', Lang::FR => 'Tyrie',      Lang::IT => '', Lang::XX => 'Tyreea',],
		self::FACTIONS         => [Lang::DE => 'Cantha',    Lang::EN => 'Cantha',    Lang::ES => '', Lang::FR => 'Cantha',     Lang::IT => '', Lang::XX => 'Cuntha',],
		self::NIGHTFALL        => [Lang::DE => 'Elona',     Lang::EN => 'Elona',     Lang::ES => '', Lang::FR => 'Elona',      Lang::IT => '', Lang::XX => 'Iluna', ],
		self::EYE_OF_THE_NORTH => [Lang::DE => 'Tyria',     Lang::EN => 'Tyria',     Lang::ES => '', Lang::FR => 'Tyrie',      Lang::IT => '', Lang::XX => 'Tyreea',],
	];

	/**
	 * Returns the readable name of the continent for the given campaign ID
	 */
	public function getContinentName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return self::CONTINENT_NAME[$this->id][$lang->id];
	}

}
