<?php
/**
 * Class BuildPawned
 *
 * @created      04.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Attribute;
use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataAwareInterface;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use Buildwars\GWSkillData\Type;
use chillerlan\Utilities\Crypto;
use function array_key_exists;
use function count;
use function implode;
use function in_array;
use function sprintf;
use function str_replace;
use function strip_tags;
use function trim;

final class BuildPawned extends BuilderAbstract implements SkillDataAwareInterface{
	use SkillDataAwareTrait;

	// "en2" is similar to the "en" database, but column 3 also has the english names.
	// this is a workaround for the english version of pawned to avoid false search results.
	private const string LANG_EN = 'en2';

	// currently supported languages (due to Windows-1252 limitation)
	/** @var array<string, string[]>  */
	public const array PAWNED_LANGUAGES = [
		Lang::DE           => [Lang::DE,           Lang::EN          ],
		Lang::DE_GUILDWIKI => [Lang::DE_GUILDWIKI, Lang::EN_GWW      ],
		Lang::EN           => [Lang::EN,           Lang::DE          ],
		Lang::EN_GWW       => [Lang::EN_GWW,       Lang::DE_GUILDWIKI],
		self::LANG_EN      => [Lang::EN_GWW,       Lang::EN          ],
		Lang::ES           => [Lang::ES,           Lang::DE_GUILDWIKI],
		Lang::FR           => [Lang::FR,           Lang::DE_GUILDWIKI],
		Lang::IT           => [Lang::IT,           Lang::DE_GUILDWIKI],
		Lang::XX           => [Lang::XX,           Lang::DE_GUILDWIKI],
	];
	// i hate this
	private const array INI_METADATA = [
		Lang::DE           => ['lang' => 'Deutsch',             'lng' => 'DE', 'wiki' => 'www.guildwiki.de'],
		Lang::DE_GUILDWIKI => ['lang' => 'Deutsch (GuildWiki)', 'lng' => 'DE', 'wiki' => 'www.guildwiki.de'],
		Lang::EN           => ['lang' => 'English',             'lng' => 'EN', 'wiki' => 'wiki.guildwars.com'],
		Lang::EN_GWW       => ['lang' => 'English (GWW)',       'lng' => 'EN', 'wiki' => 'wiki.guildwars.com'],
		self::LANG_EN      => ['lang' => 'English only (GWW)',  'lng' => 'EN', 'wiki' => 'wiki.guildwars.com'],
		Lang::ES           => ['lang' => 'Español',             'lng' => 'ES', 'wiki' => ''],
		Lang::FR           => ['lang' => 'Français',            'lng' => 'FR', 'wiki' => 'www.gwiki.fr'],
		Lang::IT           => ['lang' => 'Italiano',            'lng' => 'IT', 'wiki' => ''],
		Lang::XX           => ['lang' => 'Bork! Bork! Bork!',   'lng' => 'XX', 'wiki' => ''],
	];

	private const string INI_BODY = <<<INI
[Menu]
Name="{LANG} ({MODE}, {TYPE}, buildwars)"
Hint="Data from GuildWiki, GWW and gw.dat, compiled by smiley, github.com/build-wars"
[Update]
Provider="buildwars"
Date={DATE}
Hash={HASH}
[Network]
DownloadSafeCSV="{FILE_URL}.csv"
DownloadSafeINI="{FILE_URL}.ini"
[Wiki]
WikiShow={WIKI_SHOW}
WikiEdit={WIKI_EDIT}
PrimaryEN=False
ShowGWW=False
ShowWikia=False
[rebuilt]
Expect={SKILL_COUNT}
Lang={LANG_CODE}
INI;

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
	private const array CSV_KEYS = [
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

	private const array EMPTY_FIELDS = [
		'name2'  => '',
		'empty1' => 0,
		'empty2' => 0,
		'empty3' => '',
	];

	// pawned uses negative numbers for the pve attributes
	private const array ATTRIBUTE_MAP = [
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

	private const array TYPE_MAP = [
		Type::DOUBLE_ENCHANTMENT      => Type::ENCHANTMENT_SPELL,
		Type::TOUCH_SKILL             => Type::SKILL,
		Type::TOUCH_SPELL             => Type::SPELL,
		Type::TOUCH_ENCHANTMENT_SPELL => Type::ENCHANTMENT_SPELL,
		Type::TOUCH_HEX_SPELL         => Type::HEX_SPELL,
		Type::TOUCH_SIGNET            => Type::SIGNET,
	];

	public function build():static{

		foreach(self::PAWNED_LANGUAGES as $langID => [$lang1, $lang2]){
			$db1 = $this->getGWDB($lang1);
			$db2 = $this->getGWDB($lang2);

			foreach([Skill::MODE_PVE, Skill::MODE_PVP] as $mode){
				$pvp = ($mode === Skill::MODE_PVP);

				foreach([Skill::DESC_DESCRIPTION, Skill::DESC_CONCISE] as $desc_type){
					$pwnd = [];

					foreach($db1->getIDs(false) as $id){
						$data  = $db1->get($id, $pvp)->toArray();
						$data += self::EMPTY_FIELDS;
						$row   = [];

						foreach(self::CSV_KEYS as $pos => $key){
							$row[$pos] = $data[$key];

							// pawned does not use the split IDs
							if($key === Skill::DATA_ID){
								$row[$pos] = $id;
							}

							// second custom name field
							if($key === 'name2'){
								$row[$pos] = $db2->get($id, $pvp)->name;
							}

							if($key === Skill::DESC_DESCRIPTION){
								// pawned uses 2 dots for progressions, e.g. 0..15 instead of 0...15
								// pawned CSV does not use field enclosures, so we'll just replace semicolons
								// double spaces may remain from stripping tags
								$row[$pos] = strtr(strip_tags($data[$desc_type]), ['...' => '..', ';' => '.', '  ' => ' ']);
							}

							if($key === Skill::DATA_ATTRIBUTE && array_key_exists($data[$key], self::ATTRIBUTE_MAP)){
								$row[$pos] = self::ATTRIBUTE_MAP[$data[$key]];
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

					$this->saveCSV(implode("\n", $pwnd), $langID, $mode, count($pwnd), ($desc_type === Skill::DESC_CONCISE));
				}

			}
		}

		return $this;
	}

	private function saveCSV(string $data, string $lang, string $mode, int $count, bool $concise):void{
		$filename = sprintf('%s_buildwars_%s%s', str_replace('-', '_', $lang), $mode, ($concise ? '_concise' : ''));
		// save as utf-8 for local diffs
#		$this->saveFile(sprintf('%s/%s.csv.utf8', self::PAWNED_CACHEDIR, $filename), $data);
		$data     = mb_convert_encoding($data, 'Windows-1252', 'UTF-8');
		$savePath = $this->saveFile(sprintf('%s/%s.csv', self::PAWNED_CACHEDIR, $filename), $data);
		$hash     = Crypto::sha256file($savePath);

		$wikishow = 'False';
		$wikiedit = 'False';

		if(self::INI_METADATA[$lang]['wiki'] !== ''){
			$wikishow = sprintf('"https://%s/wiki/%%wikistr%%"', self::INI_METADATA[$lang]['wiki']);
			$wikiedit = sprintf('"https://%s/index.php?title=%%wikistr%%&action=edit"', self::INI_METADATA[$lang]['wiki']);
		}

		/** @phan-suppress-next-line PhanParamSuspiciousOrder */
		$ini = strtr(self::INI_BODY, [
			'{LANG}'        => self::INI_METADATA[$lang]['lang'],
			'{MODE}'        => $mode,
			'{TYPE}'        => $concise ? 'concise' : 'full',
			'{DATE}'        => date('YmdHi'),
			'{HASH}'        => $hash,
			'{FILE_URL}'    => sprintf('%s/pawned/%s', self::REPO_URL, $filename),
			'{WIKI_SHOW}'   => $wikishow,
			'{WIKI_EDIT}'   => $wikiedit,
			'{SKILL_COUNT}' => $count,
			'{LANG_CODE}'   => self::INI_METADATA[$lang]['lng'],
		]);

		$this->saveFile(
			sprintf('%s/%s.ini', self::PAWNED_CACHEDIR, $filename),
			mb_convert_encoding($ini, 'Windows-1252', 'UTF-8'),
		);

		$this->logger->info(sprintf('saved paw-ned² database to: %s', $savePath));
	}

}
