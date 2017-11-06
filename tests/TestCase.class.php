<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/lib/common.lib.php');
require_once('../include/base.lib.php');

define('DEBUG', false);
define('PROFILE', false);
define('CONFIG_ONLY_SELECT', false);
define('CONFIG_TIMESTAMP_FILTER', false);
define('CONFIG_DATABASE', false);
define('CONFIG_DATABASE_LIST', false);
define('CONFIG_DATABASE_REGEXP', false);
define('CONFIG_USER', false);
define('CONFIG_USER_LIST', false);
define('CONFIG_USER_REGEXP', false);
define('CONFIG_SYSLOG_IDENTITY', 'postgres');
define('CONFIG_KEEP_FORMATTING', 1);

ini_set('error_reporting', 2039);

$stderr = fopen('php://stderr', 'w');

$commonTests = &new GroupTest('Common tests');

$commonTests->addTestFile('TestRegExp.class.php');
//$commonTests->addTestFile('TestProfiler.class.php');
$commonTests->addTestFile('TestGenericLogReader.class.php');
$commonTests->addTestFile('TestLogObject.class.php');
$commonTests->addTestFile('TestQueryLogObject.class.php');
$commonTests->addTestFile('TestErrorLogObject.class.php');
$commonTests->addTestFile('TestLogStream.class.php');
$commonTests->addTestFile('TestSlowestQueryList.class.php');
$commonTests->run(new TextReporter());


$postgresqlTests = &new GroupTest('PostgreSQL tests');
$postgresqlTests->addTestFile('TestSyslogPostgreSQLParser.class.php');
$postgresqlTests->addTestFile('TestLogFiles.class.php');
$postgresqlTests->run(new TextReporter());

fclose($stderr);
