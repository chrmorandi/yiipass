<?php
/**
 * Yii console bootstrap file. Modified for PHPUnit usage: there's no
 * immediate exit after an new instance of yii\console\Application()
 * is being created.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);

// fcgi doesn't have STDIN and STDOUT defined by default
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

chdir('../../../');

require('vendor/autoload.php');
require('vendor/yiisoft/yii2/Yii.php');

$config = require('config/console.php');

$application = new yii\console\Application($config);