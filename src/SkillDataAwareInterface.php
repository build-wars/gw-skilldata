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
 * @property SkillDataInterface $skillData
 */
interface SkillDataAwareInterface{

	/**
	 * Returns a skill database instance for the given language
	 */
	public function getGWDB(string $lang):SkillDataInterface;

	/**
	 * Loads the skill database for the given language
	 *
	 * @see \Buildwars\GWSkillData\Lang::IDS
	 */
	public function setSkillDataLanguage(string $lang):static;

}
