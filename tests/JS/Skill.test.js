/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import {Attribute, Campaign, Lang, Profession, Skill, Type} from '../../es6/index.js';
import DataObjectAbstract from '../../es6/DataObjectAbstract.js';

import {suite, test} from 'mocha';
import {assert} from 'chai';

suite('SkillTest', function(){

	const _dataObjects = {
		attribute:  Attribute,
		campaign:   Campaign,
		profession: Profession,
		type:       Type,
	};

	const _data = {
		id: 979,
		campaign: 3,
		profession: 5,
		attribute: 2,
		is_elite: false,
		is_rp: false,
		is_pvp: false,
		pvp_split: true,
		split_id: 3191,
		type: 24,
		upkeep: 0,
		energy: 10,
		activation: 2,
		recharge: 12,
		adrenaline: 0,
		adrenaline_precise: 0,
		sacrifice: 0,
		overcast: 0,
		name: 'Mistrust',
		description: 'For 6 seconds, the next spell that target foe casts on one of your allies fails ' +
		             'and deals 10...80 damage to that foe and 75% of that damage to all nearby foes.',
		concise: '(6 seconds.) The next spell that target foe casts on one of your allies fails ' +
		         'and deals 10...80 damage to target and 75% of that damage to nearby foes.',
	};

	test('construct', function(){
		let skill = new Skill(_data);

		// only checking if the instances have been properly invoked
		assert.instanceOf(skill.lang, Lang);
		assert.strictEqual(skill.lang.id, Lang.EN);

		for(let key in _dataObjects){
			assert.instanceOf(skill[key], DataObjectAbstract);
			assert.instanceOf(skill[key], _dataObjects[key]);
		}
	});

	test('toArray', function(){
		let skill = new Skill(_data);
		let arr   = skill.toArray();

		assert.containsSubset(Object.keys(arr), Object.keys(_data));
		assert.containsSubset(Object.values(arr), Object.values(_data));
	});

});
