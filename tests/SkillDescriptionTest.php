<?php
/**
 * Class SkillDescriptionTest
 *
 * @created      01.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\SkillDescription;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

/**
 *
 */
class SkillDescriptionTest extends TestCase{

	protected SkillDescription $skillDescription;

	protected function setUp():void{
		$this->skillDescription = new SkillDescription;
	}

	protected function getReflectionPropertyValue(string $property):mixed{
		return (new ReflectionObject($this->skillDescription))->getProperty($property)->getValue($this->skillDescription);
	}

	protected function invokeReflectionMethod(string $method, array $args = []):mixed{
		return (new ReflectionObject($this->skillDescription))->getMethod($method)->invokeArgs($this->skillDescription, $args);
	}

	public static function professionProvider():array{
		return [
			'invalid values' => [42, 69, 0, 0],
			'same values'    => [5, 5, 5, 0],
			'valid'          => [8, 1, 8, 1],
		];
	}

	#[Test]
	#[DataProvider('professionProvider')]
	public function setProfessions(int $pri, int $sec, int $expectedPri, int $expectedSec):void{
		$this->skillDescription->setProfessions($pri, $sec);

		$this::assertSame($expectedPri, $this->getReflectionPropertyValue('pri'));
		$this::assertSame($expectedSec, $this->getReflectionPropertyValue('sec'));
	}

	#[Test]
	public function setContextSkillbar():void{
		$actual   = [0, '1', null, 5, [], 345, 678, '0', 0, 1234, 42, 69, 0];
		$expected = [0, 5, 345, 678, 0, 1234, 42, 69];

		$this->skillDescription->setContextSkillbar($actual);

		$this::assertSame($expected, $this->getReflectionPropertyValue('contextSkillbar'));
	}

	#[Test]
	public function setAttributes():void{
		$actual   = [0 => 42, 1 => -1, 5 => 'aaa', 69 => 420, 101 => 666, 102 => 20, 103 => 20, 104 => 20, 109 => 20];
		$expected = [0 => 30, 1 => 0, 101 => 0, 102 => 10, 103 => 8, 104 => 12, 109 => 10];

		$this->skillDescription->setAttributes($actual);

		$this::assertSame($expected, $this->getReflectionPropertyValue('attributes'));
	}

	#[Test]
	public function getAttributeLevel():void{
		$this->skillDescription->setAttributes([0 => 10, 2 => 12, 3 => 8, 103 => 8]);

		// invalid attribute
		$this::assertSame(0, $this->invokeReflectionMethod('getAttributeLevel', [99]));
		// given attribute
		$this::assertSame(10, $this->invokeReflectionMethod('getAttributeLevel', [0]));

		// set attribute bonus
		$this->skillDescription->setAttributeBonus(12); // will be clamped at 10

		$this::assertSame(20, $this->invokeReflectionMethod('getAttributeLevel', [0]));
		$this::assertSame(22, $this->invokeReflectionMethod('getAttributeLevel', [2]));
		$this::assertSame(18, $this->invokeReflectionMethod('getAttributeLevel', [3]));
		// no bonus added to the PvE attribute
		$this::assertSame(8, $this->invokeReflectionMethod('getAttributeLevel', [103]));

		// override level (max level clamped)
		$this::assertSame(30, $this->invokeReflectionMethod('getAttributeLevel', [0, 42]));
		$this::assertSame(8, $this->invokeReflectionMethod('getAttributeLevel', [103, 69]));
	}

	#[Test]
	public function getProgression():void{
		// standard progression -> https://wiki.guildwars.com/wiki/Ineptitude
		$this::assertSame(142, $this->invokeReflectionMethod('getProgression', [30, 135, 16, 1]));

		$table = $this->getReflectionPropertyValue('progressions')['30 - 135'];

		$this::assertSame(30, $table[0]);
		$this::assertSame(135, $table[15]);
		$this::assertSame(177, $table[21]);

		// PvE attribute: luxon/kurzick -> https://wiki.guildwars.com/wiki/Summon_Spirits
		$this::assertSame(79, $this->invokeReflectionMethod('getProgression', [60, 100, 3, 104]));

		// PvE attribute: sunspear -> https://wiki.guildwars.com/wiki/Vampirism
		$this::assertSame(120, $this->invokeReflectionMethod('getProgression', [75, 150, 3, 102]));

		// PvE attribute: lightbringer -> https://wiki.guildwars.com/wiki/Lightbringer_Signet
		$this::assertSame(22, $this->invokeReflectionMethod('getProgression', [16, 24, 3, 103]));

		// PvE attribute: eotn -> https://wiki.guildwars.com/wiki/Dwarven_Stability
		$this::assertSame(82, $this->invokeReflectionMethod('getProgression', [55, 100, 3, 107]));
	}

}
