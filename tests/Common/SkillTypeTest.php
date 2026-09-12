<?php
/**
 * Class SkillTypeTest
 *
 * @created      22.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest\Common;

use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\SkillType;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SkillTypeTest extends TestCase{

	#[Test]
	public function constructInvalidIdException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageIsOrContains('invalid ID');
		/** @phan-suppress-next-line PhanNoopNew */
		new SkillType(666);
	}

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageIsOrContains('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new SkillType(SkillType::SIGNET, 'foo');
	}

	#[Test]
	public function getName():void{
		$skilltype = new SkillType(SkillType::SIGNET);

		$this::assertSame('Signet', $skilltype->getName());
		$this::assertSame('Siegel', $skilltype->getName(Lang::DE));
	}

	#[Test]
	public function getNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessageIsOrContains('invalid language');

		(new SkillType(SkillType::SIGNET))->getName('foo');
	}

	#[Test]
	public function withSubtypes():void{
		$types = (new SkillType(SkillType::TOUCH_SKILL))->withSubtypes();

		$expected = [
			SkillType::TOUCH_SKILL,
			SkillType::TOUCH_SPELL,
			SkillType::TOUCH_ENCH,
			SkillType::TOUCH_HEX,
			SkillType::TOUCH_SIGNET,
		];

		$this::assertSame($expected, $types);
	}

}
