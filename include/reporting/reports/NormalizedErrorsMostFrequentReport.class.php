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

class NormalizedErrorsMostFrequentReport extends NormalizedErrorsReport {
	function NormalizedErrorsMostFrequentReport(& $reportAggregator) {
		$this->NormalizedErrorsReport($reportAggregator, 'Most frequent errors');
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		$text = '';
		
		$errors =& $listener->getMostFrequentErrors();
		
		$count = count($errors);
		
		for($i = 0; $i < $count; $i++) {
			$error =& $errors[$i];
			
			$text .= ($i+1).') '.$this->formatInteger($error->getTimesExecuted()).' - '.$error->getNormalizedText()."\n";
			if($error->isTextAStatement()) {
				$text .= 'Error: '.$error->getError()."\n";
			}
			if($error->getDetail()) {
				$text .= 'Detail: '.$error->getDetail()."\n";
			}
			if($error->getHint()) {
				$text .= 'Hint: '.$error->getHint()."\n";
			}
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		$errors =& $listener->getMostFrequentErrors();
		$count = count($errors);
		
		if($count == 0) {
			$html = '<p>No error found</p>';
		} else {
			$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Times reported</th>
		<th>Error</th>
	</tr>';
		
			for($i = 0; $i < $count; $i++) {
				$error =& $errors[$i];
				if($error->isTextAStatement()) {
					$errorText = $error->getError();
				} else {
					$errorText = $error->getNormalizedText();
				}
				
				$html .= '<tr class="'.$this->getRowStyle($i).'">
					<td class="center top">'.($i+1).'</td>
					<td class="relevantInformation top center"><div class="tooltipLink"><span class="information">'.$this->formatInteger($error->getTimesExecuted()).'</span>'.$this->getHourlyStatisticsTooltip($error).'</div></td>
					<td><div class="error">Error: '.$error->getError().'</div>';
				if($error->getDetail() || $error->getHint()) {
					$html .= '<div class="errorInformation">';
					if($error->getDetail()) {
						$html .= 'Detail: '.$error->getDetail();
						$html .= '<br />';
					}
					if($error->getHint()) {
						$html .= 'Hint: '.$error->getHint();
						$html .= '<br />';
					}
					$html .= '</div>';
				}
				if($error->isTextAStatement()) {
					$html .= $this->highlightSql($error->getNormalizedText());
				}
				$html .= $this->getNormalizedErrorWithExamplesHtml($i, $error).'</td>
				</tr>';
				$html .= "\n";
			}
			$html .= '</table>';
		}
		return $html;
	}
}
