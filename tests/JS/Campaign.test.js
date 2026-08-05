/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import {Campaign, Lang} from '../../es6/index.js';

import {suite, test} from 'mocha';
import {assert} from 'chai';

suite('CampaignTest', function(){

	test('constructInvalidIdException', function(){
		assert.throws(() => new Campaign(666), 'invalid ID');
	});

	test('constructInvalidLanguageException', function(){
		assert.throws(() => new Campaign(Campaign.CORE, 'foo'), 'invalid language');
	});

	test('getName', function(){
		let campaign = new Campaign(Campaign.CORE);

		assert.strictEqual(campaign.getName(), 'Core');
		assert.strictEqual(campaign.getName(Lang.DE), 'Basis');
	});

	test('getNameInvalidLanguageException', function(){
		assert.throws(() => new Campaign(Campaign.CORE).getName('foo'), 'invalid language');
	});

	test('getContinentName', function(){
		let campaign = new Campaign(Campaign.CORE);

		assert.strictEqual(campaign.getContinentName(), 'The Mists');
		assert.strictEqual(campaign.getContinentName(Lang.DE), 'Die Nebel');
	});

	test('getContinentNameInvalidLanguageException', function(){
		assert.throws(() => new Campaign(Campaign.CORE).getContinentName('foo'), 'invalid language');
	});

});
