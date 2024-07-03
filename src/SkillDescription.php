<?php
/**
 * Class SkillDescription
 *
 * @created      01.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 *
 * @noinspection PhpUnused
 */
declare(strict_types = 1);

namespace Buildwars\GWSkillData;

use Closure;
use InvalidArgumentException;
use function array_filter;
use function array_slice;
use function floor;
use function in_array;
use function intval;
use function is_int;
use function max;
use function min;
use function pow;
use function preg_replace_callback;
use function round;
use function sprintf;
use function strtolower;
use function trim;
use const PHP_ROUND_HALF_EVEN;

/**
 *
 */
class SkillDescription{
	use SkillDataAwareTrait;

	public const LANGUAGES = ['de', 'en'];

	/*
	 * Regex patterns for the several progression types and languages
	 *
	 * The named capture groups are:
	 *
	 *   - val0, val15: for skill progressions (attribute level 0 and 15)
	 *   - val: for fixed values, e.g. weapon spells and stances
	 *   - str1, str2: the leftover strings from the capture before (1) and after (2) the value
	 *
	 * A callback for `preg_replace_callback()`should return:
	 *
	 *  (str1 ?? '') . calculated_val . (str2 ?? '')
	 */

	protected const VAL015 = '((?<val0>\d+)[.]+(?<val15>\d+))';
	protected const VAL    = '(?<val>\d+)';

	// Regex for default progression replacement (0...15)
	protected const REGEX_DEFAULT = '/'.self::VAL015.'/i';

	// creature level: "level 0...15"
	protected const REGEX_CREATURE = [
		'de' => '/(?<str1>Stufe\s+)'.self::VAL015.'/i',
		'en' => '/(?<str1>level\s+)'.self::VAL015.'/i',
	];

	// time progression: "0...15 seconds"
	protected const REGEX_TIME_PROGRESSION = [
		'de' => '/'.self::VAL015.'(?<str2>\s+Sekund)/i',
		'en' => '/'.self::VAL015.'(?<str2>\s+second)/i',
	];

	// fixed time: "15 seconds"
	protected const REGEX_TIME_FIXED = [
		'de' => '/'.self::VAL.'(?<str2>\s+Sekund)/i',
		'en' => '/'.self::VAL.'(?<str2>\s+second)/i',
	];

	/**
	 * The calculated effects result array
	 */
	protected const EFFECTS = [
		'energy'       => 0,
		'activation'   => 0,
		'recharge'     => 0,
		'description'  => '',
		'concise'      => '',
		'pri_effect'   => '',
		'other_effect' => [],
	];

	/** the current language */
	protected string $lang;

	/** the primary profession */
	protected int $pri = 0;
	/** the secondary profession */
	protected int $sec = 0;
	/** the given attribute level for the skill */
	protected int $attributeLevel = 0;
	/** the given primary attribute level */
	protected int $priAttributeLevel = 0;
	/** the given attributes */
	protected array $attributes = [];
	/** the context skill bar */
	protected array $contextSkillbar = [];
	/** pvp redirect */
	protected bool $pvp = false;
	/** overall attribute bonus */
	protected int $attributeBonus = 0;

	/** the data array for the current skill */
	protected array $data;
	/** additional (primary) attribute effect info/adjusted values */
	protected array $effects = self::EFFECTS;
	/** progression levels for the current skill */
	protected array $progressions = [];

	/**
	 * SkillDescription constructor
	 */
	public function __construct(string $lang = 'de'){
		$lang = strtolower(trim($lang));

		if(!in_array($lang, $this::LANGUAGES, true)){
			throw new InvalidArgumentException('invalid language');
		}

		$this->lang = $lang;

		$this->setSkillDataLanguage($this->lang);
	}

	/**
	 * Sets the primary and secondary profession
	 */
	public function setProfessions(int $pri, int $sec = 0):static{

		// invalid primary profession
		if(!isset(SkillDataInterface::PROFESSIONS[$pri])){
			$pri = 0;
		}

		// invalid secondary profession or secondary profession is same as primary
		if(!isset(SkillDataInterface::PROFESSIONS[$sec]) || $sec === $pri){
			$sec = 0;
		}

		$this->pri = $pri;
		$this->sec = $sec;

		return $this;
	}

	/**
	 * Sets an optional skill bar to help determine skills that influence the stats
	 */
	public function setContextSkillbar(array $skillbar):static{
		$this->contextSkillbar = array_slice(array_filter($skillbar, 'is_int'), 0, 8);

		return $this;
	}

	/**
	 * Sets the attributes for the given player/skillbar
	 *
	 * (may include PvE attributes, levels are clamped to 0-30, PvE attributes are clamped to their maximum title ranks)
	 *
	 * @see \Buildwars\GWSkillData\SkillDataInterface::ATTRIBUTES
	 */
	public function setAttributes(array $attributes):static{
		$this->attributes = [];

		foreach($attributes as $attribute => $value){

			// invalid attribute
			if(!isset(SkillDataInterface::ATTRIBUTES[$attribute])){
				continue;
			}

			// invalid value
			if(!is_int($value)){
				continue;
			}

			$this->attributes[$attribute] = $this->clampAttributeLevel($value, $attribute);
		}

		return $this;
	}

	/**
	 * Sets an additional bonus to all attributes, e.g. from Grail of Might, Gold Eggs, Candy Corn, etc...
	 *
	 * value clamped to 0-10
	 */
	public function setAttributeBonus(int $attributeBonus):static{
		$this->attributeBonus = max(0, min($attributeBonus, 10));

		return $this;
	}

	/**
	 * Clamps the attribute level
	 *
	 * fun fact: fast cast levels > 33 result in negative activation & recharge for mesmer
	 */
	protected function clampAttributeLevel(int $attributeLevel, int $attributeID):int{
		$max = 30;

		// we'll clamp the PvE attributes to their respectime max title ranks
		if($attributeID > 100){
			$max = SkillDataInterface::ATTRIBUTES[$attributeID]['max'];
		}

		return max(0, min(($attributeLevel), $max));
	}

	/**
	 * Returns the level for the given attribute, including bonuses
	 */
	protected function getAttributeLevel(int $attributeID, int|null $overrideLevel = null):int{
		$level = ($overrideLevel ?? $this->attributes[$attributeID] ?? 0);

		// add the attribute bonus here, excluding PvE attributes
		if($attributeID < 100){
			$level += $this->attributeBonus;
		}

		return $this->clampAttributeLevel($level, $attributeID);
	}

	/**
	 * Whether to use PvP skill redirect
	 */
	public function setPvP(bool $pvp):static{
		$this->pvp = $pvp;

		return $this;
	}

	/**
	 * Initializes the attribute effects array
	 */
	protected function initEffects(array $skillData):array{
		$effects = [];

		foreach($this::EFFECTS as $k => $v){
			$effects[$k] = ($skillData[$k] ?? $v);
		}

		return $effects;
	}

	/**
	 * Adds a skill description that has all variable values calculated wit the given attribute levels, and for the given primary class.
	 *
	 * The returned array is similar to that from `SkillDataInterface::get()` with 2 additional keys: `effects` and `progressions`.
	 */
	public function getDescription(int $id, int|null $attributeLevel = null, int|null $priAttributeLevel = null):array{
		// fetch the skill data
		$this->data              = $this->skillData->get($id, $this->pvp);
		// initialize output
		$this->progressions      = [];
		$this->effects           = $this->initEffects($this->data);
		// get necessary attribute levels (skill attribute and primary attribute of the build)
		$this->attributeLevel    = $this->getAttributeLevel($this->data['attribute'], $attributeLevel);
		$this->priAttributeLevel = $this->getAttributeLevel(SkillDataInterface::PROFESSIONS[$this->pri]['pri'], $priAttributeLevel);

		// determine primary attribute effects
		$this->{SkillDataInterface::PROFESSIONS[$this->pri]['name']['en']}();

		// summoned creature health & armor
		$this->progressionReplace($this::REGEX_CREATURE[$this->lang], $this->creature(...));

		// the skill is a stance and we have dwarven stability in the bar
		if($this->data['type'] === 29 && in_array(2423, $this->contextSkillbar, true)){
			$this->progressionReplace($this::REGEX_TIME_PROGRESSION[$this->lang], $this->dwarvenStability(...));
			$this->progressionReplace($this::REGEX_TIME_FIXED[$this->lang], $this->dwarvenStability(...));

			$this->addEffectText(sprintf(SkillDataMisc::DESC_OTHER_EFFECT['dwarven_stability'][$this->lang], $this->getProgression(55, 100, $this->getAttributeLevel(107), 107, false)));
		}

		// the skill is a signet and we have mantra of inscriptions in the skillbar
		if($this->data['type'] === 21 && in_array(15, $this->contextSkillbar, true)){
			$reduction = $this->getProgression(10, 40, $this->getAttributeLevel(3), 3, false);

			$this->effects['recharge'] = round(($this->effects['recharge'] - ($reduction / 100 * $this->effects['recharge'])), 0, PHP_ROUND_HALF_EVEN);

			$this->addEffectText(sprintf(SkillDataMisc::DESC_OTHER_EFFECT['mantra_of_inscriptions'][$this->lang], $reduction));
		}

		// death magic: the skill is a minion skill
		if($this->isAffectedSkill('minion')){
			// maximum minions
			$minions = (floor($this->getAttributeLevel(5) / 2) + 2);

			$this->addEffectText(sprintf(SkillDataMisc::DESC_OTHER_EFFECT['max_minions'][$this->lang], $minions));
		}

		// render any remaining progression values
		$this->progressionReplace($this::REGEX_DEFAULT, $this->defaultProgression(...));

		$this->data['effects']      = $this->effects;
		$this->data['progressions'] = $this->progressions;

		return $this->data;
	}

	/**
	 * Checks whether the current skill type is affected by the current primary attribute
	 */
	protected function isAffectedSkillType(string $effectType):bool{
		return in_array($this->data['type'], (SkillDataMisc::SKILLYTPES_PRI_EFFECTS[$this->pri][$effectType] ?? []), true);
	}

	/**
	 * Checks whether the current skill is affected by an attribute
	 */
	protected function isAffectedSkill(string $effectType):bool{
		return in_array($this->data['id'], (SkillDataMisc::SKILL_EFFECTS[$effectType] ?? []), true);
	}

	/**
	 * Sets the primary attribute effect description with the given values
	 */
	protected function setPriEffectText(string $type, string|int|float ...$values):void{
		$this->effects['pri_effect'] = sprintf(SkillDataMisc::DESC_PRI[$this->pri][$type][$this->lang], ...$values);
	}

	/**
	 * Adds a line to the additional effects array
	 */
	protected function addEffectText(string $text):void{
		// check if the same text hasn't been added yet (may happen in progressionReplace)
		if(!in_array($text, $this->effects['other_effect'], true)){
			$this->effects['other_effect'][] = $text;
		}
	}

	/**
	 * Caclulates the value for the given val0-val15 progression for the given attribute and level.
	 *
	 * Creates an optional table in the `progressions` array.
	 */
	protected function getProgression(int $val0, int $val15, int $level, int $attribute, bool $table = true):int{
		$key = "$val0 - $val15";

		// shortcut
		if($table === true && isset($this->progressions[$key])){
			return $this->progressions[$key][$level];
		}

		$prog = (float)(($val15 - $val0) / 15);

		// determine the progression function with respect to PvE atrributes
		$value = match($attribute){
			// lightbringer
			103                     => fn(int $l):float => min(($l * 4), 15) * $prog + $val0,
			// Luxon/Kurzick
			104, 105                => fn(int $l):float => min(floor($l * 2.5), 15) * $prog + $val0,
			// Sunspear and EotN titles
			102, 106, 107, 108, 109 => fn(int $l):float => min(($l * 3), 15) * $prog + $val0,
			default                 => fn(int $l):float => $l * $prog + $val0,
		};

		// skip progression table creation
		if($table === false){
			return (int)round($value($level));
		}

		// collect the progression levels 0-30 for the table
		for($i = 0; $i <= 30; $i++){
			$this->progressions[$key][$i] = (int)round($value($i));
		}

		return $this->progressions[$key][$level];
	}

	/*
	 * Progression replacement and callbacks
	 */

	protected function progressionReplace(string $regex, Closure $callback):void{
		foreach(['description', 'concise'] as $type){
			$this->effects[$type] = preg_replace_callback($regex, $callback, $this->effects[$type]);
		}
	}

	protected function excludeMatch(array $match, array $exclude):bool{

		// current ID not in set
		if(!isset($exclude[$this->data['id']])){
			return false;
		}

		foreach($exclude as $id => $v){

			if($this->data['id'] !== $id){
				continue;
			}

			// single value
			if(isset($match['val']) && in_array($match['val'], $v, true)){
				return true;
			}
			// progression value
			if(isset($match['val15']) && in_array($match['val15'], $v, true)){
				return true;
			}
		}

		return false;
	}

	protected function defaultProgression(array $match):string{
		$val1 = $this->getProgression((int)$match['val0'], (int)$match['val15'], $this->attributeLevel, $this->data['attribute']);

		return sprintf('<green>%s</green>', $val1);
	}

	protected function weaponSpell(array $match):string{

		if($this->excludeMatch($match, SkillDataMisc::SKILL_FIXTURES['weapon_spell'])){
			return $match[0];
		}

		$str1 = ($match['str1'] ?? '');
		$str2 = ($match['str2'] ?? '');

		if(isset($match['val'])){
			$duration = round(intval($match['val']) * (1 + $this->priAttributeLevel * 0.04));

			return sprintf('%s<ritualist>%s</ritualist> (%s)%s', $str1, $duration, $match['val'], $str2);
		}

		$val      = $this->getProgression((int)$match['val0'], (int)$match['val15'], $this->priAttributeLevel, 36);
		$duration = round($val * (1 + $this->priAttributeLevel * 0.04));

		return sprintf('%s<ritualist>%s</ritualist> (<green>%s</green>)%s', $str1, $duration, $val, $str2);
	}

	protected function symbolicCelerity(array $match):string{
		$val1 = $this->getProgression((int)$match['val0'], (int)$match['val15'], $this->priAttributeLevel, 0);
		$val2 = $this->getProgression((int)$match['val0'], (int)$match['val15'], $this->attributeLevel, $this->data['attribute']);

		return sprintf('<mesmer>%s</mesmer> (<green>%s</green>)', $val1, $val2);
	}

	protected function creature(array $match):string{
		$creatureLevel = $this->getProgression((int)$match['val0'], (int)$match['val15'], $this->attributeLevel, $this->data['attribute']);

		// general creature stats
		$health = ($creatureLevel * 20);
		$armor  = (6 * $creatureLevel + 3); // gww says +2 which one is it???

		// creature is a minion
		if($this->isAffectedSkill('minion')){
			$health += 80;

			$armor = match($this->data['id']){
				84      => (2.84 * $creatureLevel + 3.1), // Bone Fiend
				85      => (2.9 * $creatureLevel + 1.25), // Bone Minions
				default => (3.75 * $creatureLevel + 5),
			};
		}

		$armor = round($armor, 0, PHP_ROUND_HALF_EVEN);

		// primary ritualist, spawning power effect
		// a minion from Malign Intervention (122) is not affected by spawning power
		($this->pri === 8 && $this->data['id'] !== 122)
			? $this->setPriEffectText('creature', $health, $armor, round($health * (1 + $this->priAttributeLevel * 0.04)))
			: $this->addEffectText(sprintf(SkillDataMisc::DESC_OTHER_EFFECT['creature'][$this->lang], $health, $armor));

		return sprintf('%s<greeen>%s</green>%s', ($match['str1'] ?? ''), $creatureLevel, ($match['str2'] ?? ''));
	}

	protected function dwarvenStability(array $match):string{

		if($this->excludeMatch($match, SkillDataMisc::SKILL_FIXTURES['dwarven_stability'])){
			return $match[0];
		}

		$str1 = ($match['str1'] ?? '');
		$str2 = ($match['str2'] ?? '');

		$dwarvenStability = (1 + $this->getProgression(55, 100, $this->getAttributeLevel(107), 107, false) / 100);

		// fixed time
		if(isset($match['val'])){
			return sprintf('%s<green>%s</green> (%s)%s', $str1, round(intval($match['val']) * $dwarvenStability), $match['val'], $str2);
		}

		$val = $this->getProgression((int)$match['val0'], (int)$match['val15'], $this->attributeLevel, $this->data['attribute']);

		return sprintf('%s<green>%s</green> (<green>%s</green>)%s', $str1, round($val * $dwarvenStability), $val, $str2);
	}


	/*
	 * Profession related transformations
	 */

	protected function none():void{
		$this->setPriEffectText('default');
	}

	protected function Warrior():void{
		$this->setPriEffectText('default', $this->priAttributeLevel);

		if($this->isAffectedSkillType('strength')){
			$this->setPriEffectText('self', $this->priAttributeLevel);
		}
	}

	protected function Ranger():void{
		$calc = ($this->priAttributeLevel * 4);
		$this->setPriEffectText('default', $calc);

		if($this->data['profession'] === 2 || $this->isAffectedSkillType('expertise') || $this->isAffectedSkill('touch')){
			$this->effects['energy'] = round($this->effects['energy'] * (1 - 0.04 * $this->priAttributeLevel));
			$this->setPriEffectText('self', $calc);
		}
	}

	protected function Monk():void{

		$type = match(true){
			$this->isAffectedSkill('df_target') => 'target',
			$this->isAffectedSkill('df_add')    => 'add',
			$this->isAffectedSkill('df_self')   => 'self',
			default                             => 'default',
		};

		$this->setPriEffectText($type, round($this->priAttributeLevel * 3.2));
	}

	protected function Necromancer():void{
		$this->setPriEffectText('default', $this->priAttributeLevel);
	}

	protected function Mesmer():void{
		// signet activation and spell recharge
		$calc1 = (100 * (1 - ($this->priAttributeLevel * 0.03)));
		// spell activation
		$calc2 = (100 * pow(2, ($this->priAttributeLevel * -1) / 15));

		$calc1r = round($calc1);
		$calc2r = round($calc2);

		// just start with the generic primary attribute info
		$this->setPriEffectText('default', $calc1r, $calc2r);

		// skill type = signet
		if($this->data['type'] === 21){

			// reduced activation time info
			if($this->data['profession'] === 5 || $this->data['activation'] >= 2){
				$this->effects['activation'] = round(($this->effects['activation'] / 100 * $calc1), 3);
				$this->setPriEffectText('signet', $calc1r);
			}

			// symbolic celerity is in the skill bar and the skill is not keystone signet and attribute is not "no attribute"
			if(in_array(1340, $this->contextSkillbar, true) && $this->data['id'] !== 63 && $this->data['attribute'] !== 101){
				$this->progressionReplace($this::REGEX_DEFAULT, $this->symbolicCelerity(...));

				// symbolic celerity effect info
				$this->addEffectText(SkillDataMisc::DESC_OTHER_EFFECT['symbolic_celerity'][$this->lang]);
			}

		}
		// skill type = spell
		elseif($this->isAffectedSkillType('fastcast')){

			// reduced activation time info
			if($this->data['profession'] === 5 || $this->data['activation'] >= 2){
				$this->effects['activation'] = round(($this->effects['activation'] / 100 * $calc2), 3);
				$this->setPriEffectText('spell1', $calc2r);
			}

			// Mesmer spell recharge in PvE
			if(!$this->pvp && $this->data['profession'] === 5 && $this->isAffectedSkillType('fastcast_recharge')){
				$this->effects['recharge'] = round(($this->effects['recharge'] / 100 * $calc1), 3);
				$this->setPriEffectText('spell2', $calc2r, $calc1r);
			}

		}

	}

	protected function Elementalist():void{
		$this->setPriEffectText('default', $this->priAttributeLevel * 3);
	}

	protected function Assassin():void{
		$this->setPriEffectText('default', $this->priAttributeLevel, floor(($this->priAttributeLevel + 2) / 5));
	}

	protected function Ritualist():void{
		$this->setPriEffectText('default', $this->priAttributeLevel * 4);

		// weapon spell effects
		if($this->isAffectedSkillType('spawning_weaponspell')){
			$this->progressionReplace($this::REGEX_TIME_PROGRESSION[$this->lang], $this->weaponSpell(...));
			$this->progressionReplace($this::REGEX_TIME_FIXED[$this->lang], $this->weaponSpell(...));

			$this->setPriEffectText('weaponspell', ($this->priAttributeLevel * 4));
		}
	}

	protected function Paragon():void{
		$val = floor($this->priAttributeLevel / 2);

		$this->setPriEffectText('default', $val);

		if($this->isAffectedSkillType('leadership')){
			$this->setPriEffectText('self', $val);
		}
	}

	protected function Dervish():void{
		$val = floor($this->priAttributeLevel * 4);

		$this->setPriEffectText('default', $val, $this->priAttributeLevel);

		if($this->isAffectedSkillType('mysticism') && $this->data['profession'] === $this->pri){
			$this->setPriEffectText('self', $val, $this->priAttributeLevel);
		}
	}

}
