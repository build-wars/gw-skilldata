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

use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\SkillDataInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProfessionTest extends TestCase{

	#[Test]
	public function getName():void{
		$profession = new Profession(Profession::ELEMENTALIST);

		$this::assertSame('Elementalist', $profession->getName());
		$this::assertSame('Elementarmagier', $profession->getName(SkillDataInterface::LANG_DE));
	}

}
