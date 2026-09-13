<?php
/**
 * IDComparisonTrait.php
 *
 * @created      13.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use function array_key_exists;
use function in_array;

trait IDComparisonTrait{

	protected(set) int $id;

	public function is(int $id):bool{
		return $this->id === $id;
	}

	public function in(array $ids):bool{ // phpcs:ignore
		return in_array($this->id, $ids, true);
	}

	public function inKeys(array $ids):bool{ // phpcs:ignore
		return array_key_exists($this->id, $ids);
	}

}
