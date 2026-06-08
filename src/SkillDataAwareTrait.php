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

use InvalidArgumentException;
use function array_key_exists;

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
		'de' => SkillLangGerman::class,
		'en' => SkillLangEnglish::class,
	];

	protected SkillDataInterface $skillData;

	public function setSkillDataLanguage(string $lang):static{

		if(!array_key_exists($lang, $this->LANGUAGES)){
			throw new InvalidArgumentException('invaild language');
		}

		$this->skillData = new ($this->LANGUAGES[$lang]);

		return $this;
	}

}
