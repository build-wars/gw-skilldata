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
abstract class DataObjectAbstract{

	/** @var array<int, array{de: string, en: string}> */
	public const NAME      = [];
	public const CSS_CLASS = '';

	public readonly int  $id;
	public readonly Lang $lang;

	public function __construct(int $id, Lang|string $lang = Lang::EN){

		if(!array_key_exists($id, static::NAME)){
			throw new InvalidArgumentException('invalid ID');
		}

		if(!$lang instanceof Lang){
			$lang = new Lang($lang);
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

		return static::NAME[$this->id][$lang->id];
	}

	public function is(int $id):bool{
		return $this->id === $id;
	}

	/** @param int[] $ids */
	public function in(array $ids):bool{
		return in_array($this->id, $ids, true);
	}

	public function toHTML(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return sprintf('<span class="%s">%s</span>', static::CSS_CLASS, $this->getName($lang));
	}

}
