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

/**
 * Offers a method to load the skill data in a convenient way
 */
trait SkillDataAwareTrait{

	/** @todo change to constant in PHP 8.2+ */
	private array $LANGUAGES = [
		'de' => SkillLangGerman::class,
		'en' => SkillLangEnglish::class,
	];

	protected SkillDataInterface $skillData;

	/**
	 * @implements \Buildwars\GWSkillData\SkillDataAwareInterface::setSkillDataLanguage()
	 */
	public function setSkillDataLanguage(string $lang):static{

		if(!isset($this->LANGUAGES[$lang])){
			throw new InvalidArgumentException('invaild language'); // @codeCoverageIgnore
		}

		$this->skillData = new ($this->LANGUAGES[$lang]);

		/** @phan-suppress-next-line PhanTypeMismatchReturn */
		return $this;
	}

}
