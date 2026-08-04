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
		Lang::DE => SkillLangGerman::class,
		Lang::EN => SkillLangEnglish::class,
	];

	protected SkillDataInterface $skillData;

	public function setSkillDataLanguage(Lang|string $lang):static{

		if(!$lang instanceof Lang){
			$lang = new Lang($lang);
		}

		$this->skillData = new ($this->LANGUAGES[$lang->id]);

		return $this;
	}

}
