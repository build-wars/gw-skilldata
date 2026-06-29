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

/*
 * First, parse the paw-ned² skill data.
 * This serves as a basis for all follow-up operations.
 *
 * Creates the JSON files:
 *
 *   - skilldata.json
 *   - skilldesc-de.json
 *   - skilldesc-en.json
 *
 */
require_once __DIR__.'/parse-pwnd.php';

/*
 * Fetch the skill descriptions from the wikis.
 * The concise descriptions are not included in paw-ned².
 *
 * Updates:
 *
 *   - skilldesc-de.json
 *   - skilldesc-en.json
 */
require_once __DIR__.'/fetch-skilldesc.php';

/*
 * Create the PHP classes:
 *
 *   - SkillData
 *   - SkillLangEnglish
 *   - SkillLangGerman
 */
require_once __DIR__.'/build-classes.php';

/*
 * Creates the Paw-ned² CSV databases and related .ini files.
 *
 * - de_buildwars_pve.csv
 * - de_buildwars_pvp.csv
 * - en_buildwars_pve.csv
 * - en_buildwars_pvp.csv
 */
require_once __DIR__.'/build-pwnd.php';
