/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import Lang from './Lang.js';

/**
 * Abstract parent to the Attribute, Campaign, Profession and Skilltype classes
 *
 * @abstract
 */
export default class DataObjectAbstract{

	/** @var {number|int} */
	#id;
	/** @var {Lang} */
	#lang;

	/**
	 * @param {number|int} $id
	 * @param {Lang|string} $lang
	 */
	constructor($id, $lang = Lang.EN){

		if(!this.constructor.IDS.includes($id)){
			throw new Error('invalid ID');
		}

		if(!($lang instanceof Lang)){
			$lang = new Lang($lang);
		}

		this.#id   = $id;
		this.#lang = $lang;
	}

	/**
	 * @returns {string}
	 * @codeCoverageIgnore
	 */
	static get CSS_CLASS(){
		return '';
	};

	/**
	 * @returns {number[]|int[]}
	 * @codeCoverageIgnore
	 */
	static get IDS(){
		return [];
	}

	/**
	 * @returns {Object<{}>}
	 * @codeCoverageIgnore
	 */
	static get NAME(){
		return {};
	}

	/** @returns {number|int} */
	get id(){
		return this.#id;
	}

	/** @returns {Lang} */
	get lang(){
		return this.#lang;
	}

	/**
	 * @param {Lang|string|null} $lang
	 * @returns {Lang}
	 * @protected
	 */
	_getLang($lang){

		if($lang === null){
			return this.lang;
		}

		if($lang instanceof Lang){
			return $lang;
		}

		return new Lang($lang);
	}

	/**
	 * @param {Lang|string|null} $lang
	 * @returns {string}
	 */
	getName($lang = null){
		$lang = this._getLang($lang);

		return this.constructor.NAME[this.id][$lang.id];
	}

	/**
	 * @param {number|int} $id
	 * @returns {boolean}
	 */
	is($id){
		return this.id === $id;
	}

	/**
	 * @param {number[]|int[]} $ids
	 * @returns {boolean}
	 */
	in($ids){
		return $ids.includes(this.id);
	}

}
