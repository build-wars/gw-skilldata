/**
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

import {SkillLangEnglish, SkillLangGerman} from '../es6/index.js';

import {beforeEach, suite, test} from 'mocha';
import {assert} from 'chai';
import SkillDataAbstract from '../es6/SkillDataAbstract.js';

/**
 * Tests basic functions of the SkillData class
 */
suite('SkillDataTest', function(){

	let skilldataProvider = [
		{$fqn: SkillLangEnglish, desc: 'SkillLangEnglish'},
		{$fqn: SkillLangGerman, desc: 'SkillLangGerman'},
	];

	skilldataProvider.forEach(({$fqn, desc}) => {

		let _skillData;

		beforeEach(function(){
			_skillData = new $fqn();
		});


		test('instance', function(){
			assert.instanceOf(_skillData, SkillDataAbstract);
		});

		test('get', function(){
			let data = _skillData.get(0, true);

			assert.strictEqual(data.id, 0);
		});

		test('getPvpRedirect', function(){
			let data = _skillData.get(979, true);

			assert.strictEqual(data.id, 3191);
		});

		test('invalidIdException', function(){
			assert.throws(() => _skillData.get(69420), 'invalid skill ID')
		});

		test('getAll', function(){
			let IDs  = [782, 780, 775, 1954, 952, 2356, 1649, 1018];
			let data = _skillData.getAll(IDs);

			assert.lengthOf(Object.keys(data), IDs.length)
		});

		test('getByCampaign', function(){
			let data = _skillData.getByCampaign(0);

			for(let skill of data){
				assert.strictEqual(skill.campaign, 0);
			}
		});

		test('getByProfession', function(){
			let data = _skillData.getByProfession(5);

			for(let skill of data){
				assert.strictEqual(skill.profession, 5);
			}
		});

		test('getByAttribute', function(){
			let data = _skillData.getByAttribute(0);

			for(let skill of data){
				assert.strictEqual(skill.attribute, 0);
			}
		});

		test('getByType', function(){
			let data = _skillData.getByType(24);

			for(let skill of data){
				assert.strictEqual(skill.type, 24);
			}
		});

		test('getElite', function(){
			let data = _skillData.getElite();

			for(let skill of data){
				assert.isTrue(skill.is_elite);
			}
		});

		test('getRoleplay', function(){
			let data = _skillData.getRoleplay();

			for(let skill of data){
				assert.isTrue(skill.is_rp);
			}
		});

	});

});
