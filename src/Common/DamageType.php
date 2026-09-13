<?php
/**
 * Class DamageType
 *
 * @created      16.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

/**
 * @see https://wiki.guildwars.com/wiki/Damage_type
 * @see https://www.guildwiki.de/wiki/Schaden
 */
final class DamageType extends DataObjectAbstract{

	public const string CSS_CLASS = 'damagetype';

	public const int COLD        = 0x8001;
	public const int EARTH       = 0x8002;
	public const int FIRE        = 0x8003;
	public const int LIGHTNING   = 0x8004;
	public const int BLUNT       = 0x8005;
	public const int PIERCING    = 0x8006;
	public const int SLASHING    = 0x8007;
	public const int CHAOS       = 0x8008;
	public const int HOLY        = 0x8009;
	public const int SACRAL      = 0x800a; // GW differentiate between "holy" and "sacral" damage similar to "shadow" and "dark", the latter usually only used on weapon affixes and for some skills
	public const int DARK        = 0x800b;
	public const int SHADOW      = 0x800c;

	// virtual types
	public const int NONE        = 0x8100;
	public const int ELEMENTAL   = 0x8101;
	public const int PHYSICAL    = 0x8102;
	public const int UNSPECIFIED = 0x8103;

	public const array NAME = [
		self::COLD        => [Lang::DE => 'Kälte-Schaden',        Lang::EN => 'Cold Damage',      Lang::ES => 'Daño frío',             Lang::FR => 'Dégâts du froid',     Lang::IT => 'Danno da freddo',   Lang::XX => 'Culd daemaege-a',       ],
		self::EARTH       => [Lang::DE => 'Erd-Schaden',          Lang::EN => 'Earth Damage',     Lang::ES => 'Daño de tierra',        Lang::FR => 'Dégâts terrestres',   Lang::IT => 'Danno da terra',    Lang::XX => 'Iaert daemaege-a',      ],
		self::FIRE        => [Lang::DE => 'Feuer-Schaden',        Lang::EN => 'Fire Damage',      Lang::ES => 'Daño de fuego',         Lang::FR => 'Dégâts de feu',       Lang::IT => 'Danno da fuoco',    Lang::XX => 'Fure-a daemaege-a',     ],
		self::LIGHTNING   => [Lang::DE => 'Blitz-Schaden',        Lang::EN => 'Lightning Damage', Lang::ES => 'Daño de relámpago',     Lang::FR => 'Dégâts de foudre',    Lang::IT => 'Danno da fulmine',  Lang::XX => 'Leeghtneeng daemaege-a',],
		self::BLUNT       => [Lang::DE => 'Stumpf-Schaden',       Lang::EN => 'Blunt Damage',     Lang::ES => 'Daño contundente',      Lang::FR => 'Dégâts contondant',   Lang::IT => 'Danno contundente', Lang::XX => 'Bloont daemaege-a',     ],
		self::PIERCING    => [Lang::DE => 'Stich-Schaden',        Lang::EN => 'Piercing Damage',  Lang::ES => 'Daño perforante',       Lang::FR => 'Dégâts perforants',   Lang::IT => 'Danno perforante',  Lang::XX => 'Peeerceeng daemaege-a', ],
		self::SLASHING    => [Lang::DE => 'Hieb-Schaden',         Lang::EN => 'Slashing Damage',  Lang::ES => 'Daño cortante',         Lang::FR => 'Dégâts tranchant',    Lang::IT => 'Danno da taglio',   Lang::XX => 'Slaesheeng daemaege-a', ],
		self::CHAOS       => [Lang::DE => 'Chaos-Schaden',        Lang::EN => 'Chaos Damage',     Lang::ES => 'Daño de caos',          Lang::FR => 'Dégâts chaotique',    Lang::IT => 'Danno da caos',     Lang::XX => 'Chaeus daemaege-a',     ],
		self::HOLY        => [Lang::DE => 'Heiliger Schaden',     Lang::EN => 'Holy Damage',      Lang::ES => 'Daño santo',            Lang::FR => 'Dégâts saints',       Lang::IT => 'Danno santo',       Lang::XX => 'Huly daemaege-a',       ],
		self::SACRAL      => [Lang::DE => 'Sakral-Schaden',       Lang::EN => 'Sacral Damage',    Lang::ES => 'Daño sagrado',          Lang::FR => 'Dégâts sacré',        Lang::IT => 'Danno sacrale',     Lang::XX => 'Saecrul daemaege-a',    ],
		self::DARK        => [Lang::DE => 'Dunkel-Schaden',       Lang::EN => 'Dark Damage',      Lang::ES => 'Daño oscuro',           Lang::FR => 'Dégâts d\'ombre',     Lang::IT => 'Danno da buio',     Lang::XX => 'Daerk daemaege-a',      ],
		self::SHADOW      => [Lang::DE => 'Schaatten-Schaden',    Lang::EN => 'Shadow Damage',    Lang::ES => 'Daño de sombra',        Lang::FR => 'Dégâts de l\'ombre',  Lang::IT => 'Danno da ombra',    Lang::XX => 'Shaedoo daemaege-a',    ],
		self::NONE        => [Lang::DE => '-',                    Lang::EN => '-',                Lang::ES => '-',                     Lang::FR => '-',                   Lang::IT => '-',                 Lang::XX => '-',                     ],
		self::ELEMENTAL   => [Lang::DE => 'Elementarschaden',     Lang::EN => 'Elemental damage', Lang::ES => 'Daño de los elementos', Lang::FR => 'Dégâts élémentaires', Lang::IT => 'Danno elementale',  Lang::XX => 'Ilementael daemaege-a', ],
		self::PHYSICAL    => [Lang::DE => 'Körperlicher Schaden', Lang::EN => 'Physical damage',  Lang::ES => 'Daño físico',           Lang::FR => 'Dégâts physiques',    Lang::IT => 'Danno fisico',      Lang::XX => 'Physeecael daemaege-a', ],
		self::UNSPECIFIED => [Lang::DE => 'Schaden',              Lang::EN => 'Damage',           Lang::ES => 'Daño',                  Lang::FR => 'Dégâts',              Lang::IT => 'Danno',             Lang::XX => 'Daemaege-a',            ],
	];

	// contains grammatical changes for several strings for when they are used in an affix, such as "vs. elemental damage"
	private const array AFFIX_NAME = [
		self::ELEMENTAL => [
			Lang::DE => 'Elementarschaden',
			Lang::EN => 'elemental damage',
			Lang::ES => 'daño de elementos',
			Lang::FR => 'les dégâts élémentaires',
			Lang::IT => 'danno elementale',
			Lang::XX => 'ilementael daemaege-a',
		],
		self::PHYSICAL  => [
			Lang::DE => 'körperlichen Schaden',
			Lang::EN => 'physical damage',
			Lang::ES => 'daño físico',
			Lang::FR => 'les dégâts physiques',
			Lang::IT => 'danno fisico',
			Lang::XX => 'physeecael daemaege-a',
		],
	];

	public function getAffixName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return (self::AFFIX_NAME[$this->id][$lang->id] ?? self::NAME[$this->id][$lang->id]);
	}

}
