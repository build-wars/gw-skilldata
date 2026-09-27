<?php
/**
 * Class Mod
 *
 * @created      10.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\IDComparisonInterface;
use Buildwars\GWSkillData\Common\IDComparisonTrait;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\LangTrait;
use Buildwars\GWSkillData\Common\Profession;
use InvalidArgumentException;
use RuntimeException;
use function array_key_exists;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/Upgrade_component
 * @see https://wiki.guildwars.com/wiki/Equipment_template_format#Modifier_IDs
 */
final class Mod implements IDComparisonInterface{
	use IDComparisonTrait, LangTrait;

	public const string CSS_CLASS = 'item modifier';

	public const string DATA_ID         = 'id';
	public const string DATA_TYPE       = 'type';
	public const string DATA_SUBTYPE    = 'subtype';
	public const string DATA_FOR        = 'for';
	public const string DATA_PROFESSION = 'profession';
	public const string DATA_ATTRIBUTE  = 'attribute';
	public const string DATA_EFFECTS    = 'effects';
	public const string DATA_ICON       = 'icon';

	public const string DESC_NAME       = 'name';
	public const string DESC_AFFIX      = 'affix';

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	public const array KEYS_DATA = [
		self::DATA_ID, self::DATA_TYPE, self::DATA_SUBTYPE, self::DATA_FOR,
		self::DATA_PROFESSION, self::DATA_ATTRIBUTE, self::DATA_ICON, self::DATA_EFFECTS,
	];

	public const int PREFIX      = 1;
	public const int SUFFIX      = 2;
	public const int INSCRIPTION = 3;

	private const array DataObjects = [
		self::DATA_ATTRIBUTE  => Attribute::class,
		self::DATA_PROFESSION => Profession::class,
		self::DATA_FOR        => ItemType::class,
	];

	private const array ModSubtypes = [
		AttributeRune::class,
		CommonRune::class,
		Inscription::class,
		Insignia::class,
		WeaponPrefix::class,
		WeaponSuffix::class,
	];

	protected(set) int                 $type;
	protected(set) ModSubtypeInterface $subtype;
	protected(set) ItemType            $for;
	protected(set) Profession|null     $profession = null;
	protected(set) Attribute|null      $attribute = null;
	protected(set) array|null          $effects = null;
	protected(set) int                 $icon;
	protected(set) string              $name;

	/**
	 * @param array<string, mixed> $modData
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $modData, Lang|string $lang = Lang::EN){
		$this->lang = $lang;

		foreach(self::KEYS_DATA as $key){
			if(!array_key_exists($key, $modData)){
				throw new InvalidArgumentException(sprintf('invalid mod data, missing key: "%s"', $key));
			}
		}

		// these fields should always be set or don't need invocation
		foreach([self::DATA_ID, self::DATA_TYPE, self::DATA_EFFECTS, self::DATA_ICON] as $key){
			$this->{$key} = $modData[$key];
		}

		// the data objects might be null sometimes and need to invoke their specific class instance
		foreach(self::DataObjects as $key => $fqcn){
			if($modData[$key] !== null){
				$this->{$key} = new $fqcn($modData[$key], $this->lang);
			}
		}

		// the subtypes may depend on the previously invoked classes and need to be invoked after
		$this->subtype = $this->invokeSubtype($modData[self::DATA_SUBTYPE])
			->setFor($this->for)
			->setModID($this->id)
			->setModAttribute($this->attribute)
			->setModEffects($this->effects);

		// the name is generated from the subtype
		$this->name = $this->subtype->getItemName();
	}

	private function invokeSubtype(int $subtype):ModSubtypeInterface{

		if(AttributeRune::has($subtype) && ($this->attribute === null || $this->attribute->is(Attribute::NONE))){
			throw new RuntimeException('a profession attribute is required for AttributeRune invocation');
		}

		/** @var \Buildwars\GWSkillData\Equipment\ModSubtypeInterface $fqcn */
		foreach(self::ModSubtypes as $fqcn){
			if($fqcn::has($subtype)){
				return new $fqcn($subtype, $this->lang);
			}
		}

		throw new RuntimeException('invalid mod subtype');
	}

	public function getItemName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return $this->subtype->getItemName($lang);
	}

	public function getAffix(Lang|string|null $lang = null):array{
		$lang = $this->getLang($lang);

		return $this->subtype->getAffix($lang);
	}

}
