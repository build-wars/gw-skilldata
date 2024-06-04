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

use function array_keys;
use function array_map;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function is_bool;
use function json_decode;
use function realpath;
use function sprintf;
use function str_replace;

/**
 * @var \Psr\Log\LoggerInterface $logger
 */
require_once __DIR__.'/common.php';


/*
 * skill data
 */

$json = json_decode(file_get_contents(dataFile), true);

// dump the PHP class
$content = [
	'<?php // THERE BE DRAGONS',
	'declare(strict_types=1);',
	'namespace Buildwars\\GWSkillData;',
	'abstract class SkillData extends SkillDataAbstract{',
	sprintf("public const KEYS_DATA = ['%s'];", implode("','", array_keys($json['skilldata'][0]))),
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

file_put_contents($classFile, implode("\n", $content));

$logger->info(sprintf('class SkillData saved in %s', realpath($classFile)));


/*
 * skill descriptions
 */

foreach(langFiles as $lang => [$abbr, $file]){
	$json = json_decode(file_get_contents($file), true);

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
		sprintf("public const LANG = '%s';", $abbr),
		sprintf("public const KEYS_DESC = ['%s'];", implode("','", array_keys($json['skilldesc'][0]))),
		'public const ID2DESC = [',
	];

	foreach($json['skilldesc'] as $skillID => $data){
		// escape single quotes
		$data = array_map(fn(string $str):string => str_replace("'", "\\'", $str), $data);

		$content[] = sprintf("%d=>['%s'],", $skillID, implode("','", $data));
	}

	$content[] = "];}\n";

	$classFile = __DIR__.'/../src/SkillLang'.$lang.'.php';

	file_put_contents($classFile, implode("\n", $content));

	$logger->info(sprintf('class SkillLang%s saved in %s', $lang, realpath($classFile)));
}

