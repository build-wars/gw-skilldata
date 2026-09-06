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

use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\Type;
use Buildwars\GWSkillDataTools\BuilderOptions;
use chillerlan\HTTP\Utils\MessageUtil;
use chillerlan\HTTP\Utils\QueryUtil;
use chillerlan\Settings\SettingsContainerInterface;
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
use function array_column;
use function array_combine;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function explode;
use function floatval;
use function in_array;
use function intval;
use function is_numeric;
use function mb_strtolower;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function sprintf;
use function str_contains;
use function str_replace;
use function trim;
use function usleep;
use const PREG_SET_ORDER;

/**
 * @link http://xkcd.com/208
 */
abstract class WikiFetcherAbstract implements WikFetcherInterface{

	protected const string LANG              = '';
	protected const array  REDIRECTS         = [];
	protected const array  EMPTY_SKILL       = [];
	protected const array  PRE_PARSE_REPLACE = [];
	protected const string INFOBOX_NAME      = '';

	// shouts to fix missing quotes
	protected const array Shouts = [
		316, 333, 343, 348, 364, 365, 366, 367, 368, 839, 869, 891, 906, 1141, 1412,
		1558, 1572, 1589, 1590, 1591, 1592, 1593, 1594, 1595, 1596, 1597, 1598, 1599,
		1779, 1780, 1781, 1782, 2067, 2112, 2216, 2217, 2353, 2354, 2355, 2356, 2358, 2359,
	];

	protected const array PvPShouts = [
		2858, 2879, 2880, 2883, 3026, 3027, 3031, 3032, 3033, 3034, 3035, 3036, 3037, 3456,
	];

	protected readonly array $skilltypes;

	/**
	 * WikiFetcher constructor
	 *
	 * @throws \RuntimeException
	 */
	public function __construct(
		protected readonly SettingsContainerInterface|BuilderOptions $options,
		protected readonly ClientInterface                           $http,
		protected readonly LoggerInterface                           $logger,
		protected readonly RequestFactoryInterface                   $requestFactory,
		protected readonly ResponseFactoryInterface                  $responseFactory,
		protected readonly StreamFactoryInterface                    $streamFactory,
	){

		Directory::create(static::CACHEDIR);

		if(!Directory::isWritable(static::CACHEDIR) || !Directory::isReadable(static::CACHEDIR)){
			throw new RuntimeException('cannot read/write to cache dir');
		}

		$this->skilltypes = array_combine(array_column(Type::NAME, static::LANG), array_keys(Type::NAME));
	}

	/**
	 * @return array<string, scalar>
	 */
	abstract protected function parseInfobox(string $infobox, int $id):array;

	/**
	 * @param array<string, mixed> $data
	 */
	protected function parseResponse(string $skillName, array $data, int $id):array{

		if(!isset($data['revisions'][0]['slots']['main']['*'])){
			return $this->emptySkill($skillName);
		}

		// remove/fix some templates first
		$data    = strtr($data['revisions'][0]['slots']['main']['*'], static::PRE_PARSE_REPLACE);
		$infobox = $this->getInfobox($data, static::INFOBOX_NAME, $id);

		if($infobox === null){
			$this->logger->warning(sprintf('could not parse infobox for skill %s', $id));

			return $this->emptySkill($skillName);
		}

		return $this->parseInfobox($infobox, $id);
	}

	public function getCacheFilePath(int $id):string{
		return sprintf('%s/%s.wikitext.json', static::CACHEDIR, $id);
	}

	protected function emptySkill(string|null $name = null):array{
		$skill = static::EMPTY_SKILL;

		if($name !== null){
			$skill[Skill::DESC_NAME]        = $name;
			$skill[Skill::DESC_DESCRIPTION] = '';
			$skill[Skill::DESC_CONCISE]     = '';
		}

		return $skill;
	}

	/**
	 * @phan-suppress PhanTypeInvalidThrowsIsInterface
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 * @throws \JsonException
	 */
	public function fetch(string $skillName, int $id, bool $cached = true):array{

		// shortcut for the empty slot skill
		if($id === 0){
			return $this->emptySkill();
		}

		$name = $this->prepareSkillName($skillName, $id);

		// log name substitutes
		if($name !== $skillName){
			$this->logger->info(sprintf('using skill name substitude: [%-4s] %s', $id, $name));
		}

		// static retry counter for recursive fetches
		static $retries = 0;

		$response = $this->fetchPage($name, $id, $cached);
		$status   = $response->getStatusCode();

		// 420 is the response code from the local cache
		if(!in_array($status, [200, 420], true)){
			$this->logger->error(sprintf('fetch error: HTTP/%s ([%-4s] %s)', $status, $id, $name));

			return $this->emptySkill($skillName);
		}

		$data = MessageUtil::decompress($response);
		$json = Str::jsonDecode($data, true);

		// check for a redirect and save the response data to cache
		if($status === 200){

			if(isset($json['query']['pages']['-1'])){

				if(isset($json['query']['pages']['-1']['title'])){
					$retries++;

					if($retries > 2){
						$this->logger->error(sprintf('could not find a target for [%-4s][%s] %s', $id, static::LANG, $name));
						// reset counter and exit
						$retries = 0;

						return $this->emptySkill($skillName);
					}

					$this->logger->warning(sprintf(
						'redirecting [%-4s][%s] %s to: %s',
						$id,
						static::LANG,
						$name,
						$json['query']['pages']['-1']['title']),
					);

					return $this->fetch($json['query']['pages']['-1']['title'], $id, $cached);
				}

				$this->logger->warning(sprintf('page not found: [%-4s][%s] %s', $id, static::LANG, $name));

				return $this->emptySkill($skillName);
			}

			File::saveJSON($this->getCacheFilePath($id), array_values($json['query']['pages'])[0]);
		}

		return $this->parseResponse($skillName, $json, $id);
	}

	public function prepareSkillName(string $skillName, int $id):string{

		if(array_key_exists($id, static::REDIRECTS)){
			return static::REDIRECTS[$id];
		}

		// shouts (missing quotes)
		if(in_array($id, static::Shouts, true)){
			return sprintf('"%s"', str_replace('"', '', $skillName));
		}

		// PvP shouts
		if(in_array($id, static::PvPShouts, true)){
			return sprintf('"%s" (PvP)', str_replace(['"', ' (PvP)'], '', $skillName));
		}

		// fix for pve faction skills
		return preg_replace('/(\s\((Kurzick|Luxon)\))/', '', $skillName);
	}

	public function getRequestParams(string $skillName):array{
		return [
			'action'  => 'query',
			'format'  => 'json',
			'prop'    => 'revisions',
			'rvprop'  => 'content',
			'rvslots' => 'main',
			'titles'  => $skillName,
		];
	}

	/**
	 * @phan-suppress PhanTypeInvalidThrowsIsInterface
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 */
	protected function fetchPage(string $skillName, int $id, bool $cached):ResponseInterface{

		// create a response fron the existing file
		if($cached === true && File::isReadable($this->getCacheFilePath($id))){
			$this->logger->info(sprintf('cached response for skill: [%-4s] %s', $id, $skillName));

			$stream = $this->streamFactory->createStreamFromFile($this->getCacheFilePath($id));

			// using code 420 here to indicate a cache response
			return $this->responseFactory
				->createResponse(420)
				->withHeader('Content-Type', 'application/json')
				->withBody($stream);
		}

		// otherwise just fetch from the API
		$this->logger->info(sprintf('fetching: [%-4s] %s', $id, $skillName));

		$params  = $this->getRequestParams($skillName);
		$request = $this->requestFactory->createRequest('GET', QueryUtil::merge(static::MEDIAWIKI_API, $params));

		if($this->options->use_http_compression){
			$request = $request->withHeader('Accept-Encoding', 'gzip;q=1.0, deflate;q=0.8, identity;q=0.5, *;q=0.1');
		}

		usleep($this->options->request_sleep); // avoid hammering, especially on CI

		return $this->http->sendRequest($request);
	}

	protected function getInfobox(string $data, string $templateName, int $id):string|null{
		// for some reason random html comments can break the infobox match pattern
		$data = preg_replace('/<!--(.*)-->/', '', $data);
		// find all matching pairs of double braces
		preg_match_all('/\{\{(?:(?:[^\{\}]+)|(?R))*\}\}/', $data, $matches, PREG_SET_ORDER);

		foreach($matches as $match){
			if(array_any($match, fn(string $str):bool => str_contains(mb_strtolower($str), mb_strtolower($templateName)))){
				return $match[0];
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

	protected function calcFraction(string $str):float|int{
		$str = trim($str);

		// likely an integer
		if(is_numeric($str) && !str_contains($str, '.')){
			return intval($str);
		}

		// we have a float somehow
		if(preg_match('/\d*\.\d+/', $str) > 0){
			return floatval($str);
		}

		$calc = function(string $fraction):float{

			if(!str_contains($fraction, '/')){
				return floatval($fraction);
			}

			[$top, $bottom] = explode('/', trim($fraction));

			return (intval($top) / intval($bottom));
		};

		$parts = explode(' ', $str, 2);

		if(count($parts) === 1){
			return $calc($parts[0]);
		}

		return ($calc($parts[0]) + $calc($parts[1]));
	}

}
