<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once dirname(__FILE__)."/Component.class.php";
 
/* <php4> */

define("PLOT_LEFT", 'left');
define("PLOT_RIGHT", 'right');
define("PLOT_TOP", 'top');
define("PLOT_BOTTOM", 'bottom');
define("PLOT_BOTH", 'both');

/* </php4> */
 
/**
 * Graph using X and Y axis
 *
 * @package Artichow
 */
 class awPlot extends awComponent {
	
	/**
	 * Values for Y axis
	 *
	 * @var array
	 */
	var $datay;

	/**
	 * Values for X axis
	 *
	 * @var array
	 */
	var $datax;
	
	/**
	 * Grid properties
	 *
	 * @var Grid
	 */
	var $grid;
	
	/**
	 * X axis
	 *
	 * @var Axis
	 */
	var $xAxis;
	
	/**
	 * Y axis
	 *
	 * @var Axis
	 */
	var $yAxis;
	
	/**
	 * Position of X axis
	 *
	 * @var int
	 */
	var $xAxisPosition = PLOT_BOTTOM;
	
	/**
	 * Set X axis on zero ?
	 *
	 * @var bool
	 */
	var $xAxisZero = TRUE;
	
	/**
	 * Set Y axis on zero ?
	 *
	 * @var bool
	 */
	var $yAxisZero = FALSE;
	
	/**
	 * Position of Y axis
	 *
	 * @var int
	 */
	var $yAxisPosition = PLOT_LEFT;
	
	/**
	 * Change min value for Y axis
	 *
	 * @var mixed
	 */
	var $yMin = NULL;
	
	/**
	 * Change max value for Y axis
	 *
	 * @var mixed
	 */
	var $yMax = NULL;
	
	/**
	 * Change min value for X axis
	 *
	 * @var mixed
	 */
	var $xMin = NULL;
	
	/**
	 * Change max value for X axis
	 *
	 * @var mixed
	 */
	var $xMax = NULL;
	
	/**
	 * Left axis
	 *
	 * @var int
	 */
	
	
	/**
	 * RIGHT axis
	 *
	 * @var int
	 */
	
	
	/**
	 * Top axis
	 *
	 * @var int
	 */
	
	
	/**
	 * Bottom axis
	 *
	 * @var int
	 */
	
	
	/**
	 * Both left/right or top/bottom axis
	 *
	 * @var int
	 */
	
	
	/**
	 * Build the plot
	 *
	 */
	 function awPlot() {
	
		parent::awComponent();
		
		$this->grid = new awGrid;
		$this->grid->setBackgroundColor(new awWhite);

		$this->padding->add(20, 0, 0, 20);
		
		$this->xAxis = new awAxis;
		$this->xAxis->addTick('major', new awTick(0, 5));
		$this->xAxis->addTick('minor', new awTick(0, 3));
		$this->xAxis->setTickStyle(TICK_OUT);
		$this->xAxis->label->setFont(new awDejaVuSans(7));
		
		$this->yAxis = new awAxis;
		$this->yAxis->auto(TRUE);
		$this->yAxis->addTick('major', new awTick(0, 5));
		$this->yAxis->addTick('minor', new awTick(0, 3));
		$this->yAxis->setTickStyle(TICK_OUT);
		$this->yAxis->setNumberByTick('minor', 'major', 3);
		$this->yAxis->label->setFont(new awDejaVuSans(7));
		$this->yAxis->title->setAngle(90);
		
	}
	
	/**
	 * Get plot values
	 *
	 * @return array
	 */
	 function getValues() {
		return $this->datay;
	}
	
	/**
	 * Reduce number of values in the plot
	 *
	 * @param int $number Reduce number of values to $number
	 */
	 function reduce($number) {
		
		$count = count($this->datay);
		$ratio = ceil($count / $number);
		
		if($ratio > 1) {
		
			$tmpy = $this->datay;
			$datay = array();
			
			$datax = array();
			$cbLabel = $this->xAxis->label->getCallbackFunction();
			
			for($i = 0; $i < $count; $i += $ratio) {
			
				$slice = array_slice($tmpy, $i, $ratio);
				$datay[] = array_sum($slice) / count($slice);
				
				// Reduce data on X axis if needed
				if($cbLabel !== NULL) {
					$datax[] = $cbLabel($i + round($ratio / 2));
				}
				
			}
			
			$this->setValues($datay);
			
			if($cbLabel !== NULL) {
				$this->xAxis->setLabelText($datax);
			}
			
			
		}
		
	}
	
	/**
	 * Count values in the plot
	 *
	 * @return int
	 */
	 function getXAxisNumber() {
		list($min, $max) = $this->xAxis->getRange();
		return ($max - $min + 1);
	}
	
	/**
	 * Change X axis
	 *
	 * @param int $axis
	 */
	 function setXAxis($axis) {
		$this->xAxisPosition = $axis;
	}
	
	/**
	 * Get X axis
	 *
	 * @return int
	 */
	 function getXAxis() {
		return $this->xAxisPosition;
	}
	
	/**
	 * Set X axis on zero
	 *
	 * @param bool $zero
	 */
	 function setXAxisZero($zero) {
		$this->xAxisZero = (bool)$zero;
	}
	
	/**
	 * Set Y axis on zero
	 *
	 * @param bool $zero
	 */
	 function setYAxisZero($zero) {
		$this->yAxisZero = (bool)$zero;
	}
	
	/**
	 * Change Y axis
	 *
	 * @param int $axis
	 */
	 function setYAxis($axis) {
		$this->yAxisPosition = $axis;
	}
	
	/**
	 * Get Y axis
	 *
	 * @return int
	 */
	 function getYAxis() {
		return $this->yAxisPosition;
	}
	
	/**
	 * Change min value for Y axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setYMin($value) {
		$this->yMin = $value;
		$this->yAxis->auto(FALSE);
		$this->updateAxis();
	}
	
	/**
	 * Change max value for Y axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setYMax($value) {
		$this->yMax = $value;
		$this->yAxis->auto(FALSE);
		$this->updateAxis();
	}
	
	/**
	 * Change min value for X axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setXMin($value) {
		$this->xMin = $value;
		$this->updateAxis();
	}
	
	/**
	 * Change max value for X axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setXMax($value) {
		$this->xMax = $value;
		$this->updateAxis();
	}
	
	/**
	 * Get min value for Y axis
	 *
	 * @return float $value
	 */
	 function getYMin() {
		if($this->auto) {
			if(is_null($this->yMin)) {
				$min = array_min($this->datay);
				if($min > 0) {
					return 0;
				}
			}
		}
		return is_null($this->yMin) ? array_min($this->datay) : (float)$this->yMin;
	}
	
	/**
	 * Get max value for Y axis
	 *
	 * @return float $value
	 */
	 function getYMax() {
		if($this->auto) {
			if(is_null($this->yMax)) {
				$max = array_max($this->datay);
				if($max < 0) {
					return 0;
				}
			}
		}
		return is_null($this->yMax) ? array_max($this->datay) : (float)$this->yMax;
	}
	
	/**
	 * Get min value for X axis
	 *
	 * @return float $value
	 */
	 function getXMin() {
		return floor(is_null($this->xMin) ? array_min($this->datax) : $this->xMin);
	}
	
	/**
	 * Get max value for X axis
	 *
	 * @return float $value
	 */
	 function getXMax() {
		return (ceil(is_null($this->xMax) ? array_max($this->datax) : (float)$this->xMax)) + ($this->getXCenter() ? 1 : 0);
	}
	
	/**
	 * Get min value with spaces for Y axis
	 *
	 * @return float $value
	 */
	 function getRealYMin() {
		$min = $this->getYMin();
		if($this->space->bottom !== NULL) {
			$interval = ($this->getYMax() - $min) * $this->space->bottom / 100;
			return $min - $interval;
		} else {
			return is_null($this->yMin) ? $min : (float)$this->yMin;
		}
	}
	
	/**
	 * Get max value with spaces for Y axis
	 *
	 * @return float $value
	 */
	 function getRealYMax() {
		$max = $this->getYMax();
		if($this->space->top !== NULL) {
			$interval = ($max - $this->getYMin()) * $this->space->top / 100;
			return $max + $interval;
		} else {
			return is_null($this->yMax) ? $max : (float)$this->yMax;
		}
	}
	
	 function init($drawer) {
		
		list($x1, $y1, $x2, $y2) = $this->getPosition();
		
		// Get space informations
		list($leftSpace, $rightSpace, $topSpace, $bottomSpace) = $this->getSpace($x2 - $x1, $y2 - $y1);
		
		$this->xAxis->setPadding($leftSpace, $rightSpace);
		
		if($this->space->bottom > 0 or $this->space->top > 0) {
		
			list($min, $max) = $this->yAxis->getRange();
			$interval = $max - $min;
			
			$this->yAxis->setRange(
				$min - $interval * $this->space->bottom / 100,
				$max + $interval * $this->space->top / 100
			);
			
		}
		
		// Auto-scaling mode
		$this->yAxis->autoScale();
		
		// Number of labels is not specified
		if($this->yAxis->getLabelNumber() === NULL) {
			$number = round(($y2 - $y1) / 75) + 2;
			$this->yAxis->setLabelNumber($number);
		}
		
		$this->xAxis->line->setX($x1, $x2);
		$this->yAxis->line->setY($y2, $y1);
		
		// Set ticks
		
		$this->xAxis->ticks['major']->setNumber($this->getXAxisNumber());
		$this->yAxis->ticks['major']->setNumber($this->yAxis->getLabelNumber());
		
		
		// Center X axis on zero
		if($this->xAxisZero) {
			$this->xAxis->setYCenter($this->yAxis, 0);
		}
		
		// Center Y axis on zero
		if($this->yAxisZero) {
			$this->yAxis->setXCenter($this->xAxis, 0);
		}
		
		// Set axis labels
		$labels = array();
		for($i = 0, $count = $this->getXAxisNumber(); $i < $count; $i++) {
			$labels[] = $i;
		}
		$this->xAxis->label->set($labels);
	
		parent::init($drawer);
		
		list($x1, $y1, $x2, $y2) = $this->getPosition();
		
		list($leftSpace, $rightSpace) = $this->getSpace($x2 - $x1, $y2 - $y1);
		
		// Create the grid
		$this->createGrid();
	
		// Draw the grid
		$this->grid->setSpace($leftSpace, $rightSpace, 0, 0);
		$this->grid->draw($drawer, $x1, $y1, $x2, $y2);
		
	}
	
	 function drawEnvelope($drawer) {
		
		list($x1, $y1, $x2, $y2) = $this->getPosition();
		
		if($this->getXCenter()) {
			$size = $this->xAxis->getDistance(0, 1);
			$this->xAxis->label->move($size / 2, 0);
			$this->xAxis->label->hideLast(TRUE);
		}
		
		// Draw top axis
		if($this->xAxisPosition === PLOT_TOP or $this->xAxisPosition === PLOT_BOTH) {
			$top = $this->xAxis;
			if($this->xAxisZero === FALSE) {
				$top->line->setY($y1, $y1);
			}
			$top->label->setAlign(NULL, LABEL_TOP);
			$top->label->move(0, -3);
			$top->title->move(0, -25);
			$top->draw($drawer);
		}
		
		// Draw bottom axis
		if($this->xAxisPosition === PLOT_BOTTOM or $this->xAxisPosition === PLOT_BOTH) {
			$bottom = $this->xAxis;
			if($this->xAxisZero === FALSE) {
				$bottom->line->setY($y2, $y2);
			}
			$bottom->label->setAlign(NULL, LABEL_BOTTOM);
			$bottom->label->move(0, 3);
			$bottom->reverseTickStyle();
			$bottom->title->move(0, 25);
			$bottom->draw($drawer);
		}
		
		// Draw left axis
		if($this->yAxisPosition === PLOT_LEFT or $this->yAxisPosition === PLOT_BOTH) {
			$left = $this->yAxis;
			if($this->yAxisZero === FALSE) {
				$left->line->setX($x1, $x1);
			}
			$left->label->setAlign(LABEL_RIGHT);
			$left->label->move(-6, 0);
			$left->title->move(-25, 0);
			$left->draw($drawer);
		}
		
		// Draw right axis
		if($this->yAxisPosition === PLOT_RIGHT or $this->yAxisPosition === PLOT_BOTH) {
			$right = $this->yAxis;
			if($this->yAxisZero === FALSE) {
				$right->line->setX($x2, $x2);
			}
			$right->label->setAlign(LABEL_LEFT);
			$right->label->move(6, 0);
			$right->reverseTickStyle();
			$right->title->move(25, 0);
			$right->draw($drawer);
		}
	
	}
	
	 function createGrid() {
		
		$max = $this->getRealYMax();
		$min = $this->getRealYMin();

		$number = $this->yAxis->getLabelNumber() - 1;
		
		if($number < 1) {
			return;
		}
		
		// Horizontal lines of the grid
		
		$h = array();
		for($i = 0; $i <= $number; $i++) {
			$h[] = $i / $number;
		}
		
		// Vertical lines
	
		$major = $this->yAxis->tick('major');
		$interval = $major->getInterval();
		$number = $this->getXAxisNumber() - 1;
		
		$w = array();
		
		if($number > 0) {
			
			for($i = 0; $i <= $number; $i++) {
				if($i%$interval === 0) {
					$w[] = $i / $number;
				}
			}
			
		}
	
		$this->grid->setGrid($w, $h);
	
	}
	
	/**
	 * Change values of Y axis
	 * This method ignores not numeric values
	 *
	 * @param array $datay
	 * @param array $datax
	 */
	 function setValues($datay, $datax = NULL) {
	
		$this->checkArray($datay);
		
		foreach($datay as $key => $value) {
			unset($datay[$key]);
			$datay[(int)$key] = $value;
		}
		
		if($datax === NULL) {
			$datax = array();
			for($i = 0; $i < count($datay); $i++) {
				$datax[] = $i;
			}
		} else {
			foreach($datax as $key => $value) {
				unset($datax[$key]);
				$datax[(int)$key] = $value;
			}
		}
		
		$this->checkArray($datax);
		
		if(count($datay) === count($datax)) {
		
			// Set values
			$this->datay = $datay;
			$this->datax = $datax;
			// Update axis with the new awvalues
			$this->updateAxis();
		} else {
			trigger_error("Plots must have the same number of X and Y points", E_USER_ERROR);
		}
		
	}
	
	/**
	 * Return begin and end values
	 *
	 * @return array
	 */
	 function getLimit() {
	
		$i = 0;
		while(array_key_exists($i, $this->datay) and $this->datay[$i] === NULL) {
			$i++;
		}
		$start = $i;
		$i = count($this->datay) - 1;
		while(array_key_exists($i, $this->datay) and $this->datay[$i] === NULL) {
			$i--;
		}
		$stop = $i;
		
		return array($start, $stop);
		
	}
	
	/**
	 * Return TRUE if labels must be centered on X axis, FALSE otherwise
	 *
	 * @return bool
	 */
	  
	
	 function updateAxis() {
	
		$this->xAxis->setRange(
			$this->getXMin(),
			$this->getXMax()
		);
		$this->yAxis->setRange(
			$this->getRealYMin(),
			$this->getRealYMax()
		);
		
	}
	
	 function checkArray(&$array) {
	
		if(is_array($array) === FALSE) {
			trigger_error("You tried to set a value that is not an array", E_USER_ERROR);
		}
		
		foreach($array as $key => $value) {
			if(is_numeric($value) === FALSE and is_null($value) === FALSE) {
				trigger_error("Expected numeric values for the plot", E_USER_ERROR);
			}
		}
		
		if(count($array) < 1) {
			trigger_error("Your plot must have at least 1 value", E_USER_ERROR);
		}
	
	}

}

registerClass('Plot', TRUE);

class awPlotAxis {

	/**
	 * Left axis
	 *
	 * @var Axis
	 */
	var $left;

	/**
	 * Right axis
	 *
	 * @var Axis
	 */
	var $right;

	/**
	 * Top axis
	 *
	 * @var Axis
	 */
	var $top;

	/**
	 * Bottom axis
	 *
	 * @var Axis
	 */
	var $bottom;

	/**
	 * Build the group of axis
	 */
	 function awPlotAxis() {
	
		$this->left = new awAxis;
		$this->left->auto(TRUE);
		$this->left->label->setAlign(LABEL_RIGHT);
		$this->left->label->move(-6, 0);
		$this->yAxis($this->left);
		$this->left->setTickStyle(TICK_OUT);
		$this->left->title->move(-25, 0);
		
		$this->right = new awAxis;
		$this->right->auto(TRUE);
		$this->right->label->setAlign(LABEL_LEFT);
		$this->right->label->move(6, 0);
		$this->yAxis($this->right);
		$this->right->setTickStyle(TICK_IN);
		$this->right->title->move(25, 0);
		
		$this->top = new awAxis;
		$this->top->label->setAlign(NULL, LABEL_TOP);
		$this->top->label->move(0, -3);
		$this->xAxis($this->top);
		$this->top->setTickStyle(TICK_OUT);
		$this->top->title->move(0, -25);
		
		$this->bottom = new awAxis;
		$this->bottom->label->setAlign(NULL, LABEL_BOTTOM);
		$this->bottom->label->move(0, 3);
		$this->xAxis($this->bottom);
		$this->bottom->setTickStyle(TICK_IN);
		$this->bottom->title->move(0, 25);
	
	}
	
	 function xAxis(&$axis) {
	
		$axis->addTick('major', new awTick(0, 5));
		$axis->addTick('minor', new awTick(0, 3));
		$axis->label->setFont(new awDejaVuSans(7));
		
	}
	
	 function yAxis(&$axis) {
	
		$axis->addTick('major', new awTick(0, 5));
		$axis->addTick('minor', new awTick(0, 3));
		$axis->setNumberByTick('minor', 'major', 3);
		$axis->label->setFont(new awDejaVuSans(7));
		$axis->title->setAngle(90);
		
	}

}

registerClass('PlotAxis');

/**
 * A graph with axis can contain some groups of components
 *
 * @package Artichow
 */
class awPlotGroup extends awComponentGroup {
	
	/**
	 * Grid properties
	 *
	 * @var Grid
	 */
	var $grid;
	
	/**
	 * Left, right, top and bottom axis
	 *
	 * @var PlotAxis
	 */
	var $axis;
	
	/**
	 * Set the X axis on zero
	 *
	 * @var bool
	 */
	var $xAxisZero = TRUE;
	
	/**
	 * Set the Y axis on zero
	 *
	 * @var bool
	 */
	var $yAxisZero = FALSE;
	
	/**
	 * Real axis used for Y axis
	 *
	 * @var string
	 */
	var $yRealAxis = PLOT_LEFT;
	
	/**
	 * Real axis used for X axis
	 *
	 * @var string
	 */
	var $xRealAxis = PLOT_BOTTOM;
	
	/**
	 * Change min value for Y axis
	 *
	 * @var mixed
	 */
	var $yMin = NULL;
	
	/**
	 * Change max value for Y axis
	 *
	 * @var mixed
	 */
	var $yMax = NULL;
	
	/**
	 * Change min value for X axis
	 *
	 * @var mixed
	 */
	var $xMin = NULL;
	
	/**
	 * Change max value for X axis
	 *
	 * @var mixed
	 */
	var $xMax = NULL;
	
	/**
	 * Build the PlotGroup
	 *
	 */
	 function awPlotGroup() {
	
		parent::awComponentGroup();
		
		$this->grid = new awGrid;
		$this->grid->setBackgroundColor(new awWhite);
		
		$this->axis = new awPlotAxis;
		
	}
	
	/**
	 * Set the X axis on zero or not
	 *
	 * @param bool $zero
	 */
	 function setXAxisZero($zero) {
		$this->xAxisZero = (bool)$zero;
	}
	
	/**
	 * Set the Y axis on zero or not
	 *
	 * @param bool $zero
	 */
	 function setYAxisZero($zero) {
		$this->yAxisZero = (bool)$zero;
	}
	
	/**
	 * Change min value for Y axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setYMin($value) {
		$this->axis->left->auto(FALSE);
		$this->axis->right->auto(FALSE);
		$this->yMin = $value;
	}
	
	/**
	 * Change max value for Y axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setYMax($value) {
		$this->axis->left->auto(FALSE);
		$this->axis->right->auto(FALSE);
		$this->yMax = $value;
	}
	
	/**
	 * Change min value for X axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setXMin($value) {
		$this->xMin = $value;
	}
	
	/**
	 * Change max value for X axis
	 * Set NULL for auto selection.
	 *
	 * @param float $value
	 */
	 function setXMax($value) {
		$this->xMax = $value;
	}
	
	/**
	 * Get min value for X axis
	 *
	 * @return float $value
	 */
	 function getXMin() {
		
		return $this->getX('min');
		
	}
	
	/**
	 * Get max value for X axis
	 *
	 * @return float $value
	 */
	 function getXMax() {
	
		return $this->getX('max');
		
	}
	
	 function getX($type) {
	
		switch($type) {
			case 'max' :
				if($this->xMax !== NULL) {
					return $this->xMax;
				}
				break;
			case 'min' :
				if($this->xMin !== NULL) {
					return $this->xMin;
				}
				break;
		}
		
		$value = NULL;
		$get = 'getX'.ucfirst($type);
		
		for($i = 0; $i < count($this->components); $i++) {
		
			$component = $this->components[$i];
		
			if($value === NULL) {
				$value = $component->$get();
			} else {
				$value = $type($value, $component->$get());
			}
			
		}
		
		return $value;
	
	}
	
	/**
	 * Get min value with spaces for Y axis
	 *
	 * @param string $axis Axis name
	 * @return float $value
	 */
	 function getRealYMin($axis = NULL) {
	
		if($axis === NULL) {
			return NULL;
		}
		
		$min = $this->getRealY('min', $axis);
		$max = $this->getRealY('max', $axis);
		
		if($this->space->bottom !== NULL) {
			$interval = ($min - $max) * $this->space->bottom / 100;
			return $min + $interval;
		} else {
			return $min;
		}
		
	}
	
	/**
	 * Get max value with spaces for Y axis
	 *
	 * @param string $axis Axis name
	 * @return float $value
	 */
	 function getRealYMax($axis = NULL) {
	
		if($axis === NULL) {
			return NULL;
		}
		
		$min = $this->getRealY('min', $axis);
		$max = $this->getRealY('max', $axis);
		
		if($this->space->top !== NULL) {
			$interval = ($max - $min) * $this->space->top / 100;
			return $max + $interval;
		} else {
			return $max;
		}
		
	}
	
	 function getRealY($type, $axis) {
	
		switch($type) {
			case 'max' :
				if($this->yMax !== NULL) {
					return $this->yMax;
				}
				break;
			case 'min' :
				if($this->yMin !== NULL) {
					return $this->yMin;
				}
				break;
		}
		
		$value = NULL;
		$get = 'getY'.ucfirst($type);
		
		for($i = 0; $i < count($this->components); $i++) {
		
			$component = $this->components[$i];
			
			switch($axis) {
			
				case PLOT_LEFT :
				case PLOT_RIGHT :
					$test = ($component->getYAxis() === $axis);
					break;
				default :
					$test = FALSE;
			
			}
			
			if($test) {
				if($value === NULL) {
					$value = $component->$get();
				} else {
					$value = $type($value, $component->$get());
				}
			}
			
		}
		
		return $value;
	
	}
	
	 function init($drawer) {
		
		list($x1, $y1, $x2, $y2) = $this->getPosition();
		
		// Get PlotGroup space
		list($leftSpace, $rightSpace, $topSpace, $bottomSpace) = $this->getSpace($x2 - $x1, $y2 - $y1);
		
		// Count values in the group
		$values = $this->getXAxisNumber();
		
		// Init the PlotGroup
		$this->axis->top->line->setX($x1, $x2);
		$this->axis->bottom->line->setX($x1, $x2);
		$this->axis->left->line->setY($y2, $y1);
		$this->axis->right->line->setY($y2, $y1);
		
		$this->axis->top->setPadding($leftSpace, $rightSpace);
		$this->axis->bottom->setPadding($leftSpace, $rightSpace);
		
		$xMin = $this->getXMin();
		$xMax = $this->getXMax();
		
		$this->axis->top->setRange($xMin, $xMax);
		$this->axis->bottom->setRange($xMin, $xMax);
		
		for($i = 0; $i < count($this->components); $i++) {
		
			
			$component = &$this->components[$i];
			
			$component->auto($this->auto);
			
			// Copy space to the component
			
			$component->setSpace($this->space->left, $this->space->right, $this->space->top, $this->space->bottom);
			
			$component->xAxis->setPadding($leftSpace, $rightSpace);
			$component->xAxis->line->setX($x1, $x2);
			
			$component->yAxis->line->setY($y2, $y1);
			
		}
		
		// Set Y axis range
		foreach(array('left', 'right') as $axis) {
		
			if($this->isAxisUsed($axis)) {
			
				$min = $this->getRealYMin($axis);
				$max = $this->getRealYMax($axis);
				
				$interval = $max - $min;
				
				$this->axis->{$axis}->setRange(
					$min - $interval * $this->space->bottom / 100,
					$max + $interval * $this->space->top / 100
				);
		
				// Auto-scaling mode
				$this->axis->{$axis}->autoScale();
				
			}
			
		}
		
		if($this->axis->left->getLabelNumber() === NULL) {
			$number = round(($y2 - $y1) / 75) + 2;
			$this->axis->left->setLabelNumber($number);
		}
		
		if($this->axis->right->getLabelNumber() === NULL) {
			$number = round(($y2 - $y1) / 75) + 2;
			$this->axis->right->setLabelNumber($number);
		}
		
		// Center labels on X axis if needed
		$test = array(PLOT_TOP => FALSE, PLOT_BOTTOM => FALSE);
		
		for($i = 0; $i < count($this->components); $i++) {
		
			
			$component = &$this->components[$i];
			
			
			if($component->getValues() !== NULL) {
				
				$axis = $component->getXAxis();
				
				if($test[$axis] === FALSE) {
		
					// Center labels for bar plots
					if($component->getXCenter()) {
						$size = $this->axis->{$axis}->getDistance(0, 1);
						$this->axis->{$axis}->label->move($size / 2, 0);
						$this->axis->{$axis}->label->hideLast(TRUE);
						$test[$axis] = TRUE;
					}
					
				}
				
			}
			
			
		}
		
		// Set axis labels
		$labels = array();
		for($i = $xMin; $i <= $xMax; $i++) {
			$labels[] = $i;
		}
		if($this->axis->top->label->count() === 0) {
			$this->axis->top->label->set($labels);
		}
		if($this->axis->bottom->label->count() === 0) {
			$this->axis->bottom->label->set($labels);
		}
		
		// Set ticks
		
		$this->axis->top->ticks['major']->setNumber($values);
		$this->axis->bottom->ticks['major']->setNumber($values);
		$this->axis->left->ticks['major']->setNumber($this->axis->left->getLabelNumber());
		$this->axis->right->ticks['major']->setNumber($this->axis->right->getLabelNumber());
		
		
		// Set X axis on zero
		if($this->xAxisZero) {
			$axis = $this->selectYAxis();
			$this->axis->bottom->setYCenter($axis, 0);
			$this->axis->top->setYCenter($axis, 0);
		}
		
		// Set Y axis on zero
		if($this->yAxisZero) {
			$axis = $this->selectXAxis();
			$this->axis->left->setXCenter($axis, 1);
			$this->axis->right->setXCenter($axis, 1);
		}
		
		parent::init($drawer);
		
		list($leftSpace, $rightSpace, $topSpace, $bottomSpace) = $this->getSpace($x2 - $x1, $y2 - $y1);
		
		// Create the grid
		$this->createGrid();
	
		// Draw the grid
		$this->grid->setSpace($leftSpace, $rightSpace, 0, 0);
		$this->grid->draw($drawer, $x1, $y1, $x2, $y2);
		
	}
	
	 function drawComponent($drawer, $x1, $y1, $x2, $y2, $aliasing) {
		
		$xMin = $this->getXMin();
		$xMax = $this->getXMax();
	
		$maxLeft = $this->getRealYMax(PLOT_LEFT);
		$maxRight = $this->getRealYMax(PLOT_RIGHT);
		
		$minLeft = $this->getRealYMin(PLOT_LEFT);
		$minRight = $this->getRealYMin(PLOT_RIGHT);
	
		foreach($this->components as $component) {
		
			$min = $component->getYMin();
			$max = $component->getYMax();
			
			// Set component minimum and maximum
			if($component->getYAxis() === PLOT_LEFT) {
			
				list($min, $max) = $this->axis->left->getRange();
			
				$component->setYMin($min);
				$component->setYMax($max);
				
			} else {
			
				list($min, $max) = $this->axis->right->getRange();
				
				$component->setYMin($min);
				$component->setYMax($max);
				
			}
			
			$component->setXAxisZero($this->xAxisZero);
			$component->setYAxisZero($this->yAxisZero);
			
			$component->xAxis->setRange($xMin, $xMax);
		
			$component->drawComponent(
				$drawer,
				$x1, $y1,
				$x2, $y2,
				$aliasing
			);
			
			$component->setYMin($min);
			$component->setYMax($max);
			
		}
		
	}
	
	 function drawEnvelope($drawer) {
		
		list($x1, $y1, $x2, $y2) = $this->getPosition();
		
		// Hide unused axis
		foreach(array(PLOT_LEFT, PLOT_RIGHT, PLOT_TOP, PLOT_BOTTOM) as $axis) {
			if($this->isAxisUsed($axis) === FALSE) {
				$this->axis->{$axis}->hide(TRUE);
			}
		}
		
		// Draw top axis
		$top = $this->axis->top;
		if($this->xAxisZero === FALSE) {
			$top->line->setY($y1, $y1);
		}
		$top->draw($drawer);
		
		// Draw bottom axis
		$bottom = $this->axis->bottom;
		if($this->xAxisZero === FALSE) {
			$bottom->line->setY($y2, $y2);
		}
		$bottom->draw($drawer);
		
		// Draw left axis
		$left = $this->axis->left;
		if($this->yAxisZero === FALSE) {
			$left->line->setX($x1, $x1);
		}
		$left->draw($drawer);
		
		// Draw right axis
		$right = $this->axis->right;
		if($this->yAxisZero === FALSE) {
			$right->line->setX($x2, $x2);
		}
		$right->draw($drawer);
	
	}
	
	/**
	 * Is the specified axis used ?
	 *
	 * @param string $axis Axis name
	 * @return bool
	 */
	 function isAxisUsed($axis) {
	
		for($i = 0; $i < count($this->components); $i++) {
		
			$component = $this->components[$i];
			
			switch($axis) {
			
				case PLOT_LEFT :
				case PLOT_RIGHT :
					if($component->getYAxis() === $axis) {
						return TRUE;
					}
					break;
			
				case PLOT_TOP :
				case PLOT_BOTTOM :
					if($component->getXAxis() === $axis) {
						return TRUE;
					}
					break;
			
			}
			
		}
		
		return FALSE;
	
	}
	
	 function createGrid() {
		
		$max = $this->getRealYMax(PLOT_LEFT);
		$min = $this->getRealYMin(PLOT_RIGHT);
		
		// Select axis (left if possible, right otherwise)
		$axis = $this->selectYAxis();
	
		$number = $axis->getLabelNumber() - 1;
		
		if($number < 1) {
			return;
		}
		
		// Horizontal lines of grid
		
		$h = array();
		for($i = 0; $i <= $number; $i++) {
			$h[] = $i / $number;
		}
		
		// Vertical lines
	
		$major = $axis->tick('major');
		$interval = $major->getInterval();
		$number = $this->getXAxisNumber() - 1;
		
		$w = array();
		
		if($number > 0) {
			
			for($i = 0; $i <= $number; $i++) {
				if($i%$interval === 0) {
					$w[] = $i / $number;
				}
			}
			
		}
	
		$this->grid->setGrid($w, $h);
	
	}
	
	 function selectYAxis(){
		
		// Select axis (left if possible, right otherwise)
		if($this->isAxisUsed(PLOT_LEFT)) {
			$axis = $this->axis->left;
		} else {
			$axis = $this->axis->right;
		}
		
		return $axis;
		
	}
	
	 function selectXAxis(){
		
		// Select axis (bottom if possible, top otherwise)
		if($this->isAxisUsed(PLOT_BOTTOM)) {
			$axis = $this->axis->bottom;
		} else {
			$axis = $this->axis->top;
		}
		
		return $axis;
		
	}
	
	 function getXAxisNumber() {
		$offset = $this->components[0];
		$max = $offset->getXAxisNumber();
		for($i = 1; $i < count($this->components); $i++) {
			$offset = $this->components[$i];
			$max = max($max, $offset->getXAxisNumber());
		}
		return $max;
	}

}

registerClass('PlotGroup');
?>