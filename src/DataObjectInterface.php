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

namespace Buildwars\GWSkillData;

interface DataObjectInterface{

	public const string CSS_CLASS = '';
	/** @var array<int, array{de: string, en: string, fr: string}> */
	public const array  NAME      = [];

	public function getName(Lang|string|null $lang = null):string;
	public function is(int $id):bool;
	public function in(array $ids):bool;
	public function toHTML(Lang|string|null $lang = null):string;

}
