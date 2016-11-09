<?php

namespace ParameterParser;

use Closure;

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
     * The aliases for the parameter.
     *
     * @var array
     */
    public $aliases = [];

    /**
     * The parent ParameterClosure if this object is registered
     * as an alias ParameterClosure object.
     *
     * @var ParameterClosure
     */
    public $parent;

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

    /**
     * Add an alias and associate it with a prefix.
     *
     * @param string $prefix
     * @param string $parameterName
     */
    public function addAlias($prefix, $parameterName)
    {
        $this->aliases[$prefix] = $parameterName;
    }

    /**
     * Return true if this object is a Parent ParameterClosure.
     *
     * @return bool
     */
    public function isParent()
    {
        return $this->parent != null;
    }
}
