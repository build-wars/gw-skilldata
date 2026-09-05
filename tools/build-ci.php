<?php
/**
 * Combined build script for convenience
 *
 * In theory this could be run on CI, but the wiki fetcher opens doors for supply chain attacks.
 * Update: the official Guild Wars Wiki (GWW) appears to block requests from GitHub Actions.
 *
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use Buildwars\GWSkillDataTools\Builder\BuildCombinedJSON;
use Buildwars\GWSkillDataTools\Builder\BuildPawned;
use Buildwars\GWSkillDataTools\Builder\BuildPublicIndex;
use Psr\Log\LogLevel;

require_once __DIR__.'/common.php';

$ptions = new BuilderOptions([
	'ca_info'              => __DIR__.'/cacert.pem',
	'timeout'              => 30,
	'logLevel'             => LogLevel::INFO,
	'from_cache'           => true,
	'use_http_compression' => true,
	'request_sleep'        => 100000,
]);

$builders = [
#	BuildKnownSkills::class,
#	BuildDataFromToolbox::class,
#	BuildLangFromToolbox::class,
#	BuildFromWiki::class,
	BuildCombinedJSON::class,
	BuildPawned::class,
	BuildPublicIndex::class,
];

foreach($builders as $builder){
	new $builder($ptions)->build();
}
