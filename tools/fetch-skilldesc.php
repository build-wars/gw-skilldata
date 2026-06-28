<?php
/**
 * Fetches the skill pages from the wikis and updates the skill descriptions
 *
 * @created      26.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use Buildwars\GWSkillData\Skilltype;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherEnglish;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherGerman;
use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use function array_column;
use function array_combine;
use function array_keys;
use function sprintf;
use function str_replace;

/**
 * @var \Psr\Http\Client\ClientInterface $http
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

const fetchers = [
	'English' => WikiFetcherEnglish::class,
	'German'  => WikiFetcherGerman::class,
];

const update_skilldata = true;
const from_cache       = true;

$httpFactory = new HTTPFactory;
$jsonData    = File::loadJSON(DATA_FILE, true);

foreach(fetchers as $language => $fqcn){
	// invoke fetcher
	/** @var \Buildwars\GWSkillDataTools\Fetchers\WikiFetcher $fetcher */
	$fetcher = new $fqcn($http, $httpFactory, $httpFactory, $httpFactory, $logger);

	// load the previously created JSON (see parse-pwnd)
	[$lang, $skilldescJSON] = LANG_FILES[$language];

	$skilldesc  = File::loadJSON($skilldescJSON, true);
	$skilltypes = array_combine(array_column(Skilltype::NAME, $lang), array_keys(Skilltype::NAME));

	foreach($skilldesc['skilldesc'] as &$desc){
		[$localized_desc, $skilldata] = $fetcher->fetch($desc['name'], $desc['id'], from_cache);

		// update skill data from guildwiki
		if(update_skilldata && $lang === 'de' && $skilldata !== null){
			foreach($skilldata as $k => $v){
				$jsonData['skilldata'][$desc['id']][$k] = $v;
			}
		}

		// update skill types from GWW
		if(update_skilldata && $lang === 'en' && $skilldata !== null){
			$jsonData['skilldata'][$desc['id']]['type'] = $skilltypes[$skilldata['type_name']];
		}

		if($localized_desc === null){
			continue;
		}

		[$name, $desc['description'], $desc['concise']] = $localized_desc;

		if($name !== $desc['name']){
			$logger->info(sprintf('name fix: %s => %s', $desc['name'], $name));

			$desc['name'] = $name;
		}

	}

	// save updated JSON
	File::save($skilldescJSON, str_replace('    ', "\t", Str::jsonEncode($skilldesc)));
}

if(update_skilldata){
	File::save(DATA_FILE, str_replace('    ', "\t", Str::jsonEncode($jsonData)));
}
