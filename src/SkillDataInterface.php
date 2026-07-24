<?php
/**
 * Interface SkillDataInterface
 *
 * @created      01.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 *
 * @phan-file-suppress PhanDeprecatedClassConstant
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

interface SkillDataInterface{
	/** @deprecated 1.1.0 use SkillLang::DE instead  */
	final public const LANG_DE = 'de';
	/** @deprecated 1.1.0 use SkillLang::EN instead  */
	final public const LANG_EN = 'en';

	/** @deprecated 1.1.0 use SkillLang::NAMES instead  */
	final public const LANGUAGES = [
		self::LANG_DE => 'German',
		self::LANG_EN => 'English',
	];

	/**
	 * The array keys for the descriptions array
	 *
	 * @var string[]
	 */
	final public const KEYS_DESC = [Skill::DESC_NAME, Skill::DESC_DESCRIPTION, Skill::DESC_CONCISE];

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	final public const KEYS_DATA = [
		Skill::DATA_ID, Skill::DATA_CAMPAIGN, Skill::DATA_PROFESSION, Skill::DATA_ATTRIBUTE, Skill::DATA_IS_ELITE,
		Skill::DATA_IS_RP, Skill::DATA_IS_PVP, Skill::DATA_PVP_SPLIT, Skill::DATA_SPLIT_ID, Skill::DATA_TYPE,
		Skill::DATA_UPKEEP, Skill::DATA_ENERGY, Skill::DATA_ACTIVATION, Skill::DATA_RECHARGE, Skill::DATA_ADRENALINE,
		Skill::DATA_ADRENALINE_PRECISE, Skill::DATA_SACRIFICE, Skill::DATA_EXHAUSTION,
	];

	/**
	 * The descriptions array
	 *
	 * @var array<int, string>
	 */
	public const ID2DESC = [];

	/**
	 * The data array
	 *
	 * @var array<int, scalar[]>
	 */
	public const ID2DATA = [];

	/**
	 * The language abbreviation, key for the several `name` arrays
	 */
	public const LANG = '';

	/**
	 * Returns the data for the given skill ID, including descriptions for the current language
	 */
	public function get(int $id, bool $pvp = false):Skill;

	/**
	 * Returns an array with the skill data for each of the given skill IDs
	 *
	 * @param int[] $IDs
	 * @return \Buildwars\GWSkillData\Skill[]
	 */
	public function getAll(array $IDs, bool $pvp = false):array;

	/**
	 * Returns all skills for the given campaign ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByCampaign(int $campaign, bool $pvp = false):array;

	/**
	 * Returns all skills for the given profession ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByProfession(int $profession, bool $pvp = false):array;

	/**
	 * Returns all skills for the given attribute ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByAttribute(int $attribute, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByType(int $type, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID and its subtypes (if any)
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 */
	public function getByTypeWithSubtypes(int $type, bool $pvp = false):array;

	/**
	 * Returns all elite skills
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 */
	public function getElite(bool $pvp = false):array;

	/**
	 * Returns all roleplay skills
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 */
	public function getRoleplay():array;

	/**
	 * Returns a list of all skill IDs excluding PvP versions.
	 *
	 * If `$pvp` is set to `true`, a list of PvP version IDs is returned.
	 *
	 * @return int[]
	 */
	public function getIDs(bool $pvp = false):array;

}
