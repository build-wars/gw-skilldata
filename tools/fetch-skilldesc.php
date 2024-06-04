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
use InvalidArgumentException;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use function realpath;
use function sprintf;
use function str_replace;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * @var \Psr\Http\Client\ClientInterface $http
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

const fetchers = [
	'English' => WikiFetcherEnglish::class,
	'German'  => WikiFetcherGerman::class,
];


foreach(fetchers as $language => $fqcn){
	// invoke fetcher
	$fetcher = new $fqcn($http, new HTTPFactory, new HTTPFactory, new HTTPFactory, $logger);

	// load the previously created JSON (see parse-pwnd)
	[$lang, $file] = langFiles[$language];
	$skilldescJSON = realpath($file);

	if($skilldescJSON === false){
		throw new InvalidArgumentException(sprintf('the data file "%s" does not exist', $file));
	}

	$skilldesc = json_decode(file_get_contents($skilldescJSON), true);

	foreach($skilldesc['skilldesc'] as &$desc){
		$data = $fetcher->fetch($desc['name'], $desc['id'], true);

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
	$json = json_encode($skilldesc, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

	file_put_contents($skilldescJSON, str_replace('    ', "\t", $json));
}
