/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import Attribute from './Attribute.js';
import DataObjectAbstract from './DataObjectAbstract.js';
import PHPJS from './PHPJS.js';

/**
 * Encapsulates all profession related static data
 *
 * @final
 */
export default class Profession extends DataObjectAbstract{

	static get CSS_CLASS(){return 'profession'};

	static get NONE        (){return 0}
	static get WARRIOR     (){return 1}
	static get RANGER      (){return 2}
	static get MONK        (){return 3}
	static get NECROMANCER (){return 4}
	static get MESMER      (){return 5}
	static get ELEMENTALIST(){return 6}
	static get ASSASSIN    (){return 7}
	static get RITUALIST   (){return 8}
	static get PARAGON     (){return 9}
	static get DERVISH     (){return 10}

	/** @returns {number[]|int[]}*/
	static get IDS(){
		return [
			Profession.NONE,
			Profession.WARRIOR,
			Profession.RANGER,
			Profession.MONK,
			Profession.NECROMANCER,
			Profession.MESMER,
			Profession.ELEMENTALIST,
			Profession.ASSASSIN,
			Profession.RITUALIST,
			Profession.PARAGON,
			Profession.DERVISH,
		];
	}

	/** @returns {Object<{}>} */
	static get NAME(){
		return PHPJS.array_combine(Profession.IDS, [
			{de: 'keine',           en: 'none',        },
			{de: 'Krieger',         en: 'Warrior',     },
			{de: 'Waldläufer',      en: 'Ranger',      },
			{de: 'Mönch',           en: 'Monk',        },
			{de: 'Nekromant',       en: 'Necromancer', },
			{de: 'Mesmer',          en: 'Mesmer',      },
			{de: 'Elementarmagier', en: 'Elementalist',},
			{de: 'Assassine',       en: 'Assassin',    },
			{de: 'Ritualist',       en: 'Ritualist',   },
			{de: 'Paragon',         en: 'Paragon',     },
			{de: 'Derwisch',        en: 'Dervish',     },
		]);
	}

	/** @returns {Object<{}>} */
	static get NAME_ABBR(){
		return PHPJS.array_combine(Profession.IDS, [
			{de: 'X',  en: 'X', },
			{de: 'K',  en: 'W', },
			{de: 'W',  en: 'R', },
			{de: 'Mö', en: 'Mo',},
			{de: 'N',  en: 'N', },
			{de: 'Me', en: 'Me',},
			{de: 'E',  en: 'E', },
			{de: 'A',  en: 'A', },
			{de: 'R',  en: 'Rt',},
			{de: 'P',  en: 'P', },
			{de: 'D',  en: 'D', },
		]);
	}

	static get PRIMARY_ATTRIBUTE(){
		return [
			Attribute.NONE,
			Attribute.STRENGTH,
			Attribute.EXPERTISE,
			Attribute.DIVINE_FAVOR,
			Attribute.SOUL_REAPING,
			Attribute.FAST_CASTING,
			Attribute.ENERGY_STORAGE,
			Attribute.CRITICAL_STRIKES,
			Attribute.SPAWNING_POWER,
			Attribute.LEADERSHIP,
			Attribute.MYSTICISM,
		];
	}

	/**
	 * @param {Lang|string|null} $lang
	 * @returns {string}
	 */
	getAbbr($lang){
		$lang = this._getLang($lang);

		return this.constructor.NAME_ABBR[this.id][$lang.id];
	}

	/**
	 * @param {number|int} $level
	 * @returns {Attribute}
	 */
	getPrimaryAttribute($level = 0){
		return new Attribute(this.getPrimaryAttributeID(), this.lang).setLevel($level);
	}

	/**
	 * @returns {number|int}
	 */
	getPrimaryAttributeID(){
		return this.constructor.PRIMARY_ATTRIBUTE[this.id];
	}

	/**
	 * @returns {number[]|int[]}
	 */
	getAttributes(){
		return Attribute.getByProfession(this);
	}

}
