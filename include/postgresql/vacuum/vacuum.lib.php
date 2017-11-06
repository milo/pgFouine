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

require_once('PostgreSQLVacuumAccumulator.class.php');
require_once('VacuumLogStream.class.php');

// regexps
include_once('PostgreSQLVacuumRegexps.lib.php');

// parser
require_once('parsers/PostgreSQLVacuumParser.class.php');

// lines
require_once('lines/PostgreSQLVacuumLogLine.class.php');
require_once('lines/PostgreSQLAnalyzingTableLine.class.php');
require_once('lines/PostgreSQLFSMInformationDetailLine.class.php');
require_once('lines/PostgreSQLFSMInformationLine.class.php');
require_once('lines/PostgreSQLVacuumContinuationLine.class.php');
require_once('lines/PostgreSQLVacuumCpuDetailLine.class.php');
require_once('lines/PostgreSQLVacuumingDatabaseLine.class.php');
require_once('lines/PostgreSQLVacuumDetailLine.class.php');
require_once('lines/PostgreSQLVacuumEndLine.class.php');
require_once('lines/PostgreSQLIndexCleanupInformationLine.class.php');
require_once('lines/PostgreSQLIndexCleanupDetailLine.class.php');
require_once('lines/PostgreSQLVacuumingTableLine.class.php');
require_once('lines/PostgreSQLVacuumOperationInformationLine.class.php');
require_once('lines/PostgreSQLVacuumRemovableInformationLine.class.php');

// log objects
require_once('objects/VacuumLogObject.class.php');
require_once('objects/AnalyzeTableLogObject.class.php');
require_once('objects/VacuumTableLogObject.class.php');
require_once('objects/FSMInformationLogObject.class.php');
require_once('objects/VacuumIndexInformation.class.php');

// listeners
require_once('listeners/VacuumedTablesListener.class.php');
require_once('listeners/FSMInformationListener.class.php');
require_once('listeners/VacuumOverallListener.class.php');

// reports
require_once('reporting/reports/VacuumedTablesReport.class.php');
require_once('reporting/reports/FSMInformationReport.class.php');
require_once('reporting/reports/VacuumOverallReport.class.php');
require_once('reporting/reports/VacuumedTablesDetailsReport.class.php');
