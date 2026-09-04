<?php
/**
 * Creates the combined and per-skill JSON files from the previously created JSON data/lang files
 *
 * @created      04.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use chillerlan\Utilities\File;
use function sprintf;
use function var_dump;

final class BuildCombinedJSON extends BuilderAbstract{
	use SkillDataAwareTrait;

	public function build():static{
		$jsonData = File::loadJSON(self::JSON_SKILLDATA_FILE, true);
		// change the schema
		$jsonData['$schema'] = self::SCHEMA_SKILLDATA_COMBINED;

		// cache the languages
		$jsonLang = [];

		foreach(self::GWDB_LANG as $langID => $fqcn){
			$jsonLang[$langID] = File::loadJSON($this->getJsonLangFile($langID), true);
		}

		foreach($jsonData['skilldata'] as $skillID => &$skillData){
			// create keys for each language
			foreach(self::GWDB_LANG as $langID => $fqcn){
				// fill the translations
				foreach(Skill::KEYS_DESC as $key){
					$skillData['lang'][$langID][$key] = $jsonLang[$langID]['skilldesc'][$skillID][$key];
				}
			}

			$this->saveJSON(
				sprintf('%s/%s.json', self::JSON_SKILL_DIR, $skillID),
				['$schema' => self::SCHEMA_SKILL, 'skill' => $skillData],
			);

			$this->logger->info(sprintf('JSON for [%-4s] [%s]', $skillID, $skillData['lang'][Lang::EN][Skill::DESC_NAME]));
		}

		$savepath = $this->saveJSON(static::JSON_SKILLDATA_COMBINED, $jsonData);

		$this->logger->info(sprintf('saved JSON for combined skilldata to: %s', $savepath));

		return $this;
	}

}
