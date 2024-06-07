/**
 * @created      05.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

/**
 * Attributes (by ID))
 *
 * @type {[{number: {max: number, pri: boolean, name: {de: string, en: string}, prof: number}}]}
 */
const ATTRIBUTES = {
	'0'  : {
		prof: 5,
		pri : true,
		max : 21,
		name: {
			de: 'Schnellwirkung',
			en: 'Fast Casting',
		},
	},
	'1'  : {
		prof: 5,
		pri : false,
		max : 21,
		name: {
			de: 'Illusionsmagie',
			en: 'Illusion Magic',
		},
	},
	'2'  : {
		prof: 5,
		pri : false,
		max : 21,
		name: {
			de: 'Beherrschungsmagie',
			en: 'Domination Magic',
		},
	},
	'3'  : {
		prof: 5,
		pri : false,
		max : 21,
		name: {
			de: 'Inspirationsmagie',
			en: 'Inspiration Magic',
		},
	},
	'4'  : {
		prof: 4,
		pri : false,
		max : 21,
		name: {
			de: 'Blutmagie',
			en: 'Blood Magic',
		},
	},
	'5'  : {
		prof: 4,
		pri : false,
		max : 21,
		name: {
			de: 'Todesmagie',
			en: 'Death Magic',
		},
	},
	'6'  : {
		prof: 4,
		pri : true,
		max : 21,
		name: {
			de: 'Seelensammlung',
			en: 'Soul Reaping',
		},
	},
	'7'  : {
		prof: 4,
		pri : false,
		max : 21,
		name: {
			de: 'Flüche',
			en: 'Curses',
		},
	},
	'8'  : {
		prof: 6,
		pri : false,
		max : 21,
		name: {
			de: 'Luftmagie',
			en: 'Air Magic',
		},
	},
	'9'  : {
		prof: 6,
		pri : false,
		max : 21,
		name: {
			de: 'Erdmagie',
			en: 'Earth Magic',
		},
	},
	'10' : {
		prof: 6,
		pri : false,
		max : 21,
		name: {
			de: 'Feuermagie',
			en: 'Fire Magic',
		},
	},
	'11' : {
		prof: 6,
		pri : false,
		max : 21,
		name: {
			de: 'Wassermagie',
			en: 'Water Magic',
		},
	},
	'12' : {
		prof: 6,
		pri : true,
		max : 21,
		name: {
			de: 'Energiespeicherung',
			en: 'Energy Storage',
		},
	},
	'13' : {
		prof: 3,
		pri : false,
		max : 21,
		name: {
			de: 'Heilgebete',
			en: 'Healing Prayers',
		},
	},
	'14' : {
		prof: 3,
		pri : false,
		max : 21,
		name: {
			de: 'Peinigungsgebete',
			en: 'Smiting Prayers',
		},
	},
	'15' : {
		prof: 3,
		pri : false,
		max : 21,
		name: {
			de: 'Schutzgebete',
			en: 'Protection Prayers',
		},
	},
	'16' : {
		prof: 3,
		pri : true,
		max : 21,
		name: {
			de: 'Gunst der Götter',
			en: 'Divine Favor',
		},
	},
	'17' : {
		prof: 1,
		pri : true,
		max : 21,
		name: {
			de: 'Stärke',
			en: 'Strength',
		},
	},
	'18' : {
		prof: 1,
		pri : false,
		max : 21,
		name: {
			de: 'Axtbeherrschung',
			en: 'Axe Mastery',
		},
	},
	'19' : {
		prof: 1,
		pri : false,
		max : 21,
		name: {
			de: 'Hammerbeherrschung',
			en: 'Hammer Mastery',
		},
	},
	'20' : {
		prof: 1,
		pri : false,
		max : 21,
		name: {
			de: 'Schwertkunst',
			en: 'Swordsmanship',
		},
	},
	'21' : {
		prof: 1,
		pri : false,
		max : 21,
		name: {
			de: 'Taktik',
			en: 'Tactics',
		},
	},
	'22' : {
		prof: 2,
		pri : false,
		max : 20,
		name: {
			de: 'Tierbeherrschung',
			en: 'Beast Mastery',
		},
	},
	'23' : {
		prof: 2,
		pri : true,
		max : 20,
		name: {
			de: 'Fachkenntnis',
			en: 'Expertise',
		},
	},
	'24' : {
		prof: 2,
		pri : false,
		max : 20,
		name: {
			de: 'Überleben in der Wildnis',
			en: 'Wilderness Survival',
		},
	},
	'25' : {
		prof: 2,
		pri : false,
		max : 21,
		name: {
			de: 'Treffsicherheit',
			en: 'Marksmanship',
		},
	},
	'29' : {
		prof: 7,
		pri : false,
		max : 21,
		name: {
			de: 'Dolchbeherrschung',
			en: 'Dagger Mastery',
		},
	},
	'30' : {
		prof: 7,
		pri : false,
		max : 20,
		name: {
			de: 'Tödliche Künste',
			en: 'Deadly Arts',
		},
	},
	'31' : {
		prof: 7,
		pri : false,
		max : 20,
		name: {
			de: 'Schattenkünste',
			en: 'Shadow Arts',
		},
	},
	'32' : {
		prof: 8,
		pri : false,
		max : 21,
		name: {
			de: 'Zwiesprache',
			en: 'Communing',
		},
	},
	'33' : {
		prof: 8,
		pri : false,
		max : 21,
		name: {
			de: 'Wiederherstellungsmagie',
			en: 'Restoration Magic',
		},
	},
	'34' : {
		prof: 8,
		pri : false,
		max : 21,
		name: {
			de: 'Kanalisierungsmagie',
			en: 'Channeling Magic',
		},
	},
	'35' : {
		prof: 7,
		pri : true,
		max : 20,
		name: {
			de: 'Kritische Stöße',
			en: 'Critical Strikes',
		},
	},
	'36' : {
		prof: 8,
		pri : true,
		max : 21,
		name: {
			de: 'Macht des Herbeirufens',
			en: 'Spawning Power',
		},
	},
	'37' : {
		prof: 9,
		pri : false,
		max : 21,
		name: {
			de: 'Speerbeherrschung',
			en: 'Spear Mastery',
		},
	},
	'38' : {
		prof: 9,
		pri : false,
		max : 21,
		name: {
			de: 'Befehlsgewalt',
			en: 'Command',
		},
	},
	'39' : {
		prof: 9,
		pri : false,
		max : 21,
		name: {
			de: 'Motivation',
			en: 'Motivation',
		},
	},
	'40' : {
		prof: 9,
		pri : true,
		max : 20,
		name: {
			de: 'Führung',
			en: 'Leadership',
		},
	},
	'41' : {
		prof: 10,
		pri : false,
		max : 21,
		name: {
			de: 'Sensenbeherrschung',
			en: 'Scythe Mastery',
		},
	},
	'42' : {
		prof: 10,
		pri : false,
		max : 20,
		name: {
			de: 'Windgebete',
			en: 'Wind Prayers',
		},
	},
	'43' : {
		prof: 10,
		pri : false,
		max : 20,
		name: {
			de: 'Erdgebete',
			en: 'Earth Prayers',
		},
	},
	'44' : {
		prof: 10,
		pri : true,
		max : 20,
		name: {
			de: 'Mystik',
			en: 'Mysticism',
		},
	},
	'101': {
		prof: 0,
		pri : false,
		max : 0,
		name: {
			de: 'Kein Attribut',
			en: 'No Attribute',
		},
	},
	'102': {
		prof: 0,
		pri : false,
		max : 10,
		name: {
			de: 'Sonnenspeertitel',
			en: 'Sunspear Title Track',
		},
	},
	'103': {
		prof: 0,
		pri : false,
		max : 8,
		name: {
			de: 'Lichtbringertitel',
			en: 'Lightbringer Title Track',
		},
	},
	'104': {
		prof: 0,
		pri : false,
		max : 12,
		name: {
			de: 'Freund der Luxon',
			en: 'Friend of the Luxons Title Track',
		},
	},
	'105': {
		prof: 0,
		pri : false,
		max : 12,
		name: {
			de: 'Freund der Kurzick',
			en: 'Friend of the Kurzicks Title Track',
		},
	},
	'106': {
		prof: 0,
		pri : false,
		max : 10,
		name: {
			de: 'Asuratitel',
			en: 'Asura Title Track',
		},
	},
	'107': {
		prof: 0,
		pri : false,
		max : 10,
		name: {
			de: 'Deldrimortitel',
			en: 'Deldrimor Title Track',
		},
	},
	'108': {
		prof: 0,
		pri : false,
		max : 10,
		name: {
			de: 'Ebon-Vorhut-Titel',
			en: 'Ebon Vanguard Title Track',
		},
	},
	'109': {
		prof: 0,
		pri : false,
		max : 10,
		name: {
			de: 'Norntitel',
			en: 'Norn Title Track',
		},
	},
};

/**
 * Campaigns (by ID)
 *
 * @type {[{continent: {de: string, en: string}, name: {de: string, en: string}}]}
 */
const CAMPAIGNS = [
	{
		name     : {
			de: 'Basis',
			en: 'Core',
		},
		continent: {
			de: 'Die Nebel',
			en: 'The Mists',
		},
	},
	{
		name     : {
			de: 'Prophecies',
			en: 'Prophecies',
		},
		continent: {
			de: 'Tyria',
			en: 'Tyria',
		},
	},
	{
		name     : {
			de: 'Factions',
			en: 'Factions',
		},
		continent: {
			de: 'Cantha',
			en: 'Cantha',
		},
	},
	{
		name     : {
			de: 'Nightfall',
			en: 'Nightfall',
		},
		continent: {
			de: 'Elona',
			en: 'Elona',
		},
	},
	{
		name     : {
			de: 'Eye of the North',
			en: 'Eye of the North',
		},
		continent: {
			de: 'Tyria',
			en: 'Tyria',
		},
	},
];

/**
 * Professions (by ID)
 *
 * @type {[{pri: number, name: {de: string, en: string}, abbr: {de: string, en: string}}]}
 */
const PROFESSIONS = [
	{
		pri : 101,
		name: {
			de: 'keine',
			en: 'none',
		},
		abbr: {
			de: 'X',
			en: 'X',
		},
	},
	{
		pri : 17,
		name: {
			de: 'Krieger',
			en: 'Warrior',
		},
		abbr: {
			de: 'K',
			en: 'W',
		},
	},
	{
		pri : 23,
		name: {
			de: 'Waldläufer',
			en: 'Ranger',
		},
		abbr: {
			de: 'W',
			en: 'R',
		},
	},
	{
		pri : 16,
		name: {
			de: 'Mönch',
			en: 'Monk',
		},
		abbr: {
			de: 'Mö',
			en: 'Mo',
		},
	},
	{
		pri : 6,
		name: {
			de: 'Nekromant',
			en: 'Necromancer',
		},
		abbr: {
			de: 'N',
			en: 'N',
		},
	},
	{
		pri : 0,
		name: {
			de: 'Mesmer',
			en: 'Mesmer',
		},
		abbr: {
			de: 'Me',
			en: 'Me',
		},
	},
	{
		pri : 12,
		name: {
			de: 'Elementarmagier',
			en: 'Elementalist',
		},
		abbr: {
			de: 'E',
			en: 'E',
		},
	},
	{
		pri : 35,
		name: {
			de: 'Assassine',
			en: 'Assassin',
		},
		abbr: {
			de: 'A',
			en: 'A',
		},
	},
	{
		pri : 36,
		name: {
			de: 'Ritualist',
			en: 'Ritualist',
		},
		abbr: {
			de: 'R',
			en: 'Rt',
		},
	},
	{
		pri : 40,
		name: {
			de: 'Paragon',
			en: 'Paragon',
		},
		abbr: {
			de: 'P',
			en: 'P',
		},
	},
	{
		pri : 44,
		name: {
			de: 'Derwisch',
			en: 'Dervish',
		},
		abbr: {
			de: 'D',
			en: 'D',
		},
	},
];
/**
 * @type {[{name: {de: string, en: string}}]}
 */
const SKILLTYPES = [
	{
		name: {
			de: 'Keine Fertigkeit',
			en: 'Not a Skill',
		},
	},
	{
		name: {
			de: 'Fertigkeit',
			en: 'Skill',
		},
	},
	{
		name: {
			de: 'Bogenangriff',
			en: 'Bow Attack',
		},
	},
	{
		name: {
			de: 'Nahkampfangriff',
			en: 'Melee Attack',
		},
	},
	{
		name: {
			de: 'Axtangriff',
			en: 'Axe Attack',
		},
	},
	{
		name: {
			de: 'Leithandangriff',
			en: 'Lead Attack',
		},
	},
	{
		name: {
			de: 'Begleithandangriff',
			en: 'Off-Hand Attack',
		},
	},
	{
		name: {
			de: 'Doppelangriff',
			en: 'Dual Attack',
		},
	},
	{
		name: {
			de: 'Hammerangriff',
			en: 'Hammer Attack',
		},
	},
	{
		name: {
			de: 'Sensenangriff',
			en: 'Scythe Attack',
		},
	},
	{
		name: {
			de: 'Schwertangriff',
			en: 'Sword Attack',
		},
	},
	{
		name: {
			de: 'Tiergefährtenangriff',
			en: 'Pet Attack',
		},
	},
	{
		name: {
			de: 'Speerangriff',
			en: 'Spear Attack',
		},
	},
	{
		name: {
			de: 'Anfeuerungsruf',
			en: 'Chant',
		},
	},
	{
		name: {
			de: 'Echo',
			en: 'Echo',
		},
	},
	{
		name: {
			de: 'Form',
			en: 'Form',
		},
	},
	{
		name: {
			de: 'Glyphe',
			en: 'Glyph',
		},
	},
	{
		name: {
			de: 'Vorbereitung',
			en: 'Preparation',
		},
	},
	{
		name: {
			de: 'Binderitual',
			en: 'Binding ritual',
		},
	},
	{
		name: {
			de: 'Naturritual',
			en: 'Nature ritual',
		},
	},
	{
		name: {
			de: 'Schrei',
			en: 'Shout',
		},
	},
	{
		name: {
			de: 'Siegel',
			en: 'Signet',
		},
	},
	{
		name: {
			de: 'Zauber',
			en: 'Spell',
		},
	},
	{
		name: {
			de: 'Verzauberung',
			en: 'Enchantment spell',
		},
	},
	{
		name: {
			de: 'Verhexung',
			en: 'Hex Spell',
		},
	},
	{
		name: {
			de: 'Gegenstandszauber',
			en: 'Item Spell',
		},
	},
	{
		name: {
			de: 'Abwehrzauber',
			en: 'Ward Spell',
		},
	},
	{
		name: {
			de: 'Waffenzauber',
			en: 'Weapon Spell',
		},
	},
	{
		name: {
			de: 'Brunnenzauber',
			en: 'Well Spell',
		},
	},
	{
		name: {
			de: 'Haltung',
			en: 'Stance',
		},
	},
	{
		name: {
			de: 'Falle',
			en: 'Trap',
		},
	},
	{
		name: {
			de: 'Distanzangriff',
			en: 'Ranged attack',
		},
	},
	{
		name: {
			de: 'Ebon-Vorhut-Ritual',
			en: 'Ebon Vanguard Ritual',
		},
	},
	{
		name: {
			de: 'Blitzverzauberung',
			en: 'Flash Enchantment',
		},
	},
	{
		name: {
			de: 'Doppelverzauberung',
			en: 'Double Enchantment',
		},
	},
];


export {ATTRIBUTES, CAMPAIGNS, PROFESSIONS, SKILLTYPES};
