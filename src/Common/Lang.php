<?php
/**
 * Class Lang
 *
 * @created      24.07.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillData\Common;

use InvalidArgumentException;
use function array_key_exists;
use function in_array;
use function sprintf;
use function strtolower;
use function trim;

/**
 * Encapsulates the available skill data languages
 */
final class Lang{

	public const string CN = 'chs'; // simplified chinese
	public const string DE = 'de';
	public const string EN = 'en';
	public const string ES = 'es';
	public const string FR = 'fr';
	public const string IT = 'it';
	public const string JA = 'jp';
	public const string KO = 'kr';
	public const string PL = 'pl';
	public const string RU = 'ru';
	public const string XX = 'bork';
	public const string ZH = 'cht'; // traditional chinese

	public const string DE_GUILDWIKI = 'de-guildwiki';
	public const string EN_GWW       = 'en-gww';
	public const string FR_GWIKI     = 'fr-gwiki';

	public const array IDS = [
		self::CN,
		self::DE,
		self::EN,
		self::ES,
		self::FR,
		self::IT,
		self::JA,
		self::KO,
		self::PL,
		self::RU,
		self::XX,
		self::ZH,
	];

	private const array CLASSNAME_SUFFIX = [
		self::CN => 'SimplifiedChinese',
		self::DE => 'German',
		self::EN => 'English',
		self::ES => 'Spanish',
		self::FR => 'French',
		self::IT => 'Italian',
		self::JA => 'Japanese',
		self::KO => 'Korean',
		self::PL => 'Polish',
		self::RU => 'Russian',
		self::XX => 'Bork',
		self::ZH => 'TraditionalChinese',
		// special types
		self::DE_GUILDWIKI => 'GermanGuildWiki',
		self::EN_GWW       => 'EnglishGWW',
		self::FR_GWIKI     => 'FrenchGWiki',
	];

	/**
	 * @var array<string, array<string, string>>
	 */
	public const array NAMES = [
		self::CN => [
			self::CN => '简体中文',
			self::DE => 'Chinesisch (vereinfacht)',
			self::EN => 'Chinese (simplified)',
			self::ES => 'Chino simplificado',
			self::FR => 'Chinois simplifié',
			self::IT => 'Cinese semplificato',
			self::JA => '中国語：簡体字',
			self::KO => '중국어 간체',
			self::PL => 'chiński uproszczony',
			self::RU => 'Упрощенный китайский',
			self::XX => 'Seempleefeed Cheenese-a',
			self::ZH => '簡體中文',
		],
		self::DE => [
			self::CN => '',
			self::DE => 'Deutsch',
			self::EN => 'German',
			self::ES => 'Alemán',
			self::FR => 'Allemand',
			self::IT => 'Tedesco',
			self::JA => 'ドイツ語',
			self::KO => '독일어',
			self::PL => 'niemiecki',
			self::RU => 'немецкий',
			self::XX => 'Germun',
			self::ZH => '',
		],
		self::EN => [
			self::CN => '',
			self::DE => 'Englisch',
			self::EN => 'English',
			self::ES => 'Inglés',
			self::FR => 'Anglais',
			self::IT => 'Inglese',
			self::JA => '英語',
			self::KO => '영어',
			self::PL => 'angielski',
			self::RU => 'Английский',
			self::XX => 'Ingleesh',
			self::ZH => '',
		],
		self::ES => [
			self::CN => '',
			self::DE => 'Spanisch',
			self::EN => 'Spanish',
			self::ES => 'Español',
			self::FR => 'Espagnol',
			self::IT => 'Spagnolo',
			self::JA => 'スペイン語',
			self::KO => '스페인어',
			self::PL => 'hiszpański',
			self::RU => 'испанский',
			self::XX => 'Spuneesh',
			self::ZH => '',
		],
		self::FR => [
			self::CN => '',
			self::DE => 'Französisch',
			self::EN => 'French',
			self::ES => 'Francés',
			self::FR => 'Français',
			self::IT => 'Francese',
			self::JA => 'フランス語',
			self::KO => '프랑스어',
			self::PL => 'francuski',
			self::RU => 'Французский',
			self::XX => 'French',
			self::ZH => '',
		],
		self::IT => [
			self::CN => '',
			self::DE => 'Italienisch',
			self::EN => 'Italian',
			self::ES => 'Italiano',
			self::FR => 'Italien',
			self::IT => 'Italiano',
			self::JA => 'イタリア語',
			self::KO => '이탈리아어',
			self::PL => 'włoski',
			self::RU => 'итальянский',
			self::XX => 'Itaeleeun',
			self::ZH => '',
		],
		self::JA => [
			self::CN => '',
			self::DE => 'Japanisch',
			self::EN => 'Japanese',
			self::ES => 'Japonés',
			self::FR => 'Japonais',
			self::IT => 'Giapponese',
			self::JA => '日本語',
			self::KO => '일본어',
			self::PL => 'japoński',
			self::RU => 'японский',
			self::XX => 'Jaepunese-a',
			self::ZH => '',
		],
		self::KO => [
			self::CN => '',
			self::DE => 'Koreanisch',
			self::EN => 'Korean',
			self::ES => 'Coreano',
			self::FR => 'Coréen',
			self::IT => 'Coreano',
			self::JA => '韓国語',
			self::KO => '한국어',
			self::PL => 'koreański',
			self::RU => 'корейский',
			self::XX => 'Kureun',
			self::ZH => '',
		],
		self::PL => [
			self::CN => '',
			self::DE => 'Polnisch',
			self::EN => 'Polish',
			self::ES => 'Polaco',
			self::FR => 'Polonais',
			self::IT => 'Polacco',
			self::JA => 'ポーランド語',
			self::KO => '폴란드어',
			self::PL => 'polski',
			self::RU => 'польский',
			self::XX => 'Puleesh',
			self::ZH => '',
		],
		self::RU => [
			self::CN => '',
			self::DE => 'Russisch',
			self::EN => 'Russian',
			self::ES => 'Ruso',
			self::FR => 'Russe',
			self::IT => 'Russo',
			self::JA => 'ロシア語',
			self::KO => '러시아어',
			self::PL => 'rosyjski',
			self::RU => 'Русский',
			self::XX => 'Roosseeun',
			self::ZH => '',
		],
		self::XX => [
			self::CN => '波格！波格！波格！',
			self::DE => 'Bork! Bork! Bork!',
			self::EN => 'Bork! Bork! Bork!',
			self::ES => '-¡Bork, bork, bork!-',
			self::FR => 'Bork! Bork! Bork!',
			self::IT => 'Bork! Bork! Bork!',
			self::JA => 'Bork! Bork! Bork!',
			self::KO => '보글!보글!보글!',
			self::PL => 'Bork! Bork! Bork!',
			self::RU => 'Бла-бла-бла!',
			self::XX => 'Burk! Burk! Burk!',
			self::ZH => '波格！波格！波格！',
		],
		self::ZH => [
			self::CN => '传统中文',
			self::DE => 'Chinesisch (traditionell)',
			self::EN => 'Chinese (traditional)',
			self::ES => 'Chino tradicional',
			self::FR => 'Chinois traditionnel',
			self::IT => 'Tradizionale Cinese',
			self::JA => '中国語: 繁体字',
			self::KO => '중국어 번체',
			self::PL => 'chiński tradycyjny',
			self::RU => 'Традиционный китайский',
			self::XX => 'Traedeeshunael Cheenese-a',
			self::ZH => '傳統中文',
		],
	];

	protected(set) string $id {
		set{
			$value = trim(strtolower($value));

			if(!in_array($value, self::IDS, true)){
				throw new InvalidArgumentException('invalid language');
			}

			$this->id = $value;
		}
	}

	public function __construct(string $id){
		// wiki lang fixtures
		$this->id = match($id){
			self::DE_GUILDWIKI => self::DE,
			self::EN_GWW       => self::EN,
			self::FR_GWIKI     => self::FR,
			default            => $id,
		};
	}

	/**
	 * Checks whether the object ID is equal to the given ID
	 */
	public function is(string $id):bool{
		return $this->id === $id;
	}

	/**
	 * Checks whether the object ID is in the given array of IDs
	 *
	 * @param string[] $ids
	 */
	public function in(array $ids):bool{
		return in_array($this->id, $ids, true);
	}

	public function getClassName(string|null $id = null):string{
		$id ??= $this->id;

		if(!array_key_exists($id, self::CLASSNAME_SUFFIX)){
			throw new InvalidArgumentException('invalid language');
		}

		return sprintf('SkillLang%s', self::CLASSNAME_SUFFIX[$id]);
	}

	/**
	 * Returns the readable name of the given language ID
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getName(string|null $id = null):string{

		if($id !== null && !$this->in(self::IDS)){
			throw new InvalidArgumentException('invalid language');
		}

		return self::NAMES[$this->id][($id ?? $this->id)];
	}

	/**
	 * Returns a "stacking" or "non-stacking" suffix
	 */
	public function stackable(bool $stackable):string{
		$suffix = ($stackable === false ? self::STR_NONSTACKING : self::STR_STACKING);

		return $this->string($suffix);
	}


	// @todo

	public const int STR_ARMOR       = 0x6001;
	public const int STR_HEALTH      = 0x6002;
	public const int STR_ENERGY      = 0x6003;
	public const int STR_PVP         = 0x6004;
	public const int STR_STACKING    = 0x6005;
	public const int STR_NONSTACKING = 0x6006;
	public const int STR_VERSUS      = 0x6007;

	private const array LANG_STRINGS = [
		self::STR_ARMOR => [
			self::CN => '[ARMOR]',
			self::DE => 'Rüstung',
			self::EN => 'Armor',
			self::ES => 'Armadura',
			self::FR => 'Armure',
			self::IT => 'Armatura',
			self::JA => '[ARMOR]',
			self::KO => '[ARMOR]',
			self::PL => '[ARMOR]',
			self::RU => '[ARMOR]',
			self::XX => 'Aermur',
			self::ZH => '[ARMOR]',
		],
		self::STR_HEALTH => [
			self::CN => '[HEALTH]',
			self::DE => 'Lebenspunkte',
			self::EN => 'Health',
			self::ES => 'Salud',
			self::FR => 'Santé',
			self::IT => 'Salute',
			self::JA => '[HEALTH]',
			self::KO => '[HEALTH]',
			self::PL => '[HEALTH]',
			self::RU => '[HEALTH]',
			self::XX => 'Heaelt',
			self::ZH => '[HEALTH]',
		],
		self::STR_ENERGY => [
			self::CN => '[ENERGY]',
			self::DE => 'Energie',
			self::EN => 'Energy',
			self::ES => 'Energía',
			self::FR => 'Energie',
			self::IT => 'Energia',
			self::JA => '[ENERGY]',
			self::KO => '[ENERGY]',
			self::PL => '[ENERGY]',
			self::RU => '[ENERGY]',
			self::XX => 'Inergy',
			self::ZH => '[ENERGY]',
		],
		self::STR_PVP => [
			self::CN => '[PVP]',
			self::DE => 'PvP',
			self::EN => 'PvP',
			self::ES => 'PvP',
			self::FR => 'PvP',
			self::IT => 'PvP',
			self::JA => 'PvP',
			self::KO => '대인전',
			self::PL => 'PvP',
			self::RU => 'PvP',
			self::XX => 'PfP',
			self::ZH => '[PVP]',
		],
		self::STR_STACKING => [
			self::CN => '[STACKING]',
			self::DE => 'Stapelbar',
			self::EN => 'Stacking',
			self::ES => 'Acumulable',
			self::FR => 'Cumulable',
			self::IT => 'Cumulabile',
			self::JA => '[STACKING]',
			self::KO => '[STACKING]',
			self::PL => '[STACKING]',
			self::RU => '[STACKING]',
			self::XX => 'Staeckeeng',
			self::ZH => '[STACKING]',
		],
		self::STR_NONSTACKING => [
			self::CN => '[NONSTACKING]',
			self::DE => 'Nicht stapelbar',
			self::EN => 'Non-stacking',
			self::ES => 'No acumulable',
			self::FR => 'Non cumulable',
			self::IT => 'Non cumulabile',
			self::JA => '[NONSTACKING]',
			self::KO => '[NONSTACKING]',
			self::PL => '[NONSTACKING]',
			self::RU => '[NONSTACKING]',
			self::XX => 'Nun-staeckeeng',
			self::ZH => '[NONSTACKING]',
		],
		self::STR_VERSUS => [
			self::CN => '[VS]',
			self::DE => 'gg.',
			self::EN => 'vs.',
			self::ES => 'contra',
			self::FR => 'contre',
			self::IT => 'contro',
			self::JA => '[VS]',
			self::KO => '[VS]',
			self::PL => '[VS]',
			self::RU => '[VS]',
			self::XX => 'Nun-staeckeeng',
			self::ZH => 'fs.',
		],
	];

	public function string(int $id, float|int|string $value = ''):string{

		if(!array_key_exists($id, self::LANG_STRINGS)){
			throw new InvalidArgumentException('invalid lang string');
		}

		return trim(sprintf('%s %s', self::LANG_STRINGS[$id][$this->id], $value));
	}

}
