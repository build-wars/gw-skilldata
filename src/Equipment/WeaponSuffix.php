<?php
/**
 * Class Suffix
 *
 * @created      18.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Attribute;
use Buildwars\GWSkillData\Common\Lang;
use Buildwars\GWSkillData\Common\Profession;
use InvalidArgumentException;
use function array_key_exists;
use function dechex;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/List_of_weapon_upgrades#Suffix_bonuses
 * @see https://www.guildwiki.de/wiki/Suffix
 */
final class WeaponSuffix extends ModSubtypeAbstract{

	public const int APTITUDE   = 0x2001;
	public const int DEFENSE    = 0x2002;
	public const int DEVOTION   = 0x2003;
	public const int ENCHANTING = 0x2004;
	public const int ENDURANCE  = 0x2005;
	public const int FORTITUDE  = 0x2006;
	public const int MASTERY    = 0x2007;
	public const int MEMORY     = 0x2008;
	public const int QUICKENING = 0x2009;
	public const int SHELTER    = 0x200a;
	public const int SWIFTNESS  = 0x200b;
	public const int VALOR      = 0x200c;
	public const int WARDING    = 0x200d;
	public const int PROFESSION = 0x2014; // PvE only, does not work with equipment templates

	public const array NAME = [
		self::APTITUDE   => [Lang::DE => 'Begabung',       Lang::EN => 'Aptitude',     Lang::ES => 'de aptitud',        Lang::FR => 'd\'aptitude',     Lang::IT => 'della Perspicacia',   Lang::XX => 'Aepteetoode-a',     ],
		self::DEFENSE    => [Lang::DE => 'Verteidigung',   Lang::EN => 'Defense',      Lang::ES => 'de protección',     Lang::FR => 'de Défense',      Lang::IT => 'di Difesa',           Lang::XX => 'Deffense-a',        ],
		self::DEVOTION   => [Lang::DE => 'Hingabe',        Lang::EN => 'Devotion',     Lang::ES => 'de devoción',       Lang::FR => 'de dévotion',     Lang::IT => 'della Devozione',     Lang::XX => 'Defushun',          ],
		self::ENCHANTING => [Lang::DE => 'Verzauberung',   Lang::EN => 'Enchanting',   Lang::ES => 'de encantamientos', Lang::FR => 'd\'Enchantement', Lang::IT => 'dell\'Incantesimo',   Lang::XX => 'Inchunteeng',       ],
		self::ENDURANCE  => [Lang::DE => 'Ausdauer',       Lang::EN => 'Endurance',    Lang::ES => 'de resistencia',    Lang::FR => 'd\'endurance',    Lang::IT => 'della Resistenza',    Lang::XX => 'Indoorunce-a',      ],
		self::FORTITUDE  => [Lang::DE => 'Tapferkeit',     Lang::EN => 'Fortitude',    Lang::ES => 'con poder',         Lang::FR => 'de Courage',      Lang::IT => 'del Coraggio',        Lang::XX => 'Furteetoode-a',     ],
		self::MASTERY    => [Lang::DE => 'Beherrschung',   Lang::EN => 'Mastery',      Lang::ES => 'de mastería',       Lang::FR => 'de maîtrise',     Lang::IT => 'della Destrezza',     Lang::XX => 'Maestery',          ],
		self::MEMORY     => [Lang::DE => 'Erinnerung',     Lang::EN => 'Memory',       Lang::ES => 'de memoria',        Lang::FR => 'de mémoire',      Lang::IT => 'della Memoria',       Lang::XX => 'Memury',            ],
		self::QUICKENING => [Lang::DE => 'Beschleunigung', Lang::EN => 'Quickening',   Lang::ES => 'de aceleración',    Lang::FR => 'de rapidité',     Lang::IT => 'dell\'Accelerazione', Lang::XX => 'Qooeeckeneeng',     ],
		self::SHELTER    => [Lang::DE => 'Zuflucht',       Lang::EN => 'Shelter',      Lang::ES => 'de refugio',        Lang::FR => 'de Refuge',       Lang::IT => 'del Riparo',          Lang::XX => 'Shelter',           ],
		self::SWIFTNESS  => [Lang::DE => 'Eile',           Lang::EN => 'Swiftness',    Lang::ES => 'de rapidez',        Lang::FR => 'de Rapidité',     Lang::IT => 'della Rapidità',      Lang::XX => 'Sveefftness',       ],
		self::VALOR      => [Lang::DE => 'Wertschätzung',  Lang::EN => 'Valor',        Lang::ES => 'de valor',          Lang::FR => 'de valeur',       Lang::IT => 'del Valore',          Lang::XX => 'Faelur',            ],
		self::WARDING    => [Lang::DE => 'Abwehr',         Lang::EN => 'Warding',      Lang::ES => 'de guardia',        Lang::FR => 'du Protecteur',   Lang::IT => 'della Protezione',    Lang::XX => 'Vaerdeeng',         ],
		self::PROFESSION => [Lang::DE => '[Klasse]',       Lang::EN => '[Profession]', Lang::ES => '[Profesión]',       Lang::FR => '[Profession]',    Lang::IT => '[OFPROF IT]',         Lang::XX => '[OFPROF XX]',       ],
	];

	public const array ITEM_NAME = [
		Weapon::AXE     => [Lang::DE => 'Axtgriff',         Lang::EN => 'Axe Grip',       Lang::ES => 'Empuñadura de hacha',    Lang::FR => 'Poignée de hache',    Lang::IT => 'Impugnatura dell\'ascia',  Lang::XX => 'Aexe-a Greep',       ],
		Weapon::BOW     => [Lang::DE => 'Bogengriff',       Lang::EN => 'Bow Grip',       Lang::ES => 'Empuñadura de arco',     Lang::FR => 'Poignée d\'arc',      Lang::IT => 'Impugnatura dell\'arco',   Lang::XX => 'Boo Greep',          ],
		Weapon::DAGGERS => [Lang::DE => 'Dolchgriff',       Lang::EN => 'Dagger Handle',  Lang::ES => 'Empuñadura para daga',   Lang::FR => 'Poignée de dague',    Lang::IT => 'Impugnatura per Pugnale',  Lang::XX => 'Daegger Hundle-a',   ],
		Weapon::HAMMER  => [Lang::DE => 'Hammergriff',      Lang::EN => 'Hammer Grip',    Lang::ES => 'Empuñadura de martillo', Lang::FR => 'Poignée de marteau',  Lang::IT => 'Impugnatura del martello', Lang::XX => 'Haemmer Greep',      ],
		Weapon::SWORD   => [Lang::DE => 'Schwertknauf',     Lang::EN => 'Sword Pommel',   Lang::ES => 'Pomo de espada',         Lang::FR => 'Pommeau d\'épée',     Lang::IT => 'Pomolo della spada',       Lang::XX => 'Svurd Pummel',       ],
		Weapon::SCYTHE  => [Lang::DE => 'Sensengriff',      Lang::EN => 'Scythe Grip',    Lang::ES => 'Empuñadura de guadaña',  Lang::FR => 'Poignée de faux',     Lang::IT => 'Impugnatura della Falce',  Lang::XX => 'Scyzee Greep',       ],
		Weapon::SPEAR   => [Lang::DE => 'Speergriff',       Lang::EN => 'Spear Grip',     Lang::ES => 'Empuñadura de lanza',    Lang::FR => 'Poignée de javelot',  Lang::IT => 'Impugnatura della Lancia', Lang::XX => 'Speaer Greep',       ],
		Weapon::STAFF   => [Lang::DE => 'Stabhülle',        Lang::EN => 'Staff Wrapping', Lang::ES => 'Envoltura de báculo',    Lang::FR => 'Gaine de bâton',      Lang::IT => 'Fascia del bastone',       Lang::XX => 'Staeffff Vraeppeeng',],
		Weapon::WAND    => [Lang::DE => 'Zauberstab-Hülle', Lang::EN => 'Wand Wrapping',  Lang::ES => 'Envoltura de varita',    Lang::FR => 'Gaine de baguette',   Lang::IT => 'Fascia della Bacchetta',   Lang::XX => 'Vund Vraeppeeng',    ],
		Weapon::FOCUS   => [Lang::DE => 'Fokus-Kern',       Lang::EN => 'Focus Core',     Lang::ES => 'Mango de foco',          Lang::FR => 'Noyau de focus',      Lang::IT => 'Nucleo del Focus',         Lang::XX => 'Fucoos Cure-a',      ],
		Weapon::SHIELD  => [Lang::DE => 'Schildgriff',      Lang::EN => 'Shield Handle',  Lang::ES => 'Mango de escudo',        Lang::FR => 'Poignée de bouclier', Lang::IT => 'Impugnatura dello Scudo',  Lang::XX => 'Sheeeld Hundle-a',   ],
	];

	private const array SUFFIXED_ITEM_NAME = [
		Lang::DE => '%1$s d. %2$s',
		Lang::EN => '%1$s of %2$s',
		Lang::ES => '%1$s (%2$s)',
		Lang::FR => '%1$s (%2$s)',
		Lang::IT => '%1$s %2$s',
		Lang::XX => '%1$s ooff %2$s',
	];

	private const array SUFFIX_MARTIAL = [self::FORTITUDE, self::DEFENSE, self::SHELTER, self::WARDING, self::MASTERY, self::ENCHANTING, self::PROFESSION];
	private const array DEFV           = [self::DEVOTION, self::ENDURANCE, self::FORTITUDE, self::VALOR];

	private const array TYPES = [
		Weapon::AXE     => self::SUFFIX_MARTIAL,
		Weapon::BOW     => self::SUFFIX_MARTIAL,
		Weapon::DAGGERS => self::SUFFIX_MARTIAL,
		Weapon::HAMMER  => self::SUFFIX_MARTIAL,
		Weapon::SWORD   => self::SUFFIX_MARTIAL,
		Weapon::SCYTHE  => self::SUFFIX_MARTIAL,
		Weapon::SPEAR   => self::SUFFIX_MARTIAL,
		Weapon::STAFF   => [...self::DEFV, self::MASTERY, self::ENCHANTING, self::DEFENSE, self::SHELTER, self::WARDING, self::PROFESSION],
		Weapon::WAND    => [self::MEMORY, self::QUICKENING, self::PROFESSION],
		Weapon::FOCUS   => [...self::DEFV, self::SWIFTNESS, self::APTITUDE],
		Weapon::SHIELD  => self::DEFV,
	];

	// not only have the translations for the "the <profession>" suffix items problems in german, but others were completely mistranslated...
	private const array OF_THE_NAME = [
		Profession::WARRIOR      => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el guerrero',      Lang::FR => 'le %s', Lang::IT => 'il guerriero',   Lang::XX => 'zee %s',],
		Profession::RANGER       => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el guardabosques', Lang::FR => 'le %s', Lang::IT => 'il ranger',      Lang::XX => 'zee %s',],
		Profession::MONK         => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el monje',         Lang::FR => 'le %s', Lang::IT => 'il monaco',      Lang::XX => 'zee %s',],
		Profession::NECROMANCER  => [Lang::DE => 'des %sen', Lang::EN => 'the %s', Lang::ES => 'el nigromante',    Lang::FR => 'le %s', Lang::IT => 'il negromante',  Lang::XX => 'zee %s',],
		Profession::MESMER       => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el hipnotizador',  Lang::FR => 'l\'%s', Lang::IT => 'il mesmerista',  Lang::XX => 'zee %s',],
		Profession::ELEMENTALIST => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el elementalista', Lang::FR => 'l\'%s', Lang::IT => 'l\'elementista', Lang::XX => 'zee %s',],
		Profession::ASSASSIN     => [Lang::DE => 'des %sn',  Lang::EN => 'the %s', Lang::ES => 'el asesino',       Lang::FR => 'l\'%s', Lang::IT => 'l\'assassino',   Lang::XX => 'zee %s',],
		Profession::RITUALIST    => [Lang::DE => 'des %sen', Lang::EN => 'the %s', Lang::ES => 'el ritualista',    Lang::FR => 'le %s', Lang::IT => 'il ritualista',  Lang::XX => 'zee %s',],
		Profession::PARAGON      => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el paragón',       Lang::FR => 'le %s', Lang::IT => 'il campione',    Lang::XX => 'zee %s',],
		Profession::DERVISH      => [Lang::DE => 'des %ss',  Lang::EN => 'the %s', Lang::ES => 'el derviche',      Lang::FR => 'le %s', Lang::IT => 'il derviscio',   Lang::XX => 'zee %s',],
	];

	public function setFor(ItemType $for):static{
		$id = ItemType::WEAPON[$for->id];

		if(!array_key_exists($id, self::TYPES)){
			throw new InvalidArgumentException('invalid weapon ID');
		}

		// @todo: option for strict check?
		if(!$this->in(self::TYPES[$id])){
			throw new InvalidArgumentException('invalid suffix type for the given weapon');
		}

		$this->for = $for;

		return $this;
	}

	public function getName(string|Lang|null $lang = null):string{
		$lang = $this->getLang($lang);

		if($this->id === self::PROFESSION){
			$prof  = $this->attribute->getProfession();
			$name  = $prof->getName($lang);

			return sprintf((self::OF_THE_NAME[$prof->id][$lang->id] ?? '%s'), $name);
		}

		return (self::NAME[$this->id][$lang->id] ?? static::NAME[$this->id][Lang::EN]);
	}

	public function getItemName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		// @todo: language fix
		if(!isset(self::NAME[$this->id][$lang->id])){
			return sprintf('[SUFFIX_%s_%s]', dechex($this->id), $lang->id);
		}

		$suffixName = ($this->is(self::MASTERY) && $this->attribute !== null && !$this->attribute->is(Attribute::NONE))
			? $this->attribute->getName($lang)
			: $this->getName($lang);

		$format = self::SUFFIXED_ITEM_NAME[$lang->id];

		// fix for the GW bug in the german translation "d. der Klasse"
		if($this->is(self::PROFESSION) && $lang->is(Lang::DE)){
			$format = '%1$s %2$s';
		}

		$id = ItemType::WEAPON[$this->for->id];

		return sprintf($format, self::ITEM_NAME[$id][$lang->id], $suffixName);
	}

}
