<?php
/**
 * Curse Inc.
 *
 * @author    Samuel Hilson <shhilson@curse.com>
 * @copyright 2019 Curse, inc.
 * @license   MIT
 * @package   HydraWiki
 */

use PHP_CodeSniffer\Util\Tokens;

include_once __DIR__ . '/BaseTest.php';
include_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../vendor/squizlabs/php_codesniffer/autoload.php';

$tokens = new Tokens();
