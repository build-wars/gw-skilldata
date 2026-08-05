/**
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

import data from '../data/json-full/skilldata.json' with {type: 'json'};
import Skill from './Skill.js';
import Campaign from './Campaign.js';
import Profession from './Profession.js';
import Attribute from './Attribute.js';
import Type from './Type.js';

export default class SkillDataAbstract{

	lang;
	skilldesc;
	skilldata = data.skilldata;

	/**
	 * Returns the data for the given skill ID, including descriptions for the current language
	 *
	 * @param {number|int} $id
	 * @param {boolean} $pvp
	 * @returns {Skill}
	 * @public
	 */
	get($id, $pvp = false){

		if(!this.skilldata[$id]){
			throw new Error('invalid skill ID');
		}

		if(
			// pvp mode, the skill has a pvp redirect but pve version was given
			($pvp && !this.skilldata[$id][Skill.DATA_IS_PVP] && this.skilldata[$id][Skill.DATA_PVP_SPLIT])
			// pve mode, the pvp skill was given, redirect to pve
			|| (!$pvp && this.skilldata[$id][Skill.DATA_IS_PVP] && this.skilldata[$id][Skill.DATA_SPLIT_ID] !== 0)
		){
			$id = this.skilldata[$id][Skill.DATA_SPLIT_ID];
		}

		// we're going to clone the objects here so that we don't leave backreferences
		let $skilldata = Object.assign({}, {...this.skilldata[$id], ...this.skilldesc[$id]});

		return new Skill($skilldata, this.lang);
	}

	/**
	 * @param {string} $key
	 * @param {number|int|boolean} $value
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 * @private
	 */
	#getByKey($key, $value, $pvp){
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
	 * Returns an array with the skill data for each of the given skill IDs
	 *
	 * @param {number[]|int[]} $IDs
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
	 * @param {Campaign|number|int} $campaign
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 */
	getByCampaign($campaign, $pvp = false){

		if(!($campaign instanceof Campaign)){
			$campaign = new Campaign($campaign);
		}

		return this.#getByKey('campaign', $campaign.id, $pvp);
	}

	/**
	 * Returns all skills for the given profession ID
	 *
	 * @param {Profession|number|int} $profession
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 */
	getByProfession($profession, $pvp = false){

		if(!($profession instanceof Profession)){
			$profession = new Profession($profession);
		}

		return this.#getByKey('profession', $profession.id, $pvp);
	}

	/**
	 * Returns all skills for the given attribute ID
	 *
	 * @param {Attribute|number|int} $attribute
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 */
	getByAttribute($attribute, $pvp = false){

		if(!($attribute instanceof Attribute)){
			$attribute = new Attribute($attribute);
		}

		return this.#getByKey('attribute', $attribute.id, $pvp);
	}

	/**
	 * Returns all skills for the given skill type ID
	 *
	 * @param {Type|number|int} $type
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 */
	getByType($type, $pvp = false){

		if(!($type instanceof Type)){
			$type = new Type($type);
		}

		return this.#getByKey('type', $type.id, $pvp);
	}

	/**
	 * Returns all skills for the given skill type ID and its subtypes (if any)
	 *
	 * @param {Type|number|int} $type
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 */
	getByTypeWithSubtypes($type, $pvp = false){

		if(!($type instanceof Type)){
			$type = new Type($type);
		}

		let $types  = $type.withSubtypes();
		let $skills = [];

		for(let id in this.skilldata){
			if($types.includes(this.skilldata[id].type)){
				$skills.push(this.get(id, $pvp));
			}
		}

		return $skills;
	}

	/**
	 * Returns all elite skills
	 *
	 * @param {boolean} $pvp
	 * @returns {Skill[]}
	 */
	getElite($pvp = false){
		return this.#getByKey('is_elite', true, $pvp);
	}

	/**
	 * Returns all roleplay skills
	 *
	 * @returns {Skill[]}
	 */
	getRoleplay(){
		return this.#getByKey('is_rp', true, false);
	}

}
