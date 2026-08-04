<?php
/**
 * Class Lang
 *
 * @created      24.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use InvalidArgumentException;
use function in_array;
use function strtolower;
use function trim;

/**
 * Encapsulates the available skill data languages
 */
final class Lang{

	public const DE = 'de';
	public const EN = 'en';

	public const IDS = [
		self::DE,
		self::EN,
	];

	/** @var array<string, string> */
	public const NAMES = [
		self::DE => 'German',
		self::EN => 'English',
	];

	public readonly string $id;

	public function __construct(string $id){
		$id = trim(strtolower($id));

		if(!in_array($id, self::IDS, true)){
			throw new InvalidArgumentException('invalid language');
		}

		$this->id = $id;
	}

	public function getName():string{
		return self::NAMES[$this->id];
	}

}
