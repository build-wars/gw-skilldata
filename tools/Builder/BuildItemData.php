<?php
/**
 * Class BuildItemData
 *
 * @created      06.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Common\DataObjectInterface;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Equipment\Item;
use Buildwars\GWSkillData\Equipment\ItemData;
use function sprintf;
use const Buildwars\GWSkillDataTools\DATADIR;

final class BuildItemData extends BuilderAbstract{

	protected const string JSON_ITEMDATA_FILE = DATADIR.'/json-full/itemdata.json';

	/**
	 * Currently supported languages
	 */
	private const array LANGUAGES = [Lang::DE, Lang::EN, Lang::ES, Lang::FR, Lang::IT, Lang::XX];

	public function build():static{

		$jsonData = [
			'$schema'  => static::SCHEMA_ITEMDATA,
			'itemdata' => [],
		];

		$db = [];

		foreach(self::LANGUAGES as $lang){
			$db[$lang] = new ItemData($lang);
		}

		foreach($db[Lang::EN]->getIDs() as $id){
			$data = $db[Lang::EN]->get($id);
			$item = [];

			foreach(Item::KEYS_DATA as $key){
				$val = $data->{$key};

				if($val instanceof DataObjectInterface){
					$val = $data->{$key}->id;
				}

				$item[$key] = $val;
			}

			foreach(self::LANGUAGES as $lang){
				$langdata = $db[$lang]->get($id);

				$item['lang'][$lang] = [
					Item::DESC_NAME  => $langdata->name,
					Item::DESC_AFFIX => $langdata->getAffix(),
				];
			}

			$jsonData['itemdata'][$id] = $item;
		}

		$savepath = $this->saveJSON(static::JSON_ITEMDATA_FILE, $jsonData);

		$this->logger->info(sprintf('saved JSON for item data to: %s', $savepath));

		return $this;
	}

}
