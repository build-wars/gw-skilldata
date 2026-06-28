<?php
/**
 * example.php
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types = 1);

use Buildwars\GWSkillData\SkillDataAwareInterface;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use Buildwars\GWSkillData\SkillDataInterface;

require_once __DIR__.'/../vendor/autoload.php';

class MyClass implements SkillDataAwareInterface{
	use SkillDataAwareTrait;

	public function __construct(string $lang){
		// set the language and initialize $this->skillData
		$this->setSkillDataLanguage($lang);
	}

	public function getSkill(int $skillID, bool $pvp = false):array{
		// $this->skillData is now available
		$data = $this->skillData->get($skillID, $pvp);

		// do stuff with the $data array
		// the available array keys are in $this->skillData->keys
		return $data;
	}
}

// invoke the class and do stuff
$db = new MyClass(SkillDataInterface::LANG_EN);

var_dump($db->getSkill(47));
