<?php
/**
 * Class SkilltypeTest
 *
 * @created      22.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Type;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase{

	#[Test]
	public function constructInvalidIdException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid ID');
		/** @phan-suppress-next-line PhanNoopNew */
		new Type(666);
	}

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new Type(Type::SIGNET, 'foo');
	}

	#[Test]
	public function getName():void{
		$skilltype = new Type(Type::SIGNET);

		$this::assertSame('Signet', $skilltype->getName());
		$this::assertSame('Siegel', $skilltype->getName(Lang::DE));
	}

	#[Test]
	public function getNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Type(Type::SIGNET))->getName('foo');
	}

	#[Test]
	public function withSubtypes():void{
		$types = (new Type(Type::TOUCH_SKILL))->withSubtypes();

		$expected = [
			Type::TOUCH_SKILL,
			Type::TOUCH_SPELL,
			Type::TOUCH_ENCHANTMENT_SPELL,
			Type::TOUCH_HEX_SPELL,
			Type::TOUCH_SIGNET,
		];

		$this::assertSame($expected, $types);
	}

}
