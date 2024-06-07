/**
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

import en from '../data/json-full/skilldesc-en.json' with { type: 'json' };
import SkillDataAbstract from './SkillDataAbstract.js';

export default class SkillLangEnglish extends SkillDataAbstract{
	lang      = en.lang;
	skilldesc = en.skilldesc;
}
