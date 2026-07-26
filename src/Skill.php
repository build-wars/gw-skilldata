<?php
/**
 * Class Skill
 *
 * @created      24.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use function array_key_exists;
use function array_merge;
use function property_exists;

/**
 * Represents a single skill with all its unmodified data
 */
final class Skill{

	public const MODE_PVE = 'pve';
	public const MODE_PVP = 'pvp';

	public const DATA_ATTRIBUTE          = 'attribute';
	public const DATA_CAMPAIGN           = 'campaign';
	public const DATA_PROFESSION         = 'profession';
	public const DATA_TYPE               = 'type';
	public const DATA_IS_ELITE           = 'is_elite';
	public const DATA_IS_PVP             = 'is_pvp';
	public const DATA_IS_RP              = 'is_rp';
	public const DATA_PVP_SPLIT          = 'pvp_split';
	public const DATA_ID                 = 'id';
	public const DATA_SPLIT_ID           = 'split_id';
	public const DATA_ACTIVATION         = 'activation';
	public const DATA_RECHARGE           = 'recharge';
	public const DATA_ENERGY             = 'energy';
	public const DATA_UPKEEP             = 'upkeep';
	public const DATA_ADRENALINE         = 'adrenaline';
	public const DATA_ADRENALINE_PRECISE = 'adrenaline_precise';
	public const DATA_SACRIFICE          = 'sacrifice';
	public const DATA_EXHAUSTION         = 'overcast';

	public const DESC_NAME               = 'name';
	public const DESC_DESCRIPTION        = 'description';
	public const DESC_CONCISE            = 'concise';

	private const DataObjects = [
		self::DATA_ATTRIBUTE  => Attribute::class,
		self::DATA_CAMPAIGN   => Campaign::class,
		self::DATA_PROFESSION => Profession::class,
		self::DATA_TYPE       => Skilltype::class,
	];

	public readonly SkillLang  $lang;

	public readonly Attribute  $attribute;
	public readonly Campaign   $campaign;
	public readonly Profession $profession;
	public readonly Skilltype  $type;
	public readonly bool       $is_elite;
	public readonly bool       $is_pvp;
	public readonly bool       $is_rp;
	public readonly bool       $pvp_split;
	public readonly int        $id;
	public readonly int        $split_id;
	public readonly int|float  $activation;
	public readonly int        $recharge;
	public readonly int        $energy;
	public readonly int        $upkeep;
	public readonly int        $adrenaline;
	public readonly int|float  $adrenaline_precise;
	public readonly int        $sacrifice;
	public readonly int        $overcast;

	public readonly string     $name;
	public readonly string     $description;
	public readonly string     $concise;

	public function __construct(array $skilldata, SkillLang|string $lang){

		if(!$lang instanceof SkillLang){
			$lang = new SkillLang($lang);
		}

		$this->lang = $lang;

		foreach($skilldata as $key => $val){
			if(property_exists($this, $key)){
				if(array_key_exists($key, self::DataObjects) && !$val instanceof (self::DataObjects[$key])){
					$val = new (self::DataObjects[$key])($val);
				}

				$this->{$key} = $val;
			}
		}

	}

	public function toArray():array{
		$data = [];

		foreach(array_merge(SkillDataInterface::KEYS_DATA, SkillDataInterface::KEYS_DESC) as $key){
			$value = $this->{$key};

			if($value instanceof DataObjectAbstract){
				$value = $this->{$key}->id;
			}

			$data[$key] = $value;
		}

		return $data;
	}

#	public function getFieldName(string $field, string|null $lang = null):string{}


}
