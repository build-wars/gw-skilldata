/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import Attribute from './Attribute.js';
import DataObjectAbstract from './DataObjectAbstract.js';
import PHPJS from './PHPJS.js';
import Lang from './Lang.js';

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
			{de: 'keine',           en: 'none',         fr: 'aucun',        },
			{de: 'Krieger',         en: 'Warrior',      fr: 'Guerrier',     },
			{de: 'Waldläufer',      en: 'Ranger',       fr: 'Rôdeur',       },
			{de: 'Mönch',           en: 'Monk',         fr: 'Moine',        },
			{de: 'Nekromant',       en: 'Necromancer',  fr: 'Nécromant',    },
			{de: 'Mesmer',          en: 'Mesmer',       fr: 'Envoûteur',    },
			{de: 'Elementarmagier', en: 'Elementalist', fr: 'Elémentaliste',},
			{de: 'Assassine',       en: 'Assassin',     fr: 'Assassin',     },
			{de: 'Ritualist',       en: 'Ritualist',    fr: 'Ritualiste',   },
			{de: 'Paragon',         en: 'Paragon',      fr: 'Parangon',     },
			{de: 'Derwisch',        en: 'Dervish',      fr: 'Derviche',     },
		]);
	}

	/** @returns {Object<{}>} */
	static get NAME_ABBR(){
		return PHPJS.array_combine(Profession.IDS, [
			{de: 'X',  en: 'X',  fr: 'X', },
			{de: 'K',  en: 'W',  fr: 'G', },
			{de: 'W',  en: 'R',  fr: 'R', },
			{de: 'Mö', en: 'Mo', fr: 'M', },
			{de: 'N',  en: 'N',  fr: 'N', },
			{de: 'Me', en: 'Me', fr: 'En',},
			{de: 'E',  en: 'E',  fr: 'El',},
			{de: 'A',  en: 'A',  fr: 'A', },
			{de: 'R',  en: 'Rt', fr: 'Rt',},
			{de: 'P',  en: 'P',  fr: 'P', },
			{de: 'D',  en: 'D',  fr: 'D', },
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
	 * Returns the short name for the fiven profession ID
	 *
	 * @param {Lang|string|null} $lang
	 * @returns {string}
	 */
	getAbbr($lang){
		$lang = this._getLang($lang);

		return this.constructor.NAME_ABBR[this.id][$lang.id];
	}

	/**
	 * Returns the primary attribute of the current profession
	 *
	 * @param {number|int} $level
	 * @returns {Attribute}
	 */
	getPrimaryAttribute($level = 0){
		return new Attribute(this.getPrimaryAttributeID(), this.lang).setLevel($level);
	}

	/**
	 * Returns the primary attribute ID of the current profession
	 *
	 * @returns {number|int}
	 */
	getPrimaryAttributeID(){
		return this.constructor.PRIMARY_ATTRIBUTE[this.id];
	}

	/**
	 * Returns all attributes for the current profession
	 *
	 * @returns {number[]|int[]}
	 */
	getAttributes(){
		return Attribute.getByProfession(this);
	}

	toHTML($lang = null){
		$lang        = this._getLang($lang);
		let cssClass = [this.constructor.CSS_CLASS, this.getName(Lang.EN).toLowerCase()].join(' ');

		// return an HTML snippet when DOM is not available
		if(typeof document === 'undefined'){
			return `<span class="${cssClass}" data-id="${this.id}" data-lang="${$lang.id}"` +
			       ` data-abbr="${this.getAbbr($lang)}">${this.getName($lang)}</span>`;
		}

		let el = document.createElement('span');
		el.className = cssClass;
		el.innerText = this.getName($lang);

		el.dataset.id   = String(this.id);
		el.dataset.lang = $lang.id;
		el.dataset.abbr = this.getAbbr($lang);

		return el;
	}

}
