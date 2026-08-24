/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import {Lang} from '../es6/index.js';

import {suite, test} from 'mocha';
import {assert} from 'chai';

suite('LangTest', function(){

	test('constructInvalidLanguageException', function(){
		assert.throws(() => new Lang('foo'), 'invalid language');
	});

	test('id', function(){
		let lang = new Lang();

		assert.strictEqual(lang.id, 'en');
	});

	test('getName', function(){
		let lang = new Lang(Lang.DE);

		assert.strictEqual(lang.getName(), 'German');
	});

});
