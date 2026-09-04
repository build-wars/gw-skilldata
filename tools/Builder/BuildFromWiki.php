<?php
/**
 * Fetches the wiki pages for the given skills and parses them for skill data and descriptions
 *
 * Creates:
 *
 *   - BUILDDIR/skilldata-<wiki>.json
 *   - DATADIR/skilldesc-<lang>-<wiki>.json
 *   - SRCDIR/SkillLang<lang><wiki>.php
 *
 * @created      03.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillDataTools\Fetchers\WikFetcherInterface;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherEnglish;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherFrench;
use Buildwars\GWSkillDataTools\Fetchers\WikiFetcherGerman;
use chillerlan\HTTP\Utils\MessageUtil;
use chillerlan\HTTP\Utils\QueryUtil;
use chillerlan\Utilities\File;
use function array_chunk;
use function array_flip;
use function array_key_exists;
use function implode;
use function sprintf;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class BuildFromWiki extends BuilderAbstract{

	/** @var array<string, string> */
	protected const array WIKIFETCHERS = [
		Lang::DE_GUILDWIKI => WikiFetcherGerman::class,
		Lang::EN_GWW       => WikiFetcherEnglish::class,
		Lang::FR_GWIKI     => WikiFetcherFrench::class,
	];

	private WikFetcherInterface $wf;
	private Lang                $lang;

	private function initFetcher(string $fqcn):WikFetcherInterface{
		return new $fqcn(
			$this->options,
			$this->http,
			$this->logger,
			$this->requestFactory,
			$this->responseFactory,
			$this->streamFactory,
		);
	}

	public function build():static{

		foreach(self::WIKIFETCHERS as $langID => $fqcn){
			$this->lang = new Lang($langID);
			$this->wf   = $this->initFetcher($fqcn);

			$this->bulkFetchToCache();

			// load the previously created language JSON
			$skillData = File::loadJSON(self::JSON_SKILLDATA_FILE, true)['skilldata'];
			$skillDesc = File::loadJSON($this->getJsonLangFile($this->lang->id), true)['skilldesc'];

			foreach($this->known as $id => $known){
				// we're always fetching from cache here, missing individual pages will be requested anyway
				$data = $this->wf->fetch($known[$this->lang->id], $id, true);

				// update skill data and descriptions
				$skillData[$id] = $this->updateSkillData($data, $skillData[$id]);
				$skillDesc[$id] = $this->updateSkillDesc($data, $skillDesc[$id], $known[$this->lang->id]);
			}

			// save updated JSON and classes
			$this
				->saveDataJSON($skillData, sprintf(self::WIKI_SKILLDATA_FILE, $langID))
				->saveLangJSON($skillDesc, $langID)
				->createLangClass($skillDesc, $langID);
		}

		return $this;
	}

	private function updateSkillDesc(array $data, array $current, string $knownName):array{

		if($data[Skill::DESC_NAME] !== $knownName){
			$this->logger->info(sprintf('name fix: %s => %s', $knownName, $data[Skill::DESC_NAME]));
		}

		foreach(Skill::KEYS_DESC as $key){
			$current[$key] = $data[$key];
		}

		return $current;
	}

	private function updateSkillData(array $data, array $current):array{

		foreach(Skill::KEYS_DATA as $key){
			if(array_key_exists($key, $data)){
				$current[$key] = $data[$key];
			}
		}

		return $current;
	}

	private function bulkFetchToCache():void{
		$skills = [];

		foreach($this->known as $id => $known){
			// skip the "No Skill" for the fetch request
			if($id === 0){
				continue;
			}

			$skills[$id] = $this->wf->prepareSkillName($known[$this->lang->id], $id);
		}

		// 50 articles is mediawiki limit
		foreach(array_chunk($skills, 50, true) as $pages){
			$url      = QueryUtil::merge($this->wf::MEDIAWIKI_API, $this->wf->getRequestParams(implode('|', $pages)));
			$response = $this->fetch($url, $this->wf::WIKI_BULK_CACHE, $this->options->from_cache);
			$json     = MessageUtil::decodeJSON($response, true);
			$lookup   = array_flip($pages);

			foreach($json['query']['pages'] as $page){
				// we'll just skip articles that can't be assigned, these will be fetched later anyway
				if(!array_key_exists($page['title'], $lookup)){
					$this->logger->warning(sprintf('article not assignable: %s', $page['title']));

					continue;
				}

				$id = $lookup[$page['title']];

				// don't cache missing page responses
				if(isset($page['missing']) || $page['revisions'] === []){
					$this->logger->warning(sprintf('could not fetch: [%-4s] %s', $id, $page['title']));

					continue;
				}

				$this->logger->info(sprintf('feched to cache: [%-4s] %s', $id, $page['title']));

				File::saveJSON($this->wf->getCacheFilePath($id), $page, (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
			}
		}

	}

}
