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

require_once('ReportAggregator.class.php');
require_once('TextReportAggregator.class.php');

include_once('geshi/geshi.php');
require_once('HtmlReportAggregator.class.php');

require_once('HtmlWithGraphsReportAggregator.class.php');

require_once('reports/Report.class.php');
require_once('reports/QueriesByTypeReport.class.php');
require_once('reports/OverallStatsReport.class.php');
require_once('reports/HourlyStatsReport.class.php');
require_once('reports/SlowestQueriesReport.class.php');
require_once('reports/NormalizedReport.class.php');
require_once('reports/NormalizedQueriesMostTimeReport.class.php');
require_once('reports/NormalizedQueriesMostFrequentReport.class.php');
require_once('reports/NormalizedQueriesSlowestAverageReport.class.php');
require_once('reports/QueriesHistoryReport.class.php');
require_once('reports/QueriesHistoryPerPidReport.class.php');
require_once('reports/TsungSessionsReport.class.php');
require_once('reports/CsvQueriesHistoryReport.class.php');

require_once('reports/NormalizedErrorsReport.class.php');
require_once('reports/NormalizedErrorsMostFrequentReport.class.php');

?>