/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import {Profession, Lang, Attribute} from '../es6/index.js';

import {suite, test} from 'mocha';
import {assert} from 'chai';

suite('ProfessionTest', function(){

	test('constructInvalidIdException', function(){
		assert.throws(() => new Profession(666), 'invalid ID');
	});

	test('constructInvalidLanguageException', function(){
		assert.throws(() => new Profession(Profession.ELEMENTALIST, 'foo'), 'invalid language');
	});

	test('getName', function(){
		let profession = new Profession(Profession.ELEMENTALIST);

		assert.strictEqual(profession.getName(), 'Elementalist');
		assert.strictEqual(profession.getName(Lang.DE), 'Elementarmagier');
	});

	test('getNameInvalidLanguageException', function(){
		assert.throws(() => new Profession(Profession.ELEMENTALIST).getName('foo'), 'invalid language');
	});

	test('getAbbr', function(){
		let profession = new Profession(Profession.ELEMENTALIST);

		assert.strictEqual(profession.getAbbr(), 'E');
		assert.strictEqual(profession.getAbbr(Lang.DE), 'E');
	});

	test('getAbbrInvalidLanguageException', function(){
		assert.throws(() => new Profession(Profession.ELEMENTALIST).getAbbr('foo'), 'invalid language');
	});

	test('getPrimaryAttribute', function(){
		let profession = new Profession(Profession.ELEMENTALIST);
		let attribute  = profession.getPrimaryAttribute(16);

		assert.strictEqual(attribute.id, Attribute.ENERGY_STORAGE);
		assert.strictEqual(attribute.getLevel(), 16);
	});

	test('getPrimaryAttributeID', function(){
		let profession = new Profession(Profession.ELEMENTALIST);

		assert.strictEqual(profession.getPrimaryAttributeID(), Attribute.ENERGY_STORAGE);
	});

	test('getAttributes', function(){
		let profession = new Profession(Profession.ELEMENTALIST);

		let expected = [
			Attribute.AIR_MAGIC,
			Attribute.EARTH_MAGIC,
			Attribute.FIRE_MAGIC,
			Attribute.WATER_MAGIC,
			Attribute.ENERGY_STORAGE,
		];

		assert.containsSubset(profession.getAttributes(), expected);
	});

});
