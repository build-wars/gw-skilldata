/**
 * @created      04.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import {Lang, Type} from '../../es6/index.js';

import {suite, test} from 'mocha';
import {assert} from 'chai';

suite('TypeTest', function(){

	test('constructInvalidIdException', function(){
		assert.throws(() => new Type(666), 'invalid ID');
	});

	test('constructInvalidLanguageException', function(){
		assert.throws(() => new Type(Type.SIGNET, 'foo'), 'invalid language');
	});

	test('getName', function(){
		let type = new Type(Type.SIGNET);

		assert.strictEqual(type.getName(), 'Signet');
		assert.strictEqual(type.getName(Lang.DE), 'Siegel');
	});

	test('getNameInvalidLanguageException', function(){
		assert.throws(() => new Type(Type.SIGNET).getName('foo'), 'invalid language');
	});

	test('withSubtypes', function(){
		let types = new Type(Type.TOUCH_SKILL).withSubtypes();

		let expected = [
			Type.TOUCH_SKILL,
			Type.TOUCH_SPELL,
			Type.TOUCH_ENCHANTMENT_SPELL,
			Type.TOUCH_HEX_SPELL,
			Type.TOUCH_SIGNET,
		];

		assert.containsSubset(types, expected);
	});

});
