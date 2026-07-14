<?php
/**
 * Class BuilderOptions
 *
 * @created      01.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use chillerlan\HTTP\HTTPOptions;
use Psr\Log\LogLevel;

class BuilderOptions extends HTTPOptions{

	protected string $logLevel          = LogLevel::INFO;
	protected string $builddir          = __DIR__;
	protected bool   $update_skilldata  = false;
	protected bool   $from_cache        = false;
	protected bool   $diff_descriptions = false;
	protected int    $diff_threshold    = 20;
	protected bool   $pawned_hash_check = false;
	protected string $pawned_hash_dir   = __DIR__;
	protected int    $request_sleep     = 500000;

}
