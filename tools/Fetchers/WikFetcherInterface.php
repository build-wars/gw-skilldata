<?php
/**
 * Interface WikFetcherInterface
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Fetchers;

interface WikFetcherInterface{

	public function fetch(string $skillName, int $id, bool $cached = true):array|null;

}
