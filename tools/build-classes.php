<?php
/**
 * build-classes.php
 *
 * @created      28.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use chillerlan\Utilities\File;
use function array_map;
use function implode;
use function is_bool;
use function sprintf;
use function str_replace;
use function strtoupper;

/**
 * @phan-file-suppress PhanUndeclaredGlobalVariable ??
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';


/*
 * skill data
 */

$json = File::loadJSON(DATA_FILE, true);

// dump the PHP class
$content = [
	'<?php // THERE BE DRAGONS',
	'declare(strict_types=1);',
	'namespace Buildwars\\GWSkillData;',
	'abstract class SkillData extends SkillDataAbstract{',
	'public const ID2DATA = [',
];

foreach($json['skilldata'] as $skillID => $data){

	foreach($data as &$field){
		if(is_bool($field)){
			$field = $field === true ? 'true' : 'false';
		}
	}

	$content[] = sprintf('%d=>[%s],', $skillID, implode(',', $data));
}

$content[] = "];}\n";

$classFile = __DIR__.'/../src/SkillData.php';

File::save($classFile, implode("\n", $content));

$logger->info(sprintf('class SkillData saved in %s', File::realpath($classFile)));


/*
 * skill descriptions
 */

foreach(LANG_FILES as $lang => [$abbr, $file]){
	$json = File::loadJSON($file, true);

	// unset the "id" field here
	foreach($json['skilldesc'] as &$row){
		unset($row['id']);
	}

	// dump the PHP class
	$content = [
		'<?php // THERE BE DRAGONS',
		'declare(strict_types=1);',
		'namespace Buildwars\\GWSkillData;',
		sprintf('final class SkillLang%s extends SkillData{', $lang),
		sprintf('public const LANG = self::LANG_%s;', strtoupper($abbr)),
		'public const ID2DESC = [',
	];

	foreach($json['skilldesc'] as $skillID => $data){
		// escape single quotes
		$data = array_map(fn(string $str):string => str_replace("'", "\\'", $str), $data);

		$content[] = sprintf("%d=>['%s'],", $skillID, implode("','", $data));
	}

	$content[] = "];}\n";

	$classFile = __DIR__.'/../src/SkillLang'.$lang.'.php';

	File::save($classFile, implode("\n", $content));

	$logger->info(sprintf('class SkillLang%s saved in %s', $lang, File::realpath($classFile)));
}
