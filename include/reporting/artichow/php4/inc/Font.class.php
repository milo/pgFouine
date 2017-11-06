<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

 
/**
 * Built-in PHP fonts
 *
 * @package Artichow
 */
class awFont {
	
	/**
	 * Used font
	 * 
	 * @param int $font
	 */
	var $font;
	
	/**
	 * Build the font
	 *
	 * @param int $font Font identifier
	 */
	 function awFont($font) {
	
		$this->font = $font;
	
	}
	
	/**
	 * Draw a text
	 *
	 * @param $drawer
	 * @param $p Draw text at this point
	 * @param &$text The text
	 */
	 function draw($drawer, $p, &$text) {
	
		$angle = $text->getAngle();
	
		if($angle !== 90 and $angle !== 0) {
			trigger_error("You can only use 0° and 90°", E_USER_ERROR);
		}
		
		if($angle === 90) {
			$function = 'imagestringup';
		} else {
			$function = 'imagestring';
		}
		
		if($angle === 90) {
			$add = $this->getTextHeight($text);
		} else {
			$add = 0;
		}
	
		$color = $text->getColor();
		$rgb = $color->getColor($drawer->resource);
		
		$lines = explode("\n", $text->getText());
		for($i = 0; $i < count($lines); $i++) {
			$function(
				$drawer->resource,
				$this->font,
				$drawer->x + $p->x,
				$drawer->y + $p->y + $add + $i * $this->getLineHeight($text),
				$lines[$i],
				$rgb
			);
		}
	}
	
	/**
	 * Get the width of a string
	 *
	 * @param &$text A string
	 */
	 function getTextWidth(&$text) {
	
		if($text->getAngle() === 90) {
			$text->setAngle(45);
			return $this->getTextHeight($text);
		} else if($text->getAngle() === 45) {
			$text->setAngle(90);
		}
		
		$font = $text->getFont();
		$fontWidth = imagefontwidth($font->font);
		
		if($fontWidth === FALSE) {
			trigger_error("Unable to get font size", E_USER_ERROR);
		}
		
		$lines = explode("\n", $text->getText());
		/* this is the correct algorithm but I consider only the first line for my needs
		$textLength = 0;
		for($i = 0; $i < count($lines); $i++) {
			$textLength = max($textLength, strlen($lines[$i]));
		}*/
		$textLength = strlen($lines[0]);
		
		return (int)$fontWidth * $textLength;
	
	}
	
	/**
	 * Get the height of a line
	 *
	 * @param &$text A string
	 */
	 function getLineHeight(&$text) {
	
		if($text->getAngle() === 90) {
			$text->setAngle(45);
			return $this->getTextWidth($text);
		} else if($text->getAngle() === 45) {
			$text->setAngle(90);
		}
		
		$font = $text->getFont();
		$fontHeight = imagefontheight($font->font);
		
		if($fontHeight === FALSE) {
			trigger_error("Unable to get font size", E_USER_ERROR);
		}
		
		return (int)$fontHeight;

	}
	
	/**
	 * Get the height of a string
	 *
	 * @param &$text A string
	 */
	 function getTextHeight(&$text) {
		$lines = explode("\n", $text->getText());
		$lineCount = count($lines);
		
		return $this->getLineHeight($text) * $lineCount;
	}

}

registerClass('Font');

/**
 * TTF fonts
 *
 * @package Artichow
 */
class awTTFFont extends awFont {

	/**
	 * Font size
	 *
	 * @var int
	 */
	var $size;

	/**
	 * Font file
	 *
	 * @param string $font Font file
	 * @param int $size Font size
	 */
	 function awTTFFont($font, $size) {
	
		parent::awFont($font);
		
		$this->size = (int)$size;
	
	}
	
	/**
	 * Draw a text
	 *
	 * @param $drawer
	 * @param $p Draw text at this point
	 * @param &$text The text
	 */
	 function draw($drawer, $p, &$text) {
	
		// Make easier font positionment
		$text->setText($text->getText()." ");
	
		$color = $text->getColor();
		$rgb = $color->getColor($drawer->resource);
		
		$box = imagettfbbox($this->size, $text->getAngle(), $this->font, $text->getText());
		
		$height =  - $box[5];
		
		$box = imagettfbbox($this->size, 90, $this->font, $text->getText());
		$width = abs($box[6] - $box[2]);
	
		// Restore old text
		$text->setText(substr($text->getText(), 0, strlen($text->getText()) - 1));
		
		imagettftext(
			$drawer->resource,
			$this->size,
			$text->getAngle(),
			$drawer->x + $p->x + $width  * sin($text->getAngle() / 180 * M_PI),
			$drawer->y + $p->y + $height,
			$rgb,
			$this->font,
			$text->getText()
		);
		
	}
	
	/**
	 * Get the width of a string
	 *
	 * @param &$text A string
	 */
	 function getTextWidth(&$text) {
		
		$box = imagettfbbox($this->size, $text->getAngle(), $this->font, $text->getText());
		
		if($box === FALSE) {
			trigger_error("Unable to get font size", E_USER_ERROR);
			return;
		}
		
		list(, , $x2, $y2, , , $x1, $y1) = $box;
		
		return abs($x2 - $x1);
	
	}
	
	/**
	 * Get the height of a string
	 *
	 * @param &$text A string
	 */
	 function getTextHeight(&$text) {
		
		$box = imagettfbbox($this->size, $text->getAngle(), $this->font, $text->getText());
		
		if($box === FALSE) {
			trigger_error("Unable to get font size", E_USER_ERROR);
			return;
		}
		
		list(, , $x2, $y2, , , $x1, $y1) = $box;
		
		return abs($y2 - $y1);

	}

}

registerClass('TTFFont');



$php = '';

for($i = 1; $i <= 5; $i++) {

	$php .= '
	class awFont'.$i.' extends awFont {
	
		function awFont'.$i.'() {
			parent::awFont('.$i.');
		}
	
	}
	';
	
	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.'Font'.$i.' extends awFont'.$i.' {
		}
		';
	}

}

eval($php);

$php = '';

foreach($fonts as $font) {

	$php .= '
	class aw'.$font.' extends awTTFFont {
	
		function aw'.$font.'($size) {
			parent::awTTFFont(\''.(ARTICHOW_FONT.DIRECTORY_SEPARATOR.$font.'.ttf').'\', $size);
		}
	
	}
	';
	
	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.$font.' extends aw'.$font.' {
		}
		';
	}

}

eval($php);



?>
