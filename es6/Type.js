/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import DataObjectAbstract from './DataObjectAbstract.js';
import PHPJS from './PHPJS.js';

/**
 * Encapsulates all skill type related static data
 *
 * @final
 */
export default class Type extends DataObjectAbstract{

	static get CSS_CLASS(){return 'skilltype'};

	static get NONE                   (){return 0}
	static get SKILL                  (){return 1}
	static get BOW_ATTACK             (){return 2}
	static get MELEE_ATTACK           (){return 3}
	static get AXE_ATTACK             (){return 4}
	static get LEAD_ATTACK            (){return 5}
	static get OFF_HAND_ATTACK        (){return 6}
	static get DUAL_ATTACK            (){return 7}
	static get HAMMER_ATTACK          (){return 8}
	static get SCYTHE_ATTACK          (){return 9}
	static get SWORD_ATTACK           (){return 10}
	static get PET_ATTACK             (){return 11}
	static get SPEAR_ATTACK           (){return 12}
	static get CHANT                  (){return 13}
	static get ECHO                   (){return 14}
	static get FORM                   (){return 15}
	static get GLYPH                  (){return 16}
	static get PREPARATION            (){return 17}
	static get BINDING_RITUAL         (){return 18}
	static get NATURE_RITUAL          (){return 19}
	static get SHOUT                  (){return 20}
	static get SIGNET                 (){return 21}
	static get SPELL                  (){return 22}
	static get ENCHANTMENT_SPELL      (){return 23}
	static get HEX_SPELL              (){return 24}
	static get ITEM_SPELL             (){return 25}
	static get WARD_SPELL             (){return 26}
	static get WEAPON_SPELL           (){return 27}
	static get WELL_SPELL             (){return 28}
	static get STANCE                 (){return 29}
	static get TRAP                   (){return 30}
	static get RANGED_ATTACK          (){return 31}
	static get EBON_VANGUARD_RITUAL   (){return 32}
	static get FLASH_ENCHANTMENT_SPELL(){return 33}
	static get ATTACK_SKILL           (){return 34}
	static get DAGGER_ATTACK          (){return 35}
	static get RITUAL                 (){return 36}
	static get DOUBLE_ENCHANTMENT     (){return 37}
	static get TOUCH_SKILL            (){return 38}
	static get TOUCH_SPELL            (){return 39}
	static get TOUCH_ENCHANTMENT_SPELL(){return 40}
	static get TOUCH_HEX_SPELL        (){return 41}
	static get TOUCH_SIGNET           (){return 42}

	/** @returns {number[]|int[]} */
	static get IDS(){
		return [
			Type.NONE,
			Type.SKILL,
			Type.BOW_ATTACK,
			Type.MELEE_ATTACK,
			Type.AXE_ATTACK,
			Type.LEAD_ATTACK,
			Type.OFF_HAND_ATTACK,
			Type.DUAL_ATTACK,
			Type.HAMMER_ATTACK,
			Type.SCYTHE_ATTACK,
			Type.SWORD_ATTACK,
			Type.PET_ATTACK,
			Type.SPEAR_ATTACK,
			Type.CHANT,
			Type.ECHO,
			Type.FORM,
			Type.GLYPH,
			Type.PREPARATION,
			Type.BINDING_RITUAL,
			Type.NATURE_RITUAL,
			Type.SHOUT,
			Type.SIGNET,
			Type.SPELL,
			Type.ENCHANTMENT_SPELL,
			Type.HEX_SPELL,
			Type.ITEM_SPELL,
			Type.WARD_SPELL,
			Type.WEAPON_SPELL,
			Type.WELL_SPELL,
			Type.STANCE,
			Type.TRAP,
			Type.RANGED_ATTACK,
			Type.EBON_VANGUARD_RITUAL,
			Type.FLASH_ENCHANTMENT_SPELL,
			Type.DOUBLE_ENCHANTMENT,
			Type.TOUCH_SKILL,
			Type.TOUCH_SPELL,
			Type.TOUCH_ENCHANTMENT_SPELL,
			Type.TOUCH_HEX_SPELL,
			Type.TOUCH_SIGNET,
			Type.ATTACK_SKILL,
			Type.DAGGER_ATTACK,
			Type.RITUAL,
		];
	}

	static get NAME(){
		return PHPJS.array_combine(Type.IDS, [
			{
				de: 'Keine Fertigkeit',
				en: 'No Skill',
				fr: 'Aucun Compétence',
			},
			{
				de: 'Fertigkeit',
				en: 'Skill',
				fr: 'Compétence',
			},
			{
				de: 'Bogenangriff',
				en: 'Bow Attack',
				fr: 'Attaque à l\'arc',
			},
			{
				de: 'Nahkampfangriff',
				en: 'Melee Attack',
				fr: 'Attaque au corps à corps',
			},
			{
				de: 'Axtangriff',
				en: 'Axe Attack',
				fr: 'Attaque à la hache',
			},
			{
				de: 'Leithandangriff',
				en: 'Lead Attack',
				fr: 'Attaque main droite',
			},
			{
				de: 'Begleithandangriff',
				en: 'Off-Hand Attack',
				fr: 'Attaque main gauche',
			},
			{
				de: 'Doppelangriff',
				en: 'Dual Attack',
				fr: 'Attaque ambidextre',
			},
			{
				de: 'Hammerangriff',
				en: 'Hammer Attack',
				fr: 'Attaque au marteau',
			},
			{
				de: 'Sensenangriff',
				en: 'Scythe Attack',
				fr: 'Attaque à la faux',
			},
			{
				de: 'Schwertangriff',
				en: 'Sword Attack',
				fr: 'Attaque à l\'épée',
			},
			{
				de: 'Tiergefährtenangriff',
				en: 'Pet Attack',
				fr: 'Attaque de familier',
			},
			{
				de: 'Speerangriff',
				en: 'Spear Attack',
				fr: 'Attaque au javelot',
			},
			{
				de: 'Anfeuerungsruf',
				en: 'Chant',
				fr: 'Chant',
			},
			{
				de: 'Echo',
				en: 'Echo',
				fr: 'Echo',
			},
			{
				de: 'Form',
				en: 'Form',
				fr: 'Transformation',
			},
			{
				de: 'Glyphe',
				en: 'Glyph',
				fr: 'Glyphe',
			},
			{
				de: 'Vorbereitung',
				en: 'Preparation',
				fr: 'Préparation',
			},
			{
				de: 'Binderitual',
				en: 'Binding Ritual',
				fr: 'Rituel d\'asservissement',
			},
			{
				de: 'Naturritual',
				en: 'Nature Ritual',
				fr: 'Rituel de la nature',
			},
			{
				de: 'Schrei',
				en: 'Shout',
				fr: 'Cri',
			},
			{
				de: 'Siegel',
				en: 'Signet',
				fr: 'Sceau',
			},
			{
				de: 'Zauber',
				en: 'Spell',
				fr: 'Sort',
			},
			{
				de: 'Verzauberung',
				en: 'Enchantment Spell',
				fr: 'Enchantement',
			},
			{
				de: 'Verhexung',
				en: 'Hex Spell',
				fr: 'Maléfice',
			},
			{
				de: 'Gegenstandszauber',
				en: 'Item Spell',
				fr: 'Sort d\'altération d\'objet',
			},
			{
				de: 'Abwehrzauber',
				en: 'Ward Spell',
				fr: 'Sort de protection',
			},
			{
				de: 'Waffenzauber',
				en: 'Weapon Spell',
				fr: 'Sort d\'altération d\'arme',
			},
			{
				de: 'Brunnenzauber',
				en: 'Well Spell',
				fr: 'Sort de puits',
			},
			{
				de: 'Haltung',
				en: 'Stance',
				fr: 'Pose de combat',
			},
			{
				de: 'Falle',
				en: 'Trap',
				fr: 'Piège',
			},
			{
				de: 'Distanzangriff',
				en: 'Ranged Attack',
				fr: 'Attaque à distance',
			},
			{
				de: 'Ebon-Vorhut-Ritual',
				en: 'Ebon Vanguard Ritual',
				fr: 'Rituel de l\'Avant-garde d\'Ebon',
			},
			{
				de: 'Blitzverzauberung',
				en: 'Flash Enchantment Spell',
				fr: 'Enchantement instantané',
			},
			{
				de: 'Doppelverzauberung',
				en: 'Double Enchantment',
				fr: '[Double Enchantment]',
			},
			{
				de: 'Berührungsfertigkeit',
				en: 'Touch Skill',
				fr: 'Compétence de contact',
			},
			{
				de: 'Berührungszauber',
				en: 'Touch Spell',
				fr: 'Sort de contact',
			},
			{
				de: 'Berührungsverzauberung',
				en: 'Touch Enchantment Spell',
				fr: 'Enchantement de contact',
			},
			{
				de: 'Berührungsverhexung',
				en: 'Touch Hex Spell',
				fr: 'Maléfice de contact',
			},
			{
				de: 'Berührungssiegel',
				en: 'Touch Signet',
				fr: 'Sceau de contact',
			},
			{
				de: 'Angriffsfertigkeit',
				en: 'Attack Skill',
				fr: 'Attaque',
			},
			{
				de: 'Dolchangriff',
				en: 'Dagger Attack',
				fr: 'Attaque à la dague',
			},
			{
				de: 'Ritual',
				en: 'Ritual',
				fr: 'Rituel',
			},
		]);
	}

	static get SUBTYPES(){

		let ids = [
			Type.ATTACK_SKILL,
			Type.DAGGER_ATTACK,
			Type.ENCHANTMENT_SPELL,
			Type.HEX_SPELL,
			Type.MELEE_ATTACK,
			Type.RANGED_ATTACK,
			Type.RITUAL,
			Type.SPELL,
			Type.SIGNET,
			Type.TOUCH_SKILL,
		];

		return PHPJS.array_combine(ids,[
			[
				Type.MELEE_ATTACK, Type.RANGED_ATTACK, Type.BOW_ATTACK, Type.AXE_ATTACK, Type.LEAD_ATTACK, Type.OFF_HAND_ATTACK,
				Type.DUAL_ATTACK, Type.HAMMER_ATTACK, Type.SCYTHE_ATTACK, Type.SWORD_ATTACK, Type.PET_ATTACK, Type.SPEAR_ATTACK,
			],
			[Type.LEAD_ATTACK, Type.OFF_HAND_ATTACK, Type.DUAL_ATTACK],
			[Type.FLASH_ENCHANTMENT_SPELL, Type.DOUBLE_ENCHANTMENT, Type.TOUCH_ENCHANTMENT_SPELL],
			[Type.TOUCH_HEX_SPELL],
			[
				Type.AXE_ATTACK, Type.LEAD_ATTACK, Type.OFF_HAND_ATTACK, Type.DUAL_ATTACK, Type.HAMMER_ATTACK,
				Type.SCYTHE_ATTACK, Type.SWORD_ATTACK, Type.PET_ATTACK,
			],
			[Type.BOW_ATTACK, Type.SPEAR_ATTACK],
			[Type.BINDING_RITUAL, Type.NATURE_RITUAL, Type.EBON_VANGUARD_RITUAL],
			[
				Type.ENCHANTMENT_SPELL, Type.HEX_SPELL, Type.ITEM_SPELL, Type.WARD_SPELL, Type.WEAPON_SPELL, Type.WELL_SPELL,
				Type.FLASH_ENCHANTMENT_SPELL, Type.DOUBLE_ENCHANTMENT, Type.TOUCH_SPELL, Type.TOUCH_ENCHANTMENT_SPELL,
				Type.TOUCH_HEX_SPELL,
			],
			[Type.TOUCH_SIGNET],
			[Type.TOUCH_SPELL, Type.TOUCH_ENCHANTMENT_SPELL, Type.TOUCH_HEX_SPELL, Type.TOUCH_SIGNET],
		]);
	}

	/**
	 * Returns the IDs for the given skill type including all of its subtypes
	 *
	 * @returns {int[]}
	 */
	withSubtypes(){
		let types = (Type.SUBTYPES[this.id] ?? []);
		types.push(this.id);

		return types.sort((a, b) => a - b);
	}

}
