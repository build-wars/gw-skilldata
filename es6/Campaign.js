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
			{
				de: 'Basis',
				en: 'Core',
				fr: 'Fondamentale',
			},
			{
				de: 'Prophecies',
				en: 'Prophecies',
				fr: 'Prophecies',
			},
			{
				de: 'Factions',
				en: 'Factions',
				fr: 'Factions',
			},
			{
				de: 'Nightfall',
				en: 'Nightfall',
				fr: 'Nightfall',
			},
			{
				de: 'Eye of the North',
				en: 'Eye of the North',
				fr: 'Eye of the North',
			},
		]);
	}

	/** @returns {Object<{}>} */
	static get CONTINENT_NAME(){
		return PHPJS.array_combine(Campaign.IDS, [
			{
				de: 'Die Nebel',
				en: 'The Mists',
				fr: 'Les Brumes',
			},
			{
				de: 'Tyria',
				en: 'Tyria',
				fr: 'Tyrie',
			},
			{
				de: 'Cantha',
				en: 'Cantha',
				fr: 'Cantha',
			},
			{
				de: 'Elona',
				en: 'Elona',
				fr: 'Elona',
			},
			{
				de: 'Tyria',
				en: 'Tyria',
				fr: 'Tyrie',
			},
		]);
	}

	/**
	 * Returns the readable name of the continent for the given campaign ID
	 *
	 * @param {Lang|string|null} $lang
	 * @returns {string}
	 */
	getContinentName($lang){
		$lang = this._getLang($lang);

		return this.constructor.CONTINENT_NAME[this.id][$lang.id];
	}

}
