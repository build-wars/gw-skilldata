<?php
/**
 * Class CreateKnownSkills
 *
 * @created      03.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\SkillDataAwareInterface;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use chillerlan\Utilities\Str;
use function array_values;
use function in_array;
use function ksort;
use function str_replace;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;

final class BuildKnownSkills extends BuilderAbstract implements SkillDataAwareInterface{
	use SkillDataAwareTrait;

	// @todo: allow to add new skills
	public function build():static{

		$de = $this->getGWDB(Lang::DE_GUILDWIKI);
		$en = $this->getGWDB(Lang::EN_GWW);
		$fr = $this->getGWDB(Lang::FR_GWIKI);

		$arr = [];

		foreach($en->getIDs() as $id){
			$is_pvp = in_array($id, PVP_SPLIT, true);
			$data   = $en->get($id, $is_pvp);

			$arr[$id] = [
				$id,
				$data->type->id,
				$data->name,
				$de->get($id, $is_pvp)->name,
				$fr->get($id, $is_pvp)->name,
			];
		}

		ksort($arr);

		$json = Str::jsonEncode(array_values($arr));
		// a lil formatting as pretty print is rather line heavy
		$json = str_replace(["\n        ", "\"\n    ", ','], ['', '"', ', '], $json);

		$this->saveFile(self::KNOWN_SKILLS_JSON, $json);

		return $this;
	}

}
