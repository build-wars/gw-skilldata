<?php
/**
 * Class SkillDataLang
 *
 * @created      24.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use InvalidArgumentException;
use function array_key_exists;

/**
 * Encapsulates the available skill data languages
 */
final class SkillLang{

	public const DE = 'de';
	public const EN = 'en';

	/** @var array<string, string> */
	public const NAMES = [
		self::DE => 'German',
		self::EN => 'English',
	];

	public function __construct(public readonly string $id){

		if(!array_key_exists($this->id, self::NAMES)){
			throw new InvalidArgumentException('invalid language');
		}

	}

	public function getName():string{
		return self::NAMES[$this->id];
	}

}
