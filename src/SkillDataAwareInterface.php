<?php
/**
 * Interface SkillDataAwareInterface
 *
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

/**
 *
 */
interface SkillDataAwareInterface{

	/**
	 * loads the skill data for the given language
	 *
	 * valid languages: de, en
	 */
	public function setSkillDataLanguage(string $lang):static;

}
