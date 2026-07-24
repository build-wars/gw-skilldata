<?php
/**
 * Class CampaignTest
 *
 * @created      22.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\SkillLang;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CampaignTest extends TestCase{

	#[Test]
	public function getName():void{
		$campaign = new Campaign(Campaign::CORE);

		$this::assertSame('Core', $campaign->getName());
		$this::assertSame('Basis', $campaign->getName(SkillLang::DE));
	}

	#[Test]
	public function getContinentName():void{
		$campaign = new Campaign(Campaign::CORE);

		$this::assertSame('The Mists', $campaign->getContinentName());
		$this::assertSame('Die Nebel', $campaign->getContinentName(SkillLang::DE));
	}

	#[Test]
	public function getContinentNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Campaign(Campaign::CORE))->getContinentName('foo');
	}

}
