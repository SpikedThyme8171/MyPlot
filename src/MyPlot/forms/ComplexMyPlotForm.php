<?php
declare(strict_types=1);
namespace MyPlot\forms;

use jojoe77777\FormAPI\CustomForm;
use MyPlot\Plot;

abstract class ComplexMyPlotForm extends CustomForm implements MyPlotForm {
	/** @var Plot|null $plot */
	protected $plot;

	public function __construct(?callable $callable) {
		parent::__construct($callable);
	}

	/**
	 * @param Plot|null $plot
	 */
	public function setPlot(?Plot $plot) : void {
		$this->plot = $plot;
	}

	/**
	 * @return Plot|null
	 */
	public function getPlot() : ?Plot {
		return $this->plot;
	}
}