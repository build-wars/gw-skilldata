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

use Buildwars\GWSkillData\SkillDataInterface;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use function sprintf;
use function str_replace;

/**
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

const combinedJSON = __DIR__.'/../data/json-full/skilldata-combined.json';

$jsonData = File::loadJSON(dataFile, true);
$jsonLang = [];

foreach(langFiles as [$abbr, $file]){
	$jsonLang[$abbr] = File::loadJSON($file, true);
}

foreach($jsonData['skilldata'] as $skillID => &$skillData){
	foreach(['de', 'en'] as $abbr){
		$lang = $jsonLang[$abbr]['skilldesc'][$skillID];

		$skillData['lang'][$abbr] = [
			'name'            => $lang['name'],
			'description'     => $lang['description'],
			'concise'         => $lang['concise'],
			'campaign'        => SkillDataInterface::CAMPAIGNS[$skillData['campaign']]['name'][$abbr],
			'profession'      => SkillDataInterface::PROFESSIONS[$skillData['profession']]['name'][$abbr],
			'profession_abbr' => SkillDataInterface::PROFESSIONS[$skillData['profession']]['abbr'][$abbr],
			'attribute'       => SkillDataInterface::ATTRIBUTES[$skillData['attribute']]['name'][$abbr],
			'type'            => SkillDataInterface::SKILLTYPES[$skillData['type']]['name'][$abbr],
		];

	}

	$skill = [
		'$schema' => 'https://raw.githubusercontent.com/build-wars/gw-skilldata/refs/heads/main/data/json-skills/skill.schema.json',
		'skill'   => $skillData,
	];

	$logger->info(sprintf('JSON for [%-4s] %s ', $skillID, $skillData['lang']['en']['name']));

	File::save(__DIR__.'/../data/json-skills/'.$skillID.'.json', str_replace('    ', "\t", Str::jsonEncode($skill)));
}

$logger->info(sprintf('JSON for combined skilldata: %s ', File::realpath(combinedJSON)));

$jsonData['$schema'] = 'https://raw.githubusercontent.com/build-wars/gw-skilldata/refs/heads/main/data/json-full/skilldata-combined.schema.json';

File::save(combinedJSON, str_replace('    ', "\t", Str::jsonEncode($jsonData)));
