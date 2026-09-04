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

	/** @var array<string, string> */
	protected const array GWDB_LANG = [
		Lang::CN           => SkillLangSimplifiedChinese::class,
		Lang::ZH           => SkillLangTraditionalChinese::class,
		Lang::DE           => SkillLangGerman::class,
		Lang::DE_GUILDWIKI => SkillLangGermanGuildWiki::class,
		Lang::EN           => SkillLangEnglish::class,
		Lang::EN_GWW       => SkillLangEnglishGWW::class,
		Lang::ES           => SkillLangSpanish::class,
		Lang::FR           => SkillLangFrench::class,
		Lang::FR_GWIKI     => SkillLangFrenchGWiki::class,
		Lang::IT           => SkillLangItalian::class,
		Lang::JA           => SkillLangJapanese::class,
		Lang::KO           => SkillLangKorean::class,
		Lang::PL           => SkillLangPolish::class,
		Lang::RU           => SkillLangRussian::class,
		Lang::XX           => SkillLangBork::class,
	];

	protected SkillDataInterface $skillData;

	public function setSkillDataLanguage(string $lang):static{
		$this->skillData = $this->getGWDB($lang);

		return $this;
	}

	public function getGWDB(string $lang):SkillDataInterface{

		if(!array_key_exists($lang, self::GWDB_LANG)){
			throw new InvalidArgumentException('invalid DB language');
		}

		return new (self::GWDB_LANG[$lang]);
	}

}
