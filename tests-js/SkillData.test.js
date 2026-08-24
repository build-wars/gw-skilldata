/**
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
import {Attribute, Campaign, Profession, SkillLangEnglish, SkillLangGerman, Type} from '../es6/index.js';
import SkillDataAbstract from '../es6/SkillDataAbstract.js';

import {beforeEach, suite, test} from 'mocha';
import {assert} from 'chai';

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

			if(_skillData instanceof SkillLangEnglish){
				assert.strictEqual(data.name, 'Mistrust (PvP)');
			}

			data = _skillData.get(3191, false);

			assert.strictEqual(data.id, 979);

			if(_skillData instanceof SkillLangEnglish){
				assert.strictEqual(data.name, 'Mistrust');
			}

		});

		test('invalidIdException', function(){
			assert.throws(() => _skillData.get(69420), 'invalid skill ID')
		});

		test('getAll', function(){
			let IDs  = [782, 780, 775, 1954, 952, 2356, 1649, 1018];
			let data = _skillData.getAll(IDs);
			let keys = Object.keys(data);

			assert.lengthOf(keys, IDs.length)
			assert.containsSubset(keys.map(k => k - 0), IDs)
		});

		test('getByCampaign', function(){
			let data = _skillData.getByCampaign(Campaign.CORE);

			for(let skill of data){
				assert.strictEqual(skill.campaign.id, Campaign.CORE);
			}
		});

		test('getByProfession', function(){
			let data = _skillData.getByProfession(Profession.MESMER);

			for(let skill of data){
				assert.strictEqual(skill.profession.id, Profession.MESMER);
			}
		});

		test('getByAttribute', function(){
			let data = _skillData.getByAttribute(Attribute.FAST_CASTING);

			for(let skill of data){
				assert.strictEqual(skill.attribute.id, Attribute.FAST_CASTING);
			}
		});

		test('getByType', function(){
			let data = _skillData.getByType(Type.HEX_SPELL);

			for(let skill of data){
				assert.strictEqual(skill.type.id, Type.HEX_SPELL);
			}
		});

		test('getByTypeWithSubtypes', function(){
			let data = _skillData.getByTypeWithSubtypes(Type.TOUCH_SKILL);

			let $expected = [
				Type.TOUCH_SKILL, Type.TOUCH_SPELL, Type.TOUCH_ENCHANTMENT_SPELL,
				Type.TOUCH_HEX_SPELL, Type.TOUCH_SIGNET,
			];

			for(let skill of data){
				assert.isTrue($expected.includes(skill.type.id));
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
