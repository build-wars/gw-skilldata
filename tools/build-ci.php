<?php
/**
 * Combined build script for convenience
 *
 * In theory this could be run on CI, but the wiki fetcher opens doors for supply chain attacks.
 *
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

// init http
use Buildwars\GWSkillDataTools\Builder\Builder;
use Buildwars\GWSkillDataTools\Builder\PawnedBuilder;
use Psr\Log\LogLevel;
use const BUILDDIR;

require_once __DIR__.'/common.php';

const PAWNED_DATA_DIR = BUILDDIR.'/pawned-vendor';

$ptions = new BuilderOptions([
	'ca_info'           => __DIR__.'/cacert.pem',
	'timeout'           => 30,
	'logLevel'          => LogLevel::INFO,
	'builddir'          => BUILDDIR,
	'update_skilldata'  => true,
	'from_cache'        => true,
	'pawned_hash_check' => false,
	'pawned_hash_dir'   => BUILDDIR.'/gh-pages-old/pawned',
	'request_sleep'     => 750000,
]);

(new Builder($ptions))
#	->create()
#	->fetchSkilldesc()
	->build()
	->buildJSON()
;

(new PawnedBuilder($ptions))->build();
