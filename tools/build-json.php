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
use function str_replace;

/**
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

$jsonData = File::loadJSON(dataFile, true);

foreach($jsonData['skilldata'] as $skillID => &$skillData){
	foreach(langFiles as [$abbr, $file]){
		$jsonLang = File::loadJSON($file, true);

		unset($jsonLang['skilldesc'][$skillID]['id']);

		$skillData['lang'][$abbr]                    = $jsonLang['skilldesc'][$skillID];
		$skillData['lang'][$abbr]['campaign']        = SkillDataInterface::CAMPAIGNS[$skillData['campaign']]['name'][$abbr];
		$skillData['lang'][$abbr]['profession']      = SkillDataInterface::PROFESSIONS[$skillData['profession']]['name'][$abbr];
		$skillData['lang'][$abbr]['profession_abbr'] = SkillDataInterface::PROFESSIONS[$skillData['profession']]['abbr'][$abbr];
		$skillData['lang'][$abbr]['attribute']       = SkillDataInterface::ATTRIBUTES[$skillData['attribute']]['name'][$abbr];
		$skillData['lang'][$abbr]['type']            = SkillDataInterface::SKILLTYPES[$skillData['type']]['name'][$abbr];

		$skill = [
			'$schema' => 'https://raw.githubusercontent.com/build-wars/gw-skilldata/refs/heads/main/data/json-skills/skill.schema.json',
			'skill'   => $skillData,
		];

		File::save(__DIR__.'/../data/json-skills/'.$skillID.'.json', str_replace('    ', "\t", Str::jsonEncode($skill)));
	}
}

$jsonData['$schema'] = 'https://raw.githubusercontent.com/build-wars/gw-skilldata/refs/heads/main/data/json-full/skilldata-combined.schema.json';

File::save(__DIR__.'/../data/json-full/skilldata-combined.json', str_replace('    ', "\t", Str::jsonEncode($jsonData)));
