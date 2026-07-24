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

/**
 * Abstract parent to the Attribute, Campaign, Profession and Skilltype classes
 */
abstract class DataObjectAbstract{

	/** @var array<int, array{de: string, en: string}> */
	public const NAME = [];

	public readonly int $id;
	public readonly SkillLang $lang;

	public function __construct(int $id, SkillLang|string $lang = SkillLang::EN){

		if(!array_key_exists($id, static::NAME)){
			throw new InvalidArgumentException('invalid ID');
		}

		if(!$lang instanceof SkillLang){
			$lang = new SkillLang($lang);
		}

		$this->id   = $id;
		$this->lang = $lang;
	}

	public function getName(string|null $lang = null):string{

		if($lang !== null){
			$lang = new SkillLang($lang);
		}

		return static::NAME[$this->id][($lang->id ?? $this->lang->id)];
	}

	public function is(int $id):bool{
		return $this->id === $id;
	}

	/** @param int[] $ids */
	public function in(array $ids):bool{
		return in_array($this->id, $ids, true);
	}

}
