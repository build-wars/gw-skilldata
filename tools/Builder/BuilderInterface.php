<?php
/**
 * Interface BuilderInterface
 *
 * @created      02.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

interface BuilderInterface{

	public const string REPO_URL = 'https://build-wars.github.io/gw-skilldata';

	public const string SCHEMA_SKILL              = self::REPO_URL.'/schemas/skill.schema.json';
	public const string SCHEMA_SKILLDATA          = self::REPO_URL.'/schemas/skilldata.schema.json';
	public const string SCHEMA_SKILLDESC          = self::REPO_URL.'/schemas/skilldesc.schema.json';
	public const string SCHEMA_SKILLDATA_COMBINED = self::REPO_URL.'/schemas/skilldata-combined.schema.json';

	public function build():static;

}
