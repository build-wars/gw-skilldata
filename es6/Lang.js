/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */

/**
 * Encapsulates the available skill data languages
 *
 * @final
 */
export default class Lang{

	static get DE(){return 'de'}
	static get EN(){return 'en'}

	/** @returns {string[]} */
	static get IDS(){
		return [
			Lang.DE,
			Lang.EN,
		];
	}

	/** @returns {{de: string, en: string}} */
	static get NAMES(){
		return {
			de: 'German',
			en: 'English',
		};
	}

	/** @var {string} */
	#id;

	/** @param {string} $id */
	constructor($id = Lang.EN){
		$id = $id.trim().toLowerCase();

		if(!Lang.IDS.includes($id)){
			throw new Error('invalid language');
		}

		this.#id = $id;
	}

	/** @returns {string} */
	get id(){
		return this.#id;
	}

	/** @returns {string} */
	getName(){
		return Lang.NAMES[this.#id];
	}

}
