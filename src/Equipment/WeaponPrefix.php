<?php
/**
 * Class Prefix
 *
 * @created      18.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Lang;
use InvalidArgumentException;
use function array_key_exists;
use function dechex;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/List_of_weapon_upgrades#Prefix_bonuses
 * @see https://www.guildwiki.de/wiki/Pr%C3%A4fix
 */
final class WeaponPrefix extends ModSubtypeAbstract{

	public const int ADEPT      = 0x1001;
	public const int BARBED     = 0x1002;
	public const int CRIPPLING  = 0x1003;
	public const int CRUEL      = 0x1004;
	public const int DEFENSIVE  = 0x1005;
	public const int EBON       = 0x1006;
	public const int FIERY      = 0x1007;
	public const int FURIOUS    = 0x1008;
	public const int HALE       = 0x1009;
	public const int HEAVY      = 0x100a;
	public const int ICY        = 0x100b;
	public const int INSIGHTFUL = 0x100c;
	public const int POISONOUS  = 0x100d;
	public const int SHOCKING   = 0x100e;
	public const int SILENCING  = 0x100f;
	public const int SUNDERING  = 0x1010;
	public const int SWIFT      = 0x1011;
	public const int VAMPIRIC   = 0x1012;
	public const int ZEALOUS    = 0x1013;

	public const array NAME = [
		self::ADEPT      => [Lang::DE => 'Experten',       Lang::EN => 'Adept',      Lang::ES => 'de adepto',           Lang::FR => 'd\'adepte',         Lang::IT => 'da Adepto',             Lang::XX => 'Aedept',       ],
		self::BARBED     => [Lang::DE => 'Stachel',        Lang::EN => 'Barbed',     Lang::ES => 'de espinas',          Lang::FR => 'à pointes',         Lang::IT => 'Punta',                 Lang::XX => 'Baerbed',      ],
		self::CRIPPLING  => [Lang::DE => 'Verkrüppelungs', Lang::EN => 'Crippling',  Lang::ES => 'letal',               Lang::FR => 'd\'infirmité',      Lang::IT => 'Azzoppante',            Lang::XX => 'Creeppleeng',  ],
		self::CRUEL      => [Lang::DE => 'Grausamkeits',   Lang::EN => 'Cruel',      Lang::ES => 'cruel',               Lang::FR => 'atroce',            Lang::IT => 'Crudele',               Lang::XX => 'Crooel',       ],
		self::DEFENSIVE  => [Lang::DE => 'Verteidigungs',  Lang::EN => 'Defensive',  Lang::ES => 'de defensa',          Lang::FR => 'défensif',          Lang::IT => 'da Difesa',             Lang::XX => 'Deffenseefe-a',],
		self::EBON       => [Lang::DE => 'Ebon',           Lang::EN => 'Ebon',       Lang::ES => 'con daño de granito', Lang::FR => 'terrestre',         Lang::IT => 'per danno d\'ebano',    Lang::XX => 'Ibun',         ],
		self::FIERY      => [Lang::DE => 'Hitze',          Lang::EN => 'Fiery',      Lang::ES => 'con daño ardiente',   Lang::FR => 'incendiaire',       Lang::IT => 'per danno da fuoco',    Lang::XX => 'Feeery',       ],
		self::FURIOUS    => [Lang::DE => 'Zorn',           Lang::EN => 'Furious',    Lang::ES => 'de furor',            Lang::FR => 'de fureur',         Lang::IT => 'della Furia',           Lang::XX => 'Fooreeuoos',   ],
		self::HALE       => [Lang::DE => 'Rüstigkeits',    Lang::EN => 'Hale',       Lang::ES => 'de robustez',         Lang::FR => 'de vigueur',        Lang::IT => 'del Vigore',            Lang::XX => 'Haele-a',      ],
		self::HEAVY      => [Lang::DE => 'Schwergewichts', Lang::EN => 'Heavy',      Lang::ES => 'fuerte',              Lang::FR => 'de poids',          Lang::IT => 'Pesante',               Lang::XX => 'Heaefy',       ],
		self::ICY        => [Lang::DE => 'Eis',            Lang::EN => 'Icy',        Lang::ES => 'con daño frío',       Lang::FR => 'polaire',           Lang::IT => 'per danno da ghiaccio', Lang::XX => 'Icy',          ],
		self::INSIGHTFUL => [Lang::DE => 'Einblick',       Lang::EN => 'Insightful', Lang::ES => 'de visión',           Lang::FR => 'de vision',         Lang::IT => 'dell\'Astuiza',         Lang::XX => 'Inseeghtffool',],
		self::POISONOUS  => [Lang::DE => 'Gift',           Lang::EN => 'Poisonous',  Lang::ES => 'con veneno',          Lang::FR => 'de poison',         Lang::IT => 'Veleno',                Lang::XX => 'Pueesunuoos',  ],
		self::SHOCKING   => [Lang::DE => 'Schock',         Lang::EN => 'Shocking',   Lang::ES => 'con daño descarga',   Lang::FR => 'de foudre',         Lang::IT => 'per danno da shock',    Lang::XX => 'Shuckeeng',    ],
		self::SILENCING  => [Lang::DE => 'Dämpfungs',      Lang::EN => 'Silencing',  Lang::ES => 'de silencio',         Lang::FR => 'de silence',        Lang::IT => 'del Silenzio',          Lang::XX => 'Seelenceeng',  ],
		self::SUNDERING  => [Lang::DE => 'Trenn',          Lang::EN => 'Sundering',  Lang::ES => 'de penetración',      Lang::FR => 'de fractionnement', Lang::IT => 'della Separazione',     Lang::XX => 'Soondereeng',  ],
		self::SWIFT      => [Lang::DE => 'Schnelligkeits', Lang::EN => 'Swift',      Lang::ES => 'de veloz',            Lang::FR => 'rapide',            Lang::IT => 'della Rapidità',        Lang::XX => 'Sveefft',      ],
		self::VAMPIRIC   => [Lang::DE => 'Vampir',         Lang::EN => 'Vampiric',   Lang::ES => 'de vampiro',          Lang::FR => 'vampirique',        Lang::IT => 'Vampiro',               Lang::XX => 'Faempureec',   ],
		self::ZEALOUS    => [Lang::DE => 'Eifer',          Lang::EN => 'Zealous',    Lang::ES => 'de afán',             Lang::FR => 'de zèle',           Lang::IT => 'Zelante',               Lang::XX => 'Zeaeluoos',    ],
	];

	public const array ITEM_NAME = [
		Weapon::AXE     => [Lang::DE => 'Axtstiel',    Lang::EN => 'Axe Haft',      Lang::ES => 'Mango de hacha',       Lang::FR => 'Hampe de hache',   Lang::IT => 'Manico dell\'ascia',  Lang::XX => 'Aexe-a Haefft', ],
		Weapon::BOW     => [Lang::DE => 'Bogensehne',  Lang::EN => 'Bowstring',     Lang::ES => 'Cuerda de arco',       Lang::FR => 'Corde',            Lang::IT => 'Corda d\'arco',       Lang::XX => 'Boostreeng',    ],
		Weapon::DAGGERS => [Lang::DE => 'Dolchangel',  Lang::EN => 'Dagger Tang',   Lang::ES => 'Afilador de dagas',    Lang::FR => 'Soie de dague',    Lang::IT => 'Codolo per Pugnale',  Lang::XX => 'Daegger Tung',  ],
		Weapon::HAMMER  => [Lang::DE => 'Hammerstiel', Lang::EN => 'Hammer Haft',   Lang::ES => 'Mango de martillo',    Lang::FR => 'Hampe de marteau', Lang::IT => 'Manico del martello', Lang::XX => 'Haemmer Haefft',],
		Weapon::SWORD   => [Lang::DE => 'Schwertheft', Lang::EN => 'Sword Hilt',    Lang::ES => 'Empuñadura de espada', Lang::FR => 'Poignée d\'épée',  Lang::IT => 'Elsa della spada',    Lang::XX => 'Svurd Heelt',   ],
		Weapon::SCYTHE  => [Lang::DE => 'Sensenstiel', Lang::EN => 'Scythe Snathe', Lang::ES => 'Filo de guadaña',      Lang::FR => 'Manche de faux',   Lang::IT => 'Manico della Falce',  Lang::XX => 'Scyzee Snaezee',],
		Weapon::SPEAR   => [Lang::DE => 'Speerspitze', Lang::EN => 'Spearhead',     Lang::ES => 'Punta de lanza',       Lang::FR => 'Tête de javelot',  Lang::IT => 'Punta per Lancia',    Lang::XX => 'Speaerheaed',   ],
		Weapon::STAFF   => [Lang::DE => 'Stabkopf',    Lang::EN => 'Staff Head',    Lang::ES => 'Puño de báculo',       Lang::FR => 'Pommeau de bâton', Lang::IT => 'Testa del bastone',   Lang::XX => 'Staeffff Heaed',],
	];

	private const array PREFIXED_ITEM_NAME = [
		Lang::DE => '%2$s-%1$s',
		Lang::EN => '%2$s %1$s',
		Lang::ES => '%1$s %2$s',
		Lang::FR => '%1$s %2$s',
		Lang::IT => '%1$s %2$s',
		Lang::XX => '%2$s %1$s',
	];

	private const array COMMON = [self::SUNDERING, self::ZEALOUS, self::VAMPIRIC, self::ICY, self::EBON, self::SHOCKING, self::FIERY];
	private const array BCP    = [self::BARBED, self::CRIPPLING, self::POISONOUS];

	private const array TYPES = [
		Weapon::AXE     => [...self::COMMON, ...self::BCP, self::CRUEL, self::FURIOUS, self::HEAVY],
		Weapon::BOW     => [...self::COMMON, ...self::BCP, self::SILENCING],
		Weapon::DAGGERS => [...self::COMMON, ...self::BCP, self::CRUEL, self::FURIOUS, self::SILENCING],
		Weapon::HAMMER  => [...self::COMMON, self::CRUEL, self::FURIOUS, self::HEAVY],
		Weapon::SWORD   => [...self::COMMON, ...self::BCP, self::CRUEL, self::FURIOUS],
		Weapon::SCYTHE  => [...self::COMMON, ...self::BCP, self::CRUEL, self::FURIOUS, self::HEAVY],
		Weapon::SPEAR   => [...self::COMMON, ...self::BCP, self::CRUEL, self::FURIOUS, self::HEAVY, self::SILENCING],
		Weapon::STAFF   => [self::INSIGHTFUL, self::HALE, self::ADEPT, self::SWIFT, self::DEFENSIVE],
	];

	public function setFor(ItemType $for):static{
		$id = ItemType::WEAPON[$for->id];

		if(!array_key_exists($id, self::TYPES)){
			throw new InvalidArgumentException('invalid weapon ID');
		}

		// @todo: option for strict check?
		if(!$this->in(self::TYPES[$id])){
			throw new InvalidArgumentException('invalid prefix type for the given weapon');
		}

		$this->for = $for;

		return $this;
	}

	public function getItemName(Lang|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		// @todo: language fix
		if(!isset(self::NAME[$this->id][$lang->id])){
			return sprintf('[PREFIX_%s_%s]', dechex($this->id), $lang->id);
		}

		$id = ItemType::WEAPON[$this->for->id];

		return sprintf((self::PREFIXED_ITEM_NAME[$lang->id] ?? '%1$s %2$s'), self::ITEM_NAME[$id][$lang->id], $this->getName());
	}

}
