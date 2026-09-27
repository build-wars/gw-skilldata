<?php
/**
 * Class EffectCondition
 *
 * @created      23.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use Buildwars\GWSkillData\Common\Lang as L;
use function dechex;
use function sprintf;

final class EffectCondition extends DataObjectAbstract{

	public const int CHANCE           = 0x9001;
	public const int ATTRIBUTE_REQ    = 0x9002;
	public const int ATTRIBUTE_REQ9   = 0x9003;
	public const int ATTRIBUTE_REQ13  = 0x9004;
	public const int VS_DAMAGE_TYPE   = 0x9005;
	public const int VS_ELEMENTAL     = 0x9006;
	public const int VS_PHYSICAL      = 0x9007;
	public const int HEALTH_GT        = 0x9008;
	public const int HEALTH_LT        = 0x9009;
	public const int WHILE_ENCHANTED  = 0x900A;
	public const int WHILE_IN_STANCE  = 0x900B;
	public const int WHILE_ATTACKING  = 0x900C;
	public const int WHILE_CASTING    = 0x900D;
	public const int WHILE_ACTIVATING = 0x900E;
	public const int WHILE_HEXED      = 0x900F;
	public const int WHILE_RECHARGING = 0x9010;
	// insignia
	public const int COM_BLESSED      = 0x9101;
	public const int COM_HERALDS      = 0x9102;
	public const int R_SCOUTS         = 0x9103;
	public const int R_BEASTMASTERS   = 0x9104;
	public const int M_DISCIPLES      = 0x9105; // technically the same as "while enchanted", isn't it??
	public const int N_MINION_MASTERS = 0x9106;
	public const int N_BLIGHTERS      = 0x9107;
	public const int ME_ARTIFICERS    = 0x9108;
	public const int RT_SHAMANS       = 0x9109;
	public const int RT_GHOSTFORGE    = 0x910A;
	public const int P_CENTURIONS     = 0x910B;
	public const int D_WINDWALKER     = 0x910C;
	public const int D_FORSAKEN       = 0x910D;
	// inscription
	public const int INSC_TMI         = 0x9201;
	// armor piece dependent effect
	public const int ARMOR_CHEST      = 0x9301;
	public const int ARMOR_LEGS       = 0x9302;
	public const int ARMOR_OTHER      = 0x9303;
	// non-effects
	public const int STACKING         = 0x9401; // not really an effect, but we'll add these anyway
	public const int NONSTACKING      = 0x9402;
	// unsupported
	public const int ATTR_RANK_LT     = 0x9901;

	public const array NAME = [
		self::CHANCE           => [
			L::DE => 'Zufall: %s%%',
			L::EN => 'Chance: %s%%',
			L::ES => 'Probabilidad: %s%%',
			L::FR => 'Chance : %s%%',
			L::IT => 'Probabilità: %s%%',
			L::XX => 'Chunce-a: %s%%',
		],
		self::ATTRIBUTE_REQ    => [
			L::DE => 'Erfordert %1$s %2$s',
			L::EN => 'Requires %1$s %2$s',
			L::ES => '%2$s necesitar %1$s',
			L::FR => 'Requiert %2$s %1$s',
			L::IT => '%2$s richiedere %1$s',
			L::XX => 'Reqooures %1$s %2$s',
		],
		self::ATTRIBUTE_REQ9   => [
			L::DE => 'Erfordert 9 %s',
			L::EN => 'Requires 9 %s',
			L::ES => '%s necesitar 9',
			L::FR => 'Requiert %s 9',
			L::IT => '%s richiedere 9',
			L::XX => 'Reqooures 9 %s',
		],
		self::ATTRIBUTE_REQ13  => [
			L::DE => 'Erfordert 13 %s',
			L::EN => 'Requires 13 %s',
			L::ES => '%s necesitar 13',
			L::FR => 'Requiert %s 13',
			L::IT => '%s richiedere 13',
			L::XX => 'Reqooures 13 %s',
		],
		self::VS_DAMAGE_TYPE   => [
			L::DE => 'gg. %s',
			L::EN => 'vs. %s',
			L::ES => 'contra %s',
			L::FR => 'contre %s',
			L::IT => 'contro %s',
			L::XX => 'fs. %s',
		],
		self::VS_ELEMENTAL     => [
			L::DE => 'gg. Elementarschaden',
			L::EN => 'vs. elemental damage',
			L::ES => 'contra daño de elementos',
			L::FR => 'contre les dégâts élémentaires',
			L::IT => 'contro danno elementale',
			L::XX => 'fs. ilementael daemaege-a',
		],
		self::VS_PHYSICAL      => [
			L::DE => 'gg. körperlichen Schaden',
			L::EN => 'vs. physical damage',
			L::ES => 'contra daño físico',
			L::FR => 'contre les dégâts physiques',
			L::IT => 'contro danno fisico',
			L::XX => 'fs. physeecael daemaege-a',
		],
		self::HEALTH_GT        => [
			L::DE => 'während Lebenspunkte mehr als %s%% beträgt',
			L::EN => 'while Health is above %s%%',
			L::ES => 'si la Salud es superior a %s%%',
			L::FR => 'quand la Santé est au-dessus de %s%%',
			L::IT => 'mentre la Salute è superiore al %s%%',
			L::XX => 'vheele-a Heaelt is aebufe-a %s%%',
		],
		self::HEALTH_LT        => [
			L::DE => 'während Lebenspunkte weniger als %s%% beträgt',
			L::EN => 'while Health is below %s%%',
			L::ES => 'si la Salud es inferior a %s%%',
			L::FR => 'quand la Santé est en-dessous de %s%%',
			L::IT => 'mentre la Salute è inferiore al %s%%',
			L::XX => 'vheele-a Heaelt is beloo %s%%',
		],
		self::WHILE_ENCHANTED  => [
			L::DE => 'bei Verzauberung',
			L::EN => 'while Enchanted',
			L::ES => 'mientras estás encantado',
			L::FR => 'sous les effets d\'un enchantement',
			L::IT => 'mentre sei sotto incantesimo',
			L::XX => 'vheele-a Inchunted',
		],
		self::WHILE_IN_STANCE  => [
			L::DE => 'während eine Haltung eingenommen wird',
			L::EN => 'while in a Stance',
			L::ES => 'mientras se mantiene una actitud',
			L::FR => 'avec une pose de combat',
			L::IT => 'mentre sei in posizione',
			L::XX => 'vheele-a in a Stunce-a',
		],
		self::WHILE_ATTACKING  => [
			L::DE => 'beim Angriff',
			L::EN => 'while attacking',
			L::ES => 'mientras se ataca',
			L::FR => 'en attaquant',
			L::IT => 'mentre atacchi',
			L::XX => 'vheele-a aettaeckeeng',
		],
		self::WHILE_CASTING    => [
			L::DE => 'beim Wirken',
			L::EN => 'while casting',
			L::ES => 'mientras se invoca',
			L::FR => 'en lançant un sort',
			L::IT => 'mentre lanci incantesimi',
			L::XX => 'vheele-a caesteeng',
		],
		self::WHILE_ACTIVATING => [
			L::DE => 'beim Aktivieren von Fertigkeiten',
			L::EN => 'while activating skills',
			L::ES => 'mientras actives habilidades',
			L::FR => 'pendant que vous activez des compétences',
			L::IT => 'mentre stai attivando delle abilità',
			L::XX => 'vheele-a aecteefaeteeng skeells',
		],
		self::WHILE_HEXED      => [
			L::DE => 'bei Verhexung',
			L::EN => 'while Hexed',
			L::ES => 'mientras te han echado un maleficio',
			L::FR => 'sous les effets d\'un maléfice',
			L::IT => 'mentre sei sotto fattura',
			L::XX => 'vheele-a Hexed',
		],
		self::WHILE_RECHARGING => [
			L::DE => 'beim Wiederaufladen von %s oder mehr Fertigkeiten',
			L::EN => 'while recharging %s or more skills',
			L::ES => 'mientras recargas %s habilidad[es] o más',
			L::FR => 'lorsque vous rechargez %s compétence[s] ou plus',
			L::IT => 'mentre %s o più abilità si stanno ricaricando',
			L::XX => 'vheele-a rechaergeeng %s oor mure-a skeells',
		],
		self::COM_BLESSED      => [
			L::DE => 'unter dem Einfluss von Folgendem: Verzauberungen',
			L::EN => 'while affected by an Enchantment Spell',
			L::ES => 'al estar bajo los efectos de un Conjuro con encantamiento',
			L::FR => 'sous les effets de : Enchantements',
			L::IT => 'mentre sei sotto gli effetti di un Incantesimo',
			L::XX => 'vheele-a aeffffected by an Inchuntment Spell',
		],
		self::COM_HERALDS      => [
			L::DE => 'beim Halten eines Gegenstandes',
			L::EN => 'while holding an item',
			L::ES => 'mientras lleves un objeto',
			L::FR => 'pendant que vous tenez un objet',
			L::IT => 'mentre hai in mano un oggetto',
			L::XX => 'vheele-a huldeeng un item',
		],
		self::R_SCOUTS         => [
			L::DE => 'bei der Benutzung einer Vorbereitung',
			L::EN => 'while using a Preparation',
			L::ES => 'mientras uses una preparación',
			L::FR => 'pendant que vous utilisez une préparation',
			L::IT => 'mentre usi una preparazione',
			L::XX => 'vheele-a useeng a Prepaeraeshun',
		],
		self::R_BEASTMASTERS   => [
			L::DE => 'während Euer Tiergefährte am Leben ist',
			L::EN => 'while your pet is alive',
			L::ES => 'mientras tu mascota está viva',
			L::FR => 'lorsque votre familier est vivant',
			L::IT => 'mentre la tua mascotte è in vita',
			L::XX => 'vheele-a yuoor pet is aeleefe-a',
		],
		self::M_DISCIPLES      => [
			L::DE => 'unter dem Einfluss von Folgendem: Zustände',
			L::EN => 'while affected by a Condition',
			L::ES => 'al estar bajo los efectos de una Condición',
			L::FR => 'sous les effets de : Conditions',
			L::IT => 'mentre sei sotto gli effetti di una Condizione',
			L::XX => 'vheele-a aeffffected by a Cundeeshun',
		],
		self::N_MINION_MASTERS => [
			L::DE => 'bein Kontrollieren von %s oder mehr Dienern',
			L::EN => 'while you control %s or more minions',
			L::ES => 'mientras controles %s siervo[s] o más',
			L::FR => 'lorsque vous contrôlez %s serviteur[s] ou plus',
			L::IT => 'mentre controlli %s o più sgherri',
			L::XX => 'vheele-a yuoo cuntrul %s oor mure-a meeneeuns',
		],
		self::N_BLIGHTERS      => [
			L::DE => 'unter dem Einfluss von Folgendem: Verhexungen',
			L::EN => 'while affected by a Hex Spell',
			L::ES => 'al estar bajo los efectos de un Conjuro con maleficio',
			L::FR => 'sous les effets de : Maléfices',
			L::IT => 'mentre sei sotto gli effetti di una Fattura',
			L::XX => 'vheele-a aeffffected by a Hex Spell',
		],
		self::ME_ARTIFICERS    => [
			L::DE => 'für jedes ausgerüstete Siegel',
			L::EN => 'for each equipped Signet',
			L::ES => 'por cada sello equipado',
			L::FR => 'pour chaque sceau équipé',
			L::IT => 'per ogni Sigillo equipaggiato',
			L::XX => 'fur iaech iqooeepped Seegnet',
		],
		self::RT_SHAMANS       => [
			L::DE => 'bein Kontrollieren von %s oder mehr Geistern',
			L::EN => 'while you control %s or more Spirits',
			L::ES => 'mientras controles %s o más espíritus',
			L::FR => 'lorsque vous contrôlez %s espirit[s] ou plus',
			L::IT => 'mentre controlli %s o più Spiriti',
			L::XX => 'vheele-a yuoo cuntrul %s oor mure-a Spureets',
		],
		self::RT_GHOSTFORGE    => [
			L::DE => 'unter dem Einfluss von Folgendem: Waffenzauber',
			L::EN => 'while affected by a Weapon Spell',
			L::ES => 'al estar bajo los efectos de un Conjuro con arma',
			L::FR => 'sous les effets de : Sorts d\'altération d\'arme',
			L::IT => 'mentre sei sotto gli effetti di una Magia Altera-arma',
			L::XX => 'vheele-a aeffffected by a Veaepun Spell',
		],
		self::P_CENTURIONS     => [
			L::DE => 'unter dem Einfluss enes Schreis, Echos oder Anfeuerungsrufes',
			L::EN => 'while affected by a Shout, Echo, or Chant',
			L::ES => 'al estar bajo los efectos de un grito, eco o cántico',
			L::FR => 'sous les effets d\'un cri, d\'un écho ou d\'un chant',
			L::IT => 'mentre sei sotto gli effetti di un urlo, un\'ecco o un canto',
			L::XX => 'vheele-a aeffffected by a Shuoot, Ichu, oor Chunt',
		],
		self::D_WINDWALKER     => [
			L::DE => 'unter dem Einfluss von %s oder mehr der Folgenden: Verzauberung[en]',
			L::EN => 'while affected by %s or more Enchantment Spell[s]',
			L::ES => 'al estar bajo los efectos de %s o más Conjuro[s] con encantamiento',
			L::FR => 'sous les effets de %s Enchantement[s] ou plus',
			L::IT => 'mentre sei sotto gli effetti di %s o più Incantesimi',
			L::XX => 'vheele-a aeffffected by %s oor mure-a Inchuntment Spell[s]',
		],
		self::D_FORSAKEN       => [
			L::DE => 'wenn nicht unter dem Einfluss von Folgendem: Verzauberungen',
			L::EN => 'while not affected by an Enchantment Spell',
			L::ES => 'al no estar bajo los efectos de un Conjuro con encantamiento',
			L::FR => 'lorsque vous n\'êtes pas sous les effets de : Enchantements',
			L::IT => 'mentre non sei sotto gli effetti di un Incantesimo',
			L::XX => 'vheele-a nut aeffffected by an Inchuntment Spell',
		],
		self::INSC_TMI         => [
			L::DE => 'gg. verhexte Feinde',
			L::EN => 'vs. Hexed foes',
			L::ES => 'contra enemigos bajo maleficios',
			L::FR => 'contre les ennemis victimes d\'und maléfice',
			L::IT => 'contro nemici sotto fattura',
			L::XX => 'fs. Hexed fues',
		],
		self::ARMOR_CHEST      => [
			L::DE => 'bei Brustrüstung',
			L::EN => 'on chest armor',
			L::ES => 'en armadura del pecho',
			L::FR => 'sur l\'armure de la poitrine',
			L::IT => 'su armatura per il petto',
			L::XX => 'oon chest aermur',
		],
		self::ARMOR_LEGS       => [
			L::DE => 'bei Beinrüstung',
			L::EN => 'on leg armor',
			L::ES => 'en armadura de las piernas',
			L::FR => 'sur l\'armure des jambes',
			L::IT => 'su armatura per le gambe',
			L::XX => 'oon leg aermur',
		],
		self::ARMOR_OTHER      => [
			L::DE => 'bei anderer Rüstung',
			L::EN => 'on other armor',
			L::ES => 'en otras armaduras',
			L::FR => 'sur une autre armure',
			L::IT => 'su altra parte di armatura',
			L::XX => 'oon oozeer aermur',
		],
		self::STACKING         => [
			L::DE => 'Stapelbar',
			L::EN => 'Stacking',
			L::ES => 'Acumulable',
			L::FR => 'Cumulable',
			L::IT => 'Cumulabile',
			L::XX => 'Staeckeeng',
		],
		self::NONSTACKING      => [
			L::DE => 'Nicht stapelbar',
			L::EN => 'Non-stacking',
			L::ES => 'No acumulable',
			L::FR => 'Non cumulable',
			L::IT => 'Non cumulabile',
			L::XX => 'Nun-staeckeeng',
		],
		self::ATTR_RANK_LT     => [
			L::DE => 'wenn Euer Rang niedriger ist',
			L::EN => 'if your rank is lower',
			L::ES => 'si tu rango es inferior',
			L::FR => 'si votre rang est inférieur',
			L::IT => 'se il tuo grado è inferiore',
			L::XX => 'iff yuoor runk is looer',
		],
	];

	public function getAffix(int|string ...$values):string{
		// @todo: language fix
		if(!isset(self::NAME[$this->id][$this->lang->id])){
			return sprintf('[EFFECT_CONDITION_%s_%s]', dechex($this->id), $this->lang->id);
		}

		$condition = sprintf(self::NAME[$this->id][$this->lang->id], ...$values); // phpcs:ignore

		return sprintf('<gray>(%s)</gray>', $condition);
	}

	/**
	 * Returns a "stacking" or "non-stacking" suffix
	 */
	public function stackable(bool $stackable):string{
		$condition = ($stackable === false ? self::NONSTACKING : self::STACKING);

		return sprintf('<gray>(%s)</gray>', self::NAME[$condition][$this->lang->id]);
	}

}
