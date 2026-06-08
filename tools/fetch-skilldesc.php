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

use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
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

const from_cache = false;

$httpFactory = new HTTPFactory;

foreach(fetchers as $language => $fqcn){
	// invoke fetcher
	/** @var \Buildwars\GWSkillDataTools\WikiFetcher $fetcher */
	$fetcher = new $fqcn($http, $httpFactory, $httpFactory, $httpFactory, $logger);

	// load the previously created JSON (see parse-pwnd)
	[, $skilldescJSON] = langFiles[$language];

	$skilldesc = File::loadJSON($skilldescJSON, true);

	foreach($skilldesc['skilldesc'] as &$desc){
		$data = $fetcher->fetch($desc['name'], $desc['id'], from_cache);

		if($data === null){
			continue;
		}

		[$name, $desc['description'], $desc['concise']] = $data;

		if($name !== $desc['name']){
			$logger->info(sprintf('name fix: %s => %s', $desc['name'], $name));

			$desc['name'] = $name;
		}

	}

	// save updated JSON
	File::save($skilldescJSON, str_replace('    ', "\t", Str::jsonEncode($skilldesc)));
}
