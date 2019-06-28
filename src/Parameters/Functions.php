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
     * @param  string   $name
     * @param  \Closure $closure
     * @param  bool     $required
     *
     * @return \Parameters\Parameter
     */
    function parameter(
        $prefix,
        $name,
        \Closure $closure
    ) {
        return new \Parameters\Parameter(
            $prefix,
            $name,
            $closure
        );
    }
}

if (! function_exists('parameter_result')) {
    /**
     * Create a new Result object and return it.
     *
     * @param  mixed $value
     *
     * @return \Parameters\Result
     */
    function parameter_result($value)
    {
        return new \Parameters\Result($value);
    }
}

if (! function_exists('parameter_result_and_halt')) {
    /**
     * Create a new Result object and return it,
     * once this is returned the parser will be halted.
     *
     * @param  mixed $value
     *
     * @return \Parameters\Result
     */
    function parameter_result_and_halt($value)
    {
        return new \Parameters\Result($value, true);
    }
}

if (! function_exists('parameter_result_halt')) {
    /**
     * Create a new Result object that when
     * returned will halt the parser.
     *
     * @return \Parameters\Result
     */
    function parameter_result_halt()
    {
        return new \Parameters\Result(
            \Parameters\Parser::HALT_PARSE,
            true
        );
    }
}
