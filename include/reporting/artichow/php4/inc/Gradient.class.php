<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */



/**
 * Create your gradients
 *
 * @package Artichow
 */
 class awGradient {

	/**
	 * From color
	 *
	 * @var Color
	 */
	public $from;

	/**
	 * To color
	 *
	 * @var Color
	 */
	public $to;
	
	/**
	 * Build the gradient
	 *
	 * @param $from From color
	 * @param $to To color
	 */
	 function __construct($from, $to) {
	
		$this->from = $from;
		$this->to = $to;
	
	}
	
	/**
	 * Free memory used by the colors of the gradient
	 */
	 function free() {
	
		$this->from->free();
		$this->to->free();
		
	}
	
	 function php5Destructor( ){
	
		$this->free();
		
	}

}

registerClass('Gradient', TRUE);


/**
 * Create a linear gradient
 *
 * @package Artichow
 */
class awLinearGradient extends awGradient {

	/**
	 * Gradient angle
	 *
	 * @var int
	 */
	public $angle;
	
	/**
	 * Build the linear gradient
	 *
	 * @param $from From color
	 * @param $to To color
	 * @param int $angle Gradient angle
	 */
	 function __construct($from, $to, $angle) {
	
		parent::awGradient(
			$from, $to
		);
		
		$this->angle = $angle;
	
	}

}

registerClass('LinearGradient');


/**
 * Create a bilinear gradient
 *
 * @package Artichow
 */
class awBilinearGradient extends awLinearGradient {

	/**
	 * Gradient center
	 *
	 * @var int Center between 0 and 1
	 */
	public $center;
	
	/**
	 * Build the bilinear gradient
	 *
	 * @param $from From color
	 * @param $to To color
	 * @param int $angle Gradient angle
	 * @param int $center Gradient center
	 */
	 function __construct($from, $to, $angle, $center = 0.5) {
	
		parent::awLinearGradient(
			$from, $to, $angle
		);
		
		$this->center = $center;
	
	}

}

registerClass('BilinearGradient');

/**
 * Create a radial gradient
 *
 * @package Artichow
 */
class awRadialGradient extends awGradient {

}

registerClass('RadialGradient');
