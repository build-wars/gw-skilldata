/**
 * @created      04.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

import de from '../data/json-full/skilldesc-de.json' with { type: 'json' };
import SkillDataAbstract from './SkillDataAbstract.js';

export default class SkillLangGerman extends SkillDataAbstract{
	lang      = de.lang;
	skilldesc = de.skilldesc;
}
