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

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Campaign;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\Profession;
use Closure;

interface SkillDataInterface{

	/*
	 * we're keeping the IDs for the PvE skills of the several factions here as they might come in handy
	 */

	public const array SKILLS_KURZICK      = [2091, 2092, 2093, 2094, 2095, 2096, 2097, 2098, 2099, 2100];
	public const array SKILLS_LUXON        = [1948, 1949, 1950, 1951, 1952, 1953, 1954, 1955, 1957, 2051];
	public const array SKILLS_SUNSPEAR     = [2101, 2102, 2103, 2104, 2105, 2107, 2108, 2109, 2110, 2112];
	public const array SKILLS_LIGHTBRINGER = [1814, 1815];
	public const array SKILLS_ASURA        = [2224, 2225, 2226, 2227, 2411, 2412, 2413, 2414, 2415, 2416, 2417, 2418];
	public const array SKILLS_DELDRIMOR    = [2211, 2212, 2213, 2214, 2215, 2216, 2217, 2218, 2219, 2220, 2221, 2222, 2223, 2423];
	public const array SKILLS_VANGUARD     = [2116, 2228, 2229, 2230, 2231, 2232, 2233, 2234, 2235, 2420, 2421, 2422];
	public const array SKILLS_NORN         = [2353, 2354, 2355, 2356, 2357, 2358, 2359, 2360, 2361, 2374, 2379, 2384];

	/**
	 * The language abbreviation, key for the several `name` arrays
	 *
	 * @see \Buildwars\GWSkillData\Common\Lang::IDS
	 */
	public const string LANG = '';

	/**
	 * Iterates over the entire skill data array and executes the given function for each element and returns the result.
	 *
	 *   - the result array is indexed by skill ID
	 *   - the callable is called with 2 parameters: (Skill) a skill instance and (int) skill id
	 *   - the row data array given into the callable has named keys, see `Skill::KEYS_DATA`
	 *
	 *   $callable = function(Skill $skill, int $id):mixed{}
	 *
	 * @see \Buildwars\GWSkillData\Skill::KEYS_DATA
	 */
	public function map(Closure $callable):array;

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
	public function getByType(SkillType|int $type, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID and its subtypes (if any)
	 *
	 * @return \Buildwars\GWSkillData\Skill[]
	 */
	public function getByTypeWithSubtypes(SkillType|int $type, bool $pvp = false):array;

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

	/**
	 * Returns the current language
	 */
	public function getLang():Lang;

}
