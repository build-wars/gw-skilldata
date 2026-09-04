<?php
/**
 * Class SkillTest
 *
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTest;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Campaign;
use Buildwars\GWSkillData\DataObjectAbstract;
use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Profession;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\Type;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SkillTest extends TestCase{

	private const dataObjects = [
		'attribute'  => Attribute::class,
		'campaign'   => Campaign::class,
		'profession' => Profession::class,
		'type'       => Type::class,
	];

	private const skillData = [
		'id'                 => 979,
		'campaign'           => 3,
		'profession'         => 5,
		'attribute'          => 2,
		'is_elite'           => false,
		'is_rp'              => false,
		'is_pvp'             => false,
		'pvp_split'          => true,
		'split_id'           => 3191,
		'type'               => 24,
		'upkeep'             => 0,
		'energy'             => 10,
		'activation'         => 2,
		'aftercast'          => 0.75,
		'recharge'           => 12,
		'adrenaline'         => 0,
		'adrenaline_precise' => 0,
		'sacrifice'          => 0,
		'overcast'           => 0,
		'name'               => 'Mistrust',
		'description'        => 'For 6 seconds, the next spell that target foe casts on one of your allies fails '.
		                        'and deals 10...80 damage to that foe and 75% of that damage to all nearby foes.',
		'concise'            => '(6 seconds.) The next spell that target foe casts on one of your allies fails '.
		                        'and deals 10...80 damage to target and 75% of that damage to nearby foes.',
	];

	#[Test]
	public function construct():void{
		$skill = new Skill(self::skillData);

		// only checking if the instances have been properly invoked
		$this::assertInstanceOf(Lang::class, $skill->lang);
		$this::assertSame(Lang::EN, $skill->lang->id);

		foreach(self::dataObjects as $property => $fqcn){
			$this::assertInstanceOf(DataObjectAbstract::class, $skill->{$property});
			$this::assertInstanceOf($fqcn, $skill->{$property});
		}
	}

	#[Test]
	public function toArray():void{
		$skill = new Skill(self::skillData);

		$this::assertSame(self::skillData, $skill->toArray());
	}

}
