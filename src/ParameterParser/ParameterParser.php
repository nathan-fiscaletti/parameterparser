<?php

namespace ParameterParser;

use \ReflectionFunction;
use \Closure;

class ParameterParser
{
    /**
     * The array of arguments to use.
     *
     * @var array
     */
    private $argv;

    /**
     * The parameter cluster to use for parsing arguments to closures.
     *
     * @var array
     */
    private $parameterCluster = null;

    /**
     * Construct the Parameter Parser using an array of arguments.
     *
     * @param array         $argv
     * @param ParameterCluster $prefixes
     */
    public function __construct($argv, ParameterCluster $parameterCluster = null)
    {
        $this->preloadParameters($argv);
        $this->parameterCluster = new ParameterCluster();
        if ($parameterCluster != null) {
            $this->parameterCluster = $parameterCluster;
        }
    }

    /**
     * Parse the arguments.
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
                if ($closure != null) {
                    $prefix = $this->getPrefix($parameter);
                    $closure_arguments = [];
                    $rFunction = new ReflectionFunction($closure);
                    if ($rFunction->isVariadic()) {
                        $this->parseVariadicParameter(
                            $i,
                            $results,
                            $closure,
                            $closure_arguments,
                            $prefix,
                            $parameter
                        );
                    } else {
                        $this->parseUniadicParameter(
                            $i,
                            $results,
                            $closure,
                            $closure_arguments,
                            $prefix,
                            $parameter,
                            $rFunction
                        );
                    }
                } else {
                    $this->respondDefault($i, $results, $parameter);
                }
            } else {
                $this->respondDefault($i, $results, $parameter);
            }
        }

        return $results;
    }

    /**
     * Sets the default closure.
     *
     * @param Closure $closure
     */
    public function setDefault(Closure $closure)
    {
        $this->parameterCluster->setDefault($closure);
    }

    /**
     * Parses the parameter with the default closure and increments
     * the parameter parser.
     *
     * @param  int    &$i
     * @param  array  &$results
     * @param  string $parameter
     */
    private function respondDefault(&$i, &$results, $parameter)
    {
        $results['default'] = $this->parameterCluster->default->call(
            $this, $parameter
        );
        $i++;
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
            switch (substr($argument, 0, 1)) {
                case '\'': {
                    $this->parseQuote($argv, $argument, '\'');
                    break;
                }

                case '"': {
                    $this->parseQuote($argv, $argument, '"');
                    break;
                }

                default: {
                    $this->argv[] = $argument;
                }
            }
        }
    }

    /**
     * Parse all parameters between two matching single or double quotes
     * to a single element in the parameter array.
     *
     * @param  array  &$argv
     * @param  string $argument
     * @param  string $quoteType
     */
    private function parseQuote(&$argv, $argument, $quoteType)
    {
        if (substr($argument, strlen($argument) - 1, 1) !== $quoteType) {
            $this->argv[] = substr($argument, 1);
            while (
                ($argument_part = array_shift($argv)) != null
                && substr(
                    $argument_part,
                    strlen($argument_part) - 1,
                    1
                ) !== $quoteType
            ) {
                $this->argv[count($this->argv) - 1] .= ' '.$argument_part;
            }
            $this->argv[count($this->argv) - 1] .=
            ' '.substr($argument_part, 0, strlen($argument_part) - 1);
        } else {
            $this->argv[] = substr(
                substr(
                    $argument,
                    1
                ),
                0,
                strlen($argument) - 2
            );
        }
    }

    /**
     * Parse a parameter belonging to a prefix that has a non-variadic
     * (or uniadic) structure in it's closure definition and increment
     * the parameter parser.
     *
     * @param  int                &$i
     * @param  array              &$results
     * @param  Closure            $closure
     * @param  array              &$closure_arguments
     * @param  string             $prefix
     * @param  string             $parameter
     * @param  ReflectionFunction $rFunction
     */
    private function parseUniadicParameter(
        &$i,
        &$results,
        $closure,
        &$closure_arguments,
        $prefix,
        $parameter,
        $rFunction
    ) {
        $current_argument = 0;
        $argument_count = count($rFunction->getParameters());
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
    }

    /**
     * Parse a parameter belonging to a prefix that has a variadic
     * structure in it's closure definition and increment the
     * parameter parser.
     *
     * @param  int                &$i
     * @param  array              &$results
     * @param  Closure            $closure
     * @param  array              &$closure_arguments
     * @param  string             $prefix
     * @param  string             $parameter
     */
    private function parseVariadicParameter(
        &$i,
        &$results,
        $closure,
        &$closure_arguments,
        $prefix,
        $parameter
    ) {
        $i++;
        while (
            isset($this->argv[$i]) &&
            ($argument = $this->argv[$i]) != null &&
            ! $this->prefixExists($argument)
        ) {
            $closure_arguments[] = $argument;
            $i++;
        }
        $results[
            substr(
                $parameter,
                strlen($prefix),
                strlen($parameter) - strlen($prefix)
            )
        ] = $closure(...$closure_arguments);
    }

    /**
     * Check if the prefix is defined in the parameter cluster.
     *
     * @param string $parameter
     *
     * @return bool
     */
    private function prefixExists($parameter)
    {
        $prefixExists = false;

        foreach (array_keys($this->parameterCluster->prefixes) as $prefix) {
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

        foreach (array_keys($this->parameterCluster->prefixes) as $_prefix) {
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

        foreach (array_keys($this->parameterCluster->prefixes) as $prefix) {
            if (substr($parameter, 0, strlen($prefix)) == $prefix) {
                @$closure = $this->parameterCluster->prefixes[$prefix][
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    )
                ];
            }
        }

        return $closure;
    }
}
