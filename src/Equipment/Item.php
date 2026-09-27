<?php
/**
 * Class Item
 *
 * @created      10.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\DamageType;
use Buildwars\GWSkillData\Common\DataObjectInterface;
use Buildwars\GWSkillData\Common\IDComparisonInterface;
use Buildwars\GWSkillData\Common\IDComparisonTrait;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\LangTrait;
use Buildwars\GWSkillData\Common\Profession;
use function array_key_exists;
use function property_exists;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/Equipment
 * @see https://wiki.guildwars.com/wiki/Basic_armor
 * @see https://wiki.guildwars.com/wiki/Weapon
 * @see https://wiki.guildwars.com/wiki/Equipment_template_format#Item_IDs
 */
final class Item implements IDComparisonInterface{
	use IDComparisonTrait, LangTrait;

	public const string CSS_CLASS = 'item equipment';

	public const string DATA_ID         = 'id';
	public const string DATA_POSITION   = 'position';
	public const string DATA_TYPE       = 'type';
	public const string DATA_PROFESSION = 'profession';
	public const string DATA_ATTRIBUTE  = 'attribute';
	public const string DATA_DMG_TYPE   = 'dmg_type';

	public const string DESC_NAME       = 'name';
	public const string DESC_AFFIX      = 'affix';

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	public const array KEYS_DATA = [
		self::DATA_ID, self::DATA_POSITION, self::DATA_TYPE,
		self::DATA_PROFESSION, self::DATA_ATTRIBUTE, self::DATA_DMG_TYPE,
	];

	private const array DataObjects = [
		self::DATA_ATTRIBUTE  => Attribute::class,
		self::DATA_DMG_TYPE   => DamageType::class,
		self::DATA_POSITION   => ItemPosition::class,
		self::DATA_PROFESSION => Profession::class,
		self::DATA_TYPE       => ItemType::class,
	];

	protected(set) ItemPosition    $position;
	protected(set) ItemType        $type;
	protected(set) Profession      $profession;
	protected(set) Attribute       $attribute;
	protected(set) DamageType|null $dmg_type;
	protected(set) string          $name;
	protected(set) Armor|Weapon    $item; // @todo

	/**
	 * @param array<string, mixed> $itemData
	 */
	public function __construct(array $itemData, Lang|string $lang = Lang::EN){
		$this->lang = $lang;

		foreach($itemData as $key => $val){
			if(property_exists($this, $key)){

				if($val === null){
					$val = match($key){
						self::DATA_PROFESSION => Profession::NONE,
						self::DATA_ATTRIBUTE  => Attribute::NONE,
						default               => $val,
					};
				}

				if(array_key_exists($key, self::DataObjects) && !$val instanceof DataObjectInterface && $val !== null){
					$val = new (self::DataObjects[$key])($val, $this->lang);
				}

				$this->{$key} = $val;
			}
		}

		$this->item = $this->type->inKeys(ItemType::WEAPON)
			? new Weapon(ItemType::WEAPON[$this->type->id], $this->lang)
			: new Armor($this->profession->id, $this->position, $this->lang);
	}

	public function getAffix(DamageType|null $dmgType = null):array{
		return ($this->item instanceof Armor)
			? $this->item->getAffix($this->profession, $this->attribute)
			: $this->item->getAffix($this->attribute, ($dmgType ?? $this->dmg_type));
	}

	/**
	 * @todo
	 */
	public function toHTML():string{
		return sprintf(
			'<span class="%s" data-id="%s" data-lang="%s">%s</span>',
			static::CSS_CLASS,
			$this->id,
			$this->lang->id,
			$this->name,
		);
	}

}
