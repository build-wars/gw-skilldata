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
use Buildwars\GWSkillData\Skilltype;
use chillerlan\HTTP\Utils\MessageUtil;
use chillerlan\Utilities\File;
use RuntimeException;
use function abs;
use function array_flip;
use function array_key_exists;
use function array_map;
use function count;
use function date;
use function explode;
use function hash;
use function implode;
use function intval;
use function sprintf;
use function str_replace;
use function strip_tags;
use function trim;
use const Buildwars\GWSkillDataTools\PAWNED_DATA_DIR;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;

/**
 * paw-ned² skilldb schema
 *
 * 0  = id
 * 1  = name
 * 2  = name2 (de/en)
 * 3  = desc
 * 4  = campaign
 * 5  = attribute
 * 6  = type
 * 7  = profession
 * 8  = upkeep
 * 9  = energy
 * 10 = activation
 * 11 = recharge
 * 12 = adrenaline
 * 13 = sacrifice
 * 14 = elite
 * 15 = pve
 * 16 = overcast
 * 17 = 0
 * 18 = 0
 * 19 = empty string
 */
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
WikiShow=http://www.guildwiki.de/wiki/%wikistr%
WikiEdit=http://www.guildwiki.de/gwiki/index.php?title=%wikistr%&action=edit
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
WikiShow=http://wiki.guildwars.com/wiki/%wikistr%
WikiEdit=http://wiki.guildwars.com/index.php?title=%wikistr%&action=edit
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

	public function create():static{
		$attr_map  = array_flip(self::PWND_ATTR_TRANSLATE);
		$skilldata = [];
		$skilldesc = [];

		// first, process pve data
		foreach(self::PWND_CSV['pve'] as $lang => $file){
			$this->logger->info(sprintf('preparing skilldata pve-%s: %s', $lang, File::realpath($file)));

			foreach($this->loadPawnedDatabase($file) as $skill){
				$id = (int)$skill[0];

				$skilldesc[$lang][$id] = [
					'id'          => $id,
					'name'        => trim($skill[1]),
					'description' => str_replace('"', '', trim($skill[3])),
					'concise'     => '',
				];

				$attr = (int)$skill[5];

				if($attr < 0){
					$attr = $attr_map[$attr];
				}

				$skilldata[$id] = [
					'id'                 => $id,
					'campaign'           => (int)$skill[4],
					'profession'         => (int)$skill[7],
					'attribute'          => $attr,
					'type'               => (int)$skill[6],
					'is_elite'           => (bool)$skill[14],
					'is_rp'              => (bool)$skill[15],
					'is_pvp'             => false,
					'pvp_split'          => isset(PVP_SPLIT[$id]),
					'split_id'           => (PVP_SPLIT[$id] ?? 0),
					'upkeep'             => (int)$skill[8],
					'energy'             => (int)$skill[9],
					'activation'         => abs((float)$skill[10]),
					'recharge'           => (int)$skill[11],
					'adrenaline'         => abs((float)$skill[12]),
					'adrenaline_precise' => abs((float)$skill[12]),
					'sacrifice'          => (int)$skill[13],
					'overcast'           => (int)$skill[16],
				];
			}
		}

		// now merge the pvp data
		foreach(self::PWND_CSV['pvp'] as $lang => $file){
			$this->logger->info(sprintf('preparing skilldata pvp-%s: %s', $lang, File::realpath($file)));

			foreach($this->loadPawnedDatabase($file) as $skill){
				$id = (int)$skill[0];

				if(!array_key_exists($id, PVP_SPLIT)){
					continue;
				}

				$skilldesc[$lang][PVP_SPLIT[$id]] = [
					'id'          => PVP_SPLIT[$id],
					'name'        => trim($skill[1]),
					'description' => str_replace('"', '', trim($skill[3])),
					'concise'     => '',
				];

				$attr = (int)$skill[5];

				if($attr < 0){
					$attr = $attr_map[$attr];
				}

				$skilldata[PVP_SPLIT[$id]] = [
					'id'                 => PVP_SPLIT[$id],
					'campaign'           => (int)$skill[4],
					'profession'         => (int)$skill[7],
					'attribute'          => $attr,
					'type'               => (int)$skill[6],
					'is_elite'           => (bool)$skill[14],
					'is_rp'              => false,
					'is_pvp'             => true,
					'pvp_split'          => false,
					'split_id'           => 0,
					'upkeep'             => (int)$skill[8],
					'energy'             => (int)$skill[9],
					'activation'         => abs((float)$skill[10]),
					'recharge'           => (int)$skill[11],
					'adrenaline'         => abs((float)$skill[12]),
					'adrenaline_precise' => abs((float)$skill[12]),
					'sacrifice'          => (int)$skill[13],
					'overcast'           => (int)$skill[16],
				];
			}
		}


		$this->save_skilldata($skilldata);
		$this->save_skill_descriptions($skilldesc);

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
		$url      = sprintf('%s/pawned/%s', self::REPO_URL, $filename); // still without .ext

#		$this->checkHash($url, $hash);

		File::save(sprintf('%s/%s.csv', $dir, $filename), mb_convert_encoding($data, 'Windows-1252', 'UTF-8'));

		$ini = strtr(self::ini_body[$lang], [
			'{MODE}'        => $mode,
			'{TYPE}'        => $concise ? ', concise' : '',
			'{DATE}'        => date('YmdHi'),
			'{FILE_URL}'    => $url,
			'{SKILL_COUNT}' => $count,
		]);

		File::save(sprintf('%s/%s.ini', $dir, $filename), mb_convert_encoding($ini, 'Windows-1252', 'UTF-8'));
		File::save(sprintf('%s/%s.sha256', $dir, $filename), $hash);

		$this->logger->info(sprintf('saved: %s', $filename));
	}

	protected function checkHash(string $url, string $hash):bool{
		$url      = sprintf('%s.ini', $url);
		$request  = $this->requestFactory->createRequest('GET', $url);
		$response = $this->http->sendRequest($request);

		if($response->getStatusCode() === 200){
			$oldHash = MessageUtil::getContents($response);

			return trim($oldHash) === $hash;
		}

		throw new RuntimeException(sprintf('Could not fetch hash file: %s', $url));
	}
}
