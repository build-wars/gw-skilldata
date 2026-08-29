<?php
/**
 * Class SkillLangTest
 *
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\Lang;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LangTest extends TestCase{

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new Lang('foo');
	}

	#[Test]
	public function getName():void{
		$lang = new Lang(Lang::DE);

		$this::assertSame('Deutsch', $lang->getName());
		$this::assertSame('German', $lang->getName(Lang::EN));
	}

}
