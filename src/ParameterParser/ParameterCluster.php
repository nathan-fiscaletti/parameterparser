<?php

namespace ParameterParser;

use Closure;

class ParameterCluster
{
    /**
     * The prefixes and the closures associated therewith.
     *
     * @var array
     */
    public $prefixes = [];

    /**
     * The default closure to fall back on when no prefix is found
     * for the argument being parsed.
     *
     * @var Closure
     */
    public $default = null;

    /**
     * Construct the Parameter Cluster and assign a default value to the
     * fall back closure.
     */
    public function __construct()
    {
        $this->default = function ($parameter) {
            return -1;
        };
    }

    /**
     * Add a parameter closure.
     *
     * @param ParameterClosure $closure
     *
     * @return ParameterCluster
     */
    public function add(ParameterClosure $closure)
    {
        $this->prefixes[$closure->prefix][$closure->parameterName] = $closure;

        return $this;
    }

    /**
     * Remove a parameter closure based on prefix and parameter name.
     *
     * @param  string $prefix
     * @param  string $parameterName
     *
     * @return ParameterCluster
     */
    public function remove($prefix, $parameterName)
    {
        unset($this->prefixes[$prefix][$parameterName]);

        return $this;
    }

    /**
     * Add an array of parameters.
     *
     * @param string $prefix
     * @param array  $parameters
     *
     * @return ParameterCluster
     */
    public function addMany($parameters)
    {
        foreach ($parameters as $parameter) {
            $this->prefixes[$parameter->prefix][
                $parameter->parameterName
            ] = $parameter;
        }

        return $this;
    }

    /**
     * Set the default fall back closure.
     *
     * @param Closure $closure
     *
     * @return ParameterCluster
     */
    public function setDefault(Closure $closure)
    {
        $this->default = $closure;

        return $this;
    }

    /**
     * Retrieves the full usage of the ParameterCluster as a string.
     *
     * @param string $customBinary
     * @param string $customScript
     *
     * @return string
     */
    public function getFullUsage($customBinary = null, $customScript = null)
    {
        $fullUsage = '';

        if ($customBinary == null) {
            $fullUsage = 'php ';
        } else {
            $fullUsage = $customBinary.' ';
        }

        if ($customScript == null) {
            $fullUsage .= basename($_SERVER['SCRIPT_NAME']).' ';
        } else {
            $fullUsage .= $customScript.' ';
        }

        foreach ($this->prefixes as $prefix => $parameters) {
            foreach ($parameters as $parameter) {
                if ($parameter->parent == null) {
                    $fullUsage .= $parameter->getUsage().' ';
                }
            }
        }

        return $fullUsage;
    }
}
