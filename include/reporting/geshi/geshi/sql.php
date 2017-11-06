<?php
/*************************************************************************************
 * sql.php
 * -------
 * Author: Nigel McNie (nigel@geshi.org)
 * Copyright: (c) 2004 Nigel McNie (http://qbnz.com/highlighter)
 * Release Version: 1.0.8.3
 * Date Started: 2004/06/04
 *
 * SQL language file for GeSHi.
 *
 * CHANGES
 * -------
 * 2008/05/23 (1.0.7.22)
 *  -  Added additional symbols for highlighting
 * 2004/11/27 (1.0.3)
 *  -  Added support for multiple object splitters
 * 2004/10/27 (1.0.2)
 *  -  Added "`" string delimiter
 *  -  Added "#" single comment starter
 * 2004/08/05 (1.0.1)
 *  -  Added support for symbols
 *  -  Added many more keywords (mostly MYSQL keywords)
 * 2004/07/14 (1.0.0)
 *  -  First Release
 *
 * TODO (updated 2004/11/27)
 * -------------------------
 * * Add all keywords
 * * Split this to several sql files - mysql-sql, ansi-sql etc
 *
 *************************************************************************************
 *
 *     This file is part of GeSHi.
 *
 *   GeSHi is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   GeSHi is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with GeSHi; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 ************************************************************************************/

$language_data = [
    'LANG_NAME' => 'SQL',
    'COMMENT_SINGLE' => [1 =>'--', 2 => '#'],
    'COMMENT_MULTI' => ['/*' => '*/'],
    'CASE_KEYWORDS' => 0,
    'QUOTEMARKS' => ["'", '"', '`'],
    'ESCAPE_CHAR' => '\\',
    'KEYWORDS' => [
        1 => [
            'ALL', 'ASC', 'AS',  'ALTER', 'AND', 'ADD', 'AUTO_INCREMENT', 'ANY', 'ANALYZE',
            'BETWEEN', 'BINARY', 'BOTH', 'BY', 'BOOLEAN', 'BEGIN',
            'CHANGE', 'CHECK', 'COLUMNS', 'COLUMN', 'CROSS','CREATE', 'CASE', 'COMMIT', 'COALESCE', 'CLUSTER', 'COPY',
            'DATABASES', 'DATABASE', 'DATA', 'DELAYED', 'DESCRIBE', 'DESC',  'DISTINCT', 'DELETE', 'DROP', 'DEFAULT',
            'ENCLOSED', 'ESCAPED', 'EXISTS', 'EXPLAIN', 'ELSE', 'END', 'EXCEPT',
            'FIELDS', 'FIELD', 'FLUSH', 'FOR', 'FOREIGN', 'FUNCTION', 'FROM',
            'GROUP', 'GRANT', 'GREATEST',
            'HAVING',
            'IGNORE', 'INDEX', 'INFILE', 'INSERT', 'INNER', 'INTO', 'IDENTIFIED', 'IN', 'IS', 'IF', 'INTERSECT',
            'JOIN',
            'KEYS', 'KILL','KEY',
            'LEADING', 'LIKE', 'LIMIT', 'LINES', 'LOAD', 'LOCAL', 'LOCK', 'LOW_PRIORITY', 'LEFT', 'LANGUAGE', 'LEAST',
            'MODIFY',
            'NATURAL', 'NOT', 'NULL', 'NEXTVAL', 'NULLIF',
            'OPTIMIZE', 'OPTION', 'OPTIONALLY', 'ORDER', 'OUTFILE', 'OR', 'OUTER', 'ON', 'OVERLAPS',
            'PROCEDURE','PROCEDURAL', 'PRIMARY',
            'READ', 'REFERENCES', 'REGEXP', 'RENAME', 'REPLACE', 'RETURN', 'REVOKE', 'RLIKE', 'RIGHT', 'ROLLBACK',
            'SHOW', 'SONAME', 'STATUS', 'STRAIGHT_JOIN', 'SELECT', 'SETVAL', 'SET', 'SOME', 'SEQUENCE',
            'TABLES', 'TEMINATED', 'TO', 'TRAILING','TRUNCATE', 'TABLE', 'TEMPORARY', 'TRIGGER', 'TRUSTED', 'THEN',
            'UNIQUE', 'UNLOCK', 'USE', 'USING', 'UPDATE', 'UNSIGNED',
            'VALUES', 'VARIABLES', 'VIEW', 'VACUUM',
            'WITH', 'WRITE', 'WHERE', 'WHEN',
            'ZEROFILL',
            'XOR',
            ],
        2 => [
            'ascii', 'age',
            'bit_length', 'btrim',
            'char_length', 'character_length', 'convert', 'chr', 'current_date', 'current_time', 'current_timestamp', 'count',
            'decode', 'date_part', 'date_trunc', 
            'encode', 'extract',
            'get_byte', 'get_bit',
            'initcap', 'isfinite', 'interval',
            'justify_hours', 'justify_days',
            'lower', 'length', 'lpad', 'ltrim', 'localtime', 'localtimestamp',
            'md5',
            'now',
            'octet_length', 'overlay',
            'position', 'pg_client_encoding',
            'quote_ident', 'quote_literal',
            'repeat', 'replace', 'rpad', 'rtrim',
            'substring', 'split_part', 'strpos', 'substr', 'set_byte', 'set_bit',
            'trim', 'to_ascii', 'to_hex', 'translate', 'to_char', 'to_date', 'to_timestamp', 'to_number', 'timeofday',
            'upper',
            ],
        3 => [
            'STDIN', 'STDOUT'
            ],
        ],
    'SYMBOLS' => [
        '(', ')', '=', '<', '>', '|', ',', '.', '+', '-', '*', '/', '!='
        ],
    'CASE_SENSITIVE' => [
        GESHI_COMMENTS => false,
        1 => false,
        2 => false,
        3 => false,
        ],
    'STYLES' => [
        'KEYWORDS' => [
            1 => 'color: #993333; font-weight: bold; text-transform: uppercase;',
            2 => 'color: #993333; font-style: italic;',
            3 => 'color: #993333; text-transform: uppercase;'
            ],
        'COMMENTS' => [
            1 => 'color: #808080; font-style: italic;',
            2 => 'color: #808080; font-style: italic;',
            'MULTI' => 'color: #808080; font-style: italic;'
            ],
        'ESCAPE_CHAR' => [
            0 => 'color: #000099; font-weight: bold;'
            ],
        'BRACKETS' => [
            0 => 'color: #66cc66;'
            ],
        'STRINGS' => [
            0 => 'color: #ff0000;'
            ],
        'NUMBERS' => [
            0 => 'color: #cc66cc;'
            ],
        'METHODS' => [
            ],
        'SYMBOLS' => [
            0 => 'color: #000000;'
            ],
        'SCRIPT' => [
            ],
        'REGEXPS' => [
            ]
        ],
    'URLS' => [
        1 => ''
        ],
    'OOLANG' => false,
    'OBJECT_SPLITTERS' => [
        ],
    'REGEXPS' => [
        ],
    'STRICT_MODE_APPLIES' => GESHI_NEVER,
    'SCRIPT_DELIMITERS' => [
        ],
    'HIGHLIGHT_STRICT_BLOCK' => [
        ]
];
