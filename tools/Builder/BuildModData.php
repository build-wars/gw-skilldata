<?php
/**
 * Class BuildModData
 *
 * @created      07.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Common\DataObjectInterface;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Equipment\Mod;
use Buildwars\GWSkillData\Equipment\ModData;
use function sprintf;
use const Buildwars\GWSkillDataTools\DATADIR;

final class BuildModData extends BuilderAbstract{

	protected const string JSON_MODDATA_FILE = DATADIR.'/json-full/moddata.json';

	/**
	 * Currently supported languages
	 */
	private const array LANGUAGES = [Lang::DE, Lang::EN, Lang::ES, Lang::FR, Lang::IT, Lang::XX];

	public function build():static{

		$jsonData = [
			'$schema' => static::SCHEMA_MODDATA,
			'moddata' => [],
		];

		$db = [];

		foreach(self::LANGUAGES as $lang){
			$db[$lang] = new ModData($lang);
		}

		foreach($db[Lang::EN]->getIDs() as $id){
			$data = $db[Lang::EN]->get($id);
			$item = [];

			foreach(Mod::KEYS_DATA as $key){
				$val = $data->{$key};

				if($val instanceof DataObjectInterface){
					$val = $data->{$key}->id;
				}

				$item[$key] = $val;
			}

			foreach(self::LANGUAGES as $lang){
				$langdata = $db[$lang]->get($id);

				$item['lang'][$lang] = [
					Mod::DESC_NAME  => $langdata->getItemName(),
					Mod::DESC_AFFIX => $langdata->getAffix(),
				];
			}


			$jsonData['moddata'][$id] = $item;

			unset($jsonData['moddata'][$id][Mod::DATA_EFFECTS]);
		}

		$savepath = $this->saveJSON(self::JSON_MODDATA_FILE, $jsonData);

		$this->logger->info(sprintf('saved JSON for item data to: %s', $savepath));

		return $this;
	}

}
