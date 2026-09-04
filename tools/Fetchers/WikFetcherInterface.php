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

use const BUILDDIR;

interface WikFetcherInterface{

	public const string CACHEDIR        = '';
	public const string MEDIAWIKI_API   = '';
	public const string WIKI_BULK_CACHE = BUILDDIR.'/wiki-bulk';

	public function fetch(string $skillName, int $id, bool $cached = true):array|null;
	public function prepareSkillName(string $skillName, int $id):string;
	public function getRequestParams(string $skillName):array;
	public function getCacheFilePath(int $id):string;

}
