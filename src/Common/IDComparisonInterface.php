<?php
/**
 * Interface IDComparisonInterface
 *
 * @created      13.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

interface IDComparisonInterface{

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

	/**
	 * Checks whether the object ID is in the keys of the given array
	 *
	 * @param array<int, mixed> $ids
	 */
	public function inKeys(array $ids):bool;

}
