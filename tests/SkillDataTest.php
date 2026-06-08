<?php
/**
 * Class SkillDataTest
 *
 * @created      02.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\SkillDataAwareTrait;
use Buildwars\GWSkillData\SkillDataInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function array_column;
use function array_merge;

/**
 * Tests basic functions of the SkillData class
 */
class SkillDataTest extends TestCase{
	use SkillDataAwareTrait;

	protected function setUp():void{
		$this->setSkillDataLanguage('en');
	}

	#[Test]
	public function get():void{
		$data = $this->skillData->get(0, true);
		$keys = array_merge(SkillDataInterface::KEYS_DATA, SkillDataInterface::KEYS_DESC, SkillDataInterface::KEYS_NAMES);

		$this::assertSame(0, $data['id']);

		foreach($keys as $key){
			$this::assertArrayHasKey($key, $data);
		}
	}

	#[Test]
	public function getPvpRedirect():void{
		$data = $this->skillData->get(979, true);

		$this::assertSame(3191, $data['id']);
	}

	#[Test]
	public function invalidIdException():void{
		$this->expectException(InvalidArgumentException::class);

		$this->skillData->get(69420);
	}

	#[Test]
	public function getAll():void{
		$IDs  = [782, 780, 775, 1954, 952, 2356, 1649, 1018];
		$data = $this->skillData->getAll($IDs);

		$this::assertCount(count($IDs), $data);
		$this::assertSame($IDs, array_column($data, 'id'));
	}

	#[Test]
	public function getByCampaign():void{
		$data = $this->skillData->getByCampaign(0);

		foreach($data as $skill){
			$this::assertSame(0, $skill['campaign']);
		}
	}

	#[Test]
	public function getByProfession():void{
		$data = $this->skillData->getByProfession(5);

		foreach($data as $skill){
			$this::assertSame(5, $skill['profession']);
		}
	}

	#[Test]
	public function getByAttribute():void{
		$data = $this->skillData->getByAttribute(0);

		foreach($data as $skill){
			$this::assertSame(0, $skill['attribute']);
		}
	}

	#[Test]
	public function getByType():void{
		$data = $this->skillData->getByType(24);

		foreach($data as $skill){
			$this::assertSame(24, $skill['type']);
		}
	}

	#[Test]
	public function getElite():void{
		$data = $this->skillData->getElite();

		foreach($data as $skill){
			$this::assertTrue($skill['is_elite']);
		}
	}

	#[Test]
	public function getRoleplay():void{
		$data = $this->skillData->getRoleplay();

		foreach($data as $skill){
			$this::assertTrue($skill['is_rp']);
		}
	}

}
