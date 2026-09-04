<?php
/**
 * Interface DataObjectInterface
 *
 * @created      01.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

/**
 * @property int $id
 * @property \Buildwars\GWSkillData\Lang $lang
 */
interface DataObjectInterface{

	public const string CSS_CLASS = '';
	/** @var array<int, array{de: string, en: string, fr: string}> */
	public const array  NAME      = [];

	/**
	 * Returns the readable name of the given ID
	 */
	public function getName(Lang|string|null $lang = null):string;

	/**
	 * Checks whether the object ID is equal to the given ID
	 */
	public function is(int $id):bool;

	/**
	 * Checks whether the object ID is in the given array of IDs
	 *
	 * @param int[] $ids
	 */
	public function in(array $ids):bool;

	public function toHTML(Lang|string|null $lang = null):string;

}
