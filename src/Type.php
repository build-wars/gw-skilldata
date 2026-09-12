<?php
/**
 * Class Type
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData;

use Buildwars\GWSkillData\Common\DataObjectAbstract;
use function sort;
use const SORT_NUMERIC;

/**
 * Encapsulates all skill type related static data
 */
final class Type extends DataObjectAbstract{

	public const string CSS_CLASS = 'skilltype';

	public const int NONE            = 0;
	public const int SKILL           = 1;
	public const int BOW_ATK         = 2;
	public const int MELEE_ATK       = 3;
	public const int AXE_ATK         = 4;
	public const int LEAD_ATK        = 5;
	public const int OFFHAND_ATK     = 6;
	public const int DUAL_ATK        = 7;
	public const int HAMMER_ATK      = 8;
	public const int SCYTHE_ATK      = 9;
	public const int SWORD_ATK       = 10;
	public const int PET_ATK         = 11;
	public const int SPEAR_ATK       = 12;
	public const int CHANT           = 13;
	public const int ECHO            = 14;
	public const int FORM            = 15;
	public const int GLYPH           = 16;
	public const int PREPARATION     = 17;
	public const int BINDING_RITUAL  = 18;
	public const int NATURE_RITUAL   = 19;
	public const int SHOUT           = 20;
	public const int SIGNET          = 21;
	public const int SPELL           = 22;
	public const int ENCH            = 23;
	public const int HEX             = 24;
	public const int ITEM_SPELL      = 25;
	public const int WARD_SPELL      = 26;
	public const int WEAPON_SPELL    = 27;
	public const int WELL            = 28;
	public const int STANCE          = 29;
	public const int TRAP            = 30;
	public const int RANGED_ATK      = 31;
	public const int VANGUARD_RITUAL = 32;
	public const int FLASH_ENCH      = 33;
	public const int ATK_SKILL       = 34;
	public const int DAGGER_ATK      = 35;
	public const int RITUAL          = 36;
	public const int DOUBLE_ENCH     = 37;
	public const int TOUCH_SKILL     = 38;
	public const int TOUCH_SPELL     = 39;
	public const int TOUCH_ENCH      = 40;
	public const int TOUCH_HEX       = 41;
	public const int TOUCH_SIGNET    = 42;

	public const array NAME = [
		self::NONE            => [Lang::DE => 'Keine Fertigkeit',       Lang::EN => 'No Skill',                Lang::ES => 'Sin habilidad',                         Lang::FR => 'Aucun Compétence',                 Lang::IT => 'Nessuna abilità',                    Lang::XX => 'Nu Skeell',                ],
		self::SKILL           => [Lang::DE => 'Fertigkeit',             Lang::EN => 'Skill',                   Lang::ES => 'Habilidad',                             Lang::FR => 'Compétence',                       Lang::IT => 'Abilità',                            Lang::XX => 'Skeell',                   ],
		self::BOW_ATK         => [Lang::DE => 'Bogenangriff',           Lang::EN => 'Bow Attack',              Lang::ES => 'Ataque con arco',                       Lang::FR => 'Attaque à l\'arc',                 Lang::IT => 'Attacco con Arco',                   Lang::XX => 'Boo Aettaeck',             ],
		self::MELEE_ATK       => [Lang::DE => 'Nahkampfangriff',        Lang::EN => 'Melee Attack',            Lang::ES => 'Ataque cuerpo a cuerpo',                Lang::FR => 'Attaque au corps à corps',         Lang::IT => 'Attacco Ravvicinato',                Lang::XX => 'Melee-a Aettaeck',         ],
		self::AXE_ATK         => [Lang::DE => 'Axtangriff',             Lang::EN => 'Axe Attack',              Lang::ES => 'Ataque con hacha',                      Lang::FR => 'Attaque à la hache',               Lang::IT => 'Attacco con Ascia',                  Lang::XX => 'Aexe-a Aettaeck',          ],
		self::LEAD_ATK        => [Lang::DE => 'Leithandangriff',        Lang::EN => 'Lead Attack',             Lang::ES => 'Ataque inicial',                        Lang::FR => 'Attaque main droite',              Lang::IT => 'Attacco con Arma Primaria',          Lang::XX => 'Leaed Aettaeck',           ],
		self::OFFHAND_ATK     => [Lang::DE => 'Begleithandangriff',     Lang::EN => 'Off-Hand Attack',         Lang::ES => 'Ataque complementario',                 Lang::FR => 'Attaque main gauche',              Lang::IT => 'Attacco con Arma Secondaria',        Lang::XX => 'Ooffff-Hund Aettaeck',     ],
		self::DUAL_ATK        => [Lang::DE => 'Doppelangriff',          Lang::EN => 'Dual Attack',             Lang::ES => 'Ataque doble',                          Lang::FR => 'Attaque ambidextre',               Lang::IT => 'Attacco Doppio',                     Lang::XX => 'Dooael Aettaeck',          ],
		self::HAMMER_ATK      => [Lang::DE => 'Hammerangriff',          Lang::EN => 'Hammer Attack',           Lang::ES => 'Ataque con martillo',                   Lang::FR => 'Attaque au marteau',               Lang::IT => 'Attacco con Martello',               Lang::XX => 'Haemmer Aettaeck',         ],
		self::SCYTHE_ATK      => [Lang::DE => 'Sensenangriff',          Lang::EN => 'Scythe Attack',           Lang::ES => 'Ataque con guadaña',                    Lang::FR => 'Attaque à la faux',                Lang::IT => 'Attacco con Falce',                  Lang::XX => 'Scyzee Aettaeck',          ],
		self::SWORD_ATK       => [Lang::DE => 'Schwertangriff',         Lang::EN => 'Sword Attack',            Lang::ES => 'Ataque con espada',                     Lang::FR => 'Attaque à l\'épée',                Lang::IT => 'Attacco con Spada',                  Lang::XX => 'Svurd Aettaeck',           ],
		self::PET_ATK         => [Lang::DE => 'Tiergefährtenangriff',   Lang::EN => 'Pet Attack',              Lang::ES => 'Ataque con mascota',                    Lang::FR => 'Attaque de familier',              Lang::IT => 'Attacco con Mascotte',               Lang::XX => 'Pet Aettaeck',             ],
		self::SPEAR_ATK       => [Lang::DE => 'Speerangriff',           Lang::EN => 'Spear Attack',            Lang::ES => 'Ataque con lanza',                      Lang::FR => 'Attaque au javelot',               Lang::IT => 'Attacco con Lancia',                 Lang::XX => 'Speaer Aettaeck',          ],
		self::CHANT           => [Lang::DE => 'Anfeuerungsruf',         Lang::EN => 'Chant',                   Lang::ES => 'Cántico',                               Lang::FR => 'Chant',                            Lang::IT => 'Canto',                              Lang::XX => 'Chunt',                    ],
		self::ECHO            => [Lang::DE => 'Echo',                   Lang::EN => 'Echo',                    Lang::ES => 'Eco',                                   Lang::FR => 'Echo',                             Lang::IT => 'Eco',                                Lang::XX => 'Ichu',                     ],
		self::FORM            => [Lang::DE => 'Form',                   Lang::EN => 'Form',                    Lang::ES => 'Transformación',                        Lang::FR => 'Transformation',                   Lang::IT => 'Forma',                              Lang::XX => 'Furm',                     ],
		self::GLYPH           => [Lang::DE => 'Glyphe',                 Lang::EN => 'Glyph',                   Lang::ES => 'Glifo',                                 Lang::FR => 'Glyphe',                           Lang::IT => 'Glifo',                              Lang::XX => 'Glyph',                    ],
		self::PREPARATION     => [Lang::DE => 'Vorbereitung',           Lang::EN => 'Preparation',             Lang::ES => 'Preparación',                           Lang::FR => 'Préparation',                      Lang::IT => 'Preparazione',                       Lang::XX => 'Prepaeraeshun',            ],
		self::BINDING_RITUAL  => [Lang::DE => 'Binderitual',            Lang::EN => 'Binding Ritual',          Lang::ES => 'Ritual con espíritus',                  Lang::FR => 'Rituel d\'asservissement',         Lang::IT => 'Rituale Incantentate',               Lang::XX => 'Beendeeng Reetooael',      ],
		self::NATURE_RITUAL   => [Lang::DE => 'Naturritual',            Lang::EN => 'Nature Ritual',           Lang::ES => 'Ritual de la naturaleza',               Lang::FR => 'Rituel de la nature',              Lang::IT => 'Rituale della Natura',               Lang::XX => 'Naetoore-a Reetooael',     ],
		self::SHOUT           => [Lang::DE => 'Schrei',                 Lang::EN => 'Shout',                   Lang::ES => 'Grito',                                 Lang::FR => 'Cri',                              Lang::IT => 'Urlo',                               Lang::XX => 'Shuoot',                   ],
		self::SIGNET          => [Lang::DE => 'Siegel',                 Lang::EN => 'Signet',                  Lang::ES => 'Sello',                                 Lang::FR => 'Sceau',                            Lang::IT => 'Sigillo',                            Lang::XX => 'Seegnet',                  ],
		self::SPELL           => [Lang::DE => 'Zauber',                 Lang::EN => 'Spell',                   Lang::ES => 'Conjuro',                               Lang::FR => 'Sort',                             Lang::IT => 'Magia',                              Lang::XX => 'Spell',                    ],
		self::ENCH            => [Lang::DE => 'Verzauberung',           Lang::EN => 'Enchantment Spell',       Lang::ES => 'Conjuro con encantamiento',             Lang::FR => 'Enchantement',                     Lang::IT => 'Incantesimo',                        Lang::XX => 'Inchuntment Spell',        ],
		self::HEX             => [Lang::DE => 'Verhexung',              Lang::EN => 'Hex Spell',               Lang::ES => 'Conjuro con maleficio',                 Lang::FR => 'Maléfice',                         Lang::IT => 'Fattura',                            Lang::XX => 'Hex Spell',                ],
		self::ITEM_SPELL      => [Lang::DE => 'Gegenstandszauber',      Lang::EN => 'Item Spell',              Lang::ES => 'Conjuro con objeto',                    Lang::FR => 'Sort d\'altération d\'objet',      Lang::IT => 'Magia Altera-oggetto',               Lang::XX => 'Item Spell',               ],
		self::WARD_SPELL      => [Lang::DE => 'Abwehrzauber',           Lang::EN => 'Ward Spell',              Lang::ES => 'Conjuro de protección',                 Lang::FR => 'Sort de protection',               Lang::IT => 'Guardia',                            Lang::XX => 'Vaerd Spell',              ],
		self::WEAPON_SPELL    => [Lang::DE => 'Waffenzauber',           Lang::EN => 'Weapon Spell',            Lang::ES => 'Conjuro con arma',                      Lang::FR => 'Sort d\'altération d\'arme',       Lang::IT => 'Magia Altera-arma',                  Lang::XX => 'Veaepun Spell',            ],
		self::WELL            => [Lang::DE => 'Brunnenzauber',          Lang::EN => 'Well Spell',              Lang::ES => 'Conjuro de pozo',                       Lang::FR => 'Sort de puits',                    Lang::IT => 'Pozzo',                              Lang::XX => 'Vell Spell',               ],
		self::STANCE          => [Lang::DE => 'Haltung',                Lang::EN => 'Stance',                  Lang::ES => 'Actitud',                               Lang::FR => 'Pose de combat',                   Lang::IT => 'Posizione',                          Lang::XX => 'Stunce-a',                 ],
		self::TRAP            => [Lang::DE => 'Falle',                  Lang::EN => 'Trap',                    Lang::ES => 'Trampa',                                Lang::FR => 'Piège',                            Lang::IT => 'Trappola',                           Lang::XX => 'Traep',                    ],
		self::RANGED_ATK      => [Lang::DE => 'Distanzangriff',         Lang::EN => 'Ranged Attack',           Lang::ES => 'Ataque a distancia',                    Lang::FR => 'Attaque à distance',               Lang::IT => 'Attacco dalla Distanza',             Lang::XX => 'Runged Aettaeck',          ],
		self::VANGUARD_RITUAL => [Lang::DE => 'Ebon-Vorhut-Ritual',     Lang::EN => 'Ebon Vanguard Ritual',    Lang::ES => 'Ritual de la Vanguardia de Ébano',      Lang::FR => 'Rituel de l\'Avant-garde d\'Ebon', Lang::IT => 'Rituale dell\'Avanguardia d\'Ebano', Lang::XX => 'Ibun Fungooaerd Reetooael',],
		self::FLASH_ENCH      => [Lang::DE => 'Blitzverzauberung',      Lang::EN => 'Flash Enchantment Spell', Lang::ES => 'Conjuro con encantamiento de destello', Lang::FR => 'Enchantement instantané',          Lang::IT => 'Incantesimo Lampo',                  Lang::XX => 'Flaesh Inchuntment Spell', ],
		self::DOUBLE_ENCH     => [Lang::DE => 'Doppelverzauberung',     Lang::EN => 'Double Enchantment',      Lang::ES => 'Doble encantamiento',                   Lang::FR => 'Double enchantement',              Lang::IT => 'Doppio incantesimo',                 Lang::XX => 'Duooble-a Inchuntment',    ],
		self::TOUCH_SKILL     => [Lang::DE => 'Berührungsfertigkeit',   Lang::EN => 'Touch Skill',             Lang::ES => 'Habilidad de toque',                    Lang::FR => 'Compétence de contact',            Lang::IT => 'Abilità a Tocco',                    Lang::XX => 'Tuooch Skeell',            ],
		self::TOUCH_SPELL     => [Lang::DE => 'Berührungszauber',       Lang::EN => 'Touch Spell',             Lang::ES => 'Conjuro de toque',                      Lang::FR => 'Sort de contact',                  Lang::IT => 'Magia a Tocco',                      Lang::XX => 'Tuooch Spell',             ],
		self::TOUCH_ENCH      => [Lang::DE => 'Berührungsverzauberung', Lang::EN => 'Touch Enchantment Spell', Lang::ES => 'Conjuro con encantamiento de toque',    Lang::FR => 'Enchantement de contact',          Lang::IT => 'Incantesimo a Tocco',                Lang::XX => 'Tuooch Inchuntment Spell', ],
		self::TOUCH_HEX       => [Lang::DE => 'Berührungsverhexung',    Lang::EN => 'Touch Hex Spell',         Lang::ES => 'Conjuro con maleficio de toque',        Lang::FR => 'Maléfice de contact',              Lang::IT => 'Fattura a Tocco',                    Lang::XX => 'Tuooch Hex Spell',         ],
		self::TOUCH_SIGNET    => [Lang::DE => 'Berührungssiegel',       Lang::EN => 'Touch Signet',            Lang::ES => 'Sello de toque',                        Lang::FR => 'Sceau de contact',                 Lang::IT => 'Sigillo a Tocco',                    Lang::XX => 'Tuooch Seegnet',           ],
		self::ATK_SKILL       => [Lang::DE => 'Angriffsfertigkeit',     Lang::EN => 'Attack Skill',            Lang::ES => 'Habilidad de ataque',                   Lang::FR => 'Compétence de Attaque',            Lang::IT => 'Abilità d\'attacco',                 Lang::XX => 'Aettaeck Skeell',          ],
		self::DAGGER_ATK      => [Lang::DE => 'Dolchangriff',           Lang::EN => 'Dagger Attack',           Lang::ES => 'Ataque con daga',                       Lang::FR => 'Attaque à la dague',               Lang::IT => 'Attacco con il pugnale',             Lang::XX => 'Daegger Aettaeck',         ],
		self::RITUAL          => [Lang::DE => 'Ritual',                 Lang::EN => 'Ritual',                  Lang::ES => 'Ritual',                                Lang::FR => 'Rituel',                           Lang::IT => 'Rituale',                            Lang::XX => 'Reetooael',                ],
	];

	private const array SUBTYPES = [
		self::ATK_SKILL => [
			self::MELEE_ATK, self::RANGED_ATK, self::BOW_ATK, self::AXE_ATK, self::LEAD_ATK, self::OFFHAND_ATK,
			self::DUAL_ATK, self::HAMMER_ATK, self::SCYTHE_ATK, self::SWORD_ATK, self::PET_ATK, self::SPEAR_ATK,
		],
		self::DAGGER_ATK   => [self::LEAD_ATK, self::OFFHAND_ATK, self::DUAL_ATK],
		self::ENCH         => [self::FLASH_ENCH, self::DOUBLE_ENCH, self::TOUCH_ENCH],
		self::HEX          => [self::TOUCH_HEX],
		self::MELEE_ATK    => [
			self::AXE_ATK, self::LEAD_ATK, self::OFFHAND_ATK, self::DUAL_ATK, self::HAMMER_ATK,
			self::SCYTHE_ATK, self::SWORD_ATK, self::PET_ATK,
		],
		self::RANGED_ATK   => [self::BOW_ATK, self::SPEAR_ATK],
		self::RITUAL       => [self::BINDING_RITUAL, self::NATURE_RITUAL, self::VANGUARD_RITUAL],
		self::SPELL        => [
			self::ENCH, self::HEX, self::ITEM_SPELL, self::WARD_SPELL, self::WEAPON_SPELL, self::WELL,
			self::FLASH_ENCH, self::DOUBLE_ENCH, self::TOUCH_SPELL, self::TOUCH_ENCH,
			self::TOUCH_HEX,
		],
		self::SIGNET        => [self::TOUCH_SIGNET],
		self::TOUCH_SKILL   => [self::TOUCH_SPELL, self::TOUCH_ENCH, self::TOUCH_HEX, self::TOUCH_SIGNET],
	];

	/**
	 * Returns the IDs for the given skill type including all of its subtypes
	 *
	 * @return int[]
	 */
	public function withSubtypes():array{
		$types   = (self::SUBTYPES[$this->id] ?? []);
		$types[] = $this->id;

		sort($types, SORT_NUMERIC);

		return $types;
	}

}
