<?php
/**
 * Class AttributeTest
 *
 * @created      22.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\SkillDataInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase{

	#[Test]
	public function constructInvalidIdException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid ID');
		/** @phan-suppress-next-line PhanNoopNew */
		new Attribute(666);
	}

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new Attribute(Attribute::FAST_CASTING, 'foo');
	}

	#[Test]
	public function getName():void{
		$attr = new Attribute(Attribute::FAST_CASTING);

		$this::assertSame('Fast Casting', $attr->getName());
		$this::assertSame('Schnellwirkung', $attr->getName(SkillDataInterface::LANG_DE));
	}

	#[Test]
	public function getNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Attribute(Attribute::FAST_CASTING))->getName('foo');
	}

	#[Test]
	#[TestWith([Attribute::FAST_CASTING, Profession::MESMER])]
	#[TestWith([Attribute::NONE, Profession::NONE])]
	#[TestWith([Attribute::TITLE_LUXON, Profession::NONE])]
	public function getProfession(int $attribute, int $expected):void{
		$this::assertSame($expected, (new Attribute($attribute))->getProfession());
	}

	#[Test]
	#[TestWith([Attribute::FAST_CASTING, 21])]
	#[TestWith([Attribute::BEAST_MASTERY, 20])]
	#[TestWith([Attribute::NONE, 0])]
	#[TestWith([Attribute::TITLE_LIGHTBRINGER, 8])]
	public function getMaxValue(int $attribute, int $expected):void{
		$this::assertSame($expected, (new Attribute($attribute))->getMaxValue());
	}

	#[Test]
	#[TestWith([Profession::MESMER, [
		Attribute::FAST_CASTING,
		Attribute::ILLUSION_MAGIC,
		Attribute::DOMINATION_MAGIC,
		Attribute::INSPIRATION_MAGIC,
	]])]
	#[TestWith([Profession::NONE, [
		Attribute::NONE,
		Attribute::TITLE_SUNSPEAR,
		Attribute::TITLE_LIGHTBRINGER,
		Attribute::TITLE_LUXON,
		Attribute::TITLE_KURZICK,
		Attribute::TITLE_ASURA,
		Attribute::TITLE_DELDRIMOR,
		Attribute::TITLE_VANGUARD,
		Attribute::TITLE_NORN,
	]])]
	public function getByProfession(int $profession, array $expected):void{
		$this::assertSame($expected, Attribute::getByProfession($profession));
	}

	#[Test]
	#[TestWith([Attribute::FAST_CASTING, true])]
	#[TestWith([Attribute::DOMINATION_MAGIC, false])]
	public function isPrimary(int $attribute, bool $expected):void{
		$this::assertSame($expected, (new Attribute($attribute))->isPrimary());
	}

	public static function clampValueProvider():array{
		return [
			'none'                 => [Attribute::NONE, 42, 69, 0],
			'lightbringer'         => [Attribute::TITLE_LIGHTBRINGER, 42, 69, 8],
			'norn'                 => [Attribute::TITLE_NORN, 42, 69, 10],
			'luxon'                => [Attribute::TITLE_LUXON, 42, 69, 12],
			'default'              => [Attribute::FAST_CASTING, 42, null, 21],
			'default max override' => [Attribute::FAST_CASTING, 42, 69, 30],
			'negative'             => [Attribute::FAST_CASTING, -42, null, 0],
		];
	}

	#[Test]
	#[DataProvider('clampValueProvider')]
	public function clamp(int $id, int $level, int|null $overrideMax, int $expected):void{
		$this::assertSame($expected, (new Attribute($id))->clamp($level, $overrideMax));
	}

	#[Test]
	#[TestWith([Attribute::TITLE_LIGHTBRINGER, 0.0, 15.0])]
	#[TestWith([Attribute::TITLE_NORN, 0.0, 15.0])]
	#[TestWith([Attribute::TITLE_LUXON, 0.0, 15.0])]
	#[TestWith([Attribute::FAST_CASTING, 0.0, 21.0])]
	public function getProgressionFunction(int $attribute, float $expected0, float $expected15):void{
		$attr = new Attribute($attribute);
		$fn   = $attr->getProgressionFunction();

		$this::assertSame($expected0, $fn(0, 0, 15));
		$this::assertSame($expected15, $fn($attr->getMaxValue(), 0, 15));
	}

}
