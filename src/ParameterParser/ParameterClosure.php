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
     * If set to true, the parameter will be required.
     * @var bool
     */
    public $required = false;

    /**
     * Construct the ParameterClosure with a name and closure.
     *
     * @param string  $prefix
     * @param string  $parameterName
     * @param Closure $parameterClosure
     * @param bool    $required
     */
    public function __construct(
        $prefix,
        $parameterName,
        Closure $parameterClosure,
        $required = false
    ) {
        $this->parameterName = $parameterName;
        $this->parameterClosure = $parameterClosure;
        $this->prefix = $prefix;
        $this->required = $required;
    }

    /**
     * Gets the usage of the ParameterClosure as string.
     *
     * @param bool $withEncapsulation
     * @param bool $withAliases
     *
     * @return string
     */
    public function getUsage($withEncapsulation = true, $withAliases = true)
    {
        $usage = '';
        if ($withEncapsulation) {
            $usage = ($this->required ? '' : '[');
        }
        $aliases = ($withAliases ? $this->getAliasUsage() : '');

        $usage .= $this->prefix.$this->parameterName.$aliases.' ';

        $usage .= $this->getPropertiesAsString();

        return $usage.($withEncapsulation ? ($this->required ? '' : ']') : '');
    }

    /**
     * Retrieve the properties for this parameter as a string.
     *
     * @return string
     */
    public function getPropertiesAsString()
    {
        $result = '';

        $rFunction = new ReflectionFunction($this->parameterClosure);
        if ($rFunction->isVariadic()) {
            $result .= '<'.
            $rFunction->getParameters()[0]->getName().', ...>';
        } else {
            for ($i = 0; $i < count($rFunction->getParameters()); $i++) {
                $result .= ($result == '' ? '' : ' ').'<'.
                $rFunction->getParameters()[$i]->getName().
                '>';
            }
        }

        return $result;
    }

    /**
     * Retrieve the alias usage as a String.
     *
     * @return string
     */
    public function getAliasUsage($withEncapsulation = true)
    {
        $aliases = '';

        foreach ($this->aliases as $prefix => $alias) {
            if ($withEncapsulation) {
                $aliases = ($aliases == '') ? ' (' : $aliases;
                $aliases .= ' '.$prefix.$alias;
            } else {
                $aliases = ($aliases == '') ? $prefix.$alias : $aliases.', '.$prefix.$alias;
            }
        }

        if ($withEncapsulation) {
            $aliases .= ($aliases == '') ? '' : ' )';
        }

        return $aliases;
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
     * Only one alias can exist per prefix per parameter.
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
