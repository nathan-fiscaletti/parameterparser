<?php

/*
 |--------------------------------------------------------------------------
 | Helper Functions
 |--------------------------------------------------------------------------
 */

if (!function_exists('parameter')) {
    /**
     * Creates a new ParameterClosure.
     *
     * @param  string   $parameterName
     * @param  \Closure $closure
     *
     * @return \ParameterParser\ParameterClosure
     */
    function parameter($parameterName, \Closure $closure)
    {
        return new \ParameterParser\ParameterClosure($parameterName, $closure);
    }
}
