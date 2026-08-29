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

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Type;
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
		$this->setSkillDataLanguage(Lang::EN);
	}

	#[Test]
	public function get():void{
		$data = $this->skillData->get(0, true)->toArray();
		$keys = array_merge(Skill::KEYS_DATA, Skill::KEYS_DESC);

		$this::assertSame(0, $data[Skill::DATA_ID]);

		foreach($keys as $key){
			$this::assertArrayHasKey($key, $data);
		}
	}

	#[Test]
	public function getPvpRedirect():void{
		$data = $this->skillData->get(979, true);

		$this::assertSame(3191, $data->id);
		$this::assertSame('Mistrust (PvP)', $data->name);

		$data = $this->skillData->get(3191, false);

		$this::assertSame(979, $data->id);
		$this::assertSame('Mistrust', $data->name);
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
		$this::assertSame($IDs, array_column($data, Skill::DATA_ID));
	}

	#[Test]
	public function getByCampaign():void{
		$data = $this->skillData->getByCampaign(Campaign::CORE);

		foreach($data as $skill){
			$this::assertSame(Campaign::CORE, $skill->campaign->id);
		}
	}

	#[Test]
	public function getByProfession():void{
		$data = $this->skillData->getByProfession(Profession::MESMER);

		foreach($data as $skill){
			$this::assertSame(Profession::MESMER, $skill->profession->id);
		}
	}

	#[Test]
	public function getByAttribute():void{
		$data = $this->skillData->getByAttribute(Attribute::FAST_CASTING);

		foreach($data as $skill){
			$this::assertSame(Attribute::FAST_CASTING, $skill->attribute->id);
		}
	}

	#[Test]
	public function getByType():void{
		$data = $this->skillData->getByType(Type::HEX_SPELL);

		foreach($data as $skill){
			$this::assertSame(Type::HEX_SPELL, $skill->type->id);
		}
	}

	#[Test]
	public function getByTypeWithSubtypes():void{
		$data     = $this->skillData->getByTypeWithSubtypes(Type::TOUCH_SKILL);
		$expected = [
			Type::TOUCH_SKILL, Type::TOUCH_SPELL, Type::TOUCH_ENCHANTMENT_SPELL,
			Type::TOUCH_HEX_SPELL, Type::TOUCH_SIGNET,
		];

		foreach($data as $skill){
			$this::assertContains($skill->type->id, $expected);
		}
	}

	#[Test]
	public function getElite():void{
		$data = $this->skillData->getElite();

		foreach($data as $skill){
			$this::assertTrue($skill->is_elite);
		}
	}

	#[Test]
	public function getRoleplay():void{
		$data = $this->skillData->getRoleplay();

		foreach($data as $skill){
			$this::assertTrue($skill->is_rp);
		}
	}

	#[Test]
	public function getFieldName():void{
		$skill = $this->skillData->get(42);

		$this::assertSame('PvP ID', $skill->getFieldName(Skill::DATA_SPLIT_ID));
	}

}
