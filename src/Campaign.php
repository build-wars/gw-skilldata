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

	public const string CSS_CLASS = 'campaign';

	public const int CORE             = 0;
	public const int PROPHECIES       = 1;
	public const int FACTIONS         = 2;
	public const int NIGHTFALL        = 3;
	public const int EYE_OF_THE_NORTH = 4;

	public const array NAME = [
		self::CORE             => [
			Lang::DE => 'Basis',
			Lang::EN => 'Core',
			Lang::FR => 'Core', //Fondamentale
		],
		self::PROPHECIES       => [
			Lang::DE => 'Prophecies',
			Lang::EN => 'Prophecies',
			Lang::FR => 'Prophecies',
		],
		self::FACTIONS         => [
			Lang::DE => 'Factions',
			Lang::EN => 'Factions',
			Lang::FR => 'Factions',
		],
		self::NIGHTFALL        => [
			Lang::DE => 'Nightfall',
			Lang::EN => 'Nightfall',
			Lang::FR => 'Nightfall',
		],
		self::EYE_OF_THE_NORTH => [
			Lang::DE => 'Eye of the North',
			Lang::EN => 'Eye of the North',
			Lang::FR => 'Eye of the North',
		],
	];

	public const array CONTINENT_NAME = [
		self::CORE             => [
			Lang::DE => 'Die Nebel',
			Lang::EN => 'The Mists',
			Lang::FR => 'Les Brumes',
		],
		self::PROPHECIES       => [
			Lang::DE => 'Tyria',
			Lang::EN => 'Tyria',
			Lang::FR => 'Tyrie',
		],
		self::FACTIONS         => [
			Lang::DE => 'Cantha',
			Lang::EN => 'Cantha',
			Lang::FR => 'Cantha',
		],
		self::NIGHTFALL        => [
			Lang::DE => 'Elona',
			Lang::EN => 'Elona',
			Lang::FR => 'Elona',
		],
		self::EYE_OF_THE_NORTH => [
			Lang::DE => 'Tyria',
			Lang::EN => 'Tyria',
			Lang::FR => 'Tyrie',
		],
	];

	/**
	 * Returns the readable name of the continent for the given campaign ID
	 */
	public function getContinentName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return self::CONTINENT_NAME[$this->id][$lang->id];
	}

}
