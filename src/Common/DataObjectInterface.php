<?php
/**
 * Interface DataObjectInterface
 *
 * @created      01.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

/**
 * @property int                                $id
 * @property \Buildwars\GWSkillData\Common\Lang $lang
 */
interface DataObjectInterface extends IDComparisonInterface{

	public const string CSS_CLASS = '';
	/** @var array<int, array{de: string, en: string, fr: string}> */
	public const array  NAME      = [];

	/**
	 * Checks whether the given constant is part of the current class
	 *
	 * The constants are usually keys in the NAME array, but might enumerate other arrays instead.
	 *
	 * @see \Buildwars\GWSkillData\Common\DataObjectInterface::NAME
	 */
	public static function has(int $constant):bool;

	/**
	 * Returns the readable name of the given ID
	 */
	public function getName(Lang|string|null $lang = null):string;

	public function toHTML(Lang|string|null $lang = null):string;

}
