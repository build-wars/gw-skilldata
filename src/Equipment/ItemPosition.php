<?php
/**
 * Class ItemPosition
 *
 * @created      17.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\DataObjectAbstract;
use Buildwars\GWSkillData\Common\Lang;

final class ItemPosition extends DataObjectAbstract{

	public const string CSS_CLASS = 'itemposition';

	// item position (GW enumeration)
	public const int WEAPON    = 0;
	public const int OFFHAND   = 1;
	public const int CHEST     = 2;
	public const int LEGS      = 3;
	public const int HEAD      = 4;
	public const int FEET      = 5;
	public const int HANDS     = 6;
	public const int UNDEFINED = 7;

	public const array NAME = [
		self::WEAPON    => [Lang::DE => 'Waffe',       Lang::EN => 'Weapon',   Lang::ES => 'Arma',           Lang::FR => 'Arme',           Lang::IT => 'Arma',          Lang::XX => 'Veaepun',    ],
		self::OFFHAND   => [Lang::DE => 'Begleithand', Lang::EN => 'Off-Hand', Lang::ES => 'Complementario', Lang::FR => 'Complémentaire', Lang::IT => 'Complementari', Lang::XX => 'Ooffff-Hund',],
		self::CHEST     => [Lang::DE => 'Brust',       Lang::EN => 'Chest',    Lang::ES => 'Pecho',          Lang::FR => 'Torse',          Lang::IT => 'Torace',        Lang::XX => 'Chest',      ],
		self::LEGS      => [Lang::DE => 'Beine',       Lang::EN => 'Legs',     Lang::ES => 'Piernas',        Lang::FR => 'Jambes',         Lang::IT => 'Gambe',         Lang::XX => 'Legs',       ],
		self::HEAD      => [Lang::DE => 'Kopf',        Lang::EN => 'Head',     Lang::ES => 'Cara',           Lang::FR => 'Tête',           Lang::IT => 'Testa',         Lang::XX => 'Heaed',      ],
		self::FEET      => [Lang::DE => 'Füße',        Lang::EN => 'Feet',     Lang::ES => 'Pies',           Lang::FR => 'Pieds',          Lang::IT => 'Piedi',         Lang::XX => 'Feet',       ],
		self::HANDS     => [Lang::DE => 'Hände',       Lang::EN => 'Hands',    Lang::ES => 'Manos',          Lang::FR => 'Bras',           Lang::IT => 'Mani',          Lang::XX => 'Hunds',      ],
		self::UNDEFINED => [Lang::DE => '-',           Lang::EN => '-',        Lang::ES => '-',              Lang::FR => '-',              Lang::IT => '-',             Lang::XX => '-',          ],
	];

	public const array ARMOR     = [self::CHEST, self::LEGS, self::HEAD, self::FEET, self::HANDS];
	public const array WEAPONSET = [self::WEAPON, self::OFFHAND];

}
