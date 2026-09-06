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

	protected string $logLevel                = LogLevel::INFO;
	protected bool   $from_cache              = false;
	protected bool   $use_http_compression    = false;
	protected bool   $use_known_discrepancies = false;
	protected int    $request_sleep           = 500000;

}
