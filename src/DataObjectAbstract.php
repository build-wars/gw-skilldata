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

/**
 * Abstract parent to the Attribute, Campaign, Profession and Skilltype classes
 */
abstract class DataObjectAbstract{

	/** @var array<int, array{de: string, en: string}> */
	public const NAME = [];

	public function __construct(
		public    readonly int    $id,
		protected readonly string $lang = SkillDataInterface::LANG_EN,
	){
		if(!array_key_exists($this->id, static::NAME)){
			throw new InvalidArgumentException('invalid ID');
		}

		if(!array_key_exists($this->lang, SkillDataInterface::LANGUAGES)){
			throw new InvalidArgumentException('invalid language');
		}
	}

	public function getName(string|null $lang = null):string{

		if($lang !== null && !array_key_exists($lang, SkillDataInterface::LANGUAGES)){
			throw new InvalidArgumentException('invalid language');
		}

		return static::NAME[$this->id][($lang ?? $this->lang)];
	}

}
