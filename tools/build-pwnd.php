<?php
/**
 * build-pwnd.php
 *
 * @created      28.06.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use Buildwars\GWSkillData\SkillLangEnglish;
use Buildwars\GWSkillData\SkillLangGerman;
use Buildwars\GWSkillData\Skilltype;
use chillerlan\Utilities\File;
use function array_flip;
use function array_key_exists;
use function date;
use function dirname;
use function implode;
use function intval;
use function sprintf;
use function str_replace;
use function strip_tags;
use function strtr;
use function trim;

/**
 * @phan-file-suppress PhanUndeclaredGlobalVariable ??
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';

/** @var array<string, \Buildwars\GWSkillData\SkillDataInterface> $skilldb */
$skilldb = [
	'de' => new SkillLangGerman,
	'en' => new SkillLangEnglish,
];

$attr_map = array_flip(PWND_ATTR_TRANSLATE);

const type_map = [
	Skilltype::TOUCH_SKILL             => 1,
	Skilltype::TOUCH_SPELL             => 22,
	Skilltype::TOUCH_ENCHANTMENT_SPELL => 23,
	Skilltype::TOUCH_HEX_SPELL         => 24,
	Skilltype::TOUCH_SIGNET            => 21,
];

const ini_de = <<<ini_de
[Menu]
Name=Deutsch ({MODE}, buildwars)
Hint=Daten von GuildWiki & GWW, erstellt von smiley, github.com/build-wars
[Update]
Provider=buildwars
Date={DATE}
[Network]
DownloadSafeCSV={FILE_URL}.csv
DownloadSafeINI={FILE_URL}.ini
[Wiki]
WikiShow=http://www.guildwiki.de/wiki/%wikistr%
WikiEdit=http://www.guildwiki.de/gwiki/index.php?title=%wikistr%&action=edit
[rebuilt]
Expect=1330
Lang=DE
ini_de;

const ini_en = <<<ini_en
[Menu]
Name=Englisch ({MODE}, buildwars)
Hint=Data from GuildWiki & GWW, by smiley, github.com/build-wars
[Update]
Provider=buildwars
Date={DATE}
[Network]
DownloadSafeCSV={FILE_URL}.csv
DownloadSafeINI={FILE_URL}.ini
[Wiki]
WikiShow=http://wiki.guildwars.com/wiki/%wikistr%
WikiEdit=http://wiki.guildwars.com/index.php?title=%wikistr%&action=edit
PrimaryEN=True
[rebuilt]
Expect=1330
Lang=EN
ini_en;

const ini_body = [
	'de' => ini_de,
	'en' => ini_en,
];

const repo_url = 'https://build-wars.github.io/gw-skilldata/pawned/';

foreach(PWND_CSV as $mode => $langfiles){
	$isPvP = $mode === 'pvp';

	foreach($langfiles as $lang => $file){
		$pwnd = load_pawned_file($file);

		$lang2 = match($lang){
			'de' => 'en',
			'en' => 'de',
		};

		foreach($pwnd as &$row){
			$skilldata = $skilldb[$lang]->get(intval($row[0]), $isPvP);

			if(array_key_exists($skilldata['attribute'], $attr_map)){
				$skilldata['attribute'] = $attr_map[$skilldata['attribute']];
			}

			if(array_key_exists($skilldata['type'], type_map)){
				$skilldata['type'] = type_map[$skilldata['type']];
			}

			if($skilldata['activation'] < 1 && $skilldata['activation'] > 0){
				$skilldata['activation'] = trim((string)$skilldata['activation'], '0');
			}

			$row[1]  = $skilldata['name'];
			$row[2]  = $skilldb[$lang2]->get(intval($row[0]), $isPvP)['name'];
			$row[3]  = str_replace('...', '..', strip_tags($skilldata['description']));
			$row[4]  = $skilldata['campaign'];
			$row[5]  = $skilldata['attribute'];
			$row[6]  = $skilldata['type']; // todo: map
			$row[7]  = $skilldata['profession'];
			$row[8]  = $skilldata['upkeep'];
			$row[9]  = $skilldata['energy'];
			$row[10] = $skilldata['activation'];
			$row[11] = $skilldata['recharge'];
			$row[12] = $skilldata['adrenaline_precise'];
			$row[13] = $skilldata['sacrifice'];
			$row[14] = (int)$skilldata['is_elite'];
			$row[15] = (int)$skilldata['is_rp'];
			$row[16] = $skilldata['overcast'];
			// idk what the last 3 columns are...

			$row = implode(';', $row);
		}

		save_pawned_file(dirname($file), implode("\n", $pwnd), $lang, $mode);
	}
}


function save_pawned_file(string $dir, string $data, string $lang, string $mode):void{
	global $logger; // phpcs:ignore

	$file = sprintf('%s_buildwars_%s', $lang, $mode);

	File::save(sprintf('%s/%s.csv', $dir, $file), convert_encoding($data));

	$ini = strtr(ini_body[$lang], [
		'{MODE}'     => $mode,
		'{DATE}'     => date('YmdHi'),
		'{FILE_URL}' => sprintf('%s%s', repo_url, $file),
	]);

	File::save(sprintf('%s/%s.ini', $dir, $file), convert_encoding($ini));

	$logger->info(sprintf('saved %s', $file));
}

function convert_encoding(string $data):string{
	return mb_convert_encoding($data, 'Windows-1252', 'UTF-8');
}
