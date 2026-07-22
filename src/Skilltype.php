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
		self::NONE                    => ['de' => 'Keine Fertigkeit',       'en' => 'No Skill',               ],
		self::SKILL                   => ['de' => 'Fertigkeit',             'en' => 'Skill',                  ],
		self::BOW_ATTACK              => ['de' => 'Bogenangriff',           'en' => 'Bow Attack',             ],
		self::MELEE_ATTACK            => ['de' => 'Nahkampfangriff',        'en' => 'Melee Attack',           ],
		self::AXE_ATTACK              => ['de' => 'Axtangriff',             'en' => 'Axe Attack',             ],
		self::LEAD_ATTACK             => ['de' => 'Leithandangriff',        'en' => 'Lead Attack',            ],
		self::OFF_HAND_ATTACK         => ['de' => 'Begleithandangriff',     'en' => 'Off-Hand Attack',        ],
		self::DUAL_ATTACK             => ['de' => 'Doppelangriff',          'en' => 'Dual Attack',            ],
		self::HAMMER_ATTACK           => ['de' => 'Hammerangriff',          'en' => 'Hammer Attack',          ],
		self::SCYTHE_ATTACK           => ['de' => 'Sensenangriff',          'en' => 'Scythe Attack',          ],
		self::SWORD_ATTACK            => ['de' => 'Schwertangriff',         'en' => 'Sword Attack',           ],
		self::PET_ATTACK              => ['de' => 'Tiergefährtenangriff',   'en' => 'Pet Attack',             ],
		self::SPEAR_ATTACK            => ['de' => 'Speerangriff',           'en' => 'Spear Attack',           ],
		self::CHANT                   => ['de' => 'Anfeuerungsruf',         'en' => 'Chant',                  ],
		self::ECHO                    => ['de' => 'Echo',                   'en' => 'Echo',                   ],
		self::FORM                    => ['de' => 'Form',                   'en' => 'Form',                   ],
		self::GLYPH                   => ['de' => 'Glyphe',                 'en' => 'Glyph',                  ],
		self::PREPARATION             => ['de' => 'Vorbereitung',           'en' => 'Preparation',            ],
		self::BINDING_RITUAL          => ['de' => 'Binderitual',            'en' => 'Binding Ritual',         ],
		self::NATURE_RITUAL           => ['de' => 'Naturritual',            'en' => 'Nature Ritual',          ],
		self::SHOUT                   => ['de' => 'Schrei',                 'en' => 'Shout',                  ],
		self::SIGNET                  => ['de' => 'Siegel',                 'en' => 'Signet',                 ],
		self::SPELL                   => ['de' => 'Zauber',                 'en' => 'Spell',                  ],
		self::ENCHANTMENT_SPELL       => ['de' => 'Verzauberung',           'en' => 'Enchantment Spell',      ],
		self::HEX_SPELL               => ['de' => 'Verhexung',              'en' => 'Hex Spell',              ],
		self::ITEM_SPELL              => ['de' => 'Gegenstandszauber',      'en' => 'Item Spell',             ],
		self::WARD_SPELL              => ['de' => 'Abwehrzauber',           'en' => 'Ward Spell',             ],
		self::WEAPON_SPELL            => ['de' => 'Waffenzauber',           'en' => 'Weapon Spell',           ],
		self::WELL_SPELL              => ['de' => 'Brunnenzauber',          'en' => 'Well Spell',             ],
		self::STANCE                  => ['de' => 'Haltung',                'en' => 'Stance',                 ],
		self::TRAP                    => ['de' => 'Falle',                  'en' => 'Trap',                   ],
		self::RANGED_ATTACK           => ['de' => 'Distanzangriff',         'en' => 'Ranged Attack',          ],
		self::EBON_VANGUARD_RITUAL    => ['de' => 'Ebon-Vorhut-Ritual',     'en' => 'Ebon Vanguard Ritual',   ],
		self::FLASH_ENCHANTMENT_SPELL => ['de' => 'Blitzverzauberung',      'en' => 'Flash Enchantment Spell',],
		self::DOUBLE_ENCHANTMENT      => ['de' => 'Doppelverzauberung',     'en' => 'Double Enchantment',     ],
		self::TOUCH_SKILL             => ['de' => 'Berührungsfertigkeit',   'en' => 'Touch Skill',            ],
		self::TOUCH_SPELL             => ['de' => 'Berührungszauber',       'en' => 'Touch Spell',            ],
		self::TOUCH_ENCHANTMENT_SPELL => ['de' => 'Berührungsverzauberung', 'en' => 'Touch Enchantment Spell',],
		self::TOUCH_HEX_SPELL         => ['de' => 'Berührungsverhexung',    'en' => 'Touch Hex Spell',        ],
		self::TOUCH_SIGNET            => ['de' => 'Berührungssiegel',       'en' => 'Touch Signet',           ],
		self::ATTACK_SKILL            => ['de' => 'Angriffsfertigkeit',     'en' => 'Attack Skill',           ],
		self::DAGGER_ATTACK           => ['de' => 'Dolchangriff',           'en' => 'Dagger Attack',          ],
		self::RITUAL                  => ['de' => 'Ritual',                 'en' => 'Ritual',                 ],
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
			self::FLASH_ENCHANTMENT_SPELL, self::TOUCH_SPELL, self::TOUCH_ENCHANTMENT_SPELL, self::TOUCH_HEX_SPELL,
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
