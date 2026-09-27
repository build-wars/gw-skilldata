<?php
/**
 * Class Effect
 *
 * @created      15.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use Buildwars\GWSkillData\Common\Lang as L;
use function dechex;
use function sprintf;

final class Effect extends DataObjectAbstract{

	public const int HEALTH            = 0xA001;
	public const int ARMOR             = 0xA002;
	public const int ENERGY            = 0xA003;
	public const int DAMAGE            = 0xA004;
	public const int ARMOR_PEN         = 0xA005;
	public const int HSR               = 0xA006;
	public const int HCT               = 0xA007;
	public const int HSR_ATTRIBUTE     = 0xA008;
	public const int HCT_ATTRIBUTE     = 0xA009;
	public const int LIFE_DRAIN        = 0xA00A;
	public const int HEALTH_REGEN      = 0xA00B;
	public const int ENERGY_ON_HIT     = 0xA00C;
	public const int ENERGY_REGEN      = 0xA00D;
	public const int ENCH_DURATION     = 0xA00E;
	public const int DOUBLE_ADRENALINE = 0xA00F;
	public const int DMG_REDUCTION     = 0xA010;
	public const int ATTRIBUTE_BONUS   = 0xA011;
	public const int ATTRIBUTE_ITEM    = 0xA012;
	public const int CON_DUR_INCREASE  = 0xA014;
	public const int CON_DUR_DECREASE  = 0xA015;
	// insignia effects
	public const int W_LIEUTENANTS     = 0xA101;
	public const int W_STONEFIST       = 0xA102;
	public const int W_ABSORPTION      = 0xA103;
	public const int N_BLOODSTAINED    = 0xA104;
	public const int N_TORMENTORS      = 0xA105;
	// armor bonus
	public const int ENERGY_RECOVERY   = 0xA201;
	// unsupported
	public const int OF_PROFESSION     = 0xA901;

	public const array NAME = [
		self::HEALTH            => [
			L::DE => 'Lebenspunkte %s',
			L::EN => 'Health %s',
			L::ES => 'Salud %s',
			L::FR => 'Santé %s',
			L::IT => 'Salute %s',
			L::XX => 'Heaelt %s',
		],
		self::ARMOR             => [
			L::DE => 'Rüstung: %s',
			L::EN => 'Armor: %s',
			L::ES => 'Armadura: %s',
			L::FR => 'Armure : %s',
			L::IT => 'Armatura: %s',
			L::XX => 'Aermur: %s',
		],
		self::ENERGY            => [
			L::DE => 'Energie %s',
			L::EN => 'Energy %s',
			L::ES => 'Energía %s',
			L::FR => 'Energie %s',
			L::IT => 'Energia %s',
			L::XX => 'Inergy %s',
		],
		self::DAMAGE            => [
			L::DE => 'Schaden +%s',
			L::EN => 'Damage +%s',
			L::ES => 'Daño +%s',
			L::FR => 'Dégâts +%s',
			L::IT => 'Danno +%s',
			L::XX => 'Damage +%s',
		],
		self::ARMOR_PEN         => [
			L::DE => 'Rüstungsdurchdringung +%s%%',
			L::EN => 'Armor penetration +%s%%',
			L::ES => 'Penetración de armadura +%s%%',
			L::FR => 'Pénétration d\'armure +%s%%',
			L::IT => 'Penetrazione armatura +%s%%',
			L::XX => 'Aermur penetraeshun +%s%%',
		],
		self::HSR               => [
			L::DE => 'Halbiert Fertigkeitswiederaufladung von Zaubern',
			L::EN => 'Halves skill recharge of spells',
			L::ES => 'Reduce a la mitad la recarga de los conjuros',
			L::FR => 'Réduit de moitié le Rechargement des sorts.',
			L::IT => 'Dimezza la Ricarica delle magie',
			L::XX => 'Haelfes skeell rechaerge-a ooff spells',
		],
		self::HCT               => [
			L::DE => 'Halbiert Wirkzeit von Zaubern',
			L::EN => 'Halves casting time of spells',
			L::ES => 'Reduce a la mitad el tiempo de lanzamiento de los conjuros',
			L::FR => 'Réduit de moitié le temps d\'incantation des sorts',
			L::IT => 'Dimezza il Tempo di lancio delle magie',
			L::XX => 'Haelfes caesteeng teeme-a ooff spells',
		],
		self::HSR_ATTRIBUTE     => [
			L::DE => 'Halbiert Fertigkeitswiederaufladung von Zaubern desselben Attributs des Gegenstandes',
			L::EN => 'Halves skill recharge on spells of item\'s attribute',
			L::ES => 'Reduce a la mitad la recarga de los conjuros con el attributo del objeto',
			L::FR => 'Réduit de moitié le Rechargement des sorts liés à la caractéristique de l\'objet',
			L::IT => 'Dimezza la Ricarica delle magie relative all\'attributo dell\'oggetto',
			L::XX => 'Haelfes skeell rechaerge-a oon spells ooff item\'s aettreeboote-a',
		],
		self::HCT_ATTRIBUTE     => [
			L::DE => 'Halbiert Wirkzeit von Zaubern desselben Attributs des Gegenstandes',
			L::EN => 'Halves casting time on spells of item\'s attribute',
			L::ES => 'Reduce a la mitad el tiempo de lanzamiento de los conjuros con el attributo del objeto',
			L::FR => 'Réduit de moitié le temps d\'incantation des sorts liés à la caractéristique de l\'objet',
			L::IT => 'Dimezza il Tempo di lancio delle magie relative all\'attributo dell\'oggetto',
			L::XX => 'Haelfes caesteeng teeme-a oon spells ooff item\'s aettreeboote-a',
		],
		self::LIFE_DRAIN        => [
			L::DE => 'Lebensentzug: %s',
			L::EN => 'Life Draining: %s',
			L::ES => 'Drenar vida: %s',
			L::FR => 'Drain de vie : %s',
			L::IT => 'Risuccio Salute: %s',
			L::XX => 'Leeffe-a Draeeeneeng: %s',
		],
		self::HEALTH_REGEN      => [
			L::DE => 'Regeneration von Lebenspunkten %s',
			L::EN => 'Health regeneration %s',
			L::ES => 'Regeneración de salud %s',
			L::FR => 'Régénération de santé %s',
			L::IT => 'Rigenerazione della Salute %s',
			L::XX => 'Heaelt regeneraeshun %s',
		],
		self::ENERGY_ON_HIT     => [
			L::DE => 'Energiegewinn bei Treffer: %s',
			L::EN => 'Energy gain on hit: %s',
			L::ES => 'Energía obtenida por golpe: %s',
			L::FR => 'Gain d\'énergie par coup : %s',
			L::IT => 'Incremento di Energia per colpo: %s',
			L::XX => 'Inergy gaeen oon heet: %s',
		],
		self::ENERGY_REGEN      => [
			L::DE => 'Regeneration von Energiepunkten %s',
			L::EN => 'Energy regeneration %s',
			L::ES => 'Regeneración de energía %s',
			L::FR => 'Régénération d\'énergie %s',
			L::IT => 'Rigenerazione dell\'Energia %s',
			L::XX => 'Inergy regeneration %s',
		],
		self::ENCH_DURATION     => [
			L::DE => 'Verlängert Verzauberungen um %s%%',
			L::EN => 'Enchantments last %s%% longer',
			L::ES => 'Alarga los encantamientos en un %s%%',
			L::FR => 'Les enchantements durent %s%% plus longtemps.',
			L::IT => 'Prolunga gli incantesimi del %s%%',
			L::XX => 'Inchuntments laest %s%% lunger',
		],
		self::DOUBLE_ADRENALINE => [
			L::DE => 'Doppelte Adrenalinmenge',
			L::EN => 'Double adrenaline gain',
			L::ES => 'Duplica la adrenalina ganada',
			L::FR => 'Double gain d\'adrénaline',
			L::IT => 'Fornisce il doppio di adrenalina',
			L::XX => 'Duooble-a aedrenaeleene-a gaeeen',
		],
		self::DMG_REDUCTION     => [
			L::DE => 'Erlittener körperlicher Schaden -%s',
			L::EN => 'Received physical damage -%s',
			L::ES => 'Daño físico recibido -%s',
			L::FR => 'Dégâts physiques reçus -%s',
			L::IT => 'Danno fisico ricevuto -%s',
			L::XX => 'Receefed physeecael daemaege-a -%s',
		],
		self::ATTRIBUTE_BONUS   => [
			L::DE => '%1$s +%2$s',
			L::EN => '%1$s +%2$s',
			L::ES => '%1$s +%2$s',
			L::FR => '%1$s +%2$s',
			L::IT => '%1$s +%2$s',
			L::XX => '%1$s +%2$s',
		],
		self::ATTRIBUTE_ITEM    => [
			L::DE => 'Gegenstandsattribut +%s',
			L::EN => 'Item\'s attribute +%s',
			L::ES => 'Atributo de objeto +%s',
			L::FR => 'Caractéristique de l\'objet +%s',
			L::IT => 'Attributo oggetto +%s',
			L::XX => 'Item\'s aettreeboote-a +%s',
		],
		self::CON_DUR_INCREASE  => [
			L::DE => 'Erhöht %s-Dauer beim Gegner um 33%%',
			L::EN => 'Lengthens %s duration on foes by 33%%',
			L::ES => 'Los efectos que causa %s duran un 33%% más en el enemigo',
			L::FR => 'Augmente de 33%% la durée de "%s" sur les ennemis.',
			L::IT => 'Aumenta del 33%% la durata di "%s" sui nemici',
			L::XX => 'Lengzeens %s dooraeshun oon fues by 33%%',
		],
		self::CON_DUR_DECREASE  => [
			L::DE => 'Reduziert %s-Dauer bei Euch um 20%%',
			L::EN => 'Reduces %s duration on you by 20%%',
			L::ES => 'Los efectos que causa %s duran un 20%% menos en ti',
			L::FR => 'Réduit de 20%% la durée de "%s" sur vous.',
			L::IT => 'Diminuisce del 20%% la durata di "%s"',
			L::XX => 'Redooces %s dooraeshun oon yuoo by 20%%',
		],
		self::W_LIEUTENANTS     => [
			L::DE => 'Reduziert die Dauer von Verhexungen auf Euch um 20%% und Schaden, den Ihr zufügt, um 5%%.',
			L::EN => 'Reduces hex durations on you by 20%% and damage dealt by you by 5%%',
			L::ES => 'Reduce la duración de los maleficios que te echen en un 20%% y el daño que hagas en un 5%%.',
			L::FR => 'Réduit la durée des maléfices sur vous de 20%% et les dégâts que vous infligez de 5%%.',
			L::IT => 'Reduce del 20%% la durata delle fatture da cui sei affetto e il danno da te inferto del 5%%.',
			L::XX => 'Redooces hex dooraeshuns oon yuoo by 20%% und daemaege-a deaelt by yuoo by 5%%',
		],
		self::W_STONEFIST       => [
			L::DE => 'Erhöht die Dauer, die ein Gegner außer Gefecht gesetzt wird, um 1 Sekunde. (Maximal: 3 Sekunden)',
			L::EN => 'Increases knockdown time of foes by 1 second. (Maximum: 3 seconds)',
			L::ES => 'Aumenta en 1 segundo el tiempo que estarán en el suelo los enemigos derribados (máximo: 3 segundos).',
			L::FR => 'Augmente le temps où les ennemis sont assommés de 1 seconde. (Maximum : 3 secondes)',
			L::IT => 'Aumenta di 1 secondo il tempo di abbattimento del nemico (massimo: 3 secondi).',
			L::XX => 'Inceaeses knuckdoon teeme-a ooff fues by 1 secund. (Maexeemoom: 3 secunds)',
		],
		self::W_ABSORPTION      => [
			L::DE => 'Reduziert physischen Schaden um %s.',
			L::EN => 'Reduces physical damage by %s',
			L::ES => 'Reduce %s de daño físico',
			L::FR => 'Réduit les dégâts physiques de %s.',
			L::IT => 'Riduce il danno fisico di %s',
			L::XX => 'Redooces physeecael daemaege-a by %s',
		],
		self::N_BLOODSTAINED    => [
			L::DE => 'Reduziert die Wirkzeit von Zaubern, die Kadaver benutzen, um 25%%.',
			L::EN => 'Reduces casting time of spells that exploit corpses by 25%%',
			L::ES => 'Reduce en un 25%% el tiempo de lanzamiento de los conjuros que explotan cadáveres.',
			L::FR => 'Réduit de 25%% le temps d\'incantation des sorts qui utilisent les cadavres',
			L::IT => 'Riduce del 25%% il tempo di lancio delle magie che sfruttano i cadaveri.',
			L::XX => 'Redooces caesteeng teeme-a oof spells thaet ixplueet curpses by 25%%',
		],
		self::N_TORMENTORS      => [
			L::DE => 'Sakral-Schaden, den Ihr nehmt, um %s erhöht',
			L::EN => 'Holy damage you receive increased by %s',
			L::ES => 'Se aumenta en %s los ataques que sean con daño sagrado',
			L::FR => 'Les dégâts sacré que vous subissez sont augmentés de %s',
			L::IT => 'Il danno sacrale ricevuto aumenta di %s',
			L::XX => 'Huly daemaege-a yuoo receefe-a is increaesed by %s',
		],
		self::ENERGY_RECOVERY   => [
			L::DE => 'Energierückgewinnung %s',
			L::EN => 'Energy recovery %s',
			L::ES => 'Recuperación de energía %s',
			L::FR => 'Récupération d\'énergie %s',
			L::IT => 'Recupero energia %s',
			L::XX => 'Inergy recufery %s',
		],
		self::OF_PROFESSION     => [
			L::DE => '%1$s: %2$s',
			L::EN => '%1$s: %2$s',
			L::ES => '%1$s: %2$s',
			L::FR => '%1$s : %2$s',
			L::IT => '%1$s: %2$s',
			L::XX => '%1$s: %2$s',
		],
	];

	public function getAffix(int|string ...$values):string{
		// @todo: language fix
		if(!isset(self::NAME[$this->id][$this->lang->id])){
			return sprintf('[EFFECT_%s_%s]', dechex($this->id), $this->lang->id);
		}

		return sprintf(self::NAME[$this->id][$this->lang->id], ...$values); // phpcs:ignore
	}

}
