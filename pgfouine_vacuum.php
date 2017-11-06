#! /usr/bin/php -qC
<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2008 Guillaume Smet
 *
 * pgFouine is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * pgFouine is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with pgFouine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

ini_set('max_execution_time', 18000);

if(strpos(phpversion(), '4.4') === 0) {
	error_reporting(E_ALL - E_NOTICE);
} else {
	error_reporting(E_ALL);
}

include('version.php');
require_once('include/lib/common.lib.php');
require_once('include/base.lib.php');
require_once('include/listeners/listeners.lib.php');
require_once('include/postgresql/postgresql.lib.php');
require_once('include/reporting/reports.lib.php');
require_once('include/postgresql/vacuum/vacuum.lib.php');

$stderr = fopen('php://stderr', 'w');

function usage($error = false) {
	if($error) {
		stderr('Error: '.$error);
		echo "\n";
	}
	echo 'Usage: '.$GLOBALS['executable'].' -file <file> [-report [outputfile=]<block1,block2>] [-filter <filter>]
  -file <file>                           log file to analyze
  -                                      read the log from stdin instead of -file
  -report [outputfile=]<block1,block2>   list of report blocks separated by a comma
                                         report blocks can be: overall, fsm, vacuumedtables, details
                                         you can add several -report options if you want to generate several reports at once
  -filter <filter>                       filter of the form: database or database.schema
                                         filter is applied on output only
  -title <title>                         define the title of the reports
  -memorylimit <n>                       PHP memory limit in MB. Default is 128.
  -debug                                 debug mode
  -profile                               profile mode
  -help                                  this help
';
	if($error) {
		exit(1);
	} else {
		exit(0);
	}
}

function checkOutputFilePath($filePath) {
	if(!$filePath) {
		return false;
	}
	
	$tmpOutputFilePath = $filePath;
	$tmpOutputDirectory = dirname($tmpOutputFilePath);
	$tmpOutputFileName = basename($tmpOutputFilePath);

	if(file_exists($tmpOutputFilePath) && (!is_file($tmpOutputFilePath) || !is_writable($tmpOutputFilePath))) {
		usage($tmpOutputFilePath.' already exists and is not a file or is not writable');
		return false;
	} elseif(!is_dir($tmpOutputDirectory) || !is_writable($tmpOutputDirectory)) {
		usage($tmpOutputDirectory.' is not a directory, does not exist or is not writable');
		return false;
	} elseif(!$tmpOutputFileName) {
		usage('cannot find a valid basename in '.$tmpOutputFilePath);
		return false;
	} else {
		$outputFilePath = realpath($tmpOutputDirectory).'/'.$tmpOutputFileName;
		return $outputFilePath;
	}
}

if(isset($_SERVER['argv']) && (!isset($argv) || empty($argv))) {
	$argv = $_SERVER['argv'];
}
if(is_array($argv)) {
	$executable = array_shift($argv);
} else {
	$argv = [];
	$executable = 'unknown';
}

$options = [];
$argvCount = count($argv);
for($i = 0; $i < $argvCount; $i++) {
	if(strpos($argv[$i], '-') === 0) {
		if($argv[$i] == '-') {
			define('CONFIG_STDIN', true);
		} else {
			$optionKey = substr($argv[$i], 1);
			$value = false;
			if(($i+1 < $argvCount) && (strpos($argv[$i+1], '-') !== 0)) {
				$value = $argv[$i+1];
				$i++;
			}
			if($optionKey == 'report' || $optionKey == 'reports') {
				if(!isset($options['reports'])) {
					$options['reports'] = [];
				}
				$options['reports'][] = $value;
			} else {
				$options[$optionKey] = $value;
			}
		}
	} else {
		usage('invalid options format');
	}
}

if(isset($options['memorylimit']) && ((int) $options['memorylimit']) > 0) {
	$memoryLimit = (int) $options['memorylimit'];
} else {
	$memoryLimit = 128;
}
ini_set('memory_limit', $memoryLimit.'M');

if(!defined('CONFIG_STDIN')) {
	define('CONFIG_STDIN', false);
}

if(isset($options['help']) || isset($options['h']) || isset($options['-help'])) {
	usage();
}

if(isset($options['debug'])) {
	define('DEBUG', 1);
} else {
	define('DEBUG', 0);
}
if(isset($options['profile'])) {
	define('PROFILE', 1);
} else {
	define('PROFILE', 0);
}

define('CONFIG_ONLY_SELECT', false);
define('CONFIG_KEEP_FORMATTING', false);
define('CONFIG_DURATION_UNIT', 's');
define('CONFIG_TIMESTAMP_FILTER', false);
define('CONFIG_DATABASE', false);
define('CONFIG_USER', false);

if(!CONFIG_STDIN) {
	if(!isset($options['file'])) {
		usage('the -file option is required');
	} elseif(!$options['file']) {
		usage('you have to specify a file path');
	} elseif(!is_readable($options['file'])) {
		usage('file '.$options['file'].' cannot be read');
	} else {
		$filePath = realpath($options['file']);
	}
} else {
	$filePath = 'php://stdin';
}

if(isset($options['filter']) && !empty($options['filter'])) {
	define('CONFIG_FILTER', $options['filter']);
} else {
	define('CONFIG_FILTER', false);
}

if(isset($options['title'])) {
	define('CONFIG_REPORT_TITLE', $options['title']);
} else {
	define('CONFIG_REPORT_TITLE', 'pgFouine: PostgreSQL VACUUM log analysis report');
}

$outputToFiles = false;
$supportedReportBlocks = [
	'overall' => 'VacuumOverallReport',
	'vacuumedtables' => 'VacuumedTablesReport',
	'details' => 'VacuumedTablesDetailsReport',
	'fsm' => 'FSMInformationReport'
];
$defaultReportBlocks = ['fsm', 'overall', 'vacuumedtables', 'details'];

$reports = [];
if(isset($options['reports'])) {
	foreach($options['reports'] AS $report) {
		if(strpos($report, '=') !== false) {
			list($outputFilePath, $blocks) = explode('=', $report);
			$outputToFiles = true;
		} elseif(strpos($report, '.') !== false) {
			$outputFilePath = $report;
			$blocks = 'default';
			$outputToFiles = true;
		} else {
			$outputFilePath = false;
			$blocks = $report;
			$outputToFiles = false;
		}
		if($blocks == 'default') {
			$selectedBlocks = $defaultReportBlocks;
			$notSupportedBlocks = [];
		} elseif($blocks == 'all') {
			$selectedBlocks = array_keys($supportedReportBlocks);
			$notSupportedBlocks = [];
		} else {
			$selectedBlocks = explode(',', $blocks);
			$notSupportedBlocks = array_diff($selectedBlocks, array_keys($supportedReportBlocks));
		}
		
		if(empty($notSupportedBlocks)) {
			$outputFilePath = checkOutputFilePath($outputFilePath);
			$reports[] = [
				'blocks' => $selectedBlocks,
				'file' => $outputFilePath
			];
		} else {
			usage('report types not supported: '.implode(',', $notSupportedBlocks));
		}
	}
} else {
	$reports[] = [
		'blocks' => $defaultReportBlocks,
		'file' => false
	];
}

$aggregator = 'HtmlReportAggregator';

$parser = 'PostgreSQLVacuumParser';

$logReader = new GenericLogReader($filePath, $parser, 'PostgreSQLVacuumAccumulator');

foreach($reports AS $report) {
	$reportAggregator = new $aggregator($logReader, $report['file']);
	foreach($report['blocks'] AS $block) {
		$reportAggregator->addReportBlock($supportedReportBlocks[$block]);
	}
	$logReader->addReportAggregator($reportAggregator);
	unset($reportAggregator);
}

$logReader->parse();
$logReader->output();

fclose($stderr);
