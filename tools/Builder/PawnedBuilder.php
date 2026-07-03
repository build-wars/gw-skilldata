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
use Buildwars\GWSkillData\SkillDataInterface;
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
use function hash;
use function implode;
use function in_array;
use function intval;
use function number_format;
use function sprintf;
use function str_replace;
use function strip_tags;
use function trim;
use const Buildwars\GWSkillDataTools\PAWNED_DATA_DIR;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;

class PawnedBuilder extends Builder{

	// the paw-ned skill databases for each language. more to come... probably never.
	protected const PWND_CSV = [
		'pve' => [
			'de' => PAWNED_DATA_DIR.'/de_classic_pve.csv',
			'en' => PAWNED_DATA_DIR.'/en_classic_pve.csv',
		],
		'pvp' => [
			'de' => PAWNED_DATA_DIR.'/de_classic_pvp.csv',
			'en' => PAWNED_DATA_DIR.'/en_classic_pvp.csv',
		],
	];

	/**
	 * paw-ned² skilldb schema
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
		'id', 'name', 'name2', 'description', 'campaign', 'attribute',
		'type', 'profession', 'upkeep', 'energy', 'activation', 'recharge',
		'adrenaline_precise', 'sacrifice', 'is_elite', 'is_rp', 'overcast',
		'empty1', 'empty2', 'empty3',
	];

	protected const KEYS_INT = [
		'id', 'campaign', 'attribute', 'type', 'profession',
		'upkeep', 'energy', 'recharge', 'sacrifice', 'overcast',
		'empty1', 'empty2',
	];

	protected const KEYS_FLOAT  = ['activation', 'adrenaline_precise'];
	protected const KEYS_BOOL   = ['is_elite', 'is_rp'];
	protected const KEYS_STRING = ['name', 'name2', 'description', 'empty3'];

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
		Skilltype::TOUCH_SKILL             => 1,
		Skilltype::TOUCH_SPELL             => 22,
		Skilltype::TOUCH_ENCHANTMENT_SPELL => 23,
		Skilltype::TOUCH_HEX_SPELL         => 24,
		Skilltype::TOUCH_SIGNET            => 21,
	];

	protected const INI_DE = <<<ini_de
[Menu]
Name=Deutsch ({MODE}{TYPE}, buildwars)
Hint=Daten von GuildWiki und GWW, erstellt von smiley, github.com/build-wars
[Update]
Provider=buildwars
Date={DATE}
[Network]
DownloadSafeCSV={FILE_URL}.csv
DownloadSafeINI={FILE_URL}.ini
[Wiki]
WikiShow=https://www.guildwiki.de/wiki/%wikistr%
WikiEdit=https://www.guildwiki.de/gwiki/index.php?title=%wikistr%&action=edit
PrimaryDE=False
ShowWikia=False
[rebuilt]
Expect={SKILL_COUNT}
Lang=DE
ini_de;

	protected const INI_EN = <<<ini_en
[Menu]
Name=Englisch ({MODE}{TYPE}, buildwars)
Hint=Data from GuildWiki and GWW, by smiley, github.com/build-wars
[Update]
Provider=buildwars
Date={DATE}
[Network]
DownloadSafeCSV={FILE_URL}.csv
DownloadSafeINI={FILE_URL}.ini
[Wiki]
WikiShow=https://wiki.guildwars.com/wiki/%wikistr%
WikiEdit=https://wiki.guildwars.com/index.php?title=%wikistr%&action=edit
PrimaryEN=False
ShowGWW=False
ShowWikia=False
[rebuilt]
Expect={SKILL_COUNT}
Lang=EN
ini_en;

	protected const ini_body = [
		'de' => self::INI_DE,
		'en' => self::INI_EN,
	];

	protected function assignKeys(array $skill):array{
		$skill = array_combine(static::KEYS, $skill);

		foreach(static::KEYS_INT as $key){
			$skill[$key] = intval($skill[$key]);
		}

		foreach(static::KEYS_FLOAT as $key){
			$skill[$key] = floatval($skill[$key]);
		}

		foreach(static::KEYS_BOOL as $key){
			$skill[$key] = boolval($skill[$key]);
		}

		foreach(static::KEYS_STRING as $key){
			$skill[$key] = trim($skill[$key]);
		}

		return $skill;
	}

	public function create():static{
		$attr_map = array_flip(self::PWND_ATTR_TRANSLATE);

		foreach(self::PWND_CSV as $mode => $files){
			foreach($files as $lang => $file){
				foreach($this->loadPawnedDatabase($file) as $skill){
					$skill = $this->assignKeys($skill);
					$split = array_key_exists($skill['id'], PVP_SPLIT);

					// skip duplicate skills from the PvP files
					if($mode === 'pvp' && !$split){
						continue;
					}
					// use the split ID if it exists
					if($mode === 'pvp' && $split){
						$skill['id'] = PVP_SPLIT[$skill['id']];
					}

					$id = $skill['id'];
					// create the skeleton array
					foreach(SkillDataInterface::KEYS_DATA as $key){
						$this->skilldata[$id][$key] = null;

						// we'll keep these fields as they shouldn't change, and if so, a manual update is warranted
						if(in_array($key, ['id', 'campaign', 'profession', 'attribute', 'is_elite', 'is_rp'], true)){
							$this->skilldata[$id][$key] = $skill[$key];
						}
						// reassign the attribute value
						if($key === 'attribute' && $skill['attribute'] < 0){
							$this->skilldata[$id][$key] = $attr_map[$skill[$key]];
						}
						// this skill *is* a pvp version
						if($key === 'is_pvp'){
							$this->skilldata[$id][$key] = in_array($id, PVP_SPLIT, true);
						}
						// the skill *has* a pvp version
						if($key === 'pvp_split'){
							$this->skilldata[$id][$key] = array_key_exists($id, PVP_SPLIT);
						}
						// the id of the pvp version of the current skill
						if($key === 'split_id'){
							// @todo: add pve version id to pvp skill
							$this->skilldata[$id][$key] = (PVP_SPLIT[$id] ?? 0);
						}
					}

					// add the ID field for the language files
					$this->skilldesc[$lang][$id]['id'] = $id;

					foreach(SkillDataInterface::KEYS_DESC as $key){
						$this->skilldesc[$lang][$id][$key] = '';
						// add the name field as this is the article query for the wikis
						if($key === 'name'){
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

		foreach(self::PWND_CSV as $mode => $langfiles){
			$isPvP = $mode === 'pvp';

			foreach(['description', 'concise'] as $desc_type){
				foreach($langfiles as $lang => $file){
					$pwnd = $this->loadPawnedDatabase($file);

					$lang2 = match($lang){
						'de' => 'en',
						'en' => 'de',
					};

					foreach($pwnd as &$row){
						$skilldata = $this->databases[$lang]->get(intval($row[0]), $isPvP);

						if(array_key_exists($skilldata['attribute'], self::PWND_ATTR_TRANSLATE)){
							$skilldata['attribute'] = self::PWND_ATTR_TRANSLATE[$skilldata['attribute']];
						}

						if(array_key_exists($skilldata['type'], self::TYPE_MAP)){
							$skilldata['type'] = self::TYPE_MAP[$skilldata['type']];
						}

						if($skilldata['activation'] < 1 && $skilldata['activation'] > 0){
							$skilldata['activation'] = trim((string)$skilldata['activation'], '0');
						}

						if($skilldata['adrenaline_precise'] > 0){
							$skilldata['adrenaline_precise'] = number_format($skilldata['adrenaline_precise'], 1, '.', '');
						}

						$row[1]  = $skilldata['name'];
						$row[2]  = $this->databases[$lang2]->get(intval($row[0]), $isPvP)['name'];
						// pawned CSV does not use field enclosures, so we'll just replace semicolons
						$row[3]  = str_replace(['...', ';'], ['..', '.'], strip_tags($skilldata[$desc_type]));
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
						// the last 3 columns are placeholders (number;number;string)
						$row[17] = 0;
						$row[18] = 0;
						$row[19] = '';

						$row = implode(';', $row);
					}

					$count = count($pwnd);

					$this->savePawnedDatabase(
						$this->options->builddir,
						implode("\n", $pwnd),
						$lang,
						$mode,
						$count,
						($desc_type === 'concise'),
					);
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

	protected function savePawnedDatabase(string $dir, string $data, string $lang, string $mode, int $count, bool $concise):void{
		$filename = sprintf('%s_buildwars_%s%s', $lang, $mode, ($concise ? '_concise' : ''));
		$hash     = hash('SHA256', $data);

		if($this->options->pawned_hash_check && $this->checkHash($filename, $hash)){
			$this->logger->info(sprintf('no file changes: %s', $filename));

			return;
		}

		$savepath = $this->saveFile(sprintf('%s/%s.csv', $dir, $filename), $data);

		$ini = strtr(self::ini_body[$lang], [
			'{MODE}'        => $mode,
			'{TYPE}'        => $concise ? ', concise' : '',
			'{DATE}'        => date('YmdHi'),
			'{FILE_URL}'    => sprintf('%s/pawned/%s', self::REPO_URL, $filename),
			'{SKILL_COUNT}' => $count,
		]);

		$this->saveFile(sprintf('%s/%s.ini', $dir, $filename), mb_convert_encoding($ini, 'Windows-1252', 'UTF-8'));
		$this->saveFile(sprintf('%s/%s.sha256', $dir, $filename), $hash);

		$this->logger->info(sprintf('saved paw-ned² database to: %s', $savepath));
	}

	protected function checkHash(string $filename, string $hash):bool{
		$path = sprintf('%s/%s.sha256', $this->options->pawned_hash_dir, $filename);

		if(!File::exists($path)){
			throw new RuntimeException(sprintf('Could not fetch hash file: %s', $path));
		}

		$oldHash = File::load($path);

		return $hash === $oldHash;
	}

}
