<?php

class TestLogFiles extends UnitTestCase {
	var $syslogParser;
	var $logStream;
	
	function setup() {
		$this->syslogParser = new SyslogPostgreSQLParser();
		$this->logStream = new LogStream();
	}

	function getLinesFromFile($fileName) {
		$filePath = 'logs/TestLogFiles/'.$fileName;
		if(is_readable($filePath)) {
			$lines = file($filePath);
		} else {
			$lines = array();
		}
		return $lines;
	}
	
	function testDoubleDuration() {
		$textLines = $this->getLinesFromFile('test_double_duration.log');
		
		$step = 0;
		
		foreach($textLines AS $textLine) {
			$line =& $this->syslogParser->parse($textLine);
			$logObject =& $this->logStream->append($line);
			
			switch($step) {
				case 0:
					$this->checkLine($line, 'PostgreSQLDurationLine', '1199891794', '30059', '12', '1', '', '3.617465');
					$this->assertFalse($logObject);
					break;
				case 1:
					$this->checkLine($line, 'PostgreSQLQueryStartWithDurationLine', '1199891794', '30059', '13', '1', 'select', '3.617465');
					$this->assertFalse($logObject);
					break;
				case 2:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '2', " t.tid,t.title,m.name,gn.name,to_char( t.retail_reldate, 'mm-dd-yy' ) as retail_reldate,coalesce(s0c100r0.units,0) as", false);
					$this->assertFalse($logObject);
					break;
				case 3:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '3', " w0c100r0units,'NA' as w0c100r0dollars,'NA' as w0c100r0arp,coalesce(s0c1r0.units,0) as w0c1r0units,'NA' as w0c1r0dollars,'NA' as", false);
					$this->assertFalse($logObject);
					break;
				case 4:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '4', " w0c1r0arp,coalesce(s0c2r0.units,0) as w0c2r0units,coalesce(s0c2r0.dollars,0) as w0c2r0dollars,arp(s0c2r0.dollars, s0c2r0.units)", false);
					$this->assertFalse($logObject);
					break;
				case 5:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '5', " as w0c2r0arp from title t left outer join sublabel sl on t.sublabel_rel = sl.key left outer join label s on sl.lid = s.id left", false);
					$this->assertFalse($logObject);
					break;
				case 6:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '6', " outer join label d on s.did = d.id left outer join sale_200601 s0c100r0 on t.tid = s0c100r0.tid and s0c100r0.week = 200601 and", false);
					$this->assertFalse($logObject);
					break;
				case 7:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '7', " s0c100r0.channel = 100 and s0c100r0.region = 0 left outer join sale_200601 s0c1r0 on t.tid = s0c1r0.tid and s0c1r0.week =", false);
					$this->assertFalse($logObject);
					break;
				case 8:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '8', " 200601 and s0c1r0.channel = 1 and s0c1r0.region = 0 left outer join sale_200601 s0c2r0 on t.tid = s0c2r0.tid and s0c2r0.week =", false);
					$this->assertFalse($logObject);
					break;
				case 9:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '9', " 200601 and s0c2r0.channel = 2 and s0c2r0.region = 0 left outer join media m on t.media = m.key left outer join genre_n gn on", false);
					$this->assertFalse($logObject);
					break;
				case 10:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '10', " t.genre_n = gn.key where ((((upper(t.title) like '%MATRIX%' or upper(t.artist) like '%MATRIX%') ))) and t.blob in ('L', 'M',", false);
					$this->assertFalse($logObject);
					break;
				case 11:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891794', '30059', '13', '11', " 'R') and t.source_dvd != 'IN' order by t.title asc limit 100", false);
					$this->assertFalse($logObject);
					break;
				case 12:
					$this->checkLine($line, 'PostgreSQLDurationLine', '1199891794', '30059', '14', '1', '', '0.003358');
					$this->checkQueryLogObject($logObject,
						'QueryLogObject',
						1199891794,
						13,
						"select t.tid,t.title,m.name,gn.name,to_char( t.retail_reldate, 'mm-dd-yy' ) as retail_reldate,coalesce(s0c100r0.units,0) as w0c100r0units,'NA' as w0c100r0dollars,'NA' as w0c100r0arp,coalesce(s0c1r0.units,0) as w0c1r0units,'NA' as w0c1r0dollars,'NA' as w0c1r0arp,coalesce(s0c2r0.units,0) as w0c2r0units,coalesce(s0c2r0.dollars,0) as w0c2r0dollars,arp(s0c2r0.dollars, s0c2r0.units) as w0c2r0arp from title t left outer join sublabel sl on t.sublabel_rel = sl.key left outer join label s on sl.lid = s.id left outer join label d on s.did = d.id left outer join sale_200601 s0c100r0 on t.tid = s0c100r0.tid and s0c100r0.week = 200601 and s0c100r0.channel = 100 and s0c100r0.region = 0 left outer join sale_200601 s0c1r0 on t.tid = s0c1r0.tid and s0c1r0.week = 200601 and s0c1r0.channel = 1 and s0c1r0.region = 0 left outer join sale_200601 s0c2r0 on t.tid = s0c2r0.tid and s0c2r0.week = 200601 and s0c2r0.channel = 2 and s0c2r0.region = 0 left outer join media m on t.media = m.key left outer join genre_n gn on t.genre_n = gn.key where ((((upper(t.title) like '%MATRIX%' or upper(t.artist) like '%MATRIX%') ))) and t.blob in ('L', 'M', 'R') and t.source_dvd != 'IN' order by t.title asc limit 100",
						'3.617465');
					break;
				case 13:
					$this->checkLine($line, 'PostgreSQLDurationLine', '1199891796', '30059', '15', '1', '', '1.98246');
					$this->checkQueryLogObject($logObject,
						'DurationLogObject',
						1199891794,
						14,
						false,
						'0.003358');
					break;
				case 14:
					$this->checkLine($line, 'PostgreSQLQueryStartWithDurationLine', '1199891796', '30059', '16', '1', 'select sum(coalesce(s0c100r0.units,0)) as', '1.98246');
					$this->assertFalse($logObject);
					break;
				case 15:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '2', " w0c100r0units,'' as w0c100r0dollars,'' as w0c100r0arp,sum(coalesce(s0c1r0.units,0)) as w0c1r0units,'' as w0c1r0dollars,'' as", false);
					$this->assertFalse($logObject);
					break;
				case 16:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '3', " w0c1r0arp,sum(coalesce(s0c2r0.units,0)) as w0c2r0units,sum(coalesce(s0c2r0.dollars,0)) as w0c2r0dollars,NULL as w0c2r0arp from", false);
					$this->assertFalse($logObject);
					break;
				case 17:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '4', " title t left outer join sublabel sl on t.sublabel_rel = sl.key left outer join label s on sl.lid = s.id left outer join label d", false);
					$this->assertFalse($logObject);
					break;
				case 18:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '5', " on s.did = d.id left outer join sale_200601 s0c100r0 on t.tid = s0c100r0.tid and s0c100r0.week = 200601 and s0c100r0.channel =", false);
					$this->assertFalse($logObject);
					break;
				case 19:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '6', " 100 and s0c100r0.region = 0 left outer join sale_200601 s0c1r0 on t.tid = s0c1r0.tid and s0c1r0.week = 200601 and", false);
					$this->assertFalse($logObject);
					break;
				case 20:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '7', " s0c1r0.channel = 1 and s0c1r0.region = 0 left outer join sale_200601 s0c2r0 on t.tid = s0c2r0.tid and s0c2r0.week = 200601 and", false);
					$this->assertFalse($logObject);
					break;
				case 21:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '8', " s0c2r0.channel = 2 and s0c2r0.region = 0 where ((((upper(t.title) like '%MATRIX%' or upper(t.artist) like '%MATRIX%') ))) and", false);
					$this->assertFalse($logObject);
					break;
				case 22:
					$this->checkLine($line, 'PostgreSQLContinuationLine', '1199891796', '30059', '16', '9', " t.blob in ('L', 'M', 'R') and t.source_dvd != 'IN'", false);
					$this->assertFalse($logObject);
					break;
				default:
					stderrArray($line);
					break;
			}
			unset($line);
			unset($logObject);
			$step ++;
		}
	}
	
	function testCarriageReturn() {
		$textLines = $this->getLinesFromFile('test_carriage_return.log');
		
		foreach($textLines AS $textLine) {
			$line =& $this->syslogParser->parse($textLine);
			$logObject =& $this->logStream->append($line);
		}
		
		$this->checkQueryLogObject($logObject,
			'QueryLogObject',
			1199891794,
			13,
			"select t.tid,t.title,m.name,gn.name,to_char( t.retail_reldate, 'mm-dd-yy' ) as retail_reldate,coalesce(s0c100r0.units,0) as w0c100r0units,'NA' as w0c100r0dollars,'NA' as w0c100r0arp,coalesce(s0c1r0.units,0) as w0c1r0units,'NA' as w0c1r0dollars,'NA' as w0c1r0arp,coalesce(s0c2r0.units,0) as w0c2r0units,coalesce(s0c2r0.dollars,0) as w0c2r0dollars,arp(s0c2r0.dollars, s0c2r0.units) as w0c2r0arp from title t left outer join sublabel sl on t.sublabel_rel = sl.key left outer join label s on sl.lid = s.id left outer join label d on s.did = d.id left outer join sale_200601 s0c100r0 on t.tid = s0c100r0.tid and s0c100r0.week = 200601 and s0c100r0.channel = 100 and s0c100r0.region = 0 left outer join sale_200601 s0c1r0 on t.tid = s0c1r0.tid and s0c1r0.week = 200601 and s0c1r0.channel = 1 and s0c1r0.region = 0 left outer join sale_200601 s0c2r0 on t.tid = s0c2r0.tid and s0c2r0.week = 200601 and s0c2r0.channel = 2 and s0c2r0.region = 0 left outer join media m on t.media = m.key left outer join genre_n gn on t.genre_n = gn.key where ((((upper(t.title) like '%MATRIX%' or upper(t.artist) like '%MATRIX%') ))) and t.blob in ('L', 'M', 'R') and t.source_dvd != 'IN' order by t.title asc limit 100",
			'3.617465');
	}
	
	function checkLine(& $line, $type, $timestamp, $connectionId, $commandNumber, $lineNumber, $text, $duration, $ignore = false, $database = false, $user = false) {
		$this->assertIsA($line, $type);
		$this->assertEqual($timestamp, $line->getTimestamp());
		$this->assertEqual($connectionId, $line->getConnectionId());
		$this->assertEqual($commandNumber, $line->getCommandNumber());
		$this->assertEqual($lineNumber, $line->getLineNumber());
		$this->assertEqual($text, $line->getText());
		$this->assertEqual(0, bccomp($duration, $line->getDuration()));
		$this->assertEqual($ignore, $line->isIgnored());
		$this->assertEqual($database, $line->getDatabase());
		$this->assertEqual($user, $line->getUser());
	}
	
	function checkQueryLogObject(& $logObject, $type, $timestamp, $commandNumber, $text, $duration, $ignored = false, $database = false, $user = false, $context = false, $subQueries = array()) {
		$this->assertIsA($logObject, $type);
		$this->assertEqual($timestamp, $logObject->getTimestamp());
		$this->assertEqual($commandNumber, $logObject->getCommandNumber());
		$this->assertEqual($text, $logObject->getText());
		$this->assertEqual(0, bccomp($duration, $logObject->getDuration()));
		$this->assertEqual($ignored, $logObject->isIgnored());
		$this->assertEqual($database, $logObject->getDatabase());
		$this->assertEqual($user, $logObject->getUser());
		$this->assertEqual($context, $logObject->getContext());
		if($type == 'QueryLogObject') {
			$this->assertEqual($subQueries, $logObject->getSubQueries());
		}
	}
}
