/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import DataObjectAbstract from './DataObjectAbstract.js';
import PHPJS from './PHPJS.js';

/**
 * Encapsulates all campaign related static data
 *
 * @final
 */
export default class Campaign extends DataObjectAbstract{

	static get CSS_CLASS(){return 'campaign'};

	static get CORE            (){return 0}
	static get PROPHECIES      (){return 1}
	static get FACTIONS        (){return 2}
	static get NIGHTFALL       (){return 3}
	static get EYE_OF_THE_NORTH(){return 4}

	/** @returns {number[]|int[]}*/
	static get IDS(){
		return [
			Campaign.CORE,
			Campaign.PROPHECIES,
			Campaign.FACTIONS,
			Campaign.NIGHTFALL,
			Campaign.EYE_OF_THE_NORTH,
		];
	}

	/** @returns {Object<{}>} */
	static get NAME(){
		return PHPJS.array_combine(Campaign.IDS, [
			{de: 'Basis',            en: 'Core',            },
			{de: 'Prophecies',       en: 'Prophecies',      },
			{de: 'Factions',         en: 'Factions',        },
			{de: 'Nightfall',        en: 'Nightfall',       },
			{de: 'Eye of the North', en: 'Eye of the North',},
		]);
	}

	/** @returns {Object<{}>} */
	static get CONTINENT_NAME(){
		return PHPJS.array_combine(Campaign.IDS, [
			{de: 'Die Nebel', en: 'The Mists',},
			{de: 'Tyria',     en: 'Tyria',    },
			{de: 'Cantha',    en: 'Cantha',   },
			{de: 'Elona',     en: 'Elona',    },
			{de: 'Tyria',     en: 'Tyria',    },
		]);
	}

	/**
	 * @param {Lang|string|null} $lang
	 * @returns {string}
	 */
	getContinentName($lang){
		$lang = this._getLang($lang);

		return this.constructor.CONTINENT_NAME[this.id][$lang.id];
	}

}
