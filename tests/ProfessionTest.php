<?php
/**
 * Class ProfessionTest
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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProfessionTest extends TestCase{

	#[Test]
	public function constructInvalidIdException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid ID');
		/** @phan-suppress-next-line PhanNoopNew */
		new Profession(666);
	}

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new Profession(Profession::ELEMENTALIST, 'foo');
	}

	#[Test]
	public function getName():void{
		$profession = new Profession(Profession::ELEMENTALIST);

		$this::assertSame('Elementalist', $profession->getName());
		$this::assertSame('Elementarmagier', $profession->getName(Lang::DE));
	}

	#[Test]
	public function getNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Profession(Profession::ELEMENTALIST))->getName('foo');
	}

	#[Test]
	public function getAbbr():void{
		$profession = new Profession(Profession::ELEMENTALIST);

		$this::assertSame('E', $profession->getAbbr());
		$this::assertSame('E', $profession->getAbbr(Lang::DE));
	}

	#[Test]
	public function getAbbrInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Profession(Profession::ELEMENTALIST))->getAbbr('foo');
	}

	#[Test]
	public function getPrimaryAttribute():void{
		$profession = new Profession(Profession::ELEMENTALIST);
		$attribute  = $profession->getPrimaryAttribute(16);

		$this::assertSame(Attribute::ENERGY_STORAGE, $attribute->id);
		$this::assertSame(16, $attribute->getLevel());
	}

	#[Test]
	public function getPrimaryAttributeID():void{
		$profession = new Profession(Profession::ELEMENTALIST);

		$this::assertSame(Attribute::ENERGY_STORAGE, $profession->getPrimaryAttributeID());
	}

	#[Test]
	public function getAttributes():void{
		$profession = new Profession(Profession::ELEMENTALIST);

		$expected = [
			Attribute::AIR_MAGIC,
			Attribute::EARTH_MAGIC,
			Attribute::FIRE_MAGIC,
			Attribute::WATER_MAGIC,
			Attribute::ENERGY_STORAGE,
		];

		$this::assertSame($expected, $profession->getAttributes());
	}

}
