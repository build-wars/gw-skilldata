/**
 * @created      11.07.2022
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2022 smiley
 * @license      MIT
 */

export default class PHPJS{

	/**
	 * @link  http://locutus.io/php/var/intval/
	 *
	 * @param {*} $var
	 * @param {number|int|null} $base
	 * @returns {number|int}
	 */
	static intval($var, $base = null){
		let tmp;
		let type = typeof($var);

		if(type === 'boolean'){
			return +$var;
		}

		if(type === 'string'){
			tmp = parseInt($var, $base || 10);

			return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;
		}

		if(type === 'number' && isFinite($var)){
			return $var|0;
		}

		return 0;
	}

	/**
	 * @link https://locutus.io/php/array/array_combine/
	 *
	 * @param {Array} keys
	 * @param {Array} values
	 * @returns {Object<{}>|boolean}
	 */
	static array_combine(keys, values){
		let newArray = {};
		let i = 0;
		// input sanitation
		if(
			// Only accept arrays or array-like objects
			typeof keys !== 'object'
			|| typeof values !== 'object'
			// Require arrays to have a count
			|| typeof keys.length !== 'number'
			|| typeof values.length !== 'number'
			|| !keys.length
			// number of elements does not match
			|| keys.length !== values.length
		){
			return false;
		}

		for(i = 0; i < keys.length; i++){
			newArray[keys[i]] = values[i];
		}

		return newArray;
	}

}
