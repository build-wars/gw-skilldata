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

use InvalidArgumentException;
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

	public const FIELD_NAMES = [
		self::MODE_PVE                => [
			SkillLang::DE => 'Rollenspiel',
			SkillLang::EN => 'Roleplay',
		],
		self::MODE_PVP                => [
			SkillLang::DE => 'Spieler gegen Spieler',
			SkillLang::EN => 'Player versus Player',
		],
		self::DATA_ATTRIBUTE          => [
			SkillLang::DE => 'Attribut',
			SkillLang::EN => 'Attribute',
		],
		self::DATA_CAMPAIGN           => [
			SkillLang::DE => 'Kampagne',
			SkillLang::EN => 'Campaign',
		],
		self::DATA_PROFESSION         => [
			SkillLang::DE => 'Klasse',
			SkillLang::EN => 'Profession',
		],
		self::DATA_TYPE               => [
			SkillLang::DE => 'Fertigkeitstyp',
			SkillLang::EN => 'Skill type',
		],
		self::DATA_IS_ELITE           => [
			SkillLang::DE => 'ist Elite',
			SkillLang::EN => 'is Elite',
		],
		self::DATA_IS_PVP             => [
			SkillLang::DE => 'ist PvP',
			SkillLang::EN => 'is PvP',
		],
		self::DATA_IS_RP              => [
			SkillLang::DE => 'ist Rollenspiel',
			SkillLang::EN => 'is Roleplay',
		],
		self::DATA_PVP_SPLIT          => [
			SkillLang::DE => 'hat PvP-Version',
			SkillLang::EN => 'has PvP version',
		],
		self::DATA_ID                 => [
			SkillLang::DE => 'Fertigkeits-ID',
			SkillLang::EN => 'Skill ID',
		],
		self::DATA_SPLIT_ID           => [
			SkillLang::DE => 'PvP ID',
			SkillLang::EN => 'PvP ID',
		],
		self::DATA_ACTIVATION         => [
			SkillLang::DE => 'Aktivierungszeit',
			SkillLang::EN => 'Activation time',
		],
		self::DATA_RECHARGE           => [
			SkillLang::DE => 'Wiederaufladezeit',
			SkillLang::EN => 'Recharge time',
		],
		self::DATA_ENERGY             => [
			SkillLang::DE => 'Energiekosten',
			SkillLang::EN => 'Energy cost',
		],
		self::DATA_UPKEEP             => [
			SkillLang::DE => 'Unterhaltskosten',
			SkillLang::EN => 'Upkeep cost',
		],
		self::DATA_ADRENALINE         => [
			SkillLang::DE => 'Adernalinkosten',
			SkillLang::EN => 'Adernaline cost',
		],
		self::DATA_ADRENALINE_PRECISE => [
			SkillLang::DE => 'Adernalinkosten (präzise)',
			SkillLang::EN => 'Adernaline cost (precise)',
		],
		self::DATA_SACRIFICE          => [
			SkillLang::DE => 'Opferkosten',
			SkillLang::EN => 'Sacrifice cost',
		],
		self::DATA_EXHAUSTION         => [
			SkillLang::DE => 'Überzaubert',
			SkillLang::EN => 'Overcast',
		],
		self::DESC_NAME               => [
			SkillLang::DE => 'Fertigkeitsname',
			SkillLang::EN => 'Skill name',
		],
		self::DESC_DESCRIPTION        => [
			SkillLang::DE => 'Fertigkeitsbeschreibung',
			SkillLang::EN => 'Skill description',
		],
		self::DESC_CONCISE            => [
			SkillLang::DE => 'Kurzbeschreibung',
			SkillLang::EN => 'Concise description',
		],
	];

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

	public function getFieldName(string $field, SkillLang|string $lang = SkillLang::EN):string{

		if(!array_key_exists($field, self::FIELD_NAMES)){
			throw new InvalidArgumentException('invalid field name given');
		}

		if(!$lang instanceof SkillLang){
			$lang = new SkillLang($lang);
		}

		return self::FIELD_NAMES[$field][$lang->id];
	}

}
