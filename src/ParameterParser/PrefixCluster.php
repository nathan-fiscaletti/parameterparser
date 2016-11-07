<?php

namespace ParameterParser;

class PrefixCluster
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
     * Construct the Prefix Cluster and assign a default value to the
     * fall back closure.
     */
    public function __construct()
    {
        $this->default = function ($parameter) {
            trigger_error('Unknown parameter \''.$parameter.'\'', E_USER_WARNING);
        };
    }

    /**
     * Add a prefix and closure.
     *
     * @param string $prefix
     * @param Closure $closure
     */
    public function add($prefix, $closure)
    {
        $this->prefixes[$prefix] = $closure;
    }

    /**
     * Remove a prefix and closure based on prefix.
     *
     * @param  string $prefix
     */
    public function remove($prefix)
    {
        unset($this->prefixes[$prefix]);
    }

    /**
     * Add an array of prefixes and closures.
     *
     * @param array $prefixes
     */
    public function addMany($prefixes)
    {
        array_merge($this->prefixes, $prefixes);
    }

    /**
     * Set the default fall back closure.
     *
     * @param Closure $closure
     */
    public function setDefault($closure)
    {
        $this->default = $closure;
    }
}
