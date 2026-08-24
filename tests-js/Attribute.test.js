/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import {Attribute, Lang, Profession} from '../es6/index.js';

import {suite, test} from 'mocha';
import {assert} from 'chai';

suite('AttributeTest', function(){

	test('constructInvalidIdException', function(){
		assert.throws(() => new Attribute(666), 'invalid ID');
	});

	test('constructInvalidLanguageException', function(){
		assert.throws(() => new Attribute(Attribute.FAST_CASTING, 'foo'), 'invalid language');
	});

	test('getName', function(){
		let attr = new Attribute(Attribute.FAST_CASTING);

		assert.strictEqual(attr.getName(), 'Fast Casting');
		assert.strictEqual(attr.getName(Lang.DE), 'Schnellwirkung');
	});

	test('getNameInvalidLanguageException', function(){
		assert.throws(() => new Attribute(Attribute.FAST_CASTING).getName('foo'), 'invalid language');
	});

	test('setLevel', function(){
		let attr = new Attribute(Attribute.FAST_CASTING);

		attr.setLevel(69); // test clamping

		assert.strictEqual(attr.getLevel(), 21);
	});

	test('addLevel', function(){
		let attr = new Attribute(Attribute.FAST_CASTING);

		attr.setLevel(12);
		attr.addLevel(4);

		assert.strictEqual(attr.getLevel(), 16);
	});

	let professionProvider = [
		{desc: 'default', attribute: Attribute.FAST_CASTING, expected: Profession.MESMER},
		{desc: 'none',    attribute: Attribute.NONE,         expected: Profession.NONE},
		{desc: 'title',   attribute: Attribute.TITLE_LUXON,  expected: Profession.NONE},
	];

	professionProvider.forEach(({desc, attribute, expected}) => {
		test(`getProfessionID ${desc}`, function(){
			assert.strictEqual(new Attribute(attribute).getProfessionID(), expected);
		});
	});

	professionProvider.forEach(({desc, attribute, expected}) => {
		test(`getProfession ${desc}`, function(){
			assert.isTrue(new Attribute(attribute).getProfession().is(expected));
		});
	});

	let maxValueProvider = [
		{desc: 'default 21', attribute: Attribute.FAST_CASTING,       expected: 21},
		{desc: 'default 20', attribute: Attribute.BEAST_MASTERY,      expected: 20},
		{desc: 'none',       attribute: Attribute.NONE,               expected: 0},
		{desc: 'title',      attribute: Attribute.TITLE_LIGHTBRINGER, expected: 8},
	];

	maxValueProvider.forEach(({desc, attribute, expected}) => {
		test(`getMaxValue ${desc}`, function(){
			assert.strictEqual(new Attribute(attribute).getMaxValue(), expected);
		});
	});

	let byProfessionProvider = [
		{desc: 'Mesmer', profession: Profession.MESMER, expected: [
			Attribute.FAST_CASTING, Attribute.ILLUSION_MAGIC,
			Attribute.DOMINATION_MAGIC, Attribute.INSPIRATION_MAGIC,
		]},
		{desc: 'none', profession: Profession.NONE, expected: [
			Attribute.NONE, Attribute.TITLE_SUNSPEAR, Attribute.TITLE_LIGHTBRINGER,
			Attribute.TITLE_LUXON, Attribute.TITLE_KURZICK, Attribute.TITLE_ASURA,
			Attribute.TITLE_DELDRIMOR, Attribute.TITLE_VANGUARD, Attribute.TITLE_NORN,
		]},
	];

	byProfessionProvider.forEach(({desc, profession, expected}) => {
		test(`getByProfession ${desc}`, function(){
			assert.containsSubset(Attribute.getByProfession(profession), expected);
		});
	});

	let isPrimaryProvider = [
		{desc: 'Fast Casting',     attribute: Attribute.FAST_CASTING,     expected: true},
		{desc: 'Domination Magic', attribute: Attribute.DOMINATION_MAGIC, expected: false},
	];

	isPrimaryProvider.forEach(({desc, attribute, expected}) => {
		test(`isPrimary ${desc}`, function(){
			assert.strictEqual(new Attribute(attribute).isPrimary(), expected);
		});
	});


	let clampValueProvider = [
		{desc: 'none',         attribute: Attribute.NONE,               level: 42,  expected: 0},
		{desc: 'lightbringer', attribute: Attribute.TITLE_LIGHTBRINGER, level: 42,  expected: 8},
		{desc: 'norn',         attribute: Attribute.TITLE_NORN,         level: 42,  expected: 10},
		{desc: 'luxon',        attribute: Attribute.TITLE_LUXON,        level: 42,  expected: 12},
		{desc: 'default',      attribute: Attribute.FAST_CASTING,       level: 42,  expected: 21},
		{desc: 'negative',     attribute: Attribute.FAST_CASTING,       level: -42, expected: 0},
	];

	clampValueProvider.forEach(({desc, attribute, level, expected}) => {
		test(`clamp ${desc}`, function(){
			assert.strictEqual(new Attribute(attribute).clamp(level), expected);
		});
	});

	let progressionFunctionProvider = [
		{desc: 'lightbringer', attribute: Attribute.TITLE_LIGHTBRINGER, expected15: 15},
		{desc: 'norn',         attribute: Attribute.TITLE_NORN,         expected15: 15},
		{desc: 'luxon',        attribute: Attribute.TITLE_LUXON,        expected15: 15},
		{desc: 'default',      attribute: Attribute.FAST_CASTING,       expected15: 21},
	];

	progressionFunctionProvider.forEach(({desc, attribute, expected15}) => {
		test(`getProgressionFunction ${desc}`, function(){
			let attr = new Attribute(attribute);
			let $fn   = attr.getProgressionFunction();

			assert.strictEqual($fn(0, 0, 15), 0);
			assert.strictEqual($fn(attr.getMaxValue(), 0, 15), expected15);
		});
	});

	let progressionValueProvider = [
		// standard progression -> https://wiki.guildwars.com/wiki/Ineptitude
		{desc: 'standard', attribute: Attribute.ILLUSION_MAGIC, val0: 30, val15: 135, expected: [
			[0, 30], [12, 114], [15, 135], [21, 177],
		]},
		// PvE attribute: luxon/kurzick -> https://wiki.guildwars.com/wiki/Summon_Spirits
		{desc: 'factions', attribute: Attribute.TITLE_LUXON, val0: 60, val15: 100, expected: [
			[0, 60], [3, 79], [6, 100], [12, 100],
		]},
		// PvE attribute: lightbringer -> https://wiki.guildwars.com/wiki/Lightbringer_Signet
		{desc: 'lightbringer', attribute: Attribute.TITLE_LIGHTBRINGER, val0: 16, val15: 24, expected: [
			[0, 16], [3, 22], [4, 24], [8, 24],
		]},
		// PvE attribute: sunspear -> https://wiki.guildwars.com/wiki/Vampirism
		{desc: 'pve1', attribute: Attribute.TITLE_SUNSPEAR, val0: 75, val15: 150, expected: [
			[0, 75], [3, 120], [5, 150], [10, 150],
		]},
		// PvE attribute: eotn -> https://wiki.guildwars.com/wiki/Dwarven_Stability
		{desc: 'pve2', attribute: Attribute.TITLE_DELDRIMOR, val0: 55, val15: 100, expected: [
			[0, 55], [3, 82], [5, 100], [10, 100],
		]},
	];

	progressionValueProvider.forEach(({desc, attribute, val0, val15, expected}) => {
		test(`getProgressionValue ${desc}`, function(){
			let attr = new Attribute(attribute);

			for(let [level, expectedValue] of expected){
				let value = attr.setLevel(level).getProgressionValue(val0, val15);

				assert.strictEqual(value, expectedValue);

				// alternative
				value = new Attribute(attribute).getProgressionValue(val0, val15, level);

				assert.strictEqual(value, expectedValue);
			}
		});
	});

	progressionValueProvider.forEach(({desc, attribute, val0, val15, expected}) => {
		test(`getProgressionValue ${desc}`, function(){
			let table = new Attribute(attribute).getProgressionTable(val0, val15);

			for(let [level, expectedValue] of expected){
				assert.strictEqual(table[level], expectedValue);
			}
		});
	});

});
