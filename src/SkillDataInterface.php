<?php
/**
 * Interface SkillDataInterface
 *
 * @created      01.06.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

/**
 *
 */
interface SkillDataInterface{

	public const CAMPAIGNS = [
		0 => ['name' => ['de' => 'Basis',            'en' => 'Core',           ], 'continent' => ['de' => 'Die Nebel', 'en' => 'The Mists',]],
		1 => ['name' => ['de' => 'Prophecies',       'en' => 'Prophecies',     ], 'continent' => ['de' => 'Tyria',     'en' => 'Tyria',    ]],
		2 => ['name' => ['de' => 'Factions',         'en' => 'Factions',       ], 'continent' => ['de' => 'Cantha',    'en' => 'Cantha',   ]],
		3 => ['name' => ['de' => 'Nightfall',        'en' => 'Nightfall',      ], 'continent' => ['de' => 'Elona',     'en' => 'Elona',    ]],
		4 => ['name' => ['de' => 'Eye of the North', 'en' => 'Eye of the North'], 'continent' => ['de' => 'Tyria',     'en' => 'Tyria',    ]],
	];

	public const PROFESSIONS = [
		 0 => ['pri' => 101, 'name' => ['de' => 'keine',           'en' => 'none',        ], 'abbr' => ['de' => 'X', 'en' => 'X', ]],
		 1 => ['pri' =>  17, 'name' => ['de' => 'Krieger',         'en' => 'Warrior',     ], 'abbr' => ['de' => 'K', 'en' => 'W', ]],
		 2 => ['pri' =>  23, 'name' => ['de' => 'Waldläufer',      'en' => 'Ranger',      ], 'abbr' => ['de' => 'W', 'en' => 'R', ]],
		 3 => ['pri' =>  16, 'name' => ['de' => 'Mönch',           'en' => 'Monk',        ], 'abbr' => ['de' => 'Mö','en' => 'Mo',]],
		 4 => ['pri' =>   6, 'name' => ['de' => 'Nekromant',       'en' => 'Necromancer', ], 'abbr' => ['de' => 'N', 'en' => 'N', ]],
		 5 => ['pri' =>   0, 'name' => ['de' => 'Mesmer',          'en' => 'Mesmer',      ], 'abbr' => ['de' => 'Me','en' => 'Me',]],
		 6 => ['pri' =>  12, 'name' => ['de' => 'Elementarmagier', 'en' => 'Elementalist',], 'abbr' => ['de' => 'E', 'en' => 'E', ]],
		 7 => ['pri' =>  35, 'name' => ['de' => 'Assassine',       'en' => 'Assassin',    ], 'abbr' => ['de' => 'A', 'en' => 'A', ]],
		 8 => ['pri' =>  36, 'name' => ['de' => 'Ritualist',       'en' => 'Ritualist',   ], 'abbr' => ['de' => 'R', 'en' => 'Rt',]],
		 9 => ['pri' =>  40, 'name' => ['de' => 'Paragon',         'en' => 'Paragon',     ], 'abbr' => ['de' => 'P', 'en' => 'P', ]],
		10 => ['pri' =>  44, 'name' => ['de' => 'Derwisch',        'en' => 'Dervish',     ], 'abbr' => ['de' => 'D', 'en' => 'D', ]],
	];

	public const ATTRIBUTES = [
		  0 => ['prof' =>  5, 'pri' => true,  'max' => 21, 'name' => ['de' => 'Schnellwirkung',           'en' => 'Fast Casting',                      ]],
		  1 => ['prof' =>  5, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Illusionsmagie',           'en' => 'Illusion Magic',                    ]],
		  2 => ['prof' =>  5, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Beherrschungsmagie',       'en' => 'Domination Magic',                  ]],
		  3 => ['prof' =>  5, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Inspirationsmagie',        'en' => 'Inspiration Magic',                 ]],
		  4 => ['prof' =>  4, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Blutmagie',                'en' => 'Blood Magic',                       ]],
		  5 => ['prof' =>  4, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Todesmagie',               'en' => 'Death Magic',                       ]],
		  6 => ['prof' =>  4, 'pri' => true,  'max' => 21, 'name' => ['de' => 'Seelensammlung',           'en' => 'Soul Reaping',                      ]],
		  7 => ['prof' =>  4, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Flüche',                   'en' => 'Curses',                            ]],
		  8 => ['prof' =>  6, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Luftmagie',                'en' => 'Air Magic',                         ]],
		  9 => ['prof' =>  6, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Erdmagie',                 'en' => 'Earth Magic',                       ]],
		 10 => ['prof' =>  6, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Feuermagie',               'en' => 'Fire Magic',                        ]],
		 11 => ['prof' =>  6, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Wassermagie',              'en' => 'Water Magic',                       ]],
		 12 => ['prof' =>  6, 'pri' => true,  'max' => 21, 'name' => ['de' => 'Energiespeicherung',       'en' => 'Energy Storage',                    ]],
		 13 => ['prof' =>  3, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Heilgebete',               'en' => 'Healing Prayers',                   ]],
		 14 => ['prof' =>  3, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Peinigungsgebete',         'en' => 'Smiting Prayers',                   ]],
		 15 => ['prof' =>  3, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Schutzgebete',             'en' => 'Protection Prayers',                ]],
		 16 => ['prof' =>  3, 'pri' => true,  'max' => 21, 'name' => ['de' => 'Gunst der Götter',         'en' => 'Divine Favor',                      ]],
		 17 => ['prof' =>  1, 'pri' => true,  'max' => 21, 'name' => ['de' => 'Stärke',                   'en' => 'Strength',                          ]],
		 18 => ['prof' =>  1, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Axtbeherrschung',          'en' => 'Axe Mastery',                       ]],
		 19 => ['prof' =>  1, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Hammerbeherrschung',       'en' => 'Hammer Mastery',                    ]],
		 20 => ['prof' =>  1, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Schwertkunst',             'en' => 'Swordsmanship',                     ]],
		 21 => ['prof' =>  1, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Taktik',                   'en' => 'Tactics',                           ]],
		 22 => ['prof' =>  2, 'pri' => false, 'max' => 20, 'name' => ['de' => 'Tierbeherrschung',         'en' => 'Beast Mastery',                     ]],
		 23 => ['prof' =>  2, 'pri' => true,  'max' => 20, 'name' => ['de' => 'Fachkenntnis',             'en' => 'Expertise',                         ]],
		 24 => ['prof' =>  2, 'pri' => false, 'max' => 20, 'name' => ['de' => 'Überleben in der Wildnis', 'en' => 'Wilderness Survival',               ]],
		 25 => ['prof' =>  2, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Treffsicherheit',          'en' => 'Marksmanship',                      ]],
		 29 => ['prof' =>  7, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Dolchbeherrschung',        'en' => 'Dagger Mastery',                    ]],
		 30 => ['prof' =>  7, 'pri' => false, 'max' => 20, 'name' => ['de' => 'Tödliche Künste',          'en' => 'Deadly Arts',                       ]],
		 31 => ['prof' =>  7, 'pri' => false, 'max' => 20, 'name' => ['de' => 'Schattenkünste',           'en' => 'Shadow Arts',                       ]],
		 32 => ['prof' =>  8, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Zwiesprache',              'en' => 'Communing',                         ]],
		 33 => ['prof' =>  8, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Wiederherstellungsmagie',  'en' => 'Restoration Magic',                 ]],
		 34 => ['prof' =>  8, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Kanalisierungsmagie',      'en' => 'Channeling Magic',                  ]],
		 35 => ['prof' =>  7, 'pri' => true,  'max' => 20, 'name' => ['de' => 'Kritische Stöße',          'en' => 'Critical Strikes',                  ]],
		 36 => ['prof' =>  8, 'pri' => true,  'max' => 21, 'name' => ['de' => 'Macht des Herbeirufens',   'en' => 'Spawning Power',                    ]],
		 37 => ['prof' =>  9, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Speerbeherrschung',        'en' => 'Spear Mastery',                     ]],
		 38 => ['prof' =>  9, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Befehlsgewalt',            'en' => 'Command',                           ]],
		 39 => ['prof' =>  9, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Motivation',               'en' => 'Motivation',                        ]],
		 40 => ['prof' =>  9, 'pri' => true,  'max' => 20, 'name' => ['de' => 'Führung',                  'en' => 'Leadership',                        ]],
		 41 => ['prof' => 10, 'pri' => false, 'max' => 21, 'name' => ['de' => 'Sensenbeherrschung',       'en' => 'Scythe Mastery',                    ]],
		 42 => ['prof' => 10, 'pri' => false, 'max' => 20, 'name' => ['de' => 'Windgebete',               'en' => 'Wind Prayers',                      ]],
		 43 => ['prof' => 10, 'pri' => false, 'max' => 20, 'name' => ['de' => 'Erdgebete',                'en' => 'Earth Prayers',                     ]],
		 44 => ['prof' => 10, 'pri' => true,  'max' => 20, 'name' => ['de' => 'Mystik',                   'en' => 'Mysticism',                         ]],
		101 => ['prof' =>  0, 'pri' => false, 'max' =>  0, 'name' => ['de' => 'Kein Attribut',            'en' => 'No Attribute',                      ]],
		102 => ['prof' =>  0, 'pri' => false, 'max' => 10, 'name' => ['de' => 'Sonnenspeertitel',         'en' => 'Sunspear Title Track',              ]],
		103 => ['prof' =>  0, 'pri' => false, 'max' =>  8, 'name' => ['de' => 'Lichtbringertitel',        'en' => 'Lightbringer Title Track',          ]],
		104 => ['prof' =>  0, 'pri' => false, 'max' => 12, 'name' => ['de' => 'Freund der Luxon',         'en' => 'Friend of the Luxons Title Track',  ]],
		105 => ['prof' =>  0, 'pri' => false, 'max' => 12, 'name' => ['de' => 'Freund der Kurzick',       'en' => 'Friend of the Kurzicks Title Track',]],
		106 => ['prof' =>  0, 'pri' => false, 'max' => 10, 'name' => ['de' => 'Asuratitel',               'en' => 'Asura Title Track',                 ]],
		107 => ['prof' =>  0, 'pri' => false, 'max' => 10, 'name' => ['de' => 'Deldrimortitel',           'en' => 'Deldrimor Title Track',             ]],
		108 => ['prof' =>  0, 'pri' => false, 'max' => 10, 'name' => ['de' => 'Ebon-Vorhut-Titel',        'en' => 'Ebon Vanguard Title Track',         ]],
		109 => ['prof' =>  0, 'pri' => false, 'max' => 10, 'name' => ['de' => 'Norntitel',                'en' => 'Norn Title Track',                  ]],
	];

	public const SKILLTYPES = [
		 0 => ['name' => ['de' => 'Keine Fertigkeit',     'en' => 'Not a Skill',         ]],
		 1 => ['name' => ['de' => 'Fertigkeit',           'en' => 'Skill',               ]],
		 2 => ['name' => ['de' => 'Bogenangriff',         'en' => 'Bow Attack',          ]],
		 3 => ['name' => ['de' => 'Nahkampfangriff',      'en' => 'Melee Attack',        ]],
		 4 => ['name' => ['de' => 'Axtangriff',           'en' => 'Axe Attack',          ]],
		 5 => ['name' => ['de' => 'Leithandangriff',      'en' => 'Lead Attack',         ]],
		 6 => ['name' => ['de' => 'Begleithandangriff',   'en' => 'Off-Hand Attack',     ]],
		 7 => ['name' => ['de' => 'Doppelangriff',        'en' => 'Dual Attack',         ]],
		 8 => ['name' => ['de' => 'Hammerangriff',        'en' => 'Hammer Attack',       ]],
		 9 => ['name' => ['de' => 'Sensenangriff',        'en' => 'Scythe Attack',       ]],
		10 => ['name' => ['de' => 'Schwertangriff',       'en' => 'Sword Attack',        ]],
		11 => ['name' => ['de' => 'Tiergefährtenangriff', 'en' => 'Pet Attack',          ]],
		12 => ['name' => ['de' => 'Speerangriff',         'en' => 'Spear Attack',        ]],
		13 => ['name' => ['de' => 'Anfeuerungsruf',       'en' => 'Chant',               ]],
		14 => ['name' => ['de' => 'Echo',                 'en' => 'Echo',                ]],
		15 => ['name' => ['de' => 'Form',                 'en' => 'Form',                ]],
		16 => ['name' => ['de' => 'Glyphe',               'en' => 'Glyph',               ]],
		17 => ['name' => ['de' => 'Vorbereitung',         'en' => 'Preparation',         ]],
		18 => ['name' => ['de' => 'Binderitual',          'en' => 'Binding ritual',      ]],
		19 => ['name' => ['de' => 'Naturritual',          'en' => 'Nature ritual',       ]],
		20 => ['name' => ['de' => 'Schrei',               'en' => 'Shout',               ]],
		21 => ['name' => ['de' => 'Siegel',               'en' => 'Signet',              ]],
		22 => ['name' => ['de' => 'Zauber',               'en' => 'Spell',               ]],
		23 => ['name' => ['de' => 'Verzauberung',         'en' => 'Enchantment spell',   ]],
		24 => ['name' => ['de' => 'Verhexung',            'en' => 'Hex Spell',           ]],
		25 => ['name' => ['de' => 'Gegenstandszauber',    'en' => 'Item Spell',          ]],
		26 => ['name' => ['de' => 'Abwehrzauber',         'en' => 'Ward Spell',          ]],
		27 => ['name' => ['de' => 'Waffenzauber',         'en' => 'Weapon Spell',        ]],
		28 => ['name' => ['de' => 'Brunnenzauber',        'en' => 'Well Spell',          ]],
		29 => ['name' => ['de' => 'Haltung',              'en' => 'Stance',              ]],
		30 => ['name' => ['de' => 'Falle',                'en' => 'Trap',                ]],
		31 => ['name' => ['de' => 'Distanzangriff',       'en' => 'Ranged attack',       ]],
		32 => ['name' => ['de' => 'Ebon-Vorhut-Ritual',   'en' => 'Ebon Vanguard Ritual',]],
		33 => ['name' => ['de' => 'Blitzverzauberung',    'en' => 'Flash Enchantment',   ]],
		34 => ['name' => ['de' => 'Doppelverzauberung',   'en' => 'Double Enchantment',  ]],
	];

	/**
	 * The array keys for the name translations of several fields
	 */
	final public const KEYS_NAMES = ['campaign_name', 'profession_name', 'profession_abbr', 'attribute_name', 'type_name'];

	/**
	 * The array keys for the descriptions array
	 *
	 * @var string[]
	 */
	public const KEYS_DESC = [];

	/**
	 * The array keys for the data array
	 *
	 * @var string[]
	 */
	public const KEYS_DATA = [];

	/**
	 * The descriptions array
	 */
	public const ID2DESC   = [];

	/**
	 * The data array
	 */
	public const ID2DATA   = [];

	/**
	 * The language abbreviation, key for the several `name` arrays
	 */
	public const LANG = '';

	/**
	 * Returns the data for the given skill ID, including descriptions for the current language
	 */
	public function get(int $id, bool $pvp = false):array;

	/**
	 * Returns an array with the skill data for each of the given skill IDs
	 *
	 * @param int[] $IDs
	 */
	public function getAll(array $IDs, bool $pvp = false):array;

	/**
	 * Returns all skills for the given campaign ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByCampaign(int $campaign, bool $pvp = false):array;

	/**
	 * Returns all skills for the given profession ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByProfession(int $profession, bool $pvp = false):array;

	/**
	 * Returns all skills for the given attribute ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByAttribute(int $attribute, bool $pvp = false):array;

	/**
	 * Returns all skills for the given skill type ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getByType(int $type, bool $pvp = false):array;

	/**
	 * Returns all elite skills
	 */
	public function getElite(bool $pvp = false):array;

	/**
	 * Returns all roleplay skills
	 */
	public function getRoleplay():array;

}
