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

// point this to your local paw-ned installation (the original data files are only read)
const PAWNED_DATA_DIR = 'C:\\Program Files (x86)\\paw·ned²\\skilldata';

$ptions = new BuilderOptions([
	'ca_info'           => __DIR__.'/cacert.pem',
	'timeout'           => 30,
	'logLevel'          => LogLevel::INFO,
	'builddir'          => BUILDDIR,
	'update_skilldata'  => true,
	'from_cache'        => true,
	'pawned_hash_check' => false,
	'pawned_hash_dir'   => BUILDDIR,
]);

$builder = new Builder($ptions);
$pawned  = new PawnedBuilder($ptions);

/*
 * First, parse the paw-ned² skill data.
 * This serves as a basis for all follow-up operations.
 *
 * This step is only necessary if you build the database from scratch,
 * use `$builder->create()` for subsequent updates instead.
 *
 * Creates the JSON files:
 *
 *   - skilldata.json
 *   - skilldesc-de.json
 *   - skilldesc-en.json
 */
#$pawned->create();
$builder->create();

/*
 * Fetch the skill descriptions from the wikis.
 * The concise descriptions are not included in paw-ned².
 *
 * Updates:
 *
 *   - skilldata.json
 *   - skilldesc-de.json
 *   - skilldesc-en.json
 */
$builder->fetchSkilldesc();

/*
 * Create the PHP classes:
 *
 *   - SkillData
 *   - SkillLangEnglish
 *   - SkillLangGerman
 */
$builder->build();

/*
 * Compiles the combined and per-skill JSON files
 *
 *   - skilldata-combined.json
 *   - skills/[SKILL_ID].json
 */
$builder->buildJSON();

/*
 * Creates the Paw-ned² CSV databases and related .ini files.
 *
 * - de_buildwars_pve.csv
 * - de_buildwars_pvp.csv
 * - en_buildwars_pve.csv
 * - en_buildwars_pvp.csv
 */
$pawned->build();
