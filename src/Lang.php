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
use function sprintf;
use function strtolower;
use function trim;

/**
 * Encapsulates the available skill data languages
 */
final class Lang{

	public const string DE = 'de';
	public const string EN = 'en';
	public const string FR = 'fr';

	public const array IDS = [
		self::DE,
		self::EN,
		self::FR,
	];

	/**
	 * @var array<string, array<string, string>>
	 */
	public const array NAMES = [
		self::DE => [self::DE => 'Deutsch' ,    self::EN => 'German',  self::FR => 'Allemande',],
		self::EN => [self::DE => 'Englisch',    self::EN => 'English', self::FR => 'Anglaise', ],
		self::FR => [self::DE => 'Französisch', self::EN => 'French',  self::FR => 'Français', ],
	];

	public const PVP_SUFFIX = [
	public const array PVP_SUFFIX = [
		self::DE => '%s (PvP)',
		self::EN => '%s (PvP)',
		self::FR => '%s (PvP)',
	];

	protected(set) string $id {
		set{
			$value = trim(strtolower($value));

			if(!in_array($value, self::IDS, true)){
				throw new InvalidArgumentException('invalid language');
			}

			$this->id = $value;
		}
	}

	public function __construct(string $id){
		$this->id = $id;
	}

	/**
	 * Checks whether the object ID is equal to the given ID
	 */
	public function is(string $id):bool{
		return $this->id === $id;
	}

	/**
	 * Checks whether the object ID is in the given array of IDs
	 *
	 * @param string[] $ids
	 */
	public function in(array $ids):bool{
		return in_array($this->id, $ids, true);
	}

	/**
	 * Returns the readable name of the given language ID
	 */
	public function getName(string|null $id = null):string{

		if($id !== null && !$this->in(self::IDS)){
			throw new InvalidArgumentException('invalid language');
		}

		return self::NAMES[$this->id][($id ?? $this->id)];
	}

	/**
	 * Adds a "(PvP)" suffix
	 */
	public function getPvpName(string $name):string{
		return sprintf(self::PVP_SUFFIX[$this->id], $name);
	}


}
