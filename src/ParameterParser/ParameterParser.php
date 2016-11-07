<?php

namespace ParameterParser;

class ParameterParser
{
    /**
     * The array of arguments to use.
     *
     * @var array
     */
    private $argv;

    /**
     * The prefix cluster to use for parsing arguments to closures.
     *
     * @var array
     */
    private $prefixes = null;

    /**
     * Construct the Parameter Parser using an array of arguments.
     *
     * @param array         $argv
     * @param PrefixCluster $prefixes
     */
    public function __construct($argv, PrefixCluster $prefixes = null)
    {
        $this->preloadParameters($argv);
        $this->prefixes = ($prefixes == null) ? new PrefixCluster() : $prefixes;
    }

    /**
     * Parse the parameters.
     *
     * @return array
     */
    public function parse()
    {
        $results = [];

        $i = 0;
        while ($i < count($this->argv)) {
            $parameter = $this->argv[$i];
            if ($this->prefixExists($parameter)) {
                $closure = $this->getClosure($parameter);
                $prefix = $this->getPrefix($parameter);
                $closure_arguments = [
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    ),
                ];
                $current_argument = 0;
                $argument_count = count(
                    (new \ReflectionFunction($closure))->getParameters()
                ) - 1;
                while ($current_argument < $argument_count) {
                    $closure_arguments[] = $this->argv[$i + 1];
                    $current_argument += 1;
                    $i++;
                }
                $results[
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    )
                ] = $closure(...$closure_arguments);
                $i++;
            } else {
                $results[
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    )
                ] = $this->prefixes->default->call($this, $parameter);
                $i++;
            }
        }

        return $results;
    }

    /**
     * Preloads the parameters and moves any parameters surrounded by
     * single or double quotes to their own parameter.
     *
     * @param  array $argv
     */
    private function preloadParameters($argv)
    {
        array_shift($argv);
        $this->argv = [];
        while (($argument = array_shift($argv)) != null) {
            if (substr($argument, 0, 1) == '\'') {
                $this->argv[] = substr($argument, 1);
                while (
                    ($argument_part = array_shift($argv)) != null &&
                    substr($argument_part, strlen($argument_part) - 1, 1) != '\''
                ) {
                    $this->argv[count($this->argv) - 1] .= ' '.$argument_part;
                }
                $this->argv[count($this->argv) - 1] .=
                ' '.substr($argument_part, 0, strlen($argument_part) - 1);
            } elseif (substr($argument, 0, 1) == '"') {
                $this->argv[] = substr($argument, 1);
                while (
                    ($argument_part = array_shift($argv)) != null &&
                    substr($argument_part, strlen($argument_part) - 1, 1) != '"'
                ) {
                    $this->argv[count($this->argv) - 1] .= ' '.$argument_part;
                }
                $this->argv[count($this->argv) - 1] .=
                ' '.substr($argument_part, 0, strlen($argument_part) - 1);
            } else {
                $this->argv[] = $argument;
            }
        }
    }

    /**
     * Check if the prefix is defined in the prefix cluster.
     *
     * @param string $parameter
     *
     * @return bool
     */
    private function prefixExists($parameter)
    {
        $prefixExists = false;

        foreach (array_keys($this->prefixes->prefixes) as $prefix) {
            if (substr($parameter, 0, strlen($prefix)) == $prefix) {
                $prefixExists = true;
                break;
            }
        }

        return $prefixExists;
    }

    /**
     * Attempts to find the prefix associated with the parameter.
     * If no prefix is found, null will be returned.
     *
     * @param string $parameter
     *
     * @return string
     */
    private function getPrefix($parameter)
    {
        $prefix = null;
        foreach (array_keys($this->prefixes->prefixes) as $_prefix) {
            if (substr($parameter, 0, strlen($_prefix)) == $_prefix) {
                $prefix = $_prefix;
            }
        }

        return $prefix;
    }

    /**
     * Attempts to find the closure associated with the parameter
     * based on prefix. If no prefix is found, null will be returned.
     *
     * @param string $parameter
     *
     * @return Closure
     */
    private function getClosure($parameter)
    {
        $closure = null;

        foreach (array_keys($this->prefixes->prefixes) as $prefix) {
            if (substr($parameter, 0, strlen($prefix)) == $prefix) {
                $closure = $this->prefixes->prefixes[$prefix];
            }
        }

        return $closure;
    }
}
