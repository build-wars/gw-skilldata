<?php
/**
 * Class Condition
 *
 * @created      23.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

/**
 * @see https://wiki.guildwars.com/wiki/Condition
 * @see https://www.guildwiki.de/wiki/Zustand
 */
final class Condition extends DataObjectAbstract{

	public const int BLEEDING      = 0xb001;
	public const int BLIND         = 0xb002;
	public const int BURNING       = 0xb003;
	public const int CRACKED_ARMOR = 0xb004;
	public const int CRIPPLED      = 0xb005;
	public const int DAZED         = 0xb006;
	public const int DEEP_WOUND    = 0xb007;
	public const int DISEASE       = 0xb008;
	public const int POISON        = 0xb009;
	public const int WEAKNESS      = 0xb00a;

	public const array NAME = [
		self::BLEEDING      => [
			Lang::DE => 'Blutung',
			Lang::EN => 'Bleeding',
			Lang::ES => 'la Hemorragia',
			Lang::FR => 'Saignement',
			Lang::IT => 'Emorragia',
			Lang::XX => 'Bleedeeng',
		],
		self::BLIND         => [
			Lang::DE => 'Blindheit',
			Lang::EN => 'Blind',
			Lang::ES => 'la Ceguera',
			Lang::FR => 'Aveuglement',
			Lang::IT => 'Accecamento',
			Lang::XX => 'Bleend',
		],
		self::BURNING       => [
			Lang::DE => 'Brennen',
			Lang::EN => 'Burning',
			Lang::ES => 'la Quemadura',
			Lang::FR => 'Brûlure',
			Lang::IT => 'Bruciatura',
			Lang::XX => 'Boorneeng',
		],
		self::CRACKED_ARMOR => [
			Lang::DE => 'Beschädigte Rüstung',
			Lang::EN => 'Cracked Armor',
			Lang::ES => 'la Armadura rota',
			Lang::FR => 'Armure brisée',
			Lang::IT => 'Armatura Incrinata',
			Lang::XX => 'Craecked Aermur',
		],
		self::CRIPPLED      => [
			Lang::DE => 'Verkrüppelung',
			Lang::EN => 'Crippled',
			Lang::ES => 'la Lisiadura',
			Lang::FR => 'Infirmité',
			Lang::IT => 'Storpiatura',
			Lang::XX => 'Creeppled',
		],
		self::DAZED         => [
			Lang::DE => 'Benommenheit',
			Lang::EN => 'Dazed',
			Lang::ES => 'el Aturdimiento',
			Lang::FR => 'Stupeur',
			Lang::IT => 'Stordimento',
			Lang::XX => 'Daezed',
		],
		self::DEEP_WOUND    => [
			Lang::DE => 'Tiefe Wunde',
			Lang::EN => 'Deep Wound',
			Lang::ES => 'la Herida grave',
			Lang::FR => 'Blessure profonde',
			Lang::IT => 'Ferita Profonda',
			Lang::XX => 'Deep Vuoond',
		],
		self::DISEASE       => [
			Lang::DE => 'Krankheit',
			Lang::EN => 'Disease',
			Lang::ES => 'la Enfermedad',
			Lang::FR => 'Maladie',
			Lang::IT => 'Malattia',
			Lang::XX => 'Deeseaese-a',
		],
		self::POISON        => [
			Lang::DE => 'Gift',
			Lang::EN => 'Poison',
			Lang::ES => 'el Envenenamiento',
			Lang::FR => 'Poison',
			Lang::IT => 'Veleno',
			Lang::XX => 'Pueesun',
		],
		self::WEAKNESS      => [
			Lang::DE => 'Schwäche',
			Lang::EN => 'Weakness',
			Lang::ES => 'la Debilidad',
			Lang::FR => 'Faiblesse',
			Lang::IT => 'Debolezza',
			Lang::XX => 'Veaeknees',
		],
	];

}
