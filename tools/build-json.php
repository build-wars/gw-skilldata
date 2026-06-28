<?php
/**
 * Compiles the combined and per-skill json files
 *
 * @created      03.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 *
 * @phan-file-suppress PhanTypeMismatchDimFetch
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\Skilltype;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use function sprintf;
use function str_replace;

/**
 * @phan-file-suppress PhanUndeclaredGlobalVariable ??
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

const combinedJSON = __DIR__.'/../data/json-full/skilldata-combined.json';
const skillJSONDir = __DIR__.'/../data/json-skills/';

Directory::create(skillJSONDir);

$jsonData = File::loadJSON(DATA_FILE, true);
$jsonLang = [];

foreach(LANG_FILES as [$abbr, $file]){
	$jsonLang[$abbr] = File::loadJSON($file, true);
}

foreach($jsonData['skilldata'] as $skillID => &$skillData){
	foreach(['de', 'en'] as $abbr){
		$lang = $jsonLang[$abbr]['skilldesc'][$skillID];
		$prof = new Profession($skillData['profession'], $abbr);

		$skillData['lang'][$abbr] = [
			'name'            => $lang['name'],
			'description'     => $lang['description'],
			'concise'         => $lang['concise'],
			'campaign'        => (new Campaign($skillData['campaign'], $abbr))->getName(),
			'profession'      => $prof->getName(),
			'profession_abbr' => $prof->getAbbr(),
			'attribute'       => (new Attribute($skillData['attribute'], 0, $abbr))->getName(),
			'type'            => (new Skilltype($skillData['type'], $abbr))->getName(),
		];

	}

	$skill = [
		'$schema' => 'https://build-wars.github.io/gw-skilldata/schemas/skill.schema.json',
		'skill'   => $skillData,
	];

	$logger->info(sprintf('JSON for [%-4s] %s ', $skillID, $skillData['lang']['en']['name']));

	File::save(skillJSONDir.$skillID.'.json', str_replace('    ', "\t", Str::jsonEncode($skill)));
}

$jsonData['$schema'] = 'https://build-wars.github.io/gw-skilldata/schemas/skilldata-combined.schema.json';

File::save(combinedJSON, str_replace('    ', "\t", Str::jsonEncode($jsonData)));

$logger->info(sprintf('JSON for combined skilldata: %s ', File::realpath(combinedJSON)));
