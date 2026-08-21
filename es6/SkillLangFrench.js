/**
 * @created      21.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */

import fr from '../data/json-full/skilldesc-fr.json' with { type: 'json' };
import SkillDataAbstract from './SkillDataAbstract.js';

/** @final */
export default class SkillLangGerman extends SkillDataAbstract{
	lang      = fr.lang;
	skilldesc = fr.skilldesc;
}
