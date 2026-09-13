<?php
/**
 * Class DataObjectAbstract
 *
 * @created      22.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use InvalidArgumentException;
use function array_key_exists;
use function sprintf;

/**
 * Abstract parent to the Attribute, Campaign, Profession and Skilltype classes
 */
abstract class DataObjectAbstract implements DataObjectInterface{
	use IDComparisonTrait;

	protected(set) Lang $lang {
		set(Lang|string $lang){

			if(!$lang instanceof Lang){
				$lang = new Lang($lang);
			}

			$this->lang = $lang;
		}
	}

	public function __construct(int $id, Lang|string $lang = Lang::EN){
		// "Conflict resolution between hooked properties is currently not supported."
		if(!array_key_exists($id, static::NAME)){
			throw new InvalidArgumentException(sprintf('invalid ID "%s" (%s)', $id, static::class));
		}

		$this->id   = $id;
		$this->lang = $lang;
	}

	protected function getLang(Lang|string|null $lang):Lang{

		if($lang === null){
			return $this->lang;
		}

		if($lang instanceof Lang){
			return $lang;
		}

		return new Lang($lang);
	}

	public function getName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);
		// @todo: temp fix for missing translations
		return (static::NAME[$this->id][$lang->id] ?? static::NAME[$this->id][Lang::EN]);
	}

	public function toHTML(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return sprintf(
			'<span class="%s" data-id="%s" data-lang="%s">%s</span>',
			static::CSS_CLASS,
			$this->id,
			$lang->id,
			$this->getName($lang),
		);
	}

	protected function gray(string $text):string{
		return sprintf('<gray>%s</gray>', $text);
	}

	protected function blue(string $text):string{
		return sprintf('<blue>%s</blue>', $text);
	}

}
