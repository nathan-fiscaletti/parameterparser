<?php

/*
 |--------------------------------------------------------------------------
 | Helper Functions
 |--------------------------------------------------------------------------
 */

if (! function_exists('parameter')) {
    /**
     * Creates a new ParameterClosure.
     *
     * @param  string   $prefix
     * @param  string   $parameterName
     * @param  \Closure $closure
     * @param  bool     $required
     *
     * @return \ParameterParser\ParameterClosure
     */
    function parameter(
        $prefix,
        $parameterName,
        \Closure $closure,
        $required = false
    ) {
        return new \ParameterParser\ParameterClosure(
            $prefix,
            $parameterName,
            $closure,
            $required
        );
    }
}

if (! function_exists('parameter_result')) {
    /**
     * Create a new ParameterResult object and return it.
     *
     * @param  mixed $value
     *
     * @return \ParameterParser\ParameterResult
     */
    function parameter_result($value)
    {
        return new \ParameterParser\ParameterResult($value);
    }
}

if (! function_exists('parameter_result_and_halt')) {
    /**
     * Create a new ParameterResult object and return it,
     * once this is returned the parser will be halted.
     *
     * @param  mixed $value
     *
     * @return \ParameterParser\ParameterResult
     */
    function parameter_result_and_halt($value)
    {
        return new \ParameterParser\ParameterResult($value, true);
    }
}

if (! function_exists('parameter_result_halt')) {
    /**
     * Create a new ParameterResult object that when
     * returned will halt the parser.
     *
     * @return \ParameterParser\ParameterResult
     */
    function parameter_result_halt()
    {
        return new \ParameterParser\ParameterResult(
            \ParameterParser\ParameterParser::HALT_PARSE,
            true
        );
    }
}
