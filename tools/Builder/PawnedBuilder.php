<?php
/**
 * Class PawnedParser
 *
 * @created      01.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillData\SkillLang;
use Buildwars\GWSkillData\Skilltype;
use chillerlan\Utilities\File;
use RuntimeException;
use function array_combine;
use function array_flip;
use function array_key_exists;
use function array_map;
use function boolval;
use function count;
use function date;
use function explode;
use function floatval;
use function hash_file;
use function hex2bin;
use function implode;
use function in_array;
use function intval;
use function mb_convert_encoding;
use function parse_ini_file;
use function rename;
use function sprintf;
use function strip_tags;
use function strtr;
use function tempnam;
use function trim;
use function unlink;
use const Buildwars\GWSkillDataTools\PAWNED_DATA_DIR;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;
use const PVP_SPLIT_FLIP;

final class PawnedBuilder extends Builder{

	// "en2" is similar to the "en" database, but column 3 also has the english names.
	// this is a workaround for the english version of pawned to avoid false search results.
	protected const LANG_EN = 'en2';

	// the paw-ned skill databases for each language. more to come... probably never.
	protected const PWND_CSV = [
		Skill::MODE_PVE => [
			SkillLang::DE => PAWNED_DATA_DIR.'/de_classic_pve.csv',
			SkillLang::EN => PAWNED_DATA_DIR.'/en_classic_pve.csv',
			self::LANG_EN => PAWNED_DATA_DIR.'/en2_classic_pve.csv',
		],
		Skill::MODE_PVP => [
			SkillLang::DE => PAWNED_DATA_DIR.'/de_classic_pvp.csv',
			SkillLang::EN => PAWNED_DATA_DIR.'/en_classic_pvp.csv',
			self::LANG_EN => PAWNED_DATA_DIR.'/en2_classic_pvp.csv',
		],
	];

	/**
	 * paw-ned² csv skilldb schema
	 *
	 * 0  = id (int)
	 * 1  = name (string)
	 * 2  = name2 (string)
	 * 3  = desc (string)
	 * 4  = campaign (int)
	 * 5  = attribute (int)
	 * 6  = type (int)
	 * 7  = profession (int)
	 * 8  = upkeep (int)
	 * 9  = energy (int)
	 * 10 = activation (float)
	 * 11 = recharge (int)
	 * 12 = adrenaline (float)
	 * 13 = sacrifice (int)
	 * 14 = elite (bool)
	 * 15 = pve (bool)
	 * 16 = overcast (int)
	 * 17 = 0
	 * 18 = 0
	 * 19 = empty string
	 */
	protected const KEYS = [
		0  => Skill::DATA_ID,
		1  => Skill::DESC_NAME,
		2  => 'name2',
		3  => Skill::DESC_DESCRIPTION,
		4  => Skill::DATA_CAMPAIGN,
		5  => Skill::DATA_ATTRIBUTE,
		6  => Skill::DATA_TYPE,
		7  => Skill::DATA_PROFESSION,
		8  => Skill::DATA_UPKEEP,
		9  => Skill::DATA_ENERGY,
		10 => Skill::DATA_ACTIVATION,
		11 => Skill::DATA_RECHARGE,
		12 => Skill::DATA_ADRENALINE_PRECISE,
		13 => Skill::DATA_SACRIFICE,
		14 => Skill::DATA_IS_ELITE,
		15 => Skill::DATA_IS_RP,
		16 => Skill::DATA_EXHAUSTION,
		17 => 'empty1',
		18 => 'empty2',
		19 => 'empty3',
	];

	protected const KEYS_INT = [
		Skill::DATA_ID,
		Skill::DATA_CAMPAIGN,
		Skill::DATA_ATTRIBUTE,
		Skill::DATA_TYPE,
		Skill::DATA_PROFESSION,
		Skill::DATA_UPKEEP,
		Skill::DATA_ENERGY,
		Skill::DATA_RECHARGE,
		Skill::DATA_SACRIFICE,
		Skill::DATA_EXHAUSTION,
		'empty1',
		'empty2',
	];

	protected const KEYS_FLOAT  = [Skill::DATA_ACTIVATION, Skill::DATA_ADRENALINE_PRECISE];
	protected const KEYS_BOOL   = [Skill::DATA_IS_ELITE, Skill::DATA_IS_RP];
	protected const KEYS_STRING = [Skill::DESC_NAME, Skill::DESC_DESCRIPTION, 'name2', 'empty3'];

	// pawned uses negative numbers for the pve attributes
	protected const PWND_ATTR_TRANSLATE = [
		Attribute::TITLE_NORN         => -9,
		Attribute::TITLE_VANGUARD     => -8,
		Attribute::TITLE_DELDRIMOR    => -7,
		Attribute::TITLE_ASURA        => -6,
		Attribute::TITLE_KURZICK      => -5,
		Attribute::TITLE_LUXON        => -4,
		Attribute::TITLE_LIGHTBRINGER => -3,
		Attribute::TITLE_SUNSPEAR     => -2,
		Attribute::NONE               => -1,
	];

	protected const TYPE_MAP = [
		Skilltype::DOUBLE_ENCHANTMENT      => Skilltype::ENCHANTMENT_SPELL,
		Skilltype::TOUCH_SKILL             => Skilltype::SKILL,
		Skilltype::TOUCH_SPELL             => Skilltype::SPELL,
		Skilltype::TOUCH_ENCHANTMENT_SPELL => Skilltype::ENCHANTMENT_SPELL,
		Skilltype::TOUCH_HEX_SPELL         => Skilltype::HEX_SPELL,
		Skilltype::TOUCH_SIGNET            => Skilltype::SIGNET,
	];

	protected const INI_DE = <<<ini_de
[Menu]
Name="Deutsch ({MODE}, {TYPE}, buildwars)"
Hint="Daten von GuildWiki und GWW, erstellt von smiley, github.com/build-wars"
[Update]
Provider="buildwars"
Date={DATE}
Hash={HASH}
[Network]
DownloadSafeCSV="{FILE_URL}.csv"
DownloadSafeINI="{FILE_URL}.ini"
[Wiki]
WikiShow="https://www.guildwiki.de/wiki/%wikistr%"
WikiEdit="https://www.guildwiki.de/gwiki/index.php?title=%wikistr%&action=edit"
PrimaryDE=False
ShowWikia=False
[rebuilt]
Expect={SKILL_COUNT}
Lang=DE
ini_de;

	protected const INI_EN = <<<ini_en
[Menu]
Name="Englisch ({MODE}, {TYPE}, buildwars)"
Hint="Data from GuildWiki and GWW, by smiley, github.com/build-wars"
[Update]
Provider="buildwars"
Date={DATE}
Hash={HASH}
[Network]
DownloadSafeCSV="{FILE_URL}.csv"
DownloadSafeINI="{FILE_URL}.ini"
[Wiki]
WikiShow="https://wiki.guildwars.com/wiki/%wikistr%"
WikiEdit="https://wiki.guildwars.com/index.php?title=%wikistr%&action=edit"
PrimaryEN=False
ShowGWW=False
ShowWikia=False
[rebuilt]
Expect={SKILL_COUNT}
Lang=EN
ini_en;

	protected const ini_body = [
		SkillLang::DE => self::INI_DE,
		SkillLang::EN => self::INI_EN,
		self::LANG_EN => self::INI_EN,
	];

	protected function assignKeys(array $skill):array{
		$skill = array_combine(self::KEYS, $skill);

		foreach(self::KEYS_INT as $key){
			$skill[$key] = intval($skill[$key]);
		}

		foreach(self::KEYS_FLOAT as $key){
			$skill[$key] = floatval($skill[$key]);
		}

		foreach(self::KEYS_BOOL as $key){
			$skill[$key] = boolval($skill[$key]);
		}

		foreach(self::KEYS_STRING as $key){
			$skill[$key] = trim($skill[$key]);
		}

		return $skill;
	}

	/**
	 * Creates the JSON skeletons, filled with some basic, non-changing from the given paw-ned² CSV database
	 */
	public function create():static{
		$attr_map = array_flip(self::PWND_ATTR_TRANSLATE);

		foreach(self::PWND_CSV as $mode => $files){
			foreach($files as $lang => $file){
				// skip the en-version database
				if($lang === self::LANG_EN){
					continue;
				}

				foreach($this->loadPawnedDatabase($file) as $skill){
					$skill = $this->assignKeys($skill);
					$split = array_key_exists($skill[Skill::DATA_ID], PVP_SPLIT);

					// skip duplicate skills from the PvP files
					if($mode === Skill::MODE_PVP && !$split){
						continue;
					}
					// use the split ID if it exists
					if($mode === Skill::MODE_PVP && $split){
						$skill[Skill::DATA_ID] = PVP_SPLIT[$skill[Skill::DATA_ID]];
					}

					$id = $skill[Skill::DATA_ID];
					// create the skeleton array
					foreach(SkillDataInterface::KEYS_DATA as $key){
						$this->skilldata[$id][$key] = null;

						// we'll keep these fields as they shouldn't change, and if so, a manual update is warranted
						if(in_array($key, [
							Skill::DATA_ID, Skill::DATA_CAMPAIGN, Skill::DATA_PROFESSION,
							Skill::DATA_ATTRIBUTE, Skill::DATA_IS_ELITE, Skill::DATA_IS_RP,
						], true)){
							$this->skilldata[$id][$key] = $skill[$key];
						}
						// reassign the attribute value
						if($key === Skill::DATA_ATTRIBUTE && $skill[Skill::DATA_ATTRIBUTE] < 0){
							$this->skilldata[$id][$key] = $attr_map[$skill[$key]];
						}
						// this skill *is* a pvp version
						if($key === Skill::DATA_IS_PVP){
							$this->skilldata[$id][$key] = in_array($id, PVP_SPLIT, true);
						}
						// the skill *has* a pvp version
						if($key === Skill::DATA_PVP_SPLIT){
							$this->skilldata[$id][$key] = array_key_exists($id, PVP_SPLIT);
						}
						// the id of the pvp version of the current skill
						if($key === Skill::DATA_SPLIT_ID){
							$this->skilldata[$id][$key] = (PVP_SPLIT[$id] ?? 0);
							// add the base id to pvp-split skills
							if($this->skilldata[$id][Skill::DATA_IS_PVP] === true){
								$this->skilldata[$id][$key] = (PVP_SPLIT_FLIP[$id] ?? 0);
							}
						}
					}

					// add the ID field for the language files
					$this->skilldesc[$lang][$id][Skill::DATA_ID] = $id;

					foreach(SkillDataInterface::KEYS_DESC as $key){
						$this->skilldesc[$lang][$id][$key] = '';
						// add the name field as this is the article query for the wikis
						if($key === Skill::DESC_NAME){
							$this->skilldesc[$lang][$id][$key] = $skill[$key];
						}
					}

				}
			}
		}

		$this->saveSkilldata($this->skilldata);
		$this->saveSkillDescriptions($this->skilldesc);

		return $this;
	}

	public function build():static{
		// technically we could const these
		$empty_fields = ['name2' => '', 'empty1' => 0, 'empty2' => 0, 'empty3' => ''];

		// now create the CSV
		foreach([SkillLang::DE, SkillLang::EN, self::LANG_EN] as $lang){

			$lang2 = match($lang){
				SkillLang::DE, self::LANG_EN => SkillLang::EN,
				SkillLang::EN                => SkillLang::DE,
			};

			$dblang = match($lang){
				SkillLang::DE                => SkillLang::DE,
				SkillLang::EN, self::LANG_EN => SkillLang::EN,
			};

			foreach([Skill::MODE_PVE, Skill::MODE_PVP] as $mode){
				$pvp = ($mode === Skill::MODE_PVP);

				foreach([Skill::DESC_DESCRIPTION, Skill::DESC_CONCISE] as $desc_type){
					$pwnd = [];

					foreach($this->databases[$dblang]->getIDs() as $id){
						$data  = $this->databases[$dblang]->get($id, $pvp)->toArray();
						$data += $empty_fields;
						$row   = [];

						foreach(self::KEYS as $pos => $key){
							$row[$pos] = $data[$key];

							// pawned does not use the split IDs
							if($key === Skill::DATA_ID){
								$row[$pos] = $id;
							}

							// second custom name field
							if($key === 'name2'){
								$row[$pos] = $this->databases[$lang2]->get($id, $pvp)->name;
							}

							if($key === Skill::DESC_DESCRIPTION){
								// pawned uses 2 dots for progressions, e.g. 0..15 instead of 0...15
								// pawned CSV does not use field enclosures, so we'll just replace semicolons
								// double spaces may remain from stripping tags
								$row[$pos] = strtr(strip_tags($data[$desc_type]), ['...' => '..', ';' => '.', '  ' => ' ']);
							}

							if($key === Skill::DATA_ATTRIBUTE && array_key_exists($data[$key], self::PWND_ATTR_TRANSLATE)){
								$row[$pos] = self::PWND_ATTR_TRANSLATE[$data[$key]];
							}

							if($key === Skill::DATA_TYPE && array_key_exists($data[$key], self::TYPE_MAP)){
								$row[$pos] = self::TYPE_MAP[$data[$key]];
							}

							if(in_array($key, [Skill::DATA_IS_ELITE, Skill::DATA_IS_RP], true)){
								$row[$pos] = (int)$data[$key];
							}

							if($key === Skill::DATA_ACTIVATION){
								if($data[$key] < 1 && $data[$key] > 0){
									// pawned is a bit picky here with leading zeroes apparently
									$row[$pos] = trim((string)$data[$key], '0');
								}
							}

						}

						$pwnd[] = implode(';', $row);
					}

					$this->saveCSV(implode("\n", $pwnd), $lang, $mode, count($pwnd), ($desc_type === Skill::DESC_CONCISE));
				}

			}
		}

		return $this;
	}

	protected function loadPawnedDatabase(string $file):array{
		$data = File::load($file);

		// the original paw-ned² files are stored in Windows-1252
		if(mb_detect_encoding($data, ['Windows-1252', 'UTF-8']) !== 'UTF-8'){
			$data = mb_convert_encoding($data, 'UTF-8', 'Windows-1252');
		}

		// split the CSV into an array
		return array_map(fn(string $line):array => explode(';', trim($line), 20), explode("\n", trim($data)));
	}

	protected function saveCSV(string $data, string $lang, string $mode, int $count, bool $concise):void{
		$dir      = $this->options->builddir;
		$filename = sprintf('%s_buildwars_%s%s', $lang, $mode, ($concise ? '_concise' : ''));

		// save as utf-8 for local diffs
#		$this->saveFile(sprintf('%s/%s.csv.utf8', $dir, $filename), $data);

		$data     = mb_convert_encoding($data, 'Windows-1252', 'UTF-8');
		$tempfile = $this->saveFile(tempnam($this->options->builddir, 'csv'), $data);
		$hash     = hash_file('SHA256', $tempfile);

		if($this->options->pawned_hash_check && $this->checkHash($filename, $hash)){
			$this->logger->info(sprintf('no file changes: %s', $filename));
			// delete the temp file here
			unlink($tempfile);

			return;
		}

		$csvpath = sprintf('%s/%s.csv', $dir, $filename);

		if(!rename($tempfile, $csvpath)){
			throw new RuntimeException(sprintf('could not move temp file "%s" to "%s"', $tempfile, $csvpath));
		}

		$ini = strtr(self::ini_body[$lang], [
			'{MODE}'        => $mode,
			'{TYPE}'        => $concise ? 'concise' : 'full',
			'{DATE}'        => date('YmdHi'),
			'{HASH}'        => $hash,
			'{FILE_URL}'    => sprintf('%s/pawned/%s', self::REPO_URL, $filename),
			'{SKILL_COUNT}' => $count,
		]);

		$this->saveFile(sprintf('%s/%s.ini', $dir, $filename), mb_convert_encoding($ini, 'Windows-1252', 'UTF-8'));

		$this->logger->info(sprintf('saved paw-ned² database to: %s', File::realpath($csvpath)));
	}

	protected function checkHash(string $filename, string $hash):bool{
		$path = sprintf('%s/%s.ini', $this->options->pawned_hash_dir, $filename);

		if(!File::exists($path)){
			throw new RuntimeException(sprintf('Could not fetch hash file: %s', $path));
		}

		$ini = parse_ini_file($path);
		$h1  = hex2bin($hash);
		$h2  = hex2bin($ini['Hash']);

		if($h1 === false || $h2 === false){
			throw new RuntimeException('invalid hash given');
		}

		return $h1 === $h2;
	}

}
