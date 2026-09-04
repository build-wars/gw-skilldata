<?php
/**
 * Class WikiFetcherFrench
 *
 * @created      21.08.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Fetchers;

use Buildwars\GWSkillData\Lang;
use Buildwars\GWSkillData\Skill;
use Buildwars\GWSkillData\SkillDataInterface;
use function array_column;
use function array_combine;
use function array_filter;
use function array_map;
use function explode;
use function in_array;
use function preg_replace;
use function str_ends_with;
use function str_ireplace;
use function trim;
use const BUILDDIR;
use const Buildwars\GWSkillDataTools\PVP_SPLIT;

/**
 * Fetches from the french Guild Wars wiki (gwiki.fr)
 * @todo
 */
final class WikiFetcherFrench extends WikiFetcherAbstract{

	public const string CACHEDIR         = BUILDDIR.'/gwiki';
	public const string MEDIAWIKI_API    = 'https://www.gwiki.fr/w/api.php';

	protected const string LANG          = Lang::FR;
	protected const string INFOBOX_NAME  = 'Infobox Compétence';

	protected const array REDIRECTS = [
		74   => 'Echo (Compétence)',
		79   => 'Drain d\'énergie (Compétence)',
		116  => 'Aura noire (Compétence)',
		258  => 'Gardien (Compétence)',
		343  => 'Pour la justice !',
		344  => 'Rafale (Guerrier)',
		365  => 'A moi la victoire !',
		442  => 'Attaque féroce (Rôdeur)',
		783  => 'Attaque sournoise (Attaques critiques)',
		843  => 'Rafale (Elémentaliste)',
		850  => 'Attaque féroce (Guerrier)',
		993  => 'Coup enragé (Guerrier)',
		1076 => 'Buveur de sang (Compétence)',
		1202 => 'Coup enragé (Rôdeur)',
		1236 => 'Chaînes d\'asservissement (Maléfice)',
		1473 => 'Sables mouvants (Compétence)',
		1754 => 'Attaque (Compétence)',
		1756 => 'Empoigne de Grenth (Compétence)',
		1948 => 'Sanctuaire de l\'ombre (Luxon)',
		1949 => 'Cauchemar éthéré (Luxon)',
		1950 => 'Sceau de corruption (Luxon)',
		1951 => 'Seigneur élémentaire (Luxon)',
		1952 => 'Esprit altruiste (Luxon)',
		1953 => 'Triple tir (Luxon)',
		1954 => '"Sauvez votre peau !" (Luxon)',
		1955 => 'Aura de la puissance sacrée (Luxon)',
		1957 => 'Javelot de furie (Luxon)',
		2051 => 'Invocation des esprits (Luxon)',
		2091 => 'Sanctuaire de l\'ombre (Kurzick)',
		2092 => 'Cauchemar éthéré (Kurzick)',
		2093 => 'Sceau de corruption (Kurzick)',
		2094 => 'Seigneur élémentaire (Kurzick)',
		2095 => 'Esprit altruiste (Kurzick)',
		2096 => 'Triple tir (Kurzick)',
		2097 => '"Sauvez votre peau !" (Kurzick)',
		2098 => 'Aura de la puissance sacrée (Kurzick)',
		2099 => 'Javelot de furie (Kurzick)',
		2100 => 'Invocation des esprits (Kurzick)',
		2116 => 'Attaque sournoise (Titre de l\'Avant-garde d\'Ebon)',
		2883 => 'Pour la justice ! (PvP)',
		3263 => 'Frappe implacable (PvP)',
	];

	protected const array EMPTY_SKILL = [
		Skill::DESC_NAME        => 'Compétence vide',
		Skill::DESC_DESCRIPTION => 'Représente une case vide de la barre de compétence.',
		Skill::DESC_CONCISE     => 'Aucune compétence.',
		Skill::DATA_TYPE        => 0,
		Skill::DATA_UPKEEP      => 0,
		Skill::DATA_ENERGY      => 0,
		Skill::DATA_ACTIVATION  => 0,
		Skill::DATA_RECHARGE    => 0,
		Skill::DATA_ADRENALINE  => 0,
		Skill::DATA_SACRIFICE   => 0,
		Skill::DATA_EXHAUSTION  => 0,
	];

	protected const array PRE_PARSE_REPLACE = [
		'&nbsp;'      => ' ',
		'&#20;'       => ' ',
		'&#45;'       => '-',
		'&#x2d;'      => '-',
		'&#34;'       => '"',
		'&#39;'       => "'",
	];

	protected function parseInfobox(string $infobox, int $id):array{

		// replace some templates
		$s = [
			// progression
			'/\{\{range2?\|(\d+)\|(\d+)(?:\|([+-])?)?(?:\|(%)?)?\}\}/i',
			// article links
			'/\[\[[^\[\|]+\|([^\[\|]+)\]\]/',
			// colored text
			'/\{\{gris\|([^\{\}]+)\}\}/i',
			'/(\d+)\s+%/',
			'/\s+/',
		];

		$r = [
			'$3$1...$2$4',
			'$1',
			'<gray>$1</gray>',
			'$1%',
			' ',
		];

		$infobox = preg_replace($s, $r, $infobox);

		// clean out unwanted braces etc.
		$infobox = str_ireplace([self::INFOBOX_NAME, '<b>', '</b>','{', '}', '[', ']', "'''"], '', $infobox);

		$infobox = strtr($infobox, [
			'<span style="color: grey;">' => '<gray>',
			'<span style="color: gray;">' => '<gray>',
			'</span>'                     => '</gray>',
		]);

		// split into key=value pairs
		$infobox = array_map($this->splitKV(...), array_filter(explode('|', trim($infobox))));
		// combine keys and values
		$infobox = array_combine(array_column($infobox, 0), array_column($infobox, 1));

		$name = ($infobox['nom_infobox'] ?: $infobox['nom']); // phpcs:ignore
		$name .= match(true){
			in_array($id, SkillDataInterface::SKILLS_LUXON, true)             => ' (Luxon)',
			in_array($id, SkillDataInterface::SKILLS_KURZICK, true)           => ' (Kurzick)',
			in_array($id, PVP_SPLIT, true) && !str_ends_with($name, ' (PvP)') => ' (PvP)',
			default => '',
		};

		return [
			Skill::DESC_NAME        => $name,
			Skill::DESC_DESCRIPTION => $infobox['description'],
			Skill::DESC_CONCISE     => ($infobox['desc_concise'] ?? ''),
		];
	}

}
