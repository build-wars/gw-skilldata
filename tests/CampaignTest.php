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
use Buildwars\GWSkillData\Lang;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CampaignTest extends TestCase{

	#[Test]
	public function constructInvalidIdException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid ID');
		/** @phan-suppress-next-line PhanNoopNew */
		new Campaign(666);
	}

	#[Test]
	public function constructInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');
		/** @phan-suppress-next-line PhanNoopNew */
		new Campaign(Campaign::CORE, 'foo');
	}

	#[Test]
	public function getName():void{
		$campaign = new Campaign(Campaign::CORE);

		$this::assertSame('Core', $campaign->getName());
		$this::assertSame('Basis', $campaign->getName(Lang::DE));
	}

	#[Test]
	public function getNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Campaign(Campaign::CORE))->getName('foo');
	}

	#[Test]
	public function getContinentName():void{
		$campaign = new Campaign(Campaign::CORE);

		$this::assertSame('The Mists', $campaign->getContinentName());
		$this::assertSame('Die Nebel', $campaign->getContinentName(Lang::DE));
	}

	#[Test]
	public function getContinentNameInvalidLanguageException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid language');

		(new Campaign(Campaign::CORE))->getContinentName('foo');
	}

}
