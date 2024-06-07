/**
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

import data from '../data/json-full/skilldata.json' with {type: 'json'};
import {ATTRIBUTES, CAMPAIGNS, PROFESSIONS, SKILLTYPES} from './constants.js';

export default class SkillDataAbstract{

	lang;
	skilldesc;
	skilldata = data.skilldata;

	/**
	 * @param {int} $id
	 * @returns {*}
	 * @private
	 */
	combine($id){

		if(!this.skilldata[$id]){
			throw new Error('invalid skill ID');
		}

		// we're going to clone the objects here so that we don't leave backreferences
		let skilldata = Object.assign({}, {...this.skilldata[$id], ...this.skilldesc[$id]});

		return Object.assign(skilldata, {
			campaign_name  : CAMPAIGNS[skilldata.campaign].name[this.lang],
			profession_name: PROFESSIONS[skilldata.profession].name[this.lang],
			profession_abbr: PROFESSIONS[skilldata.profession].abbr[this.lang],
			attribute_name : ATTRIBUTES[skilldata.attribute].name[this.lang],
			type_name      : SKILLTYPES[skilldata.type].name[this.lang],
		});
	}

	/**
	 * @param {string} $key
	 * @param {int|boolean} $value
	 * @param {boolean} $pvp
	 * @returns {[]}
	 * @private
	 */
	getByKey($key, $value, $pvp){
		let skills = [];

		for(let id in this.skilldata){
			let data = this.skilldata[id];

			if(data[$key] === $value){
				skills.push(this.get(id, $pvp));
			}

		}

		return skills;
	}

	/**
	 * Returns the data for the given skill ID, including descriptions for the current language
	 *
	 * @param {int} $id
	 * @param {boolean} $pvp
	 * @returns {*}
	 * @public
	 */
	get($id, $pvp = false){
		let data = this.combine($id);

		if($pvp === false || data.pvp_split === false){
			return data;
		}

		return this.combine(data.split_id);
	}

	/**
	 * Returns an array with the skill data for each of the given skill IDs
	 *
	 * @param {int[]} $IDs
	 * @param {boolean} $pvp
	 * @returns {*}
	 * @public
	 */
	getAll($IDs, $pvp = false){
		let skills = {};

		for(let id of $IDs){
			skills[id] = this.get(id, $pvp);
		}

		return skills;
	}

	/**
	 * Returns all skills for the given campaign ID
	 *
	 * @param {int} $campaign
	 * @param {boolean} $pvp
	 * @returns {[]}
	 */
	getByCampaign($campaign, $pvp = false){

		if(!CAMPAIGNS[$campaign]){
			throw new Error('invalid campaign ID'); // @codeCoverageIgnore
		}

		return this.getByKey('campaign', $campaign, $pvp);
	}

	/**
	 * Returns all skills for the given profession ID
	 *
	 * @param {int} $profession
	 * @param {boolean} $pvp
	 * @returns {[]}
	 */
	getByProfession($profession, $pvp = false){

		if(!PROFESSIONS[$profession]){
			throw new Error('invalid profession ID'); // @codeCoverageIgnore
		}

		return this.getByKey('profession', $profession, $pvp);
	}

	/**
	 * Returns all skills for the given attribute ID
	 *
	 * @param {int} $attribute
	 * @param {boolean} $pvp
	 * @returns {[]}
	 */
	getByAttribute($attribute, $pvp = false){

		if(!ATTRIBUTES[$attribute]){
			throw new Error('invalid attribute ID'); // @codeCoverageIgnore
		}

		return this.getByKey('attribute', $attribute, $pvp);
	}

	/**
	 * Returns all skills for the given skill type ID
	 *
	 * @param {int} $type
	 * @param {boolean} $pvp
	 * @returns {[]}
	 */
	getByType($type, $pvp = false){

		if(!SKILLTYPES[$type]){
			throw new Error('invalid skill type ID'); // @codeCoverageIgnore
		}

		return this.getByKey('type', $type, $pvp);
	}

	/**
	 * Returns all elite skills
	 *
	 * @param {boolean} $pvp
	 * @returns {[]}
	 */
	getElite($pvp = false){
		return this.getByKey('is_elite', true, $pvp);
	}

	/**
	 * Returns all roleplay skills
	 *
	 * @returns {[]}
	 */
	getRoleplay(){
		return this.getByKey('is_rp', true, false);
	}

}
