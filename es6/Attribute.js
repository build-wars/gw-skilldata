/**
 * @created      03.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
import DataObjectAbstract from './DataObjectAbstract.js';
import PHPJS from './PHPJS.js';
import Profession from './Profession.js';
import Lang from './Lang.js';

/**
 * Encapsulates all skill attribute related static data
 *
 * @final
 */
export default class Attribute extends DataObjectAbstract{
	// we're using static getters here to emulate PHP class constants
	static get CSS_CLASS(){return 'attribute'};

	static get FAST_CASTING       (){return 0}
	static get ILLUSION_MAGIC     (){return 1}
	static get DOMINATION_MAGIC   (){return 2}
	static get INSPIRATION_MAGIC  (){return 3}
	static get BLOOD_MAGIC        (){return 4}
	static get DEATH_MAGIC        (){return 5}
	static get SOUL_REAPING       (){return 6}
	static get CURSES             (){return 7}
	static get AIR_MAGIC          (){return 8}
	static get EARTH_MAGIC        (){return 9}
	static get FIRE_MAGIC         (){return 10}
	static get WATER_MAGIC        (){return 11}
	static get ENERGY_STORAGE     (){return 12}
	static get HEALING_PRAYERS    (){return 13}
	static get SMITING_PRAYERS    (){return 14}
	static get PROTECTION_PRAYERS (){return 15}
	static get DIVINE_FAVOR       (){return 16}
	static get STRENGTH           (){return 17}
	static get AXE_MASTERY        (){return 18}
	static get HAMMER_MASTERY     (){return 19}
	static get SWORDMANSHIP       (){return 20}
	static get TACTICS            (){return 21}
	static get BEAST_MASTERY      (){return 22}
	static get EXPERTISE          (){return 23}
	static get WILDERNESS_SURVIVAL(){return 24}
	static get MARKMANSHIP        (){return 25}
	static get DAGGER_MASTERY     (){return 29}
	static get DEADLY_ARTS        (){return 30}
	static get SHADOW_ARTS        (){return 31}
	static get COMMUNING          (){return 32}
	static get RESTORATION_MAGIC  (){return 33}
	static get CHANNELING_MAGIC   (){return 34}
	static get CRITICAL_STRIKES   (){return 35}
	static get SPAWNING_POWER     (){return 36}
	static get SPEAR_MASTERY      (){return 37}
	static get COMMAND            (){return 38}
	static get MOTIVATION         (){return 39}
	static get LEADERSHIP         (){return 40}
	static get SCYTHE_MASTERY     (){return 41}
	static get WIND_PRAYERS       (){return 42}
	static get EARTH_PRAYERS      (){return 43}
	static get MYSTICISM          (){return 44}
	// not exactly sure what to do with the "no attribute" - technically we could move it to -1
	static get NONE               (){return 101}
	// PvE titles are technically attributes - WTB "official" internal IDs
	static get TITLE_SUNSPEAR     (){return 102}
	static get TITLE_LIGHTBRINGER (){return 103}
	static get TITLE_LUXON        (){return 104}
	static get TITLE_KURZICK      (){return 105}
	static get TITLE_ASURA        (){return 106}
	static get TITLE_DELDRIMOR    (){return 107}
	static get TITLE_VANGUARD     (){return 108}
	static get TITLE_NORN         (){return 109}

	/** @returns {number[]|int[]} */
	static get IDS(){
		return [
			Attribute.FAST_CASTING, Attribute.ILLUSION_MAGIC, Attribute.DOMINATION_MAGIC, Attribute.INSPIRATION_MAGIC,
			Attribute.BLOOD_MAGIC, Attribute.DEATH_MAGIC, Attribute.SOUL_REAPING, Attribute.CURSES,
			Attribute.AIR_MAGIC, Attribute.EARTH_MAGIC, Attribute.FIRE_MAGIC, Attribute.WATER_MAGIC, Attribute.ENERGY_STORAGE,
			Attribute.HEALING_PRAYERS, Attribute.SMITING_PRAYERS, Attribute.PROTECTION_PRAYERS, Attribute.DIVINE_FAVOR,
			Attribute.STRENGTH, Attribute.AXE_MASTERY, Attribute.HAMMER_MASTERY, Attribute.SWORDMANSHIP, Attribute.TACTICS,
			Attribute.BEAST_MASTERY, Attribute.EXPERTISE, Attribute.WILDERNESS_SURVIVAL, Attribute.MARKMANSHIP,
			Attribute.DAGGER_MASTERY, Attribute.DEADLY_ARTS, Attribute.SHADOW_ARTS,
			Attribute.COMMUNING, Attribute.RESTORATION_MAGIC, Attribute.CHANNELING_MAGIC,
			Attribute.CRITICAL_STRIKES, Attribute.SPAWNING_POWER,
			Attribute.SPEAR_MASTERY, Attribute.COMMAND, Attribute.MOTIVATION, Attribute.LEADERSHIP,
			Attribute.SCYTHE_MASTERY, Attribute.WIND_PRAYERS, Attribute.EARTH_PRAYERS, Attribute.MYSTICISM,
			Attribute.NONE,
			Attribute.TITLE_SUNSPEAR, Attribute.TITLE_LIGHTBRINGER, Attribute.TITLE_LUXON, Attribute.TITLE_KURZICK,
			Attribute.TITLE_ASURA, Attribute.TITLE_DELDRIMOR, Attribute.TITLE_VANGUARD, Attribute.TITLE_NORN,
		];
	}

	/** @returns {Object<{}>} */
	static get NAME(){
		return PHPJS.array_combine(Attribute.IDS, [
			{
				de: 'Schnellwirkung',
				en: 'Fast Casting',
				fr: 'Incantation rapide',
			},
			{
				de: 'Illusionsmagie',
				en: 'Illusion Magic',
				fr: 'Magie de l\'illusion',
			},
			{
				de: 'Beherrschungsmagie',
				en: 'Domination Magic',
				fr: 'Magie de domination',
			},
			{
				de: 'Inspirationsmagie',
				en: 'Inspiration Magic',
				fr: 'Magie de l\'inspiration',
			},
			{
				de: 'Blutmagie',
				en: 'Blood Magic',
				fr: 'Magie du sang',
			},
			{
				de: 'Todesmagie',
				en: 'Death Magic',
				fr: 'Magie de la mort',
			},
			{
				de: 'Seelensammlung',
				en: 'Soul Reaping',
				fr: 'Moisson des âmes',
			},
			{
				de: 'Flüche',
				en: 'Curses',
				fr: 'Malédictions',
			},
			{
				de: 'Luftmagie',
				en: 'Air Magic',
				fr: 'Magie de l\'air',
			},
			{
				de: 'Erdmagie',
				en: 'Earth Magic',
				fr: 'Magie de la terre',
			},
			{
				de: 'Feuermagie',
				en: 'Fire Magic',
				fr: 'Magie du feu',
			},
			{
				de: 'Wassermagie',
				en: 'Water Magic',
				fr: 'Magie de l\'eau',
			},
			{
				de: 'Energiespeicherung',
				en: 'Energy Storage',
				fr: 'Conservation d\'énergie',
			},
			{
				de: 'Heilgebete',
				en: 'Healing Prayers',
				fr: 'Prières de guérison',
			},
			{
				de: 'Peinigungsgebete',
				en: 'Smiting Prayers',
				fr: 'Prières de châtiment',
			},
			{
				de: 'Schutzgebete',
				en: 'Protection Prayers',
				fr: 'Prières de protection',
			},
			{
				de: 'Gunst der Götter',
				en: 'Divine Favor',
				fr: 'Faveur divine',
			},
			{
				de: 'Stärke',
				en: 'Strength',
				fr: 'Force',
			},
			{
				de: 'Axtbeherrschung',
				en: 'Axe Mastery',
				fr: 'Maîtrise de la hache',
			},
			{
				de: 'Hammerbeherrschung',
				en: 'Hammer Mastery',
				fr: 'Maîtrise du marteau',
			},
			{
				de: 'Schwertkunst',
				en: 'Swordsmanship',
				fr: 'Maîtrise de l\'épée',
			},
			{
				de: 'Taktik',
				en: 'Tactics',
				fr: 'Tactique',
			},
			{
				de: 'Tierbeherrschung',
				en: 'Beast Mastery',
				fr: 'Domptage',
			},
			{
				de: 'Fachkenntnis',
				en: 'Expertise',
				fr: 'Expertise',
			},
			{
				de: 'Überleben in der Wildnis',
				en: 'Wilderness Survival',
				fr: 'Survie',
			},
			{
				de: 'Treffsicherheit',
				en: 'Marksmanship',
				fr: 'Adresse au tir',
			},
			{
				de: 'Dolchbeherrschung',
				en: 'Dagger Mastery',
				fr: 'Maîtrise de la dague',
			},
			{
				de: 'Tödliche Künste',
				en: 'Deadly Arts',
				fr: 'Arts létaux',
			},
			{
				de: 'Schattenkünste',
				en: 'Shadow Arts',
				fr: 'Arts des ombres',
			},
			{
				de: 'Zwiesprache',
				en: 'Communing',
				fr: 'Communion',
			},
			{
				de: 'Wiederherstellungsmagie',
				en: 'Restoration Magic',
				fr: 'Magie de restauration',
			},
			{
				de: 'Kanalisierungsmagie',
				en: 'Channeling Magic',
				fr: 'Magie de la canalisation',
			},
			{
				de: 'Kritische Stöße',
				en: 'Critical Strikes',
				fr: 'Attaques critiques',
			},
			{
				de: 'Macht des Herbeirufens',
				en: 'Spawning Power',
				fr: 'Puissance de l\'Invocation',
			},
			{
				de: 'Speerbeherrschung',
				en: 'Spear Mastery',
				fr: 'Maîtrise du javelot',
			},
			{
				de: 'Befehlsgewalt',
				en: 'Command',
				fr: 'Commandement',
			},
			{
				de: 'Motivation',
				en: 'Motivation',
				fr: 'Motivation',
			},
			{
				de: 'Führung',
				en: 'Leadership',
				fr: 'Charisme',
			},
			{
				de: 'Sensenbeherrschung',
				en: 'Scythe Mastery',
				fr: 'Maîtrise de la faux',
			},
			{
				de: 'Windgebete',
				en: 'Wind Prayers',
				fr: 'Prières du Vent',
			},
			{
				de: 'Erdgebete',
				en: 'Earth Prayers',
				fr: 'Prières de la Terre',
			},
			{
				de: 'Mystik',
				en: 'Mysticism',
				fr: 'Mysticisme',
			},
			{
				de: 'Kein Attribut',
				en: 'No Attribute',
				fr: 'Aucune caractéristique',
			},
			{
				de: 'Sonnenspeertitel',
				en: 'Sunspear Title Track',
				fr: 'Titre de Lancier du Soleil',
			},
			{
				de: 'Lichtbringertitel',
				en: 'Lightbringer Title Track',
				fr: 'Titre de Porteur de Lumière',
			},
			{
				de: 'Freund der Luxon',
				en: 'Friend of the Luxons Title Track',
				fr: 'Titre d\'Ami des Luxons',
			},
			{
				de: 'Freund der Kurzick',
				en: 'Friend of the Kurzicks Title Track',
				fr: 'Titre d\'Ami des Kurzicks',
			},
			{
				de: 'Asuratitel',
				en: 'Asura Title Track',
				fr: 'Titre d\'Asura',
			},
			{
				de: 'Deldrimortitel',
				en: 'Deldrimor Title Track',
				fr: 'Titre de Deldrimor',
			},
			{
				de: 'Ebon-Vorhut-Titel',
				en: 'Ebon Vanguard Title Track',
				fr: 'Titre de l\'Avant-garde d\'Ebon',
			},
			{
				de: 'Norntitel',
				en: 'Norn Title Track',
				fr: 'Titre de Norn',
			},
		]);
	}

	/** @returns {Object<{}>} */
	static get PROFESSION(){
		return PHPJS.array_combine(Attribute.IDS, [
			Profession.MESMER, Profession.MESMER, Profession.MESMER, Profession.MESMER,
			Profession.NECROMANCER, Profession.NECROMANCER, Profession.NECROMANCER, Profession.NECROMANCER,
			Profession.ELEMENTALIST, Profession.ELEMENTALIST, Profession.ELEMENTALIST, Profession.ELEMENTALIST, Profession.ELEMENTALIST,
			Profession.MONK, Profession.MONK, Profession.MONK, Profession.MONK,
			Profession.WARRIOR, Profession.WARRIOR, Profession.WARRIOR, Profession.WARRIOR, Profession.WARRIOR,
			Profession.RANGER, Profession.RANGER, Profession.RANGER, Profession.RANGER,
			Profession.ASSASSIN, Profession.ASSASSIN, Profession.ASSASSIN,
			Profession.RITUALIST, Profession.RITUALIST, Profession.RITUALIST,
			Profession.ASSASSIN, Profession.RITUALIST,
			Profession.PARAGON, Profession.PARAGON, Profession.PARAGON, Profession.PARAGON,
			Profession.DERVISH, Profession.DERVISH, Profession.DERVISH, Profession.DERVISH,
			Profession.NONE,
			Profession.NONE, Profession.NONE, Profession.NONE, Profession.NONE,
			Profession.NONE, Profession.NONE, Profession.NONE, Profession.NONE,
		]);
	}

	/** @returns {Object<{}>} */
	static get MAX_VALUE(){
		return PHPJS.array_combine(Attribute.IDS, [
			21, 21, 21, 21, 21, 21, 21, 21, 21, 21,
			21, 21, 21, 21, 21, 21, 21, 21, 21, 21,
			21, 21, 20, 20, 20, 21, 21, 20, 20, 21,
			21, 21, 20, 21, 21, 21, 21, 20, 21, 20,
			20, 21,  0, 10,  8, 12, 12, 10, 10, 10,
			10,
		]);
	}

	/** @var {number|int} */
	#level = 0;

	/**
	 * Sets the attribute level
	 *
	 * @param {number|int} $level
	 * @returns {Attribute}
	 */
	setLevel($level){
		this.#level = this.clamp($level);

		return this;
	}

	/**
	 * Adds the given amount to the current attribute level
	 *
	 * @param {number|int} $level
	 * @returns {Attribute}
	 */
	addLevel($level){
		return this.setLevel(this.#level + $level);
	}

	/**
	 * Returns the current attribute level
	 *
	 * @returns {number|int}
	 */
	getLevel(){
		return this.#level;
	}

	/**
	 * Returns the profession for the current attribute
	 *
	 * @returns {Profession}
	 */
	getProfession(){
		return new Profession(this.getProfessionID(), this.lang);
	}

	/**
	 * Returns the profession ID for the current attribute
	 *
	 * @returns {number|int}
	 */
	getProfessionID(){
		return Attribute.PROFESSION[this.id];
	}

	/**
	 * Returns the internal max value for the current attribute
	 *
	 * @returns {number|int}
	 */
	getMaxValue(){
		return Attribute.MAX_VALUE[this.id];
	}

	/**
	 * Returns all attributes for the given profession
	 *
	 * @returns {number[]|int[]}
	 */
	static getByProfession($profession){

		if(!($profession instanceof Profession)){
			$profession = new Profession($profession);
		}

		let $attributeProfessions = Attribute.PROFESSION;
		let $attributes           = [];

		for(let $attr in $attributeProfessions){
			if($attributeProfessions[$attr] === $profession.id){
				$attributes.push(PHPJS.intval($attr));
			}
		}

		return $attributes;
	}

	/**
	 * Checks whether the current attribute is a primary attribute
	 *
	 * @returns {boolean}
	 */
	isPrimary(){
		return this.in(Profession.PRIMARY_ATTRIBUTE)
	}

	/**
	 * Clamps the given value to the internal max value for the current attribute
	 *
	 * @param {number|int|null} $level
	 * @returns {number|int}
	 */
	clamp($level = null){
		return Math.max(0, Math.min(($level ?? this.#level), this.getMaxValue()));
	}

	/**
	 * Returns the progression function for the given title rank or attribute
	 *
	 * @returns {function(number|int, number|int, number|int): number|int}
	 */
	getProgressionFunction(){
		switch(this.getMaxValue()){
			// lightbringer
			case  8: return this.progression8;
			// sunspear and eotn titles
			case 10: return this.progression10;
			// luxon/kurzick
			case 12: return this.progression12;
			// regular skill progression
			default: return this.progression15;
		}
	}

	/**
	 * @param {number|int} $level
	 * @param {number|int} $val0
	 * @param {number|int} $val15
	 * @returns {number|int}
	 */
	progression8($level, $val0, $val15){
		return Math.round(Math.min(($level * 4), 15) * (($val15 - $val0) / 15) + $val0);
	}

	/**
	 * @param {number|int} $level
	 * @param {number|int} $val0
	 * @param {number|int} $val15
	 * @returns {number|int}
	 */
	progression10($level, $val0, $val15){
		return Math.round(Math.min(($level * 3), 15) * (($val15 - $val0) / 15) + $val0);
	}

	/**
	 * @param {number|int} $level
	 * @param {number|int} $val0
	 * @param {number|int} $val15
	 * @returns {number|int}
	 */
	progression12($level, $val0, $val15){
		return Math.round(Math.min(Math.floor($level * 2.5), 15) * (($val15 - $val0) / 15) + $val0);
	}

	/**
	 * @param {number|int} $level
	 * @param {number|int} $val0
	 * @param {number|int} $val15
	 * @returns {number|int}
	 */
	progression15($level, $val0, $val15){
		return Math.round($level * (($val15 - $val0) / 15) + $val0);
	}

	/**
	 * Calculates the value for the given val0-val15 progression for the given attribute and level
	 *
	 * @param  {number|int|string} $val0
	 * @param  {number|int|string} $val15
	 * @param  {number|int|null} $level
	 * @returns {number|int}
	 */
	getProgressionValue($val0, $val15, $level = null){

		if($level !== null){
			$level = this.clamp($level);
		}

		let $fn = this.getProgressionFunction();

		// values might come in as strings from preg_match()
		return $fn(($level ?? this.getLevel()), PHPJS.intval($val0), PHPJS.intval($val15));

	}

	/**
	 * Creates a progression table for the values 0 to attribute-max of the given val0 and val15
	 *
	 * @param {number|int} $val0
	 * @param {number|int} $val15
	 * @param {number|int|null} $max
	 * @returns {number[]|int[]}
	 */
	getProgressionTable($val0, $val15, $max = null){
		let $maxValue = this.getMaxValue();
		// the internal maximum attribute level for player characters is 20-21, monsters are capped at 30
		// fast cast levels > 33 result in negative activation & recharge for mesmer - THE CHRONOMANCER IS REAL
		$max = Math.min(($max ?? $maxValue), 30);

		// we'll clamp the PvE attributes to their respectime max title ranks
		if(this.id > 100){
			$max = $maxValue;
		}

		let $fn = this.getProgressionFunction();

		return [...Array($max + 1).keys()].map(i => $fn(i, $val0, $val15));
	}

	/**
	 * @param {Lang|string|null} $lang
	 * @param {boolean} $includeLevel
	 * @returns {HTMLElement|string}
	 */
	toHTML($lang = null, $includeLevel = false){
		$lang        = this._getLang($lang);
		let pri      = this.isPrimary() ? 'true' : 'false';
		let cssClass = [this.constructor.CSS_CLASS, this.getProfession().getName(Lang.EN).toLowerCase()];
		let level    = '';

		if(this.isPrimary()){
			cssClass.push('primary');
		}

		// return an HTML snippet when DOM is not available
		if(typeof document === 'undefined'){

			if($includeLevel){
				level = `<span class="level">${this.getLevel()}</span>`;
			}

			return `<span class="${cssClass.join(' ')}" data-id="${this.id}" data-lang="${$lang.id}"` +
			       ` data-level="${this.getLevel()}" data-max="${this.getMaxValue()}" data-primary="${pri}"` +
			       ` data-profession="${this.getProfessionID()}">${level}${this.getName($lang)}</span>`;
		}

		let el = document.createElement('span');
		el.className = cssClass.join(' ');

		el.dataset.id         = String(this.id);
		el.dataset.lang       = $lang.id;
		el.dataset.level      = String(this.getLevel());
		el.dataset.max        = String(this.getMaxValue());
		el.dataset.primary    = pri;
		el.dataset.profession = String(this.getProfessionID());

		if($includeLevel){
			let v = document.createElement('span');
			v.className = 'level';
			v.innerText = this.getLevel();

			el.appendChild(v);
		}

		el.append(this.getName($lang));

		return el;
	}

}
