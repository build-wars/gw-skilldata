<?php
/**
 * Class Type
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use function sort;
use const SORT_NUMERIC;

/**
 * Encapsulates all skill type related static data
 */
final class Type extends DataObjectAbstract{

	public const CSS_CLASS = 'skilltype';

	public const NONE                    = 0;
	public const SKILL                   = 1;
	public const BOW_ATTACK              = 2;
	public const MELEE_ATTACK            = 3;
	public const AXE_ATTACK              = 4;
	public const LEAD_ATTACK             = 5;
	public const OFF_HAND_ATTACK         = 6;
	public const DUAL_ATTACK             = 7;
	public const HAMMER_ATTACK           = 8;
	public const SCYTHE_ATTACK           = 9;
	public const SWORD_ATTACK            = 10;
	public const PET_ATTACK              = 11;
	public const SPEAR_ATTACK            = 12;
	public const CHANT                   = 13;
	public const ECHO                    = 14;
	public const FORM                    = 15;
	public const GLYPH                   = 16;
	public const PREPARATION             = 17;
	public const BINDING_RITUAL          = 18;
	public const NATURE_RITUAL           = 19;
	public const SHOUT                   = 20;
	public const SIGNET                  = 21;
	public const SPELL                   = 22;
	public const ENCHANTMENT_SPELL       = 23;
	public const HEX_SPELL               = 24;
	public const ITEM_SPELL              = 25;
	public const WARD_SPELL              = 26;
	public const WEAPON_SPELL            = 27;
	public const WELL_SPELL              = 28;
	public const STANCE                  = 29;
	public const TRAP                    = 30;
	public const RANGED_ATTACK           = 31;
	public const EBON_VANGUARD_RITUAL    = 32;
	public const FLASH_ENCHANTMENT_SPELL = 33;
	public const ATTACK_SKILL            = 34;
	public const DAGGER_ATTACK           = 35;
	public const RITUAL                  = 36;
	public const DOUBLE_ENCHANTMENT      = 37;
	public const TOUCH_SKILL             = 38;
	public const TOUCH_SPELL             = 39;
	public const TOUCH_ENCHANTMENT_SPELL = 40;
	public const TOUCH_HEX_SPELL         = 41;
	public const TOUCH_SIGNET            = 42;

	public const NAME = [
		self::NONE                    => [
			Lang::DE => 'Keine Fertigkeit',
			Lang::EN => 'No Skill',
			Lang::FR => 'Aucun Compétence',
		],
		self::SKILL                   => [
			Lang::DE => 'Fertigkeit',
			Lang::EN => 'Skill',
			Lang::FR => 'Compétence',
		],
		self::BOW_ATTACK              => [
			Lang::DE => 'Bogenangriff',
			Lang::EN => 'Bow Attack',
			Lang::FR => 'Attaque à l\'arc',
		],
		self::MELEE_ATTACK            => [
			Lang::DE => 'Nahkampfangriff',
			Lang::EN => 'Melee Attack',
			Lang::FR => 'Attaque au corps à corps',
		],
		self::AXE_ATTACK              => [
			Lang::DE => 'Axtangriff',
			Lang::EN => 'Axe Attack',
			Lang::FR => 'Attaque à la hache',
		],
		self::LEAD_ATTACK             => [
			Lang::DE => 'Leithandangriff',
			Lang::EN => 'Lead Attack',
			Lang::FR => 'Attaque main droite',
		],
		self::OFF_HAND_ATTACK         => [
			Lang::DE => 'Begleithandangriff',
			Lang::EN => 'Off-Hand Attack',
			Lang::FR => 'Attaque main gauche',
		],
		self::DUAL_ATTACK             => [
			Lang::DE => 'Doppelangriff',
			Lang::EN => 'Dual Attack',
			Lang::FR => 'Attaque ambidextre',
		],
		self::HAMMER_ATTACK           => [
			Lang::DE => 'Hammerangriff',
			Lang::EN => 'Hammer Attack',
			Lang::FR => 'Attaque au marteau',
		],
		self::SCYTHE_ATTACK           => [
			Lang::DE => 'Sensenangriff',
			Lang::EN => 'Scythe Attack',
			Lang::FR => 'Attaque à la faux',
		],
		self::SWORD_ATTACK            => [
			Lang::DE => 'Schwertangriff',
			Lang::EN => 'Sword Attack',
			Lang::FR => 'Attaque à l\'épée',
		],
		self::PET_ATTACK              => [
			Lang::DE => 'Tiergefährtenangriff',
			Lang::EN => 'Pet Attack',
			Lang::FR => 'Attaque de familier',
		],
		self::SPEAR_ATTACK            => [
			Lang::DE => 'Speerangriff',
			Lang::EN => 'Spear Attack',
			Lang::FR => 'Attaque au javelot',
		],
		self::CHANT                   => [
			Lang::DE => 'Anfeuerungsruf',
			Lang::EN => 'Chant',
			Lang::FR => 'Chant',
		],
		self::ECHO                    => [
			Lang::DE => 'Echo',
			Lang::EN => 'Echo',
			Lang::FR => 'Echo',
		],
		self::FORM                    => [
			Lang::DE => 'Form',
			Lang::EN => 'Form',
			Lang::FR => 'Transformation',
		],
		self::GLYPH                   => [
			Lang::DE => 'Glyphe',
			Lang::EN => 'Glyph',
			Lang::FR => 'Glyphe',
		],
		self::PREPARATION             => [
			Lang::DE => 'Vorbereitung',
			Lang::EN => 'Preparation',
			Lang::FR => 'Préparation',
		],
		self::BINDING_RITUAL          => [
			Lang::DE => 'Binderitual',
			Lang::EN => 'Binding Ritual',
			Lang::FR => 'Rituel d\'asservissement',
		],
		self::NATURE_RITUAL           => [
			Lang::DE => 'Naturritual',
			Lang::EN => 'Nature Ritual',
			Lang::FR => 'Rituel de la nature',
		],
		self::SHOUT                   => [
			Lang::DE => 'Schrei',
			Lang::EN => 'Shout',
			Lang::FR => 'Cri',
		],
		self::SIGNET                  => [
			Lang::DE => 'Siegel',
			Lang::EN => 'Signet',
			Lang::FR => 'Sceau',
		],
		self::SPELL                   => [
			Lang::DE => 'Zauber',
			Lang::EN => 'Spell',
			Lang::FR => 'Sort',
		],
		self::ENCHANTMENT_SPELL       => [
			Lang::DE => 'Verzauberung',
			Lang::EN => 'Enchantment Spell',
			Lang::FR => 'Enchantement',
		],
		self::HEX_SPELL               => [
			Lang::DE => 'Verhexung',
			Lang::EN => 'Hex Spell',
			Lang::FR => 'Maléfice',
		],
		self::ITEM_SPELL              => [
			Lang::DE => 'Gegenstandszauber',
			Lang::EN => 'Item Spell',
			Lang::FR => 'Sort d\'altération d\'objet',
		],
		self::WARD_SPELL              => [
			Lang::DE => 'Abwehrzauber',
			Lang::EN => 'Ward Spell',
			Lang::FR => 'Sort de protection',
		],
		self::WEAPON_SPELL            => [
			Lang::DE => 'Waffenzauber',
			Lang::EN => 'Weapon Spell',
			Lang::FR => 'Sort d\'altération d\'arme',
		],
		self::WELL_SPELL              => [
			Lang::DE => 'Brunnenzauber',
			Lang::EN => 'Well Spell',
			Lang::FR => 'Sort de puits',
		],
		self::STANCE                  => [
			Lang::DE => 'Haltung',
			Lang::EN => 'Stance',
			Lang::FR => 'Pose de combat',
		],
		self::TRAP                    => [
			Lang::DE => 'Falle',
			Lang::EN => 'Trap',
			Lang::FR => 'Piège',
		],
		self::RANGED_ATTACK           => [
			Lang::DE => 'Distanzangriff',
			Lang::EN => 'Ranged Attack',
			Lang::FR => 'Attaque à distance',
		],
		self::EBON_VANGUARD_RITUAL    => [
			Lang::DE => 'Ebon-Vorhut-Ritual',
			Lang::EN => 'Ebon Vanguard Ritual',
			Lang::FR => 'Rituel de l\'Avant-garde d\'Ebon',
		],
		self::FLASH_ENCHANTMENT_SPELL => [
			Lang::DE => 'Blitzverzauberung',
			Lang::EN => 'Flash Enchantment Spell',
			Lang::FR => 'Enchantement instantané',
		],
		self::DOUBLE_ENCHANTMENT      => [
			Lang::DE => 'Doppelverzauberung',
			Lang::EN => 'Double Enchantment',
			Lang::FR => '[Double Enchantment]',
		],
		self::TOUCH_SKILL             => [
			Lang::DE => 'Berührungsfertigkeit',
			Lang::EN => 'Touch Skill',
			Lang::FR => 'Compétence de contact',
		],
		self::TOUCH_SPELL             => [
			Lang::DE => 'Berührungszauber',
			Lang::EN => 'Touch Spell',
			Lang::FR => 'Sort de contact',
		],
		self::TOUCH_ENCHANTMENT_SPELL => [
			Lang::DE => 'Berührungsverzauberung',
			Lang::EN => 'Touch Enchantment Spell',
			Lang::FR => 'Enchantement de contact',
		],
		self::TOUCH_HEX_SPELL         => [
			Lang::DE => 'Berührungsverhexung',
			Lang::EN => 'Touch Hex Spell',
			Lang::FR => 'Maléfice de contact',
		],
		self::TOUCH_SIGNET            => [
			Lang::DE => 'Berührungssiegel',
			Lang::EN => 'Touch Signet',
			Lang::FR => 'Sceau de contact',
		],
		self::ATTACK_SKILL            => [
			Lang::DE => 'Angriffsfertigkeit',
			Lang::EN => 'Attack Skill',
			Lang::FR => 'Attaque',
		],
		self::DAGGER_ATTACK           => [
			Lang::DE => 'Dolchangriff',
			Lang::EN => 'Dagger Attack',
			Lang::FR => 'Attaque à la dague',
		],
		self::RITUAL                  => [
			Lang::DE => 'Ritual',
			Lang::EN => 'Ritual',
			Lang::FR => 'Rituel',
		],
	];

	private const SUBTYPES = [
		self::ATTACK_SKILL      => [
			self::MELEE_ATTACK, self::RANGED_ATTACK, self::BOW_ATTACK, self::AXE_ATTACK, self::LEAD_ATTACK, self::OFF_HAND_ATTACK,
			self::DUAL_ATTACK, self::HAMMER_ATTACK, self::SCYTHE_ATTACK, self::SWORD_ATTACK, self::PET_ATTACK, self::SPEAR_ATTACK,
		],
		self::DAGGER_ATTACK     => [self::LEAD_ATTACK, self::OFF_HAND_ATTACK, self::DUAL_ATTACK],
		self::ENCHANTMENT_SPELL => [self::FLASH_ENCHANTMENT_SPELL, self::DOUBLE_ENCHANTMENT, self::TOUCH_ENCHANTMENT_SPELL],
		self::HEX_SPELL         => [self::TOUCH_HEX_SPELL],
		self::MELEE_ATTACK      => [
			self::AXE_ATTACK, self::LEAD_ATTACK, self::OFF_HAND_ATTACK, self::DUAL_ATTACK, self::HAMMER_ATTACK,
			self::SCYTHE_ATTACK, self::SWORD_ATTACK, self::PET_ATTACK,
		],
		self::RANGED_ATTACK     => [self::BOW_ATTACK, self::SPEAR_ATTACK],
		self::RITUAL            => [self::BINDING_RITUAL, self::NATURE_RITUAL, self::EBON_VANGUARD_RITUAL],
		self::SPELL             => [
			self::ENCHANTMENT_SPELL, self::HEX_SPELL, self::ITEM_SPELL, self::WARD_SPELL, self::WEAPON_SPELL, self::WELL_SPELL,
			self::FLASH_ENCHANTMENT_SPELL, self::DOUBLE_ENCHANTMENT, self::TOUCH_SPELL, self::TOUCH_ENCHANTMENT_SPELL,
			self::TOUCH_HEX_SPELL,
		],
		self::SIGNET            => [self::TOUCH_SIGNET],
		self::TOUCH_SKILL       => [self::TOUCH_SPELL, self::TOUCH_ENCHANTMENT_SPELL, self::TOUCH_HEX_SPELL, self::TOUCH_SIGNET],
	];

	/**
	 * Returns the IDs for the given skill type including all of its subtypes
	 *
	 * @return int[]
	 */
	public function withSubtypes():array{
		$types   = (self::SUBTYPES[$this->id] ?? []);
		$types[] = $this->id;

		sort($types, SORT_NUMERIC);

		return $types;
	}

}
