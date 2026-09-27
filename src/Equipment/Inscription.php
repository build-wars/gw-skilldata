<?php
/**
 * Class Inscription
 *
 * @created      19.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Lang as L;
use function sprintf;

/**
 * @see https://wiki.guildwars.com/wiki/Inscription
 * @see https://wiki.guildwars.com/wiki/List_of_weapon_upgrades#Inscription_bonuses
 */
final class Inscription extends ModSubtypeAbstract{

	public const string CSS_CLASS = 'mod inscription';

	public const int WEAPONS = 0x3001;
	public const int MARTIAL = 0x3002;
	public const int CASTER  = 0x3003;
	public const int FOCUS   = 0x3004;
	public const int OFFHAND = 0x3005;

	private const array INSCRIPTION_FORMAT = [
		L::DE => 'Inschrift: "%s"',
		L::EN => 'Inscription: "%s"',
		L::ES => 'Inscripción: "%s"',
		L::FR => 'Inscription : "%s"',
		L::IT => 'Iscrizione "%s"',
		L::XX => 'Inscreepshun: "%s"',
	];

	private const array ATTACHES_TO = [
		L::DE => 'Gehört zu: Gravierbare %s',
		L::EN => 'Attaches to: Inscribable %s',
		L::ES => 'Para: %s inscribibles',
		L::FR => 'Pour : %s inscriptibles',
		L::IT => 'Per: %s inscrivibili',
		L::XX => 'Aettaeches tu: Insceebaeble-a %s',
	];

	public const array NAME = [
		self::WEAPONS => [
			L::DE => 'Waffen',
			L::EN => 'Weapons',
			L::ES => 'Armas',
			L::FR => 'Armes',
			L::IT => 'Armi',
			L::XX => 'Veaepuns',
		],
		self::MARTIAL => [
			L::DE => 'Kampfwaffen',
			L::EN => 'Martial weapons',
			L::ES => 'Armas marciales',
			L::FR => 'Arme de mêlée',
			L::IT => 'Armi marziali',
			L::XX => 'Maerteeael veaepuns',
		],
		self::CASTER  => [
			L::DE => 'Zauberwirker-Waffen',
			L::EN => 'Spellcasting weapons',
			L::ES => 'Armas de lanzamiento de conjuros',
			L::FR => 'Arme de lancement de sort',
			L::IT => 'Armi da magia',
			L::XX => 'Spellcaesteeng veaepuns',
		],
		self::FOCUS   => [
			L::DE => 'Fokus-Gegenstände',
			L::EN => 'Focus items',
			L::ES => 'Focos',
			L::FR => 'Focus',
			L::IT => 'Focus',
			L::XX => 'Fucoos items',
		],
		self::OFFHAND => [
			L::DE => 'Fokus-Gegenstände oder Schilde',
			L::EN => 'Focus items or shields',
			L::ES => 'Focos o escudos',
			L::FR => 'Focus ou bouclier',
			L::IT => 'Focus o Scudi',
			L::XX => 'Fucoos items oor sheeelds',
		],
	];

	public const array ITEM_NAME = [
		277 => [L::DE => 'Auf die Erinnerung!',                          L::EN => 'Let the Memory Live Again' , L::ES => 'Que vuelvan los recuerdos',       L::FR => 'Vers l\'infini et au-delà',                            L::IT => 'Facciamo Rivivere i Ricordi',                   L::XX => 'Let zee Memury Leefe-a Aegaeeen',  ],
		278 => [L::DE => 'Habt Vertrauen',                               L::EN => 'Have Faith'                , L::ES => 'Tened fe',                        L::FR => 'Ayez la foi',                                          L::IT => 'Abbi Fede',                                     L::XX => 'Haefe-a Faeeet',                   ],
		279 => [L::DE => 'Sie kehren niemals wieder!',                   L::EN => 'Don\'t call it a comeback!', L::ES => '¡No será la última palabra!',     L::FR => 'Aucun recours !',                                      L::IT => 'Non Consideratelo un Ritorno!',                 L::XX => 'Dun\'t caell it a cumebaeck!',     ],
		280 => [L::DE => 'Ich bin es leid',                              L::EN => 'I am Sorrow'               , L::ES => 'Un mar de lágrimas',              L::FR => 'Je suis la douleur',                                   L::IT => 'Io Sono la Sofferenza',                         L::XX => 'I aem Surroo.',                    ],
		281 => [L::DE => 'Zauderei ist keine Zier',                      L::EN => 'Don\'t Think Twice'        , L::ES => 'No te lo pienses',                L::FR => 'Pas le temps de réfléchir',                            L::IT => 'Non Pensarci Due Volte',                        L::XX => 'Dun\'t Theenk Tveece-a',           ],
		282 => [L::DE => 'Zu viel Information',                          L::EN => 'Too Much Information'      , L::ES => 'Demasiada información',           L::FR => 'Trop de détails',                                      L::IT => 'Troppe Informazioni',                           L::XX => 'Tuu Mooch Inffurmaeshun',          ],
		283 => [L::DE => 'Wink des Schicksals',                          L::EN => 'Guided by Fate'            , L::ES => 'Guiado por el destino',           L::FR => 'Soyez maître de votre destin',                         L::IT => 'Guidato dal Fato',                              L::XX => 'Gooeeded by Faete-a',              ],
		284 => [L::DE => 'Ein gesunder Geist...',                        L::EN => 'Soundness of Mind'         , L::ES => 'Sano juicio',                     L::FR => 'Bon sens',                                             L::IT => 'Mente Sana',                                    L::XX => 'Suoondness ooff Meend',            ],
		285 => [L::DE => 'Nur die Stärksten überleben!',                 L::EN => 'Only the Strong Survive'   , L::ES => 'Sólo sobreviven los más fuertes', L::FR => 'Seuls les plus forts survivent',                       L::IT => 'Superstite è soltanto il Forte',                L::XX => 'Oonly zee Strung Soorfeefe-a',     ],
		286 => [L::DE => 'Keine Angst vorm Sensenmann',                  L::EN => 'Don\'t Fear the Reaper'    , L::ES => 'No temas la guadaña',             L::FR => 'Ne craignez pas le Faucheur',                          L::IT => 'Non Temere la Falce',                           L::XX => 'Dun\'t Feaer zee Reaeper',         ],
		287 => [L::DE => 'Tanz mit dem Tod',                             L::EN => 'Dance with Death'          , L::ES => 'Baila con la muerte',             L::FR => 'Danse avec la mort',                                   L::IT => 'Balla coi Lutti',                               L::XX => 'Dunce-a veet Deaet',               ],
		288 => [L::DE => 'Körper über Geist',                            L::EN => 'Brawn over Brains'         , L::ES => 'Más vale maña que fuerza',        L::FR => 'Tout en muscles',                                      L::IT => 'Più Muscoli che Cervello',                      L::XX => 'Braevn oofer Braeeens',            ],
		289 => [L::DE => 'Fühlt den Schmerz!',                           L::EN => 'To the Pain!'              , L::ES => '¡A que duele!',                   L::FR => 'Vive la douleur !',                                    L::IT => 'Patisci!',                                      L::XX => 'Tu zee Paeeen!',                   ],
		325 => [L::DE => 'Der Glaube ist mein Schild',                   L::EN => 'Faith is My Shield'        , L::ES => 'La fe es mi escudo',              L::FR => 'La foi est mon bouclier',                              L::IT => 'La Fede è il Mio Scudo',                        L::XX => 'Faeeet is My Sheeeld',             ],
		326 => [L::DE => 'Lebt den Tag',                                 L::EN => 'Live for Today'            , L::ES => 'Vive el presente',                L::FR => 'Aujourd\'hui, la vie',                                 L::IT => 'Vivi alla Giornata',                            L::XX => 'Leefe-a fur Tudaey',               ],
		327 => [L::DE => 'Auf die Gelassenheit',                         L::EN => 'Serenity Now'              , L::ES => 'Calma ahora',                     L::FR => 'Un peu de sérénité',                                   L::IT => 'Serenità Immediata',                            L::XX => 'Sereneety Noo',                    ],
		328 => [L::DE => 'Vergesst mein nicht!',                         L::EN => 'Forget Me Not'             , L::ES => 'No me olvides',                   L::FR => 'Souvenir gravé à jamais',                              L::IT => 'Non Ti Scordar di Me',                          L::XX => 'Furget Me-a Nut',                  ],
		329 => [L::DE => 'Ich habe die Kraft!',                          L::EN => 'I have the power!'         , L::ES => '¡Tengo el poder!',                L::FR => 'Je détiens le pouvoir !',                              L::IT => 'A Me Il Potere!',                               L::XX => 'I haefe-a zee pooer!',             ],
		330 => [L::DE => 'Glück im Spiel...',                            L::EN => 'Luck of the Draw'          , L::ES => 'La suerte del apostante',         L::FR => 'Une question de chance',                               L::IT => 'La Fortuna è Cieca',                            L::XX => 'Loock ooff zee Draev',             ],
		331 => [L::DE => 'Vertrauen ist gut',                            L::EN => 'Sheltered by Faith'        , L::ES => 'Fe ciega',                        L::FR => 'Protégé par la Foi',                                   L::IT => 'Rifugio della Speranza',                        L::XX => 'Sheltered by Faeeet',              ],
		332 => [L::DE => 'Nichts zu befürchten',                         L::EN => 'Nothing to Fear'           , L::ES => 'Nada que temer',                  L::FR => 'Rien à craindre',                                      L::IT => 'Niente Paura',                                  L::XX => 'Nutheeng tu Feaer',                ],
		333 => [L::DE => 'Rennt um Euer Leben!',                         L::EN => 'Run For Your Life!'        , L::ES => '¡Ponte a salvo!',                 L::FR => 'Sauve-qui-peut !',                                     L::IT => 'Gambe in Spalla!',                              L::XX => 'Roon Fur Yuoor Leeffe-a!',         ],
		334 => [L::DE => 'Herr in meinem Haus',                          L::EN => 'Master of My Domain'       , L::ES => 'Amo de mi reino',                 L::FR => 'Maître du Domaine',                                    L::IT => 'Padrone in Casa Mia',                           L::XX => 'Maester ooff My Dumaeeen',         ],
		335 => [L::DE => 'Gut gewirkt ist halb gewonnen',                L::EN => 'Aptitude not Attitude'     , L::ES => 'Aptitud, no actitud',             L::FR => 'Les compétences prévalent',                            L::IT => 'Inclinazione non Finzione',                     L::XX => 'Aepteetoode-a nut Aetteetoode-a',  ],
		336 => [L::DE => 'Nutzt den Tag',                                L::EN => 'Seize the Day'             , L::ES => 'Salvad el día',                   L::FR => 'Vivez dans l\'instant',                                L::IT => 'Carpe Diem',                                    L::XX => 'Seeeze-a zee Daey',                ],
		337 => [L::DE => 'Gesund und Munter',                            L::EN => 'Hale and Hearty'           , L::ES => 'Viejo pero joven',                L::FR => 'En pleine santé',                                      L::IT => 'Vivo e Vegeto',                                 L::XX => 'Haele-a and Heaerty',              ],
		338 => [L::DE => 'Stärke und Ehre',                              L::EN => 'Strength and Honor'        , L::ES => 'Fuerza y honor',                  L::FR => 'Force et honneur',                                     L::IT => 'Forza e Onore',                                 L::XX => 'Strengt und Hunur',                ],
		339 => [L::DE => 'Die Rache ist mein',                           L::EN => 'Vengeance is Mine'         , L::ES => 'La venganza será mía',            L::FR => 'La vengeance sera mienne',                             L::IT => 'La Vendetta è Mia',                             L::XX => 'Fengeunce-a is Meene-a',           ],
		368 => [L::DE => 'Was ich nicht weiß...',                        L::EN => 'Ignorance is Bliss'        , L::ES => 'La ignorancia es felicidad',      L::FR => 'Il vaut mieux ne pas savoir',                          L::IT => 'Benedetta Ignoranza',                           L::XX => 'Ignurunce-a is Bleess',            ],
		369 => [L::DE => 'Das Leben tut weh',                            L::EN => 'Life is Pain'              , L::ES => 'La vida es dolor',                L::FR => 'La vie n\'est que douleur',                            L::IT => 'Vita è Dolore',                                 L::XX => 'Leeffe-a is Paeeen',               ],
		370 => [L::DE => 'Ein Mann für alle Jahreszeiten',               L::EN => 'Man for All Seasons'       , L::ES => 'Hombre para todo',                L::FR => 'L\'homme de la situation',                             L::IT => 'Un Uomo per Tutte le Stagioni',                 L::XX => 'Mun fur Aell Seaesuns',            ],
		371 => [L::DE => 'Hart wie Stahl',                               L::EN => 'Survival of the Fittest'   , L::ES => 'Supervivencia del más fuerte',    L::FR => 'La survie du plus fort',                               L::IT => 'Sopravvivenza Integrale',                       L::XX => 'Soorfeefael ooff zee Feettest',    ],
		372 => [L::DE => 'Macht gibt Recht',                             L::EN => 'Might makes Right'         , L::ES => 'Querer es poder',                 L::FR => 'La force prime le droit',                              L::IT => 'La Ragione della Forza',                        L::XX => 'Meeght maekes Reeght',             ],
		373 => [L::DE => 'Wissen ist die halbe Miete',                   L::EN => 'Knowing is Half the Battle', L::ES => 'Saber es ganar media batalla',    L::FR => 'La connaissance n\'est que la moitié d\'une bataille', L::IT => 'Con la Conoscenza si è a Metà della Battaglia', L::XX => 'Knooeeng is Haelff zee Baettle-a.',],
		374 => [L::DE => 'Am Boden aber nicht zerstört',                 L::EN => 'Down But Not Out'          , L::ES => 'Derribado pero no muerto',        L::FR => 'Même affaibli, je ne m\'avoue pas vaincu',             L::IT => 'Se Sei a Terra non Strisciare Mai',             L::XX => 'Doon Boot Nut Oooot',              ],
		375 => [L::DE => 'Gelobt sei der König',                         L::EN => 'Hail to the King'          , L::ES => 'Viva el rey',                     L::FR => 'Longue vie au roi',                                    L::IT => 'Viva il Re',                                    L::XX => 'Haeeel tu zee Keeng',              ],
		376 => [L::DE => 'Der Gerechte braucht sich nicht zu fürchten.', L::EN => 'Be Just and Fear Not'      , L::ES => 'Sé justo y no temas',             L::FR => 'Soyez juste et ne craignez rien',                      L::IT => 'Giustizia Sì Timore No',                        L::XX => 'Be-a Joost und Feaer Nut',         ],
		377 => [L::DE => 'Nicht das Gesicht!',                           L::EN => 'Not the face!'             , L::ES => '¡En la cara no!',                 L::FR => 'Pas le visage !',                                      L::IT => 'Non al Volto!',                                 L::XX => 'Nut zee faece-a!',                 ],
		378 => [L::DE => 'Grashalm im Wind',                             L::EN => 'Leaf on the Wind'          , L::ES => 'Hoja en el viento',               L::FR => 'La feuille portée par le vent',                        L::IT => 'Foglia nel Vento',                              L::XX => 'Leaeff oon zee Veend',             ],
		379 => [L::DE => 'Marmor, Stein und Erde bricht',                L::EN => 'Like a Rolling Stone'      , L::ES => 'Like a Rolling Stone',            L::FR => 'Solide comme le roc',                                  L::IT => 'Come Una Pietra Scalciata',                     L::XX => 'Leeke-a a Rulleeng Stune-a',       ],
		380 => [L::DE => 'Geblitzt wird nicht!',                         L::EN => 'Riders on the Storm'       , L::ES => 'Jinetes de la tormenta',          L::FR => 'Les chevaliers du ciel',                               L::IT => 'Viaggiatori nella Burrasca',                    L::XX => 'Reeders oon zee Sturm',            ],
		381 => [L::DE => 'Geborgenheit im Feuer',                        L::EN => 'Sleep Now in the Fire'     , L::ES => 'Descansa en la hoguera',          L::FR => 'Faisons la lumière sur les ténèbres',                  L::IT => 'Dormi Ardentemente',                            L::XX => 'Sleep Noo in zee Fure-a',          ],
		382 => [L::DE => 'Durch dick und dünn',                          L::EN => 'Through Thick and Thin'    , L::ES => 'En lo bueno y en lo malo',        L::FR => 'Contre vents et marées',                               L::IT => 'Nella Buona e nella Cattiva Sorte',             L::XX => 'Thruoogh Theeck and Theen',        ],
		383 => [L::DE => 'Hieb- und stahlfest',                          L::EN => 'The Riddle of Steel'       , L::ES => 'El enigma de acero',              L::FR => 'L\'énigme de l\'acier',                                L::IT => 'Enigma d\'Acciaio',                             L::XX => 'Zee Reeddle-a ooff Steel',         ],
		384 => [L::DE => 'Die Furcht schneidet tiefer',                  L::EN => 'Fear Cuts Deeper'          , L::ES => 'El miedo hace más daño',          L::FR => 'La peur fait plus de mal',                             L::IT => 'Il Timore Trafigge',                            L::XX => 'Feaer Coots Deeper',               ],
		385 => [L::DE => 'Klar wie Morgentau',                           L::EN => 'I Can See Clearly Now'     , L::ES => 'He abierto los ojos',             L::FR => 'Je vois clair à présent',                              L::IT => 'Chiara Come Un\'Alba',                          L::XX => 'I Cun See-a Cleaerly Noo',         ],
		386 => [L::DE => 'Schnell wie der Wind',                         L::EN => 'Swift as the Wind'         , L::ES => 'Veloz como el viento',            L::FR => 'Rapide comme le vent',                                 L::IT => 'Raffica di Bora',                               L::XX => 'Sveefft aes zee Veend',            ],
		387 => [L::DE => 'Hart im Nehmen',                               L::EN => 'Strength of Body'          , L::ES => 'Fuerza bruta',                    L::FR => 'La Force réside du corps',                             L::IT => 'Corpo Gagliardo',                               L::XX => 'Strengt ooff Budy',                ],
		388 => [L::DE => 'Gute Besserung!',                              L::EN => 'Cast Out the Unclean'      , L::ES => 'Desterremos a los impuros',       L::FR => 'Au rebut les impurs',                                  L::IT => 'Esorcizza l\'Eresia',                           L::XX => 'Caest Oooot zee Uncleun',          ],
		389 => [L::DE => 'Mein Herz ist rein',                           L::EN => 'Pure of Heart'             , L::ES => 'Puro de corazón',                 L::FR => 'Pureté du coeur',                                      L::IT => 'Purezza di Cuore',                              L::XX => 'Poore-a ooff Heaert',              ],

	];

	public function getName(L|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		// @todo: temp fix for missing translations
		$name   = (self::NAME[$this->id][$lang->id] ?? self::NAME[$this->id][L::EN]);
		$attach = (self::ATTACHES_TO[$lang->id] ?? self::ATTACHES_TO[L::EN]);

		return sprintf($attach, $name);
	}

	public function getItemName(L|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		// @todo: language fix
		if(!isset(self::NAME[$this->id][$lang->id])){
			return sprintf('[SUFFIX_%s_%s]', $this->modID, $lang->id);
		}

		return sprintf(self::INSCRIPTION_FORMAT[$lang->id], self::ITEM_NAME[$this->modID][$lang->id]);
	}

}
