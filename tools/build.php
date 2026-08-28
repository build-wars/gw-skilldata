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

#use Buildwars\GWSkillData\Attribute;
#use Buildwars\GWSkillData\Campaign;
#use Buildwars\GWSkillData\Lang;
#use Buildwars\GWSkillData\Profession;
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
	'diff_descriptions' => false,
	'pawned_hash_check' => false,
	'pawned_hash_dir'   => BUILDDIR,
	'request_sleep'     => 100000,
]);

$builder = new Builder($ptions);
$pawned  = new PawnedBuilder($ptions);

/*
 * Add new Skills
 */
/*
$builder->addSkill(3446, Campaign::CORE, Profession::ELEMENTALIST, Attribute::ENERGY_STORAGE, false, true)
	->addSkillLang(3446, Lang::DE, 'Soul Ignition')
	->addSkillLang(3446, Lang::EN, 'Soul Ignition')
	->addSkillLang(3446, Lang::FR, 'Soul Ignition');
// Dash was moved to shadow arts
$builder->addSkill(1043, Campaign::FACTIONS, Profession::ASSASSIN, Attribute::SHADOW_ARTS, false, false)
	->addSkillLang(1043, Lang::DE, 'Preschen')
	->addSkillLang(1043, Lang::EN, 'Dash')
	->addSkillLang(1043, Lang::FR, 'Ruée');
$builder->addSkill(3453, Campaign::FACTIONS, Profession::ASSASSIN, Attribute::SHADOW_ARTS, false, false)
	->addSkillLang(3453, Lang::DE, 'Preschen (PvP)')
	->addSkillLang(3453, Lang::EN, 'Dash (PvP)')
	->addSkillLang(3453, Lang::FR, 'Ruée (PvP)');
// Frenzy was moved to strength
$builder->addSkill(346, Campaign::CORE, Profession::WARRIOR, Attribute::STRENGTH, false, false)
	->addSkillLang(346, Lang::DE, 'Raserei')
	->addSkillLang(346, Lang::EN, 'Frenzy')
	->addSkillLang(346, Lang::FR, 'Frénésie');
$builder->addSkill(3443, Campaign::CORE, Profession::WARRIOR, Attribute::STRENGTH, false, false)
	->addSkillLang(3443, Lang::DE, 'Raserei (PvP)')
	->addSkillLang(3443, Lang::EN, 'Frenzy (PvP)')
	->addSkillLang(3443, Lang::FR, 'Frénésie (PvP)');
*/

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
$builder->fetchSkilldescToCache();
$builder->fetchSkilldesc();

/*
 * Compiles the combined and per-skill JSON files
 *
 *   - skilldata-combined.json
 *   - skills/[SKILL_ID].json
 */
$builder->buildJSON();

/*
 * Create the PHP classes:
 *
 *   - SkillData
 *   - SkillLangEnglish
 *   - SkillLangGerman
 */
$builder->build();

/*
 * Creates the Paw-ned² CSV databases and related .ini files.
 *
 * - de_buildwars_pve.csv
 * - de_buildwars_pvp.csv
 * - en_buildwars_pve.csv
 * - en_buildwars_pvp.csv
 */
$pawned->build();
