<?php
/**
 * Class Skilltype
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use function sort;
use const SORT_NUMERIC;

/**
 * Encapsulates all skill type related static data
 */
final class Skilltype extends DataObjectAbstract{

	public const NONE                    = 0;
	public const SKILL                   = 1;
	public const BOW_ATTACK              = 2;
	public const MELEE_ATTACK            = 3;
	public const AXE_ATTACK              = 4;
	public const LEAD_ATTACK             = 5;
	public const OFF_HAND_ATTACK         = 6;
	public const DUAL_ATTACK             = 7;
	public const HAMMER_ATTACK           = 8;
	public const SCYTHE_ATTACK           = 9;
	public const SWORD_ATTACK            = 10;
	public const PET_ATTACK              = 11;
	public const SPEAR_ATTACK            = 12;
	public const CHANT                   = 13;
	public const ECHO                    = 14;
	public const FORM                    = 15;
	public const GLYPH                   = 16;
	public const PREPARATION             = 17;
	public const BINDING_RITUAL          = 18;
	public const NATURE_RITUAL           = 19;
	public const SHOUT                   = 20;
	public const SIGNET                  = 21;
	public const SPELL                   = 22;
	public const ENCHANTMENT_SPELL       = 23;
	public const HEX_SPELL               = 24;
	public const ITEM_SPELL              = 25;
	public const WARD_SPELL              = 26;
	public const WEAPON_SPELL            = 27;
	public const WELL_SPELL              = 28;
	public const STANCE                  = 29;
	public const TRAP                    = 30;
	public const RANGED_ATTACK           = 31;
	public const EBON_VANGUARD_RITUAL    = 32;
	public const FLASH_ENCHANTMENT_SPELL = 33;
	public const ATTACK_SKILL            = 34;
	public const DAGGER_ATTACK           = 35;
	public const RITUAL                  = 36;
	public const DOUBLE_ENCHANTMENT      = 37;
	public const TOUCH_SKILL             = 38;
	public const TOUCH_SPELL             = 39;
	public const TOUCH_ENCHANTMENT_SPELL = 40;
	public const TOUCH_HEX_SPELL         = 41;
	public const TOUCH_SIGNET            = 42;

	public const NAME = [
		self::NONE                    => [SkillLang::DE => 'Keine Fertigkeit',       SkillLang::EN => 'No Skill',               ],
		self::SKILL                   => [SkillLang::DE => 'Fertigkeit',             SkillLang::EN => 'Skill',                  ],
		self::BOW_ATTACK              => [SkillLang::DE => 'Bogenangriff',           SkillLang::EN => 'Bow Attack',             ],
		self::MELEE_ATTACK            => [SkillLang::DE => 'Nahkampfangriff',        SkillLang::EN => 'Melee Attack',           ],
		self::AXE_ATTACK              => [SkillLang::DE => 'Axtangriff',             SkillLang::EN => 'Axe Attack',             ],
		self::LEAD_ATTACK             => [SkillLang::DE => 'Leithandangriff',        SkillLang::EN => 'Lead Attack',            ],
		self::OFF_HAND_ATTACK         => [SkillLang::DE => 'Begleithandangriff',     SkillLang::EN => 'Off-Hand Attack',        ],
		self::DUAL_ATTACK             => [SkillLang::DE => 'Doppelangriff',          SkillLang::EN => 'Dual Attack',            ],
		self::HAMMER_ATTACK           => [SkillLang::DE => 'Hammerangriff',          SkillLang::EN => 'Hammer Attack',          ],
		self::SCYTHE_ATTACK           => [SkillLang::DE => 'Sensenangriff',          SkillLang::EN => 'Scythe Attack',          ],
		self::SWORD_ATTACK            => [SkillLang::DE => 'Schwertangriff',         SkillLang::EN => 'Sword Attack',           ],
		self::PET_ATTACK              => [SkillLang::DE => 'Tiergefährtenangriff',   SkillLang::EN => 'Pet Attack',             ],
		self::SPEAR_ATTACK            => [SkillLang::DE => 'Speerangriff',           SkillLang::EN => 'Spear Attack',           ],
		self::CHANT                   => [SkillLang::DE => 'Anfeuerungsruf',         SkillLang::EN => 'Chant',                  ],
		self::ECHO                    => [SkillLang::DE => 'Echo',                   SkillLang::EN => 'Echo',                   ],
		self::FORM                    => [SkillLang::DE => 'Form',                   SkillLang::EN => 'Form',                   ],
		self::GLYPH                   => [SkillLang::DE => 'Glyphe',                 SkillLang::EN => 'Glyph',                  ],
		self::PREPARATION             => [SkillLang::DE => 'Vorbereitung',           SkillLang::EN => 'Preparation',            ],
		self::BINDING_RITUAL          => [SkillLang::DE => 'Binderitual',            SkillLang::EN => 'Binding Ritual',         ],
		self::NATURE_RITUAL           => [SkillLang::DE => 'Naturritual',            SkillLang::EN => 'Nature Ritual',          ],
		self::SHOUT                   => [SkillLang::DE => 'Schrei',                 SkillLang::EN => 'Shout',                  ],
		self::SIGNET                  => [SkillLang::DE => 'Siegel',                 SkillLang::EN => 'Signet',                 ],
		self::SPELL                   => [SkillLang::DE => 'Zauber',                 SkillLang::EN => 'Spell',                  ],
		self::ENCHANTMENT_SPELL       => [SkillLang::DE => 'Verzauberung',           SkillLang::EN => 'Enchantment Spell',      ],
		self::HEX_SPELL               => [SkillLang::DE => 'Verhexung',              SkillLang::EN => 'Hex Spell',              ],
		self::ITEM_SPELL              => [SkillLang::DE => 'Gegenstandszauber',      SkillLang::EN => 'Item Spell',             ],
		self::WARD_SPELL              => [SkillLang::DE => 'Abwehrzauber',           SkillLang::EN => 'Ward Spell',             ],
		self::WEAPON_SPELL            => [SkillLang::DE => 'Waffenzauber',           SkillLang::EN => 'Weapon Spell',           ],
		self::WELL_SPELL              => [SkillLang::DE => 'Brunnenzauber',          SkillLang::EN => 'Well Spell',             ],
		self::STANCE                  => [SkillLang::DE => 'Haltung',                SkillLang::EN => 'Stance',                 ],
		self::TRAP                    => [SkillLang::DE => 'Falle',                  SkillLang::EN => 'Trap',                   ],
		self::RANGED_ATTACK           => [SkillLang::DE => 'Distanzangriff',         SkillLang::EN => 'Ranged Attack',          ],
		self::EBON_VANGUARD_RITUAL    => [SkillLang::DE => 'Ebon-Vorhut-Ritual',     SkillLang::EN => 'Ebon Vanguard Ritual',   ],
		self::FLASH_ENCHANTMENT_SPELL => [SkillLang::DE => 'Blitzverzauberung',      SkillLang::EN => 'Flash Enchantment Spell',],
		self::DOUBLE_ENCHANTMENT      => [SkillLang::DE => 'Doppelverzauberung',     SkillLang::EN => 'Double Enchantment',     ],
		self::TOUCH_SKILL             => [SkillLang::DE => 'Berührungsfertigkeit',   SkillLang::EN => 'Touch Skill',            ],
		self::TOUCH_SPELL             => [SkillLang::DE => 'Berührungszauber',       SkillLang::EN => 'Touch Spell',            ],
		self::TOUCH_ENCHANTMENT_SPELL => [SkillLang::DE => 'Berührungsverzauberung', SkillLang::EN => 'Touch Enchantment Spell',],
		self::TOUCH_HEX_SPELL         => [SkillLang::DE => 'Berührungsverhexung',    SkillLang::EN => 'Touch Hex Spell',        ],
		self::TOUCH_SIGNET            => [SkillLang::DE => 'Berührungssiegel',       SkillLang::EN => 'Touch Signet',           ],
		self::ATTACK_SKILL            => [SkillLang::DE => 'Angriffsfertigkeit',     SkillLang::EN => 'Attack Skill',           ],
		self::DAGGER_ATTACK           => [SkillLang::DE => 'Dolchangriff',           SkillLang::EN => 'Dagger Attack',          ],
		self::RITUAL                  => [SkillLang::DE => 'Ritual',                 SkillLang::EN => 'Ritual',                 ],
	];

	public const SUBTYPES = [
		self::ATTACK_SKILL      => [
			self::MELEE_ATTACK, self::RANGED_ATTACK, self::BOW_ATTACK, self::AXE_ATTACK, self::LEAD_ATTACK, self::OFF_HAND_ATTACK,
			self::DUAL_ATTACK, self::HAMMER_ATTACK, self::SCYTHE_ATTACK, self::SWORD_ATTACK, self::PET_ATTACK, self::SPEAR_ATTACK,
		],
		self::DAGGER_ATTACK     => [self::LEAD_ATTACK, self::OFF_HAND_ATTACK, self::DUAL_ATTACK],
		self::ENCHANTMENT_SPELL => [self::FLASH_ENCHANTMENT_SPELL, self::DOUBLE_ENCHANTMENT, self::TOUCH_ENCHANTMENT_SPELL],
		self::HEX_SPELL         => [self::TOUCH_HEX_SPELL],
		self::MELEE_ATTACK      => [
			self::AXE_ATTACK, self::LEAD_ATTACK, self::OFF_HAND_ATTACK, self::DUAL_ATTACK, self::HAMMER_ATTACK,
			self::SCYTHE_ATTACK, self::SWORD_ATTACK, self::PET_ATTACK,
		],
		self::RANGED_ATTACK     => [self::BOW_ATTACK, self::SPEAR_ATTACK],
		self::RITUAL            => [self::BINDING_RITUAL, self::NATURE_RITUAL, self::EBON_VANGUARD_RITUAL],
		self::SPELL             => [
			self::ENCHANTMENT_SPELL, self::HEX_SPELL, self::ITEM_SPELL, self::WARD_SPELL, self::WEAPON_SPELL, self::WELL_SPELL,
			self::FLASH_ENCHANTMENT_SPELL, self::DOUBLE_ENCHANTMENT, self::TOUCH_SPELL, self::TOUCH_ENCHANTMENT_SPELL,
			self::TOUCH_HEX_SPELL,
		],
		self::SIGNET            => [self::TOUCH_SIGNET],
		self::TOUCH_SKILL       => [self::TOUCH_SPELL, self::TOUCH_ENCHANTMENT_SPELL, self::TOUCH_HEX_SPELL, self::TOUCH_SIGNET],
	];

	/**
	 * @return int[]
	 */
	public function withSubtypes():array{
		$types   = (self::SUBTYPES[$this->id] ?? []);
		$types[] = $this->id;

		sort($types, SORT_NUMERIC);

		return $types;
	}

}
