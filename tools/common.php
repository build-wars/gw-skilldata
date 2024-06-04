<?php
/**
 * common.php
 *
 * @created      25.05.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools;

use chillerlan\HTTP\CurlClient;
use chillerlan\HTTP\HTTPOptions;
use chillerlan\HTTP\Psr7\HTTPFactory;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;
use function ini_set;

require_once __DIR__.'/../vendor/autoload.php';

ini_set('date.timezone', 'UTC');
ini_set('memory_limit', -1);

const logLevel = LogLevel::DEBUG;
const cacert   = __DIR__.'/cacert.pem';

// init logger
$formatter = new LineFormatter(null, 'Y-m-d H:i:s', true, true);
$formatter->setJsonPrettyPrint(true);
$logHandler = (new StreamHandler('php://stdout', logLevel))->setFormatter($formatter);
$logger = new Logger('log', [$logHandler]);

// init http
$httpOptions = new HTTPOptions(['ca_info' => cacert, 'timeout' => 30]);

$http = new CurlClient(new HTTPFactory, $httpOptions);

const dataFile = __DIR__.'/../data/json-full/skilldata.json';

const langFiles = [
	'English' => ['en', __DIR__.'/../data/json-full/skilldesc-en.json'],
	'German'  => ['de', __DIR__.'/../data/json-full/skilldesc-de.json'],
];
