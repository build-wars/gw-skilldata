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
use function array_search;
use function implode;
use function property_exists;
use function sprintf;
use function strtolower;

/**
 * Represents a single skill with all its unmodified data
 */
final class Skill extends DataObjectAbstract{

	public const CSS_CLASS = 'skill';

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

	/**
	 * The array keys for the descriptions array
	 *
	 * @var string[]
	 */
	public const KEYS_DESC = [self::DESC_NAME, self::DESC_DESCRIPTION, self::DESC_CONCISE];

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	public const KEYS_DATA = [
		self::DATA_ID, self::DATA_CAMPAIGN, self::DATA_PROFESSION, self::DATA_ATTRIBUTE, self::DATA_IS_ELITE,
		self::DATA_IS_RP, self::DATA_IS_PVP, self::DATA_PVP_SPLIT, self::DATA_SPLIT_ID, self::DATA_TYPE,
		self::DATA_UPKEEP, self::DATA_ENERGY, self::DATA_ACTIVATION, self::DATA_RECHARGE, self::DATA_ADRENALINE,
		self::DATA_ADRENALINE_PRECISE, self::DATA_SACRIFICE, self::DATA_EXHAUSTION,
	];


	public const FIELD_NAMES = [
		self::MODE_PVE                => [Lang::DE => 'Rollenspiel',               Lang::EN => 'Roleplay',                 ],
		self::MODE_PVP                => [Lang::DE => 'Spieler gegen Spieler',     Lang::EN => 'Player versus Player',     ],
		self::DATA_ATTRIBUTE          => [Lang::DE => 'Attribut',                  Lang::EN => 'Attribute',                ],
		self::DATA_CAMPAIGN           => [Lang::DE => 'Kampagne',                  Lang::EN => 'Campaign',                 ],
		self::DATA_PROFESSION         => [Lang::DE => 'Klasse',                    Lang::EN => 'Profession',               ],
		self::DATA_TYPE               => [Lang::DE => 'Fertigkeitstyp',            Lang::EN => 'Skill type',               ],
		self::DATA_IS_ELITE           => [Lang::DE => 'ist Elite',                 Lang::EN => 'is Elite',                 ],
		self::DATA_IS_PVP             => [Lang::DE => 'ist PvP',                   Lang::EN => 'is PvP',                   ],
		self::DATA_IS_RP              => [Lang::DE => 'ist Rollenspiel',           Lang::EN => 'is Roleplay',              ],
		self::DATA_PVP_SPLIT          => [Lang::DE => 'hat PvP-Version',           Lang::EN => 'has PvP version',          ],
		self::DATA_ID                 => [Lang::DE => 'Fertigkeits-ID',            Lang::EN => 'Skill ID',                 ],
		self::DATA_SPLIT_ID           => [Lang::DE => 'PvP ID',                    Lang::EN => 'PvP ID',                   ],
		self::DATA_ACTIVATION         => [Lang::DE => 'Aktivierungszeit',          Lang::EN => 'Activation time',          ],
		self::DATA_RECHARGE           => [Lang::DE => 'Wiederaufladezeit',         Lang::EN => 'Recharge time',            ],
		self::DATA_ENERGY             => [Lang::DE => 'Energiekosten',             Lang::EN => 'Energy cost',              ],
		self::DATA_UPKEEP             => [Lang::DE => 'Unterhaltskosten',          Lang::EN => 'Upkeep cost',              ],
		self::DATA_ADRENALINE         => [Lang::DE => 'Adernalinkosten',           Lang::EN => 'Adernaline cost',          ],
		self::DATA_ADRENALINE_PRECISE => [Lang::DE => 'Adernalinkosten (präzise)', Lang::EN => 'Adernaline cost (precise)',],
		self::DATA_SACRIFICE          => [Lang::DE => 'Opferkosten',               Lang::EN => 'Sacrifice cost',           ],
		self::DATA_EXHAUSTION         => [Lang::DE => 'Überzaubert',               Lang::EN => 'Overcast',                 ],
		self::DESC_NAME               => [Lang::DE => 'Fertigkeitsname',           Lang::EN => 'Skill name',               ],
		self::DESC_DESCRIPTION        => [Lang::DE => 'Fertigkeitsbeschreibung',   Lang::EN => 'Skill description',        ],
		self::DESC_CONCISE            => [Lang::DE => 'Kurzbeschreibung',          Lang::EN => 'Concise description',      ],
	];

	private const DataObjects = [
		self::DATA_ATTRIBUTE  => Attribute::class,
		self::DATA_CAMPAIGN   => Campaign::class,
		self::DATA_PROFESSION => Profession::class,
		self::DATA_TYPE       => Type::class,
	];

	public readonly Attribute  $attribute;
	public readonly Campaign   $campaign;
	public readonly Profession $profession;
	public readonly Type       $type;
	public readonly bool       $is_elite;
	public readonly bool       $is_pvp;
	public readonly bool       $is_rp;
	public readonly bool       $pvp_split;
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

	/**
	 * @todo: remove with php 8.4
	 * @internal
	 */
	public const NAME = SkillData::ID2DATA;

	public function __construct(array $skilldata, Lang|string $lang = Lang::EN){
		// @todo: remove with php 8.4
		parent::__construct($skilldata[self::DATA_ID], $lang);

		foreach($skilldata as $key => $val){
			if(property_exists($this, $key)){

				// id is already set in the constructor (silly pre-8.4 readonly behaviour)
				if($key === self::DATA_ID){
					continue;
				}

				if(array_key_exists($key, self::DataObjects) && !$val instanceof (self::DataObjects[$key])){
					$val = new (self::DataObjects[$key])($val);
				}

				$this->{$key} = $val;
			}
		}

	}

	public function getName(Lang|string|null $lang = null):string{
		return $this->name;
	}

	/**
	 * Returns a pure array representation of the `Skill` instance
	 */
	public function toArray():array{
		$data = [];

		foreach(array_merge(self::KEYS_DATA, self::KEYS_DESC) as $key){
			$value = $this->{$key};

			if($value instanceof DataObjectAbstract){
				$value = $this->{$key}->id;
			}

			$data[$key] = $value;
		}

		return $data;
	}

	/**
	 * Returns the display name for the given field
	 */
	public function getFieldName(string $field, Lang|string $lang = Lang::EN):string{

		if(!array_key_exists($field, self::FIELD_NAMES)){
			throw new InvalidArgumentException('invalid field name given');
		}

		if(!$lang instanceof Lang){
			$lang = new Lang($lang);
		}

		return self::FIELD_NAMES[$field][$lang->id];
	}

	/**
	 * Returns the numeric key ID (position) in the data keys for the given key
	 */
	public static function getDataKeyID(string $key):int{
		return array_search($key, self::KEYS_DATA, true);
	}

	/**
	 * Returns the numeric key ID (position) in the description keys for the given key
	 */
	public static function getLangKeyID(string $key):int{
		return array_search($key, self::KEYS_DESC, true);
	}

	public function toHTML(Lang|string|null $lang = null):string{

		$cssClasses = [
			self::CSS_CLASS,
			sprintf('%s-%s', self::CSS_CLASS, $this->id),
			strtolower($this->profession->getName(Lang::EN)),
		];

		if($this->is_elite){
			$cssClasses[] = 'elite';
		}

		return sprintf(
			'<div class="%s" data-id="%d" data-lang="%s" data-attribute="%d" data-campaign="%d" data-profession="%d"'.
			' data-type="%d" data-elite="%s" data-roleplay="%s" data-pvp="%s">%s</div>',
			implode(' ', $cssClasses),
			$this->id,
			$this->lang->id,
			$this->attribute->id,
			$this->campaign->id,
			$this->profession->id,
			$this->type->id,
			($this->is_elite ? 'true' : 'false'),
			($this->is_rp ? 'true' : 'false'),
			($this->is_pvp ? 'true' : 'false'),
			$this->name,
		);
	}

}
