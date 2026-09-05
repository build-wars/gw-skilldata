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
use Buildwars\GWSkillDataTools\Builder\BuildDataFromToolbox;
use Buildwars\GWSkillDataTools\Builder\BuildFromWiki;
use Buildwars\GWSkillDataTools\Builder\BuildKnownSkills;
use Buildwars\GWSkillDataTools\Builder\BuildLangFromToolbox;
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
	// creates a "known skills" file with IDs and skill names for the en, de and fr wikis
	BuildKnownSkills::class,

	// these steps build and overwrite the existing database

	// fetches the data dumped from the game client and saves it as JOSN and PHP classes
	BuildDataFromToolbox::class,
	// fetches the translations  dumped from the game client and saves it as JOSN and PHP classes
	BuildLangFromToolbox::class,
	// fetches the skill descriptions from the wikis.
	BuildFromWiki::class,

	// these steps need to follow *after* the new database was built

	// creates the combined andper-skill JSON files
	BuildCombinedJSON::class,
	// Creates the Paw-ned² CSV databases and related .ini files.
	BuildPawned::class,
	// updates the index.html for GitHub pages
	BuildPublicIndex::class,
];

foreach($builders as $builder){
	new $builder($ptions)->build();
}
