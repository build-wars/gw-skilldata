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
use Buildwars\GWSkillData\Skilltype;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SkilltypeTest extends TestCase{

	#[Test]
	public function getName():void{
		$skilltype = new Skilltype(Skilltype::SIGNET);

		$this::assertSame('Signet', $skilltype->getName());
		$this::assertSame('Siegel', $skilltype->getName(Lang::DE));
	}

}
