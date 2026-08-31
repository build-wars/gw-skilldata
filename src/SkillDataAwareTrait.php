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

	/** @var array<string, string> */
	private const array LANGUAGES = [
		Lang::DE => SkillLangGerman::class,
		Lang::EN => SkillLangEnglish::class,
		Lang::FR => SkillLangFrench::class,
	];

	protected SkillDataInterface $skillData;

	public function setSkillDataLanguage(Lang|string $lang):static{

		if(!$lang instanceof Lang){
			$lang = new Lang($lang);
		}

		$this->skillData = new (self::LANGUAGES[$lang->id]);

		return $this;
	}

}
