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
    function parameter($prefix, $parameterName, \Closure $closure, $required = false)
    {
        return new \ParameterParser\ParameterClosure(
            $prefix,
            $parameterName,
            $closure,
            $required
        );
    }
}
