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

namespace Buildwars\GWSkillData;

use InvalidArgumentException;
use function array_key_exists;
use function in_array;
use function sprintf;

/**
 * Abstract parent to the Attribute, Campaign, Profession and Skilltype classes
 */
abstract class DataObjectAbstract implements DataObjectInterface{

	protected(set) int $id {
		set{
			if(!array_key_exists($value, static::NAME)){
				throw new InvalidArgumentException(sprintf('invalid ID "%s" (%s)', $value, static::class));
			}

			$this->id = $value; // phpcs:ignore
		}
	}

	protected(set) Lang $lang {
		set(Lang|string $lang){

			if(!$lang instanceof Lang){
				$lang = new Lang($lang);
			}

			$this->lang = $lang;
		}
	}

	public function __construct(int $id, Lang|string $lang = Lang::EN){
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

		return static::NAME[$this->id][$lang->id];
	}

	public function is(int $id):bool{
		return $this->id === $id;
	}

	public function in(array $ids):bool{ // phpcs:ignore
		return in_array($this->id, $ids, true);
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

}
