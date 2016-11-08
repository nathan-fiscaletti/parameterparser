<?php

namespace ParameterParser;

use \Closure;

class ParameterClosure
{
	/**
	 * The name of the parameter.
	 *
	 * @var string
	 */
	public $parameterName;

	/**
	 * The closure to associate with the parameter.
	 *
	 * @var Closure
	 */
	public $parameterClosure;

	/**
	 * Construct the ParameterClosure with a name and closure.
	 *
	 * @param string   $parameterName
	 * @param Closure  $parameterClosure
	 */
	public function __construct($parameterName, Closure $parameterClosure)
	{
		$this->parameterName = $parameterName;
		$this->parameterClosure = $parameterClosure;
	}
}