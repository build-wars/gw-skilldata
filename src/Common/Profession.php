<?php
/**
 * Class Profession
 *
 * @created      27.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 *
 * @codeCoverageIgnore
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use function implode;
use function sprintf;
use function strtolower;

/**
 * Encapsulates all profession related static data
 *
 * Right now, we're only using the generic masculinum because it's a mess to implement grammatical genders
 * troughout skill descriptions, items etc. and idk if it's even worth it for this project.
 *
 * If anyone complains, i'm gonna change evreything to generic femininum and you can't stop me.
 */
final class Profession extends DataObjectAbstract{

	public const string CSS_CLASS = 'profession';

	public const int NONE         = 0;
	public const int WARRIOR      = 1;
	public const int RANGER       = 2;
	public const int MONK         = 3;
	public const int NECROMANCER  = 4;
	public const int MESMER       = 5;
	public const int ELEMENTALIST = 6;
	public const int ASSASSIN     = 7;
	public const int RITUALIST    = 8;
	public const int PARAGON      = 9;
	public const int DERVISH      = 10;

	public const array NAME = [
		self::NONE         => [Lang::DE => 'keine',           Lang::EN => 'none',         Lang::ES => 'ninguno',       Lang::FR => 'aucun',         Lang::IT => 'alcuno',        Lang::XX => 'nune-a',        ],
		self::WARRIOR      => [Lang::DE => 'Krieger',         Lang::EN => 'Warrior',      Lang::ES => 'Guerrero',      Lang::FR => 'Guerrier',      Lang::IT => 'Guerriero',     Lang::XX => 'Vaerreeur',     ],
		self::RANGER       => [Lang::DE => 'Waldläufer',      Lang::EN => 'Ranger',       Lang::ES => 'Guardabosques', Lang::FR => 'Rôdeur',        Lang::IT => 'Esploratore',   Lang::XX => 'Runger',        ],
		self::MONK         => [Lang::DE => 'Mönch',           Lang::EN => 'Monk',         Lang::ES => 'Monje',         Lang::FR => 'Moine',         Lang::IT => 'Mistico',       Lang::XX => 'Munk',          ],
		self::NECROMANCER  => [Lang::DE => 'Nekromant',       Lang::EN => 'Necromancer',  Lang::ES => 'Nigromante',    Lang::FR => 'Nécromant',     Lang::IT => 'Negromante',    Lang::XX => 'Necrumuncer',   ],
		self::MESMER       => [Lang::DE => 'Mesmer',          Lang::EN => 'Mesmer',       Lang::ES => 'Hipnotizador',  Lang::FR => 'Envoûteur',     Lang::IT => 'Ipnotizzatore', Lang::XX => 'Mesmer',        ],
		self::ELEMENTALIST => [Lang::DE => 'Elementarmagier', Lang::EN => 'Elementalist', Lang::ES => 'Elementalista', Lang::FR => 'Elémentaliste', Lang::IT => 'Elementalista', Lang::XX => 'Ilementaeleest',],
		self::ASSASSIN     => [Lang::DE => 'Assassine',       Lang::EN => 'Assassin',     Lang::ES => 'Asesino',       Lang::FR => 'Assassin',      Lang::IT => 'Assassino',     Lang::XX => 'Aessaesseen',   ],
		self::RITUALIST    => [Lang::DE => 'Ritualist',       Lang::EN => 'Ritualist',    Lang::ES => 'Ritualista',    Lang::FR => 'Ritualiste',    Lang::IT => 'Ritualista',    Lang::XX => 'Reetooaeleest', ],
		self::PARAGON      => [Lang::DE => 'Paragon',         Lang::EN => 'Paragon',      Lang::ES => 'Paragón',       Lang::FR => 'Parangon',      Lang::IT => 'Paragon',       Lang::XX => 'Paeraegun',     ],
		self::DERVISH      => [Lang::DE => 'Derwisch',        Lang::EN => 'Dervish',      Lang::ES => 'Derviche',      Lang::FR => 'Derviche',      Lang::IT => 'Derviscio',     Lang::XX => 'Derfeesh',      ],
	];

	/** @var array<int, array{de: string, en: string}> */
	public const array NAME_ABBR = [
		self::NONE         => [Lang::DE => 'X',  Lang::EN => 'X',  Lang::ES => 'X',  Lang::FR => 'X',  Lang::IT => 'X',  Lang::XX => 'X',   ],
		self::WARRIOR      => [Lang::DE => 'K',  Lang::EN => 'W',  Lang::ES => 'Gr', Lang::FR => 'G',  Lang::IT => 'G',  Lang::XX => 'V',   ],
		self::RANGER       => [Lang::DE => 'W',  Lang::EN => 'R',  Lang::ES => 'Gu', Lang::FR => 'R',  Lang::IT => 'Es', Lang::XX => 'R',   ],
		self::MONK         => [Lang::DE => 'Mö', Lang::EN => 'Mo', Lang::ES => 'M',  Lang::FR => 'M',  Lang::IT => 'M',  Lang::XX => 'Mu',  ],
		self::NECROMANCER  => [Lang::DE => 'N',  Lang::EN => 'N',  Lang::ES => 'N',  Lang::FR => 'N',  Lang::IT => 'N',  Lang::XX => 'N',   ],
		self::MESMER       => [Lang::DE => 'Me', Lang::EN => 'Me', Lang::ES => 'H',  Lang::FR => 'En', Lang::IT => 'I',  Lang::XX => 'Me-a',],
		self::ELEMENTALIST => [Lang::DE => 'E',  Lang::EN => 'E',  Lang::ES => 'E',  Lang::FR => 'El', Lang::IT => 'El', Lang::XX => 'I',   ],
		self::ASSASSIN     => [Lang::DE => 'A',  Lang::EN => 'A',  Lang::ES => 'A',  Lang::FR => 'A',  Lang::IT => 'A',  Lang::XX => 'A',   ],
		self::RITUALIST    => [Lang::DE => 'R',  Lang::EN => 'Rt', Lang::ES => 'R',  Lang::FR => 'Rt', Lang::IT => 'R',  Lang::XX => 'Rt',  ],
		self::PARAGON      => [Lang::DE => 'P',  Lang::EN => 'P',  Lang::ES => 'P',  Lang::FR => 'P',  Lang::IT => 'P',  Lang::XX => 'P',   ],
		self::DERVISH      => [Lang::DE => 'D',  Lang::EN => 'D',  Lang::ES => 'D',  Lang::FR => 'D',  Lang::IT => 'D',  Lang::XX => 'D',   ],
	];

	/** @var array<int, int> */
	public const array PRIMARY_ATTRIBUTE = [
		self::NONE         => Attribute::NONE,
		self::WARRIOR      => Attribute::STRENGTH,
		self::RANGER       => Attribute::EXPERTISE,
		self::MONK         => Attribute::DIVINE_FAVOR,
		self::NECROMANCER  => Attribute::SOUL_REAPING,
		self::MESMER       => Attribute::FAST_CASTING,
		self::ELEMENTALIST => Attribute::ENERGY_STORAGE,
		self::ASSASSIN     => Attribute::CRITICAL_STRIKES,
		self::RITUALIST    => Attribute::SPAWNING_POWER,
		self::PARAGON      => Attribute::LEADERSHIP,
		self::DERVISH      => Attribute::MYSTICISM,
	];

	/** @var array<int, int> */
	private const array CAMPAIGN = [
		self::NONE         => Campaign::CORE,
		self::WARRIOR      => Campaign::CORE,
		self::RANGER       => Campaign::CORE,
		self::MONK         => Campaign::CORE,
		self::NECROMANCER  => Campaign::CORE,
		self::MESMER       => Campaign::CORE,
		self::ELEMENTALIST => Campaign::CORE,
		self::ASSASSIN     => Campaign::FACTIONS,
		self::RITUALIST    => Campaign::FACTIONS,
		self::PARAGON      => Campaign::NIGHTFALL,
		self::DERVISH      => Campaign::NIGHTFALL,
	];

	/**
	 * Returns the short name for the fiven profession ID
	 */
	public function getAbbr(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return self::NAME_ABBR[$this->id][$lang->id];
	}

	/**
	 * Returns the primary attribute of the current profession
	 */
	public function getPrimaryAttribute(int $level = 0):Attribute{
		return (new Attribute(self::PRIMARY_ATTRIBUTE[$this->id], $this->lang))->setLevel($level);
	}

	/**
	 * Returns the primary attribute ID of the current profession
	 */
	public function getPrimaryAttributeID():int{
		return self::PRIMARY_ATTRIBUTE[$this->id];
	}

	/**
	 * Returns the campaign of the current profession
	 */
	public function getCampaign():Campaign{
		return new Campaign(self::CAMPAIGN[$this->id], $this->lang);
	}

	/**
	 * Returns the campaign ID of the current profession
	 */
	public function getCampaignID():int{
		return self::CAMPAIGN[$this->id];
	}

	/**
	 * Returns all attributes for the current profession
	 *
	 * @return int[]
	 */
	public function getAttributes():array{
		return Attribute::getByProfession($this);
	}

	public function toHTML(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return sprintf(
			'<span class="%s" data-id="%d" data-lang="%s">%s</span>',
			implode(' ', [self::CSS_CLASS, strtolower($this->getName(Lang::EN))]),
			$this->id,
			$lang->id,
			$this->getName($lang),
		);
	}

}
