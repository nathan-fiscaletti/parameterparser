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
     *
     * @return \ParameterParser\ParameterClosure
     */
    function parameter($prefix, $parameterName, \Closure $closure)
    {
        return new \ParameterParser\ParameterClosure(
            $prefix,
            $parameterName,
            $closure
        );
    }
}
