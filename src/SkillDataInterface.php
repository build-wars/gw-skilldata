<?php
/**
 * Interface SkillDataInterface
 *
 * @created      01.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

interface SkillDataInterface{

	final public const LANG_DE = 'de';
	final public const LANG_EN = 'en';

	final public const LANGUAGES = [
		self::LANG_DE => 'German',
		self::LANG_EN => 'English',
	];

	/**
	 * The array keys for the name translations of several fields
	 *
	 * @var string[]
	 */
	final public const KEYS_NAMES = ['campaign_name', 'profession_name', 'profession_abbr', 'attribute_name', 'type_name'];

	/**
	 * The array keys for the descriptions array
	 *
	 * @var string[]
	 */
	final public const KEYS_DESC = ['name', 'description', 'concise'];

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	final public const KEYS_DATA = [
		'id', 'campaign', 'profession', 'attribute', 'is_elite', 'is_rp', 'is_pvp', 'pvp_split', 'split_id', 'type',
		'upkeep', 'energy', 'activation', 'recharge', 'adrenaline', 'adrenaline_precise', 'sacrifice', 'overcast',
	];

	/**
	 * The descriptions array
	 */
	public const ID2DESC = [];

	/**
	 * The data array
	 */
	public const ID2DATA = [];

	/**
	 * The language abbreviation, key for the several `name` arrays
	 */
	public const LANG = '';

	/**
	 * Returns the data for the given skill ID, including descriptions for the current language
	 */
	public function get(int $id, bool $pvp = false):array;

	/**
	 * Returns an array with the skill data for each of the given skill IDs
	 *
	 * @param int[] $IDs
	 */
	public function getAll(array $IDs, bool $pvp = false):array;

	/**
	 * Returns all skills for the given campaign ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByCampaign(int $campaign, bool $pvp = false):array;

	/**
	 * Returns all skills for the given profession ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByProfession(int $profession, bool $pvp = false):array;

	/**
	 * Returns all skills for the given attribute ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByAttribute(int $attribute, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByType(int $type, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID and its subtypes (if any)
	 */
	public function getByTypeWithSubtypes(int $type, bool $pvp = false):array;

	/**
	 * Returns all elite skills
	 */
	public function getElite(bool $pvp = false):array;

	/**
	 * Returns all roleplay skills
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
