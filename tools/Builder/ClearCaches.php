<?php
/**
 * Class ClearCaches
 *
 * @created      07.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use chillerlan\Utilities\Directory;
use function count;
use function sprintf;

class ClearCaches extends BuilderAbstract{

	public function build():static{

		foreach(self::CACHE_DIRS as $dir){
			$result = Directory::clear($dir, ['json', 'cjs', 'csv', 'ini', 'js', 'map', 'mjs']);

			$this->logger->info(sprintf('cache directory [%s]: %s files deleted', $dir, count($result)));
		}

		return $this;
	}

}
