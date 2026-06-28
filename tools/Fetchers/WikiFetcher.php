<?php
/**
 * Class WikiFetcher
 *
 * @created      26.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 *
 * @noinspection RegExpUnnecessaryNonCapturingGroup, RegExpRedundantEscape
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Fetchers;

use chillerlan\HTTP\Utils\QueryUtil;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use chillerlan\Utilities\Str;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use function abs;
use function array_key_exists;
use function array_map;
use function array_values;
use function count;
use function explode;
use function floatval;
use function in_array;
use function intval;
use function is_int;
use function mb_strtolower;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function sprintf;
use function str_contains;
use function str_replace;
use function trim;
use const PREG_SET_ORDER;

/**
 * @link http://xkcd.com/208
 */
abstract class WikiFetcher implements WikFetcherInterface{

	protected const MEDIAWIKI_API = '';
	protected const CACHEDIR      = '';
	protected const REDIRECTS     = [];

	// we need to fix the skill suffix that we cut in order to fetch from the wiki: Skill Name (Luxon)
	protected const Luxon   = [1948, 1949, 1950, 1951, 1952, 1953, 1954, 1955, 1957, 2051];
	protected const Kurzick = [2091, 2092, 2093, 2094, 2095, 2096, 2097, 2098, 2099, 2100];

	/**
	 * WikiFetcher constructor
	 */
	public function __construct(
		protected ClientInterface $http,
		protected RequestFactoryInterface $requestFactory,
		protected ResponseFactoryInterface $responseFactory,
		protected StreamFactoryInterface $streamFactory,
		protected LoggerInterface $logger,
	){
		Directory::create(static::CACHEDIR);

		if(!Directory::isWritable(static::CACHEDIR) || !Directory::isReadable(static::CACHEDIR)){
			throw new RuntimeException('cannot read/write to cache dir');
		}
	}

	abstract protected function parseResponse(array $data, int $id):array|null;

	abstract protected function parseInfobox(string $infobox, int $id):array;

	protected function getCachFileName(int $id):string{
		return sprintf('%s%s.wikitext.json', static::CACHEDIR, $id);
	}

	public function fetch(string $skillName, int $id, bool $cached = true):array|null{

		// shortcut for the empty slot skill
		if($id === 0){
			return $this->parseResponse([], $id);
		}

		$name = $this->prepareSkillName($skillName, $id);

		// log name substitutes
		if($name !== $skillName){
			$this->logger->info(sprintf('using skill name substitude: [%-4s] %s', $id, $name));
		}

		$response = $this->fetchPage($name, $id, $cached);
		$status   = $response->getStatusCode();

		// 420 is the response code from the local cache
		if(!in_array($status, [200, 420], true)){
			$this->logger->error(sprintf('fetch error: HTTP/%s ([%-4s] %s)', $status, $id, $name));

			return null;
		}

		$data = $response->getBody()->getContents();
		$json = Str::jsonDecode($data, true);

		// check for a redirect and save the response data to cache
		if($status === 200){

			if(isset($json['query']['pages']['-1'])){

				if(isset($json['query']['pages']['-1']['title'])){
					$this->logger->warning(sprintf('redirecting [%-4s] %s to: %s',  $id, $name, $json['query']['pages']['-1']['title'])); // phpcs:ignore

					return $this->fetch($json['query']['pages']['-1']['title'], $id, $cached);
				}

				$this->logger->warning(sprintf('page not found: [%-4s] %s',  $id, $name));

				return null;
			}

			File::save($this->getCachFileName($id), $data);
		}

		return $this->parseResponse(array_values($json['query']['pages'])[0], $id);
	}

	protected function prepareSkillName(string $skillName, int $id):string{

		if(array_key_exists($id, static::REDIRECTS)){
			return static::REDIRECTS[$id];
		}

		// shouts (missing quotes)
		if(in_array($id, [
			316, 333, 343, 348, 364, 365, 366, 367, 368, 839, 869, 891, 906, 1141, 1412,
			1558, 1572, 1589, 1590, 1591, 1592, 1593, 1594, 1595, 1596, 1597, 1598, 1599,
			1779, 1780, 1781, 1782, 2067, 2112, 2216, 2217, 2353, 2354, 2355, 2356, 2358, 2359,
		], true)){
			return sprintf('"%s"', str_replace('"', '', $skillName));
		}

		// PvP shouts
		if(in_array($id, [
			2858, 2879, 2880, 2883, 3026, 3027, 3031, 3032, 3033, 3034, 3035, 3036, 3037,
		], true)){
			return sprintf('"%s" (PvP)', str_replace(['"', ' (PvP)'], '', $skillName));
		}

		// fix for pve faction skills
		return preg_replace('/(\s\((Kurzick|Luxon)\))/', '', $skillName);
	}

	protected function getRequestParams(string $skillName):array{
		return [
			'format'  => 'json',
			'action'  => 'query',
			'prop'    => 'revisions',
			'rvprop'  => 'content',
			'rvslots' => 'main',
			'titles'  => $skillName,
		];
	}

	protected function fetchPage(string $skillName, int $id, bool $cached):ResponseInterface{

		// create a response fron the existing file
		if($cached === true && File::isReadable($this->getCachFileName($id))){
			$this->logger->info(sprintf('cached response for skill: [%-4s] %s', $id, $skillName));

			$stream = $this->streamFactory->createStreamFromFile($this->getCachFileName($id));
			// using code 420 here to indicate a cache response
			return $this->responseFactory
				->createResponse(420)
				->withHeader('content-type', 'application/json')
				->withBody($stream);
		}

		// otherwise just fetch from the API
		$this->logger->info(sprintf('fetching: [%-4s] %s', $id, $skillName));

		$params  = $this->getRequestParams($skillName);
		$request = $this->requestFactory->createRequest('GET', QueryUtil::merge(static::MEDIAWIKI_API, $params));

		return $this->http->sendRequest($request);
	}

	protected function getInfobox(string $data, string $templateName):string|null{
		// find all matching pairs of double braces
		preg_match_all('/\{\{(?:(?:[^\{\}]+)|(?R))*\}\}/', $data, $matches, PREG_SET_ORDER);

		foreach($matches as $match){
			foreach($match as $str){
				if(str_contains(mb_strtolower($str), mb_strtolower($templateName))){
					return $match[0];
				}
			}
		}

		return null;
	}

	protected function splitKV(string $str):array{
		$kv    = array_map('trim', explode('=', $str, 2));
		$kv[0] = mb_strtolower($kv[0]);

		// fix possibly empty parameters
		if(count($kv) < 2){
			$kv[] = '';
		}

		return $kv;
	}

	protected function calcFraction(string $str):float{
		$str = trim($str);

		if(is_int($str)){
			return intval($str);
		}

		// we have a float somehow
		if(preg_match('/\d+\.\d+/', $str) > 0){
			return abs(floatval($str));
		}

		$calc = function(string $fraction):float{

			if(!str_contains($fraction, '/')){
				return floatval($fraction);
			}

			[$top, $bottom] = explode('/', trim($fraction));

			return abs(intval($top) / intval($bottom));
		};

		$parts = explode(' ', $str, 2);

		if(count($parts) === 1){
			return $calc($parts[0]);
		}

		return ($calc($parts[0]) + $calc($parts[1]));
	}

}
