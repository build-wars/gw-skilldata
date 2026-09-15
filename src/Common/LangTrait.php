<?php
/**
 * LangTrait.php
 *
 * @created      15.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use RuntimeException;

trait LangTrait{

	protected(set) Lang $lang {
		set(Lang|string $lang){

			if(!$lang instanceof Lang){
				$lang = new Lang($lang);
			}

			$this->lang = $lang;
		}
	}

	protected function getLang(Lang|string|null $lang):Lang{

		if($lang === null){

			if(!isset($this->lang)){
				throw new RuntimeException('language not set');
			}

			return $this->lang;
		}

		if($lang instanceof Lang){
			return $lang;
		}

		return new Lang($lang);
	}

}
