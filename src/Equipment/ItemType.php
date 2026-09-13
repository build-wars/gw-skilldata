<?php
/**
 * Class ItemType
 *
 * @created      16.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\DataObjectAbstract;
use Buildwars\GWSkillData\Common\Lang;

/**
 * @see https://wiki.guildwars.com/wiki/Equipment
 */
final class ItemType extends DataObjectAbstract{

	public const string CSS_CLASS = 'itemtype';

	// GW enumeration (or maybe slopbox? idk)
	public const int ARMOR       = 1; // virtual (not in game)
	public const int AXE         = 2;
	public const int ARMOR_FEET  = 4;
	public const int BOW         = 5;
	public const int ARMOR_CHEST = 7;
	public const int FOCUS       = 12;
	public const int ARMOR_HANDS = 13;
	public const int HAMMER      = 15;
	public const int ARMOR_HEAD  = 16;
	public const int ARMOR_LEGS  = 19;
	public const int WAND        = 22;
	public const int SHIELD      = 24;
	public const int STAFF       = 26;
	public const int SWORD       = 27;
	public const int DAGGERS     = 32;
	public const int SCYTHE      = 35;
	public const int SPEAR       = 36;
	public const int WEAPONS     = 37; // virtual
	public const int MARTIAL     = 38; // virtual
	public const int OFFHAND     = 39; // virtual
	public const int CASTER      = 41; // virtual

	public const array NAME = [
		self::ARMOR       => [Lang::DE => 'Rüstung',                 Lang::EN => 'Armor',               Lang::ES => 'Armadura',                         Lang::FR => 'Armure',                      Lang::IT => 'Armatura',              Lang::XX => 'Aermur',                 ],
		self::AXE         => [Lang::DE => 'Axt',                     Lang::EN => 'Axe',                 Lang::ES => 'Hacha',                            Lang::FR => 'Hache',                       Lang::IT => 'Ascia',                 Lang::XX => 'Aexe-a',                 ],
		self::ARMOR_FEET  => [Lang::DE => 'Schuhwerk',               Lang::EN => 'Footwear',            Lang::ES => 'Calzado',                          Lang::FR => 'Chausses',                    Lang::IT => 'Calzature',             Lang::XX => 'Fuutveaer',              ],
		self::BOW         => [Lang::DE => 'Bogen',                   Lang::EN => 'Bow',                 Lang::ES => 'Arco',                             Lang::FR => 'Arc',                         Lang::IT => 'Arco',                  Lang::XX => 'Boo',                    ],
		self::ARMOR_CHEST => [Lang::DE => 'Brustrüstung',            Lang::EN => 'Chest armor',         Lang::ES => 'Armadura del pecho',               Lang::FR => 'Armure de la poitrine',       Lang::IT => 'Armatura per il petto', Lang::XX => 'Chest aermur',           ],
		self::FOCUS       => [Lang::DE => 'Fokus',                   Lang::EN => 'Focus',               Lang::ES => 'Foco',                             Lang::FR => 'Focus',                       Lang::IT => 'Focus',                 Lang::XX => 'Fucoos',                 ],
		self::ARMOR_HANDS => [Lang::DE => 'Handschuhe',              Lang::EN => 'Gloves',              Lang::ES => 'Guantes',                          Lang::FR => 'Gants',                       Lang::IT => 'Guanti',                Lang::XX => 'Glufes',                 ],
		self::HAMMER      => [Lang::DE => 'Hammer',                  Lang::EN => 'Hammer',              Lang::ES => 'Martillo',                         Lang::FR => 'Marteau',                     Lang::IT => 'Martello',              Lang::XX => 'Haemmer',                ],
		self::ARMOR_HEAD  => [Lang::DE => 'Kopbedeckung',            Lang::EN => 'Headpiece',           Lang::ES => 'Casco',                            Lang::FR => 'Coiffe',                      Lang::IT => 'Copricapo',             Lang::XX => 'Heaedpeeece-a',          ],
		self::ARMOR_LEGS  => [Lang::DE => 'Beinrüstung',             Lang::EN => 'Leg armor',           Lang::ES => 'Armadura de las piernas',          Lang::FR => 'Armure des jambes',           Lang::IT => 'Armatura per le gambe', Lang::XX => 'Leg aermur',             ],
		self::WAND        => [Lang::DE => 'Stecken',                 Lang::EN => 'Wand',                Lang::ES => 'Varita',                           Lang::FR => 'Baguette',                    Lang::IT => 'Bacchetta',             Lang::XX => 'Vund',                   ],
		self::SHIELD      => [Lang::DE => 'Schild',                  Lang::EN => 'Shield',              Lang::ES => 'Escudo',                           Lang::FR => 'Bouclier',                    Lang::IT => 'Scudo',                 Lang::XX => 'Sheeeld',                ],
		self::STAFF       => [Lang::DE => 'Stab',                    Lang::EN => 'Staff',               Lang::ES => 'Báculo',                           Lang::FR => 'Bâton',                       Lang::IT => 'Bastone',               Lang::XX => 'Staeffff',               ],
		self::SWORD       => [Lang::DE => 'Schwert',                 Lang::EN => 'Sword',               Lang::ES => 'Espada',                           Lang::FR => 'Epée',                        Lang::IT => 'Spada',                 Lang::XX => 'Svurd',                  ],
		self::DAGGERS     => [Lang::DE => 'Dolche',                  Lang::EN => 'Daggers',             Lang::ES => 'Daga',                             Lang::FR => 'Dagues',                      Lang::IT => 'Pugnale',               Lang::XX => 'Daegger',                ],
		self::SCYTHE      => [Lang::DE => 'Sense',                   Lang::EN => 'Scythe',              Lang::ES => 'Guadaña',                          Lang::FR => 'Faux',                        Lang::IT => 'Falce',                 Lang::XX => 'Scyzee',                 ],
		self::SPEAR       => [Lang::DE => 'Speer',                   Lang::EN => 'Spear',               Lang::ES => 'Lanza',                            Lang::FR => 'Lance',                       Lang::IT => 'Lancia',                Lang::XX => 'Speaer',                 ],
		self::WEAPONS     => [Lang::DE => 'Waffen',                  Lang::EN => 'Weapons',             Lang::ES => 'Armas',                            Lang::FR => 'Armes',                       Lang::IT => 'Armi',                  Lang::XX => 'Veaepuns',               ],
		self::MARTIAL     => [Lang::DE => 'Kampfwaffen',             Lang::EN => 'Martial weapons',     Lang::ES => 'Armas marciales',                  Lang::FR => 'Armes de mêlée',              Lang::IT => 'Armi marziali',         Lang::XX => 'Maerteeael veaepuns',    ],
		self::OFFHAND     => [Lang::DE => 'Begleithand-Gegenstände', Lang::EN => 'Off-Hand items',      Lang::ES => 'Objetos complementarios',          Lang::FR => 'Objets complémentaires',      Lang::IT => 'Oggetti complementari', Lang::XX => 'Ooffff-Hund Items',      ],
		self::CASTER      => [Lang::DE => 'Zauberwirker-Waffen',     Lang::EN => 'Spellcaster weapons', Lang::ES => 'Armas de lanzamiento de conjuros', Lang::FR => 'Armes de lancement de sorts', Lang::IT => 'Armi da magia',         Lang::XX => 'Spellcaesteeng veaepuns',],
	];

	public const array WEAPON = [
		self::AXE     => Weapon::AXE,
		self::BOW     => Weapon::BOW,
		self::DAGGERS => Weapon::DAGGERS,
		self::HAMMER  => Weapon::HAMMER,
		self::SWORD   => Weapon::SWORD,
		self::SCYTHE  => Weapon::SCYTHE,
		self::SPEAR   => Weapon::SPEAR,
		self::STAFF   => Weapon::STAFF,
		self::WAND    => Weapon::WAND,
		self::FOCUS   => Weapon::FOCUS,
		self::SHIELD  => Weapon::SHIELD,
	];

	private const array NUM_MODS = [
		self::ARMOR       => 2,
		self::AXE         => 3,
		self::ARMOR_FEET  => 2,
		self::BOW         => 3,
		self::ARMOR_CHEST => 2,
		self::FOCUS       => 2,
		self::ARMOR_HANDS => 2,
		self::HAMMER      => 3,
		self::ARMOR_HEAD  => 2,
		self::ARMOR_LEGS  => 2,
		self::WAND        => 2,
		self::SHIELD      => 2,
		self::STAFF       => 3,
		self::SWORD       => 3,
		self::DAGGERS     => 3,
		self::SCYTHE      => 3,
		self::SPEAR       => 3,
		self::MARTIAL     => 3,
		self::OFFHAND     => 2,
	];

	public function getNumMods():int|null{
		// null means: be more specific
		return (self::NUM_MODS[$this->id] ?? null);
	}

}
