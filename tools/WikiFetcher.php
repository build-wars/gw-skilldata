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

namespace Buildwars\GWSkillDataTools;

use chillerlan\HTTP\Utils\QueryUtil;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use function array_map;
use function array_values;
use function explode;
use function file_put_contents;
use function is_dir;
use function is_file;
use function json_decode;
use function mkdir;
use function preg_match_all;
use function sprintf;
use function strtolower;
use const PREG_SET_ORDER;

/**
 * @link http://xkcd.com/208
 */
abstract class WikiFetcher{

	protected const MEDIAWIKI_API = '';
	protected const CACHEDIR      = '';

	// we need to fix the skill suffix that we cut in order to fetch from the wiki: Skill Name (Luxon)
	protected const Luxon   = [1948, 1949, 1950, 1951, 1952, 1953, 1954, 1955, 1957, 2051];
	protected const Kurzick = [2091, 2092, 2093, 2094, 2095, 2096, 2097, 2098, 2099, 2100];

	protected bool $isCachedResponse = false;

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
		if(!is_dir(static::CACHEDIR)){
			mkdir(static::CACHEDIR, 0o777, true);
		}
	}

	/**
	 *
	 */
	public function fetch(string $skillName, int $id, bool $cached = true):array|null{

		// shortcut for the empty slot skill
		if($id === 0){
			return $this->parseResponse([], $id);
		}

		$name = $this->prepareSkillName($skillName, $id);

		// log name substitutes
		if($name !== $skillName){
			$this->logger->info(sprintf('using skill name substitude: %s', $name));
		}

		$response = $this->fetchPage($name, $id, $cached);

		if($response->getStatusCode() === 200){
			$data = $response->getBody()->getContents();

			$json = json_decode($data, true);

			if(isset($json['query']['pages']['-1'])){
				$this->logger->warning(sprintf('page not found: %s', $name));
			}

			// save cache file
			if(!$this->isCachedResponse){
				file_put_contents($this->getCachFileName($id), $data);
			}

			return $this->parseResponse(array_values($json['query']['pages'])[0], $id);
		}

		$this->logger->warning(sprintf('fetch error: HTTP/%s (%s)', $response->getStatusCode(), $name));

		return null;
	}

	/**
	 *
	 */
	abstract protected function prepareSkillName(string $skillName, int $id):string;

	/**
	 *
	 */
	abstract protected function parseResponse(array $data, int $id):array|null;

	/**
	 *
	 */
	abstract protected function parseInfobox(string $infobox, $id):array;

	/**
	 *
	 */
	protected function getCachFileName(int $id):string{
		return sprintf('%s%s.wikitext.json', static::CACHEDIR, $id);
	}

	/**
	 *
	 */
	protected function hasCacheFile(int $id):bool{
		return is_file($this->getCachFileName($id));
	}

	/**
	 *
	 */
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

	/**
	 *
	 */
	protected function fetchPage(string $skillName, int $id, bool $cached):ResponseInterface{
		$this->isCachedResponse = false;

		// create a response fron the existing file
		if($cached === true && $this->hasCacheFile($id)){
			$this->isCachedResponse = true;
			$this->logger->info(sprintf('cached response for skill: %s', $id));

			$stream = $this->streamFactory->createStreamFromFile($this->getCachFileName($id));

			return $this->responseFactory->createResponse()->withHeader('content-type', 'application/json')->withBody($stream);
		}

		// otherwise just fetch from the API
		$this->logger->info(sprintf('fetching: %s', $skillName));

		$params  = $this->getRequestParams($skillName);
		$request = $this->requestFactory->createRequest('GET', QueryUtil::merge(static::MEDIAWIKI_API, $params));

		return $this->http->sendRequest($request);
	}

	/**
	 *
	 */
	protected function getInfobox(string $data, string $templateName):string|null{
		// find all matching pairs of double braces
		preg_match_all('/\{\{(?:(?:[^\{\}]+)|(?R))*\}\}/', $data, $matches, PREG_SET_ORDER);

		foreach($matches as $match){
			foreach($match as $str){
				if(str_contains(strtolower($str), strtolower($templateName))){
					return $match[0];
				}
			}
		}

		return null;
	}

	/**
	 *
	 */
	protected function splitKV(string $str):array{
		$kv    = array_map('trim', explode('=', $str, 2));
		$kv[0] = strtolower($kv[0]);

		return $kv;
	}

}
