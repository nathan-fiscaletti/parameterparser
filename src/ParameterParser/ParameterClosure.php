<?php

namespace ParameterParser;

use Closure;
use ReflectionFunction;

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
     * The description of the parameter.
     *
     * @var string
     */
    public $description;

    /**
     * The prefix to associate with this parameter.
     *
     * @var string
     */
    public $prefix;

    /**
     * Construct the ParameterClosure with a name and closure.
     *
     * @param string  $prefix
     * @param string  $parameterName
     * @param Closure $parameterClosure
     */
    public function __construct($prefix, $parameterName, Closure $parameterClosure)
    {
        $this->parameterName = $parameterName;
        $this->parameterClosure = $parameterClosure;
        $this->prefix = $prefix;
    }

    /**
     * Gets the usage of the ParameterClosure as string.
     *
     * @return string
     */
    public function getUsage()
    {
        $usage = '';

        $rFunction = new ReflectionFunction($this->parameterClosure);
        if ($rFunction->isVariadic()) {
            $usage = $this->prefix.$this->parameterName.
            ' ['.$rFunction->getParameters()[0]->getName().'...]';
        } else {
            $usage = $this->prefix.$this->parameterName;
            for ($i = 0; $i < count($rFunction->getParameters()); $i++) {
                $usage .= ' ['.
                $rFunction->getParameters()[$i]->getName().']';
            }
        }

        return $usage;
    }

    /**
     * Set the description for the ParameterParser.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Add an alias and associate it with a prefix.
     * If no prefix is defined, the default for
     * the cluster will be used.
     *
     * @param string $parameterName
     * @param string $prefix
     */
    public function addAlias($parameterName, $prefix = null)
    {
        if ($prefix == null) {
            $this->aliases[$this->prefix] = $parameterName;
        } else {
            $this->aliases[$prefix] = $parameterName;
        }
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
