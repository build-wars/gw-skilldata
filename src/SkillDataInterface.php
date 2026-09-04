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

	public const array SKILLS_ASURA        = [];
	public const array SKILLS_DELDRIMOR    = [];
	public const array SKILLS_VANGUARD     = [];
	public const array SKILLS_NORN         = [];
	/**
	 * The language abbreviation, key for the several `name` arrays
	 *
	 * @see \Buildwars\GWSkillData\Lang::IDS
	 */
	public const string LANG = '';

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
	public function getByCampaign(Campaign|int $campaign, bool $pvp = false):array;

	/**
	 * Returns all skills for the given profession ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByProfession(Profession|int $profession, bool $pvp = false):array;

	/**
	 * Returns all skills for the given attribute ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByAttribute(Attribute|int $attribute, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 * @throws \InvalidArgumentException
	 */
	public function getByType(Type|int $type, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID and its subtypes (if any)
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 */
	public function getByTypeWithSubtypes(Type|int $type, bool $pvp = false):array;

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
	 * If `$pvp` is set to `true` or `false`, a list of PvP or PvE only respecively IDs is returned,
	 * if it is set to `null` (default), the full list of IDs is returned.
	 *
	 * @return int[]
	 */
	public function getIDs(bool|null $pvp = null):array;

}
