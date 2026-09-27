<?php
/**
 * Class Insignia
 *
 * @created      14.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Equipment;

use Buildwars\GWSkillData\Common\Lang as L;
use function sprintf;

class Insignia extends ModSubtypeAbstract{

	public const int COMMON     = 0x1101;
	public const int PROFESSION = 0x1102;

	public const array NAME = [
		self::COMMON       => [L::DE => '[INSIG DE]', L::EN => '[INSIG EN]', L::ES => '[INSIG ES]', L::FR => '[INSIG FR]', L::IT => '[INSIG IT]', L::XX => '[INSIG XX]', ],
		self::PROFESSION   => [L::DE => '[INSIG DE]', L::EN => '[INSIG EN]', L::ES => '[INSIG ES]', L::FR => '[INSIG FR]', L::IT => '[INSIG IT]', L::XX => '[INSIG XX]', ],
	];

	private const array ITEM_NAMES = [
		290 => [L::DE => 'Überlebende Befähigung', L::EN => 'Survivor Insignia', L::ES => 'Insignia de superviviente', L::FR => 'Insigne du survivant', L::IT => 'Insegne del Superstite', L::XX => 'Soorfeefur Inseegneea',],
		291 => [L::DE => 'Radianten-Befähigung', L::EN => 'Radiant Insignia', L::ES => 'Insignia radiante', L::FR => 'Insigne du rayonnement', L::IT => 'Insegne Radianti', L::XX => 'Raedeeunt Inseegneea',],
		292 => [L::DE => 'Entschlossenheits-Befähigung', L::EN => 'Stalwart Insignia', L::ES => 'Insignia firme', L::FR => 'Insigne robuste', L::IT => 'Insegne della Robustezza', L::XX => 'Staelvaert Inseegneea',],
		293 => [L::DE => 'Raufbold-Befähigung', L::EN => 'Brawler\'s Insignia', L::ES => 'Insignia del pendenciero', L::FR => 'Insigne de l\'agitateur', L::IT => 'Insegne da Lottatore', L::XX => 'Braevier\'s Inseegneea',],
		294 => [L::DE => 'Segens-Befähigung', L::EN => 'Blessed Insignia', L::ES => 'Insignia con bendición', L::FR => 'Insigne de la bénédiction', L::IT => 'Insegne della Benedizione', L::XX => 'Blessed Inseegneea',],
		295 => [L::DE => 'Herold-Befähigung', L::EN => 'Herald\'s Insignia', L::ES => 'Insignia de heraldo', L::FR => 'Insigne de héraut', L::IT => 'Insegne da Araldo', L::XX => 'Heraeld\'s Inseegneea',],
		296 => [L::DE => 'Wachposten-Befähigung', L::EN => 'Sentry\'s Insignia', L::ES => 'Insignia de centinela', L::FR => 'Insigne de factionnaire', L::IT => 'Insegne da Sentinella', L::XX => 'Sentry\'s Inseegneea',],
		297 => [L::DE => 'Hauptmann-Befähigung', L::EN => 'Vanguard\'s Insignia', L::ES => 'Insignia de avanzado', L::FR => 'Insigne de l\'avant-garde', L::IT => 'Insegne da Avanguardia', L::XX => 'Fungooaerd\'s Inseegneea',],
		298 => [L::DE => 'Eindringlings-Befähigung', L::EN => 'Infiltrator\'s Insignia', L::ES => 'Insignia de infiltrado', L::FR => 'Insigne de l\'infiltré', L::IT => 'Insegne da Spia', L::XX => 'Inffeeltraetur\'s Inseegneea',],
		299 => [L::DE => 'Saboteur-Befähigung', L::EN => 'Saboteur\'s Insignia', L::ES => 'Insignia de saboteador', L::FR => 'Insigne de saboteur', L::IT => 'Insegne da Sabotatore', L::XX => 'Saebuteoor\'s Inseegneea',],
		300 => [L::DE => 'Nachtprischer-Befähigung', L::EN => 'Nightstalker\'s Insignia', L::ES => 'Insignia de acechador nocturno', L::FR => 'Insigne de traqueur nocturne', L::IT => 'Insegne da Inseguitore Notturno', L::XX => 'Neeghtstaelker\'s Inseegneea',],
		301 => [L::DE => 'Virtuosen-Befähigung', L::EN => 'Virtuoso\'s Insignia', L::ES => 'Insignia de virtuoso', L::FR => 'Insigne de virtuose', L::IT => 'Insegne da Intenditore', L::XX => 'Furtoousu\'s Inseegneea',],
		302 => [L::DE => 'Blutfleck-Befähigung', L::EN => 'Bloodstained Insignia', L::ES => 'Insignia con sangre', L::FR => 'Insigne de Sang', L::IT => 'Insegne di Sangue', L::XX => 'Bluudstaeeened Inseegneea',],
		303 => [L::DE => 'Folterer-Befähigung', L::EN => 'Tormentor\'s Insignia', L::ES => 'Insignia de torturador', L::FR => 'Insigne de persécuteur', L::IT => 'Insegne da Tormentatore', L::XX => 'Turmentur\'s Inseegneea',],
		304 => [L::DE => 'Klöppelspitzen-Befähigung', L::EN => 'Bonelace Insignia', L::ES => 'Insignia de cordones de hueso', L::FR => 'Insigne de dentelle', L::IT => 'Insegne di Maglia d\'Ossa', L::XX => 'Bunelaece-a Inseegneea',],
		305 => [L::DE => 'Dienermeister-Befähigung', L::EN => 'Minion Master\'s Insignia', L::ES => 'Insignia de maestro de siervos', L::FR => 'Insigne du Maître des serviteurs', L::IT => 'Insegne da Domasgherri', L::XX => 'Meeneeun Maester\'s Inseegneea',],
		306 => [L::DE => 'Verderber-Befähigung', L::EN => 'Blighter\'s Insignia', L::ES => 'Insignia de malhechor', L::FR => 'Insigne de destructeur', L::IT => 'Insegne da Malfattore', L::XX => 'Bleeghter\'s Inseegneea',],
		307 => [L::DE => 'Hydromanten-Befähigung', L::EN => 'Hydromancer\'s Insignia', L::ES => 'Insignia de hidromante', L::FR => 'Insigne d\'hydromancie', L::IT => 'Insegne da Idromante', L::XX => 'Hydrumuncer Inseegneea',],
		308 => [L::DE => 'Geomanten-Befähigung', L::EN => 'Geomancer\'s Insignia', L::ES => 'Insignia de geomante', L::FR => 'Insigne de géomancie', L::IT => 'Insegne da Geomante', L::XX => 'Geumuncer Inseegneea',],
		309 => [L::DE => 'Pyromanten-Befähigung', L::EN => 'Pyromancer\'s Insignia', L::ES => 'Insignia de piromante', L::FR => 'Insigne de pyromancie', L::IT => 'Insegne da Piromante', L::XX => 'Pyrumuncer Inseegneea',],
		310 => [L::DE => 'Aeromanten-Befähigung', L::EN => 'Aeromancer\'s Insignia', L::ES => 'Insignia de aeromante', L::FR => 'Insigne d\'aéromancie', L::IT => 'Insegne da Aeromante', L::XX => 'Aeerumuncer Inseegneea',],
		311 => [L::DE => 'Wanderer-Befähigung', L::EN => 'Wanderer\'s Insignia', L::ES => 'Insignia de trotamundos', L::FR => 'Insigne de vagabond', L::IT => 'Insegne da Vagabondo', L::XX => 'Vunderer\'s Inseegneea',],
		312 => [L::DE => 'Jünger-Befähigung', L::EN => 'Disciple\'s Insignia', L::ES => 'Insignia de discípulo', L::FR => 'Insigne de disciple', L::IT => 'Insegne da Discepolo', L::XX => 'Deesceeple-a\'s Inseegneea',],
		313 => [L::DE => 'Ritter-Befähigung', L::EN => 'Knight\'s Insignia', L::ES => 'Insignia de caballero', L::FR => 'Insigne de chevalier', L::IT => 'Insegne da Cavaliere', L::XX => 'Kneeght\'s Inseegneea',],
		314 => [L::DE => 'Leutnant-Befähigung', L::EN => 'Lieutenant\'s Insignia', L::ES => 'Insignia de teniente', L::FR => 'Insigne du Lieutenant', L::IT => 'Insegne da Luogotenente', L::XX => 'Leeeootenunt\'s Inseegneea',],
		315 => [L::DE => 'Steinfaust-Befähigung', L::EN => 'Stonefist Insignia', L::ES => 'Insignia de piedra', L::FR => 'Insigne Poing-de-fer', L::IT => 'Insegne di Pietra', L::XX => 'Stuneffeest Inseegneea',],
		316 => [L::DE => 'Panzerschiff-Befähigung', L::EN => 'Dreadnought Insignia', L::ES => 'Insignia de Dreadnought', L::FR => 'Insigne de Dreadnaught', L::IT => 'Insegne da Dreadnought', L::XX => 'Dreaednuooght Inseegneea',],
		317 => [L::DE => 'Wächter-Befähigung', L::EN => 'Sentinel\'s Insignia', L::ES => 'Insignia de centinela', L::FR => 'Insigne de sentinelle', L::IT => 'Insegne da Sentinella', L::XX => 'Senteenel\'s Inseegneea',],
		318 => [L::DE => 'Permafrost-Verfähigung', L::EN => 'Frostbound Insignia', L::ES => 'Insignia de montaña', L::FR => 'Insigne de givre', L::IT => 'Insegne da Ghiaccio', L::XX => 'Frustbuoond Inseegneea',],
		319 => [L::DE => 'Scheiterhaufen-Befähigung', L::EN => 'Pyrebound Insignia', L::ES => 'Insignia de leñero', L::FR => 'Insigne du bûcher', L::IT => 'Insegne da Rogo', L::XX => 'Pyrebuoond Inseegneea',],
		320 => [L::DE => 'Unwetter-Befähigung', L::EN => 'Stormbound Insignia', L::ES => 'Insignia de hidromántico', L::FR => 'Insigne de tonnerre', L::IT => 'Insegne da Bufera', L::XX => 'Sturmbuoond Inseegneea',],
		321 => [L::DE => 'Späher-Befähigung', L::EN => 'Scout\'s Insignia', L::ES => 'Insignia de explorador', L::FR => 'Insigne d\'éclaireur', L::IT => 'Insegne da Perlustratore', L::XX => 'Scuoot\'s Inseegneea',],
		322 => [L::DE => 'Schamanen-Befähigung', L::EN => 'Shaman\'s Insignia', L::ES => 'Insignia de chamán', L::FR => 'Insigne de chaman', L::IT => 'Insegne da Sciamano', L::XX => 'Shaemun\'s Inseegneea',],
		323 => [L::DE => 'Geisterschmiede-Befähigung', L::EN => 'Ghost Forge Insignia', L::ES => 'Insignia de fragua fantasma', L::FR => 'Insigne de la forge du fantôme', L::IT => 'Insegne della Fucina Spettrale', L::XX => 'Ghust Furge-a Inseegneea',],
		324 => [L::DE => 'Mystiker-Befähigung', L::EN => 'Mystic\'s Insignia', L::ES => 'Insignia de místico', L::FR => 'Insigne mystique', L::IT => 'Insegne del Misticismo', L::XX => 'Mysteec\'s Inseegneea',],

		358 => [L::DE => 'Feuerwerker-Befähigung', L::EN => 'Artificer\'s Insignia', L::ES => 'Insignia de artífice', L::FR => 'Insigne de l\'artisan', L::IT => 'Insegne da Artefice', L::XX => 'Aerteeffeecer\'s Inseegneea',],
		359 => [L::DE => 'Wunder-Befähigung', L::EN => 'Prodigy\'s Insignia', L::ES => 'Insignia de prodigio', L::FR => 'Insigne prodige', L::IT => 'Insegne da Prodigio', L::XX => 'Prudeegy’s Inseegneea',],
		360 => [L::DE => 'Leichenbestatter-Befähigung', L::EN => 'Undertaker\'s Insignia', L::ES => 'Insignia de enterrador', L::FR => 'Insigne du fossoyeur', L::IT => 'Insegne da Becchino', L::XX => 'Undertaeker\'s Inseegneea',],
		361 => [L::DE => 'Spektral-Befähigung', L::EN => 'Prismatic Insignia', L::ES => 'Insignia de prismático', L::FR => 'Insigne prismatique', L::IT => 'Insegne a Prisma', L::XX => 'Preesmaeteec Inseegneea',],
		362 => [L::DE => 'Einsiedler-Befähigung', L::EN => 'Anchorite\'s Insignia', L::ES => 'Insignia de anacoreta', L::FR => 'Insigne d\'anachorète', L::IT => 'Insegne da Anacoreta', L::XX => 'Unchureete-a\'s Inseegneea',],
		363 => [L::DE => 'Erdbindung-Befähigung', L::EN => 'Earthbound Insignia', L::ES => 'Insignia de tierra', L::FR => 'Insigne terrestre', L::IT => 'Insegne da Terra', L::XX => 'Iaerthbuoond Inseegneea',],
		364 => [L::DE => 'Tierbändiger-Befähigung', L::EN => 'Beastmaster\'s Insignia', L::ES => 'Insignia de domador', L::FR => 'Insigne de belluaire', L::IT => 'Insegne da Domatore', L::XX => 'Beaestmaester\'s Inseegneea',],
		365 => [L::DE => 'Windläufer-Befähigung', L::EN => 'Windwalker Insignia', L::ES => 'Insignia de caminante del viento', L::FR => 'Insigne du Marche-vent', L::IT => 'Insegne da Camminatore nel Vento', L::XX => 'Veendvaelker Inseegneea',],
		366 => [L::DE => 'Verlassenen-Befähigung', L::EN => 'Forsaken Insignia', L::ES => 'Insignia de abandonado', L::FR => 'Insigne de l\'oubli', L::IT => 'Insegne da Abbandonato', L::XX => 'Fursaekee Inseegneea',],
		367 => [L::DE => 'Zenturio-Befähigung', L::EN => 'Centurion\'s Insignia', L::ES => 'Insignia de centurión', L::FR => 'Insigne du centurion', L::IT => 'Insegne da Centurione', L::XX => 'Centooreeun\'s Inseegneea',],
	];

	public function getItemName(L|string|null $lang = null):string{
		$lang = $this->getLang($lang);

		return (self::ITEM_NAMES[$this->modID][$lang->id] ?? sprintf('[INSIGNIA_%s_%s]', $this->modID, $lang->id));
	}
}
