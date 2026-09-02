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
use function in_array;
use function property_exists;
use function rawurlencode;
use function sprintf;
use function strtolower;

/**
 * Represents a single skill with all its unmodified data
 */
final class Skill{

	public const string CSS_CLASS = 'skill';

	public const string MODE_PVE = 'pve';
	public const string MODE_PVP = 'pvp';

	public const string DATA_ATTRIBUTE          = 'attribute';
	public const string DATA_CAMPAIGN           = 'campaign';
	public const string DATA_PROFESSION         = 'profession';
	public const string DATA_TYPE               = 'type';
	public const string DATA_IS_ELITE           = 'is_elite';
	public const string DATA_IS_PVP             = 'is_pvp';
	public const string DATA_IS_RP              = 'is_rp';
	public const string DATA_PVP_SPLIT          = 'pvp_split';
	public const string DATA_ID                 = 'id';
	public const string DATA_SPLIT_ID           = 'split_id';
	public const string DATA_ACTIVATION         = 'activation';
	public const string DATA_AFTERCAST          = 'aftercast';
	public const string DATA_RECHARGE           = 'recharge';
	public const string DATA_ENERGY             = 'energy';
	public const string DATA_UPKEEP             = 'upkeep';
	public const string DATA_ADRENALINE         = 'adrenaline';
	public const string DATA_ADRENALINE_PRECISE = 'adrenaline_precise';
	public const string DATA_SACRIFICE          = 'sacrifice';
	public const string DATA_EXHAUSTION         = 'overcast';

	public const string DESC_NAME               = 'name';
	public const string DESC_DESCRIPTION        = 'description';
	public const string DESC_CONCISE            = 'concise';

	/**
	 * The array keys for the descriptions array
	 *
	 * @var string[]
	 */
	public const array KEYS_DESC = [self::DESC_NAME, self::DESC_DESCRIPTION, self::DESC_CONCISE];

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	public const array KEYS_DATA = [
		self::DATA_ID, self::DATA_CAMPAIGN, self::DATA_PROFESSION, self::DATA_ATTRIBUTE, self::DATA_IS_ELITE,
		self::DATA_IS_RP, self::DATA_IS_PVP, self::DATA_PVP_SPLIT, self::DATA_SPLIT_ID, self::DATA_TYPE,
		self::DATA_UPKEEP, self::DATA_ENERGY, self::DATA_ACTIVATION, /*self::DATA_AFTERCAST,*/ self::DATA_RECHARGE,
		self::DATA_ADRENALINE, self::DATA_ADRENALINE_PRECISE, self::DATA_SACRIFICE, self::DATA_EXHAUSTION,
	];

	private const array FIELD_NAMES = [
		self::MODE_PVE                => [
			Lang::DE => 'Rollenspiel',
			Lang::EN => 'Roleplay',
			Lang::FR => 'Jeu de rôle',
		],
		self::MODE_PVP                => [
			Lang::DE => 'Spieler gegen Spieler',
			Lang::EN => 'Player versus Player',
			Lang::FR => 'Joueur contre Joueur',
		],
		self::DATA_ATTRIBUTE          => [
			Lang::DE => 'Attribut',
			Lang::EN => 'Attribute',
			Lang::FR => 'Caractéristique',
		],
		self::DATA_CAMPAIGN           => [
			Lang::DE => 'Kampagne',
			Lang::EN => 'Campaign',
			Lang::FR => 'Campagne',
		],
		self::DATA_PROFESSION         => [
			Lang::DE => 'Klasse',
			Lang::EN => 'Profession',
			Lang::FR => 'Profession',
		],
		self::DATA_TYPE               => [
			Lang::DE => 'Fertigkeitstyp',
			Lang::EN => 'Skill type',
			Lang::FR => 'Type de compétence',
		],
		self::DATA_IS_ELITE           => [
			Lang::DE => 'ist Elite',
			Lang::EN => 'is Elite',
			Lang::FR => 'est Elite',
		],
		self::DATA_IS_PVP             => [
			Lang::DE => 'ist PvP',
			Lang::EN => 'is PvP',
			Lang::FR => 'est PvP',
		],
		self::DATA_IS_RP              => [
			Lang::DE => 'ist Rollenspiel',
			Lang::EN => 'is Roleplay',
			Lang::FR => 's\'agit d\'une compétence PvE',
		],
		self::DATA_PVP_SPLIT          => [
			Lang::DE => 'hat PvP-Version',
			Lang::EN => 'has PvP version',
			Lang::FR => 'a une version PvP',
		],
		self::DATA_ID                 => [
			Lang::DE => 'Fertigkeits-ID',
			Lang::EN => 'Skill ID',
			Lang::FR => 'ID de la compétence',
		],
		self::DATA_SPLIT_ID           => [
			Lang::DE => 'PvP ID',
			Lang::EN => 'PvP ID',
			Lang::FR => 'PvP ID',
		],
		self::DATA_ACTIVATION         => [
			Lang::DE => 'Aktivierungszeit',
			Lang::EN => 'Activation time',
			Lang::FR => 'Durée d\'activation',
		],
		self::DATA_AFTERCAST          => [
			Lang::DE => 'Nachwirkzeit',
			Lang::EN => 'Aftercast delay',
			Lang::FR => 'ACD',
		],
		self::DATA_RECHARGE           => [
			Lang::DE => 'Wiederaufladezeit',
			Lang::EN => 'Recharge time',
			Lang::FR => 'Temps de rechargement',
		],
		self::DATA_ENERGY             => [
			Lang::DE => 'Energiekosten',
			Lang::EN => 'Energy cost',
			Lang::FR => 'Coûts en énergie',
		],
		self::DATA_UPKEEP             => [
			Lang::DE => 'Unterhaltskosten',
			Lang::EN => 'Upkeep cost',
			Lang::FR => 'Coûts en maintien',
		],
		self::DATA_ADRENALINE         => [
			Lang::DE => 'Adernalinkosten',
			Lang::EN => 'Adernaline cost',
			Lang::FR => 'Coûts en adrénaline',
		],
		self::DATA_ADRENALINE_PRECISE => [
			Lang::DE => 'Adernalinkosten (präzise)',
			Lang::EN => 'Adernaline cost (precise)',
			Lang::FR => 'Coûts en adrénaline (précise)',
		],
		self::DATA_SACRIFICE          => [
			Lang::DE => 'Opferkosten',
			Lang::EN => 'Sacrifice cost',
			Lang::FR => 'Coûts en sacrifice',
		],
		self::DATA_EXHAUSTION         => [
			Lang::DE => 'Überzaubert',
			Lang::EN => 'Overcast',
			Lang::FR => 'Epuisement',
		],
		self::DESC_NAME               => [
			Lang::DE => 'Fertigkeitsname',
			Lang::EN => 'Skill name',
			Lang::FR => 'Nom de la compétence',
		],
		self::DESC_DESCRIPTION        => [
			Lang::DE => 'Fertigkeitsbeschreibung',
			Lang::EN => 'Skill description',
			Lang::FR => 'Description de la compétence',
		],
		self::DESC_CONCISE            => [
			Lang::DE => 'Kurzbeschreibung',
			Lang::EN => 'Concise description',
			Lang::FR => 'Description concise',
		],
	];

	private(set) Lang $lang {
		set(Lang|string $lang){

			if(!$lang instanceof Lang){
				$lang = new Lang($lang);
			}

			$this->lang = $lang;
		}
	}

	private(set) Attribute $attribute{
		set(Attribute|int $attribute){

			if(!$attribute instanceof Attribute){
				$attribute = new Attribute($attribute);
			}

			$this->attribute = $attribute;
		}
	}

	private(set) Campaign $campaign{
		set(Campaign|int $campaign){

			if(!$campaign instanceof Campaign){
				$campaign = new Campaign($campaign);
			}

			$this->campaign = $campaign;
		}
	}

	private(set) Profession $profession{
		set(Profession|int $profession){

			if(!$profession instanceof Profession){
				$profession = new Profession($profession);
			}

			$this->profession = $profession;
		}
	}

	private(set) Type $type{
		set(Type|int $type){

			if(!$type instanceof Type){
				$type = new Type($type);
			}

			$this->type = $type;
		}
	}

	private(set) int        $id;
	private(set) bool       $is_elite;
	private(set) bool       $is_pvp;
	private(set) bool       $is_rp;
	private(set) bool       $pvp_split;
	private(set) int        $split_id;
	private(set) int|float  $activation;
	private(set) int        $recharge;
	private(set) int        $energy;
	private(set) int        $upkeep;
	private(set) int        $adrenaline;
	private(set) int|float  $adrenaline_precise;
	private(set) int        $sacrifice;
	private(set) int        $overcast;

	private(set) string     $name;
	private(set) string     $description;
	private(set) string     $concise;

	public function __construct(array $skilldata, Lang|string $lang = Lang::EN){
		$this->lang = $lang;

		foreach($skilldata as $key => $val){
			if(property_exists($this, $key)){
				$this->{$key} = $val;
			}
		}

	}

	/**
	 * Checks whether the object ID is equal to the given ID
	 */
	public function is(int $id):bool{
		return $this->id === $id;
	}

	/**
	 * Checks whether the object ID is in the given array of IDs
	 *
	 * @param int[] $ids
	 */
	public function in(array $ids):bool{
		return in_array($this->id, $ids, true);
	}

	/**
	 * Returns a pure array representation of the `Skill` instance
	 */
	public function toArray():array{
		$data = [];

		foreach(array_merge(self::KEYS_DATA, self::KEYS_DESC) as $key){
			$value = $this->{$key};

			if($value instanceof DataObjectInterface){
				$value = $value->id;
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

	public function toHTML(string|null $icon = null, string|null $link = null):string{

		$cssClasses = [
			self::CSS_CLASS,
			sprintf('%s-%s', self::CSS_CLASS, $this->id),
			strtolower($this->profession->getName(Lang::EN)),
		];

		if($this->is_elite){
			$cssClasses[] = 'elite';
		}

		$inner = $this->name;

		if($icon !== null){
			/** @noinspection HtmlUnknownTarget */
			$inner = sprintf('<img src="%1$s" alt="%2$s" title="%2$s" />', $icon, rawurlencode($this->name));
		}

		if($link !== null){
			/** @noinspection HtmlUnknownTarget */
			$inner = sprintf('<a href="%s" target="_blank">%s</a>', $link, $inner);
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
			$inner,
		);
	}

}
