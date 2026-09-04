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
use Buildwars\GWSkillData\Lang;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class AttributeTest extends TestCase{

	#[Test]
	public function constructInvalidIdException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageIsOrContains('invalid ID');
		/** @phan-suppress-next-line PhanNoopNew */
		new Attribute(666);
	}

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageIsOrContains('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new Attribute(Attribute::FAST_CASTING, 'foo');
	}

	#[Test]
	public function getName():void{
		$attr = new Attribute(Attribute::FAST_CASTING);

		$this::assertSame('Fast Casting', $attr->getName());
		$this::assertSame('Schnellwirkung', $attr->getName(Lang::DE));
	}

	#[Test]
	public function getNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageIsOrContains('invalid language');

		(new Attribute(Attribute::FAST_CASTING))->getName('foo');
	}

	#[Test]
	public function setLevel():void{
		$attr = new Attribute(Attribute::FAST_CASTING);

		$attr->setLevel(69); // test clamping

		$this::assertSame(21, $attr->level);
	}

	#[Test]
	public function addLevel():void{
		$attr = new Attribute(Attribute::FAST_CASTING);

		$attr->setLevel(12);
		$attr->addLevel(4);

		$this::assertSame(16, $attr->lang);
	}

	public static function professionProvider():array{
		return [
			'default' => [Attribute::FAST_CASTING, Profession::MESMER],
			'none'    => [Attribute::NONE, Profession::NONE],
			'title'   => [Attribute::TITLE_LUXON, Profession::NONE],
		];
	}

	#[Test]
	#[DataProvider('professionProvider')]
	public function getProfessionID(int $attribute, int $expected):void{
		$this::assertSame($expected, (new Attribute($attribute))->getProfessionID());
	}

	#[Test]
	#[DataProvider('professionProvider')]
	public function getProfession(int $attribute, int $expected):void{
		$this::assertTrue((new Attribute($attribute))->getProfession()->is($expected));
	}

	#[Test]
	#[TestWith([Attribute::FAST_CASTING, 21])]
	#[TestWith([Attribute::BEAST_MASTERY, 20])]
	#[TestWith([Attribute::NONE, 0])]
	#[TestWith([Attribute::TITLE_LIGHTBRINGER, 8])]
	public function getMaxValue(int $attribute, int $expected):void{
		$this::assertSame($expected, (new Attribute($attribute))->getMaxValue());
	}

	/**
	 * @param int[] $expected
	 *
	 * @return void
	 */
	#[Test]
	#[TestWith([Profession::MESMER, [
		Attribute::FAST_CASTING, Attribute::ILLUSION_MAGIC,
		Attribute::DOMINATION_MAGIC, Attribute::INSPIRATION_MAGIC,
	]])]
	#[TestWith([Profession::NONE, [
		Attribute::NONE, Attribute::TITLE_SUNSPEAR, Attribute::TITLE_LIGHTBRINGER,
		Attribute::TITLE_LUXON, Attribute::TITLE_KURZICK, Attribute::TITLE_ASURA,
		Attribute::TITLE_DELDRIMOR, Attribute::TITLE_VANGUARD, Attribute::TITLE_NORN,
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
			'none'                 => [Attribute::NONE, 42, 0],
			'lightbringer'         => [Attribute::TITLE_LIGHTBRINGER, 42, 8],
			'norn'                 => [Attribute::TITLE_NORN, 42, 10],
			'luxon'                => [Attribute::TITLE_LUXON, 42, 12],
			'default'              => [Attribute::FAST_CASTING, 42, 21],
			'negative'             => [Attribute::FAST_CASTING, -42, 0],
		];
	}

	#[Test]
	#[DataProvider('clampValueProvider')]
	public function clamp(int $id, int $level, int $expected):void{
		$this::assertSame($expected, (new Attribute($id))->clamp($level));
	}

	#[Test]
	#[TestWith([Attribute::TITLE_LIGHTBRINGER, 15])]
	#[TestWith([Attribute::TITLE_NORN, 15])]
	#[TestWith([Attribute::TITLE_LUXON, 15])]
	#[TestWith([Attribute::FAST_CASTING, 21])]
	public function getProgressionFunction(int $attribute, int $expected15):void{
		$attr = new Attribute($attribute);
		$fn   = $attr->getProgressionFunction();

		$this::assertSame(0, $fn(0, 0, 15));
		$this::assertSame($expected15, $fn($attr->getMaxValue(), 0, 15));
	}

	public static function progressionValueProvider():array{
		return [
			// standard progression -> https://wiki.guildwars.com/wiki/Ineptitude
			'standard'     => [Attribute::ILLUSION_MAGIC, 30, 135, [0 => 30, 12 => 114, 15 => 135, 21 => 177]],
			// PvE attribute: luxon/kurzick -> https://wiki.guildwars.com/wiki/Summon_Spirits
			'factions'     => [Attribute::TITLE_LUXON, 60, 100, [0 => 60, 3 => 79, 6 => 100, 12 => 100]],
			// PvE attribute: lightbringer -> https://wiki.guildwars.com/wiki/Lightbringer_Signet
			'lightbringer' => [Attribute::TITLE_LIGHTBRINGER, 16, 24, [0 => 16, 3 => 22, 4 => 24, 8 => 24]],
			// PvE attribute: sunspear -> https://wiki.guildwars.com/wiki/Vampirism
			'pve1'         => [Attribute::TITLE_SUNSPEAR, 75, 150, [0 => 75, 3 => 120, 5 => 150, 10 => 150]],
			// PvE attribute: eotn -> https://wiki.guildwars.com/wiki/Dwarven_Stability
			'pve2'         => [Attribute::TITLE_DELDRIMOR, 55, 100, [0 => 55, 3 => 82, 5 => 100, 10 => 100]],
		];
	}

	/**
	 * @param array<int, int> $expected
	 */
	#[Test]
	#[DataProvider('progressionValueProvider')]
	public function getProgressionValue(int $attribute, int $val0, int $val15, array $expected):void{
		$attr = new Attribute($attribute);

		foreach($expected as $level => $expectedValue){
			$value = $attr->setLevel($level)->getProgressionValue($val0, $val15);

			$this::assertSame($expectedValue, $value);

			// alternative
			$value = (new Attribute($attribute))->getProgressionValue($val0, $val15, $level);

			$this::assertSame($expectedValue, $value);
		}
	}

	/**
	 * @param array<int, int> $expected
	 */
	#[Test]
	#[DataProvider('progressionValueProvider')]
	public function progressionTable(int $attribute, int $val0, int $val15, array $expected):void{
		$table = (new Attribute($attribute))->getProgressionTable($val0, $val15);

		foreach($expected as $level => $expectedValue){
			$this::assertSame($expectedValue, $table[$level]);
		}

	}

}
