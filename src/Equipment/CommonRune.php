<?php
/**
 * Class CommonRune
 *
 * @created      14.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Lang as L;
use function sprintf;

final class CommonRune extends ModSubtypeAbstract{

	public const int MINOR    = 0x200e;
	public const int MAJOR    = 0x200f;
	public const int SUPERIOR = 0x2010;

	public const array NAME = [
		self::MINOR      => [L::DE => 'Kleinere Rune',   L::EN => 'Minor rune',    L::ES => 'Runa menor',       L::FR => 'Rune mineur',    L::IT => 'Runa minore',   L::XX => 'Meenur roone-a',    ],
		self::MAJOR      => [L::DE => 'Hohe Rune',       L::EN => 'Major rune',    L::ES => 'Runa mayor',       L::FR => 'Rune majeur',    L::IT => 'Runa maggiore', L::XX => 'Maejur roone-a',    ],
		self::SUPERIOR   => [L::DE => 'Überlegene Rune', L::EN => 'Superior rune', L::ES => 'Runa excepcional', L::FR => 'Rune supérieur', L::IT => 'Runa supremo',  L::XX => 'Soopereeur roone-a',],
	];

	private const array ITEM_NAMES = [
		156 => [L::DE => 'Rune d. kleineren Lebenskraft', L::EN => 'Rune of Minor Vigor', L::ES => 'Runa (vigor de grado menor)', L::FR => 'Rune (Vigueur : bonus mineur)', L::IT => 'Runa Vigore di grado minore', L::XX => 'Roone-a ooff Meenur Feegur',],
		157 => [L::DE => 'Rune d. hohen Lebenskraft', L::EN => 'Rune of Major Vigor', L::ES => 'Runa (vigor de grado mayor)', L::FR => 'Rune (Vigueur : bonus majeur)', L::IT => 'Runa Vigore di grado maggiore', L::XX => 'Roone-a ooff Maejur Feegur',],
		158 => [L::DE => 'Rune d. überlegenen Lebenskraft', L::EN => 'Rune of Superior Vigor', L::ES => 'Runa (vigor de grado excepcional)', L::FR => 'Rune (Vigueur : bonus supérieur)', L::IT => 'Runa Vigore di grado supremo', L::XX => 'Roone-a ooff Soopereeur Feegur',],
		159 => [L::DE => 'Krieger-Rune d. kleineren Absorption', L::EN => 'Rune of Minor Absorption', L::ES => 'Runa de Guerrero (absorción de grado menor)', L::FR => 'Rune de Guerrier (Absorption : bonus mineur)', L::IT => 'Runa del Guerriero Assorbimento di grado minore', L::XX => 'Vaerreeur Roone-a ooff Meenur Aebsurpshun',],
		160 => [L::DE => 'Krieger-Rune d. hohen Absorption', L::EN => 'Rune of Major Absorption', L::ES => 'Runa de Guerrero (absorción de grado mayor)', L::FR => 'Rune de Guerrier (Absorption : bonus majeur)', L::IT => 'Runa del Guerriero Assorbimento di grado maggiore', L::XX => 'Vaerreeur Roone-a ooff Maejur Aebsurpshun',],
		161 => [L::DE => 'Krieger-Rune d. überlegenen Absorption', L::EN => 'Rune of Superior Absorption', L::ES => 'Runa de Guerrero (absorción de grado excepcional)', L::FR => 'Rune de Guerrier (Absorption : bonus supérieur)', L::IT => 'Runa del Guerriero Assorbimento di grado supremo', L::XX => 'Vaerreeur Roone-a ooff Soopereeur Aebsurpshun',],

		352 => [L::DE => 'Rune d. Einstimmung', L::EN => 'Rune of Attunement', L::ES => 'Runa (de sintonía)', L::FR => 'Rune (d\'affinité)', L::IT => 'Runa dell\'Armonia', L::XX => 'Roone-a ooff Aettoonement',],
		353 => [L::DE => 'Rune d. Lebenskraft', L::EN => 'Rune of Vitae', L::ES => 'Runa (de vida)', L::FR => 'Rune (de la vie)', L::IT => 'Runa della Vita', L::XX => 'Roone-a ooff Feetaee-a',],
		354 => [L::DE => 'Rune d. Gesundung', L::EN => 'Rune of Recovery', L::ES => 'Runa (de mejoría)', L::FR => 'Rune (de récupération)', L::IT => 'Runa della Ripresa', L::XX => 'Roone-a ooff Recufery',],
		355 => [L::DE => 'Rune d. Wiederherstellung', L::EN => 'Rune of Restoration', L::ES => 'Runa (de restauración)', L::FR => 'Rune (de rétablissement)', L::IT => 'Runa del Ripristino', L::XX => 'Roone-a ooff Resturaeshun',],
		356 => [L::DE => 'Rune d. Klarheit', L::EN => 'Rune of Clarity', L::ES => 'Runa (de claridad)', L::FR => 'Rune (de la clarté)', L::IT => 'Runa della Trasparenza', L::XX => 'Roone-a ooff Claereety',],
		357 => [L::DE => 'Rune d. Reinheit', L::EN => 'Rune of Purity', L::ES => 'Runa (de pureza)', L::FR => 'Rune (de la pureté)', L::IT => 'Runa della Purezza', L::XX => 'Roone-a ooff Pooreety',],
	];

	public function getItemName(L|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return (self::ITEM_NAMES[$this->modID][$lang->id] ?? sprintf('[RUNE_%s_%s]', $this->modID, $lang->id));
	}

}
