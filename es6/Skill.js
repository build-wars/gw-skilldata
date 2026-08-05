/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import Lang from './Lang.js';
import Attribute from './Attribute.js';
import Campaign from './Campaign.js';
import Profession from './Profession.js';
import Type from './Type.js';
import DataObjectAbstract from './DataObjectAbstract.js';

/**
 * Represents a single skill with all its unmodified data
 *
 * @final
 */
export default class Skill{

	static get MODE_PVE(){return 'pve'};
	static get MODE_PVP(){return 'pvp'};

	static get DATA_ATTRIBUTE         (){return 'attribute'};
	static get DATA_CAMPAIGN          (){return 'campaign'};
	static get DATA_PROFESSION        (){return 'profession'};
	static get DATA_TYPE              (){return 'type'};
	static get DATA_IS_ELITE          (){return 'is_elite'};
	static get DATA_IS_PVP            (){return 'is_pvp'};
	static get DATA_IS_RP             (){return 'is_rp'};
	static get DATA_PVP_SPLIT         (){return 'pvp_split'};
	static get DATA_ID                (){return 'id'};
	static get DATA_SPLIT_ID          (){return 'split_id'};
	static get DATA_ACTIVATION        (){return 'activation'};
	static get DATA_RECHARGE          (){return 'recharge'};
	static get DATA_ENERGY            (){return 'energy'};
	static get DATA_UPKEEP            (){return 'upkeep'};
	static get DATA_ADRENALINE        (){return 'adrenaline'};
	static get DATA_ADRENALINE_PRECISE(){return 'adrenaline_precise'};
	static get DATA_SACRIFICE         (){return 'sacrifice'};
	static get DATA_EXHAUSTION        (){return 'overcast'};

	static get DESC_NAME              (){return 'name'};
	static get DESC_DESCRIPTION       (){return 'description'};
	static get DESC_CONCISE           (){return 'concise'};

	// we don't have interfaces in JS, so we'll keep these here for now

	/**
	 * The array keys for the descriptions array
	 *
	 * @var {string[]}
	 */
	static get KEYS_DESC(){return [Skill.DESC_NAME, Skill.DESC_DESCRIPTION, Skill.DESC_CONCISE]};

	/**
	 * The array keys for the data array
	 *
	 * @var {string[]}
	 */
	static get KEYS_DATA(){return [
		Skill.DATA_ID, Skill.DATA_CAMPAIGN, Skill.DATA_PROFESSION, Skill.DATA_ATTRIBUTE, Skill.DATA_IS_ELITE,
		Skill.DATA_IS_RP, Skill.DATA_IS_PVP, Skill.DATA_PVP_SPLIT, Skill.DATA_SPLIT_ID, Skill.DATA_TYPE,
		Skill.DATA_UPKEEP, Skill.DATA_ENERGY, Skill.DATA_ACTIVATION, Skill.DATA_RECHARGE, Skill.DATA_ADRENALINE,
		Skill.DATA_ADRENALINE_PRECISE, Skill.DATA_SACRIFICE, Skill.DATA_EXHAUSTION,
	]};

	// ok so now JS has private class fields which... cool.
	// but of course they once again half-assed the implementation and there's no way
	// to check for private properties with Object.hasOwn() or whatever, not even this['#prop'].
	// why even bother when you always just do such short-sighted horseshit, JS Working Group???
	// https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Classes/Private_elements

	#DataObjects = {
		attribute:  Attribute,
		campaign:   Campaign,
		profession: Profession,
		type:       Type,
	};

	#data = {
		id: 0,
		campaign: new Campaign(Campaign.CORE),
		profession: new Profession(Profession.NONE),
		attribute: new Attribute(Attribute.NONE),
		is_elite: false,
		is_rp: false,
		is_pvp: false,
		pvp_split: false,
		split_id: 0,
		type: new Type(Type.NONE),
		upkeep: 0,
		energy: 0,
		activation: 0,
		recharge: 0,
		adrenaline: 0,
		adrenaline_precise: 0,
		sacrifice: 0,
		overcast: 0,
		name: '',
		description: '',
		concise: '',
	};

	#lang;

	/** @returns {Lang} */
	get lang(){return this.#lang};

	/** @returns {Attribute} */
	get attribute(){return this.#data.attribute};
	/** @returns {Campaign} */
	get campaign(){return this.#data.campaign};
	/** @returns {Profession} */
	get profession(){return this.#data.profession};
	/** @returns {Type} */
	get type(){return this.#data.type};

	/** @returns {bool} */
	get is_elite(){return this.#data.is_elite};
	/** @returns {bool} */
	get is_pvp(){return this.#data.is_pvp};
	/** @private */
	/** @returns {bool} */
	get is_rp(){return this.#data.is_rp};
	/** @returns {bool} */
	get pvp_split(){return this.#data.pvp_split};
	/** @returns {int} */
	get id(){return this.#data.id};
	/** @returns {int} */
	get split_id(){return this.#data.split_id};
	/** @returns {int|float} */
	get activation(){return this.#data.activation};
	/** @returns {int} */
	get recharge(){return this.#data.recharge};
	/** @returns {int} */
	get energy(){return this.#data.energy};
	/** @returns {int} */
	get upkeep(){return this.#data.upkeep};
	/** @returns {int} */
	get adrenaline(){return this.#data.adrenaline};
	/** @returns {int|float} */
	get adrenaline_precise(){return this.#data.adrenaline_precise};
	/** @returns {int} */
	get sacrifice(){return this.#data.sacrifice};
	/** @returns {int} */
	get overcast(){return this.#data.overcast};

	/** @returns {string} */
	get name(){return this.#data.name};
	/** @returns {string} */
	get description(){return this.#data.description};
	/** @returns {string} */
	get concise(){return this.#data.concise};

	/**
	 * @param {*} $skilldata
	 * @param {Lang|string} $lang
	 */
	constructor($skilldata, $lang = Lang.EN){

		if(!($lang instanceof Lang)){
			$lang = new Lang($lang);
		}

		this.#lang = $lang;

		for(let key in $skilldata){
			let value = $skilldata[key];

			if(Object.hasOwn(this.#data, key)){

				if(Object.hasOwn(this.#DataObjects, key) && !(value instanceof this.#DataObjects[key])){
					value = new (this.#DataObjects[key])(value);
				}

				this.#data[key] = value;
			}
		}

	}

	toArray(){
		let data = {};

		for(let key of [...Skill.KEYS_DATA, ...Skill.KEYS_DESC]){
			let value = this.#data[key];

			if(value instanceof DataObjectAbstract){
				value = value.id;
			}

			data[key] = value;
		}

		return data;
	}

}
