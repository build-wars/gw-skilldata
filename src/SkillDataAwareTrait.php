<?php
/**
 * SkillDataAwareTrait.php
 *
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

/**
 * Offers a method to load the skill data in a convenient way
 *
 * @implements \Buildwars\GWSkillData\SkillDataAwareInterface
 */
trait SkillDataAwareTrait{

	/**
	 * @todo change to constant in PHP 8.2+
	 *
	 * @var array<string, string>
	 */
	private array $LANGUAGES = [
		SkillLang::DE => SkillLangGerman::class,
		SkillLang::EN => SkillLangEnglish::class,
	];

	protected SkillDataInterface $skillData;

	public function setSkillDataLanguage(SkillLang|string $lang):static{

		if(!$lang instanceof SkillLang){
			$lang = new SkillLang($lang);
		}

		$this->skillData = new ($this->LANGUAGES[$lang->id]);

		return $this;
	}

}
