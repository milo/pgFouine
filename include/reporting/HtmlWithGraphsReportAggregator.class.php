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

class HtmlWithGraphsReportAggregator extends HtmlReportAggregator {
	function __construct(& $logReader, $outputFilePath = false) {
		require_once('artichow/LinePlot.class.php');
		require_once('artichow/BarPlot.class.php');
		
		$this->HtmlReportAggregator($logReader, $outputFilePath);
	}
	
	function getHtmlOutput(& $reportBlock) {
		if(method_exists($reportBlock, 'getHtmlWithGraphs')) {
			$html = $reportBlock->getHtmlWithGraphs();
		} else {
			$html = $reportBlock->getHtml();
		}
		return $html;
	}

	function getImagePath($imageName) {
		$directory = dirname($this->outputFilePath);
		$imagePath = $directory.'/'.$this->getImageBaseName($imageName);
		return $imagePath;
	}

	function getImageBaseName($imageName) {
		$fileName = basename($this->outputFilePath);
		if(strpos($fileName, '.') > 0) {
			$baseName = substr($fileName, 0, strrpos($fileName, '.'));
		} else {
			$baseName = $fileName;
		}
		$imageBaseName = $baseName.'_'.$imageName.'.png';
		return $imageBaseName;
	}
}
