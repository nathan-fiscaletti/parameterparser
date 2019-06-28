<?php

namespace Parameters;

use Closure;
use ReflectionFunction;

class Parameter
{
    /**
     * The name of the parameter.
     *
     * @var string
     */
    public $name;

    /**
     * The closure to associate with the parameter.
     *
     * @var Closure
     */
    public $closure;

    /**
     * The aliases for the parameter.
     *
     * @var array
     */
    public $aliases = [];

    /**
     * The parent Parameter if this object is registered
     * as an alias Parameter object.
     *
     * @var Parameter
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
     * Construct the Parameter with a name and closure.
     *
     * @param string  $prefix
     * @param string  $name
     * @param Closure $closure
     * @param bool    $required
     */
    public function __construct(
        $prefix,
        $name,
        Closure $closure
    ) {
        $this->name = $name;
        $this->closure = $closure;
        $this->prefix = $prefix;
    }

    /**
     * Gets the usage of the Parameter as string.
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

        $usage .= $this->prefix.$this->name.$aliases.' ';

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

        $rFunction = new ReflectionFunction($this->closure);
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
                $aliases = ($aliases == '') ? ' (' : $aliases.',';
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
     * Set the description for the Parameter.
     *
     * @param string $description
     *
     * @return \Parameters\Parameter
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Add an alias and associate it with a prefix.
     * If no prefix is defined, the default for
     * the cluster will be used.
     *
     * Only one alias can exist per prefix per parameter.
     *
     * @param string $name
     * @param string $prefix
     *
     * @return \Parameters\Parameter
     */
    public function addAlias($name, $prefix = null)
    {
        if ($prefix == null) {
            $this->aliases[$this->prefix] = $name;
        } else {
            $this->aliases[$prefix] = $name;
        }

        return $this;
    }

    /**
     * Set the Required value.
     *
     * @param bool $value
     *
     * @return \Parameters\Parameter
     */
    public function setRequired(bool $value)
    {
        $this->required = $value;

        return $this;
    }

    /**
     * Return true if this object is a Parent Parameter.
     *
     * @return bool
     */
    public function isParent()
    {
        return $this->parent != null;
    }
}
