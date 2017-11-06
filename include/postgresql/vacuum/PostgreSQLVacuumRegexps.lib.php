<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Open Wide
 * Copyright (c) 2006-2008 Guillaume Smet
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

$postgreSQLVacuumRegexps = [];

// PostgreSQLVacuumParser
$postgreSQLVacuumRegexps['VacuumingDatabase'] = new RegExp('/vacuuming database "([^".]*)"/');
$postgreSQLVacuumRegexps['VacuumingOrAnalyzingTable'] = new RegExp('/(vacuuming|analyzing) "(?:([^".]*)\.)?([^".]*)"/');
$postgreSQLVacuumRegexps['RemovableInformation'] = new RegExp('/: found ([0-9]+) removable, ([0-9]+) nonremovable row versions in ([0-9]+) pages/');
$postgreSQLVacuumRegexps['OperationInformation'] = new RegExp('/: moved ([0-9]+) row versions, truncated ([0-9]+) to ([0-9]+) pages/');
$postgreSQLVacuumRegexps['CpuDetailLine'] = new RegExp('/CPU ([0-9.]+)s\/([0-9.]+)u sec elapsed ([0-9.]+) sec\./');
$postgreSQLVacuumRegexps['VacuumDetail'] = new RegExp('/([0-9]+) dead row versions cannot be removed yet./');
$postgreSQLVacuumRegexps['IndexCleanupInformation'] = new RegExp('/index "([^".]*)" now contains ([0-9]+) row versions in ([0-9]+) pages/');
$postgreSQLVacuumRegexps['IndexCleanupDetail1'] = new RegExp('/([0-9]+) index row versions were removed./');
$postgreSQLVacuumRegexps['IndexCleanupDetail2'] = new RegExp('/([0-9]+) index pages have been deleted, ([0-9]+) are currently reusable./');

$postgreSQLVacuumRegexps['FSMInformation'] = new RegExp('/free space map contains ([0-9]+) pages in ([0-9]+) relations/');
$postgreSQLVacuumRegexps['FSMInformationDetail'] = new RegExp('/A total of ([0-9]+) page slots are in use \(including overhead\)\./');
$postgreSQLVacuumRegexps['VacuumEnd'] = new RegExp('/^VACUUM$/');

// PostgreSQLVacuumDetailLine
$postgreSQLVacuumRegexps['VacuumFullDetailLine'] = new RegExp('/([0-9]+) dead row versions cannot be removed yet\. Nonremovable row versions range from ([0-9]+) to ([0-9]+) bytes long\. There were ([0-9]+) unused item pointers\. Total free space \(including removable row versions\) is ([0-9]+) bytes\. ([0-9]+) pages are or will become empty, including ([0-9]+) at the end of the table\. ([0-9]+) pages containing ([0-9]+) free bytes are potential move destinations\. CPU ([0-9.]+)s\/([0-9.]+)u sec elapsed ([0-9.]+) sec\./');
$postgreSQLVacuumRegexps['VacuumDetailLine'] = new RegExp('/([0-9]+) dead row versions cannot be removed yet\. There were ([0-9]+) unused item pointers\. ([0-9]+) pages are entirely empty\. CPU ([0-9.]+)s\/([0-9.]+)u sec elapsed ([0-9.]+) sec\./');
$postgreSQLVacuumRegexps['FSMDetailLine'] = new RegExp('/A total of ([0-9]+) page slots are in use \(including overhead\). ([0-9]+) page slots are required to track all free space. Current limits are:  ([0-9]+) page slots, ([0-9]+) relations, using ([0-9]+) KB./i');

// PostgreSQLIndexCleanupDetailLine
$postgreSQLVacuumRegexps['IndexCleanupDetailLine'] = new RegExp('/(?:([0-9]+) index row versions were removed\. )?([0-9]+) index pages have been deleted, ([0-9]+) are currently reusable\. CPU ([0-9.]+)s\/([0-9.]+)u sec elapsed ([0-9.]+) sec\./');

$GLOBALS['postgreSQLVacuumRegexps'] =& $postgreSQLVacuumRegexps;
