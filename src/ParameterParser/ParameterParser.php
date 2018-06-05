<?php

namespace ParameterParser;

use Closure;
use ReflectionFunction;

class ParameterParser
{
    /**
     * The constant value that is used to halt the parser.
     */
    public const HALT_PARSE = "parameter_parser_halt_parser";

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
     * The validity of the parsed parameters.
     *
     * @var bool
     */
    private $valid = true;

    /**
     * The error handler closure.
     *
     * @var Closure
     */
    private $errorHandler;

    /**
     * If a parameter halts the execution fot he parser,
     * it will be stored here.
     *
     * @var string
     */
    private $haltedBy = null;

    /**
     * Construct the Parameter Parser using an array of arguments.
     *
     * @param array            $argv
     * @param ParameterCluster $prefixes
     */
    public function __construct(
        $argv = null,
        ParameterCluster $parameterCluster = null
    ) {
        $this->parameterCluster = new ParameterCluster();
        $this->errorHandler = function (
            ParameterClosure $parameterClosure,
            $errorMessage
        ) {
            // Empty Error Handler
        };
        $this->initialize($argv, $parameterCluster);
    }

    /**
     * Parse the arguments.
     *
     * @param array            $argv
     * @param ParameterCluster $parameterCluster
     *
     * @return array
     */
    public function parse(
        $argv = null,
        ParameterCluster $parameterCluster = null
    ) {
        $results = [];

        $this->initialize($argv, $parameterCluster);

        $valid = $this->validateRequiredParameters();
        if ($valid !== true) {
            $this->errorHandler->call(
                $this,
                $valid,
                'Missing required argument: '.$valid->parameterName
            );
            $this->valid = false;

            return;
        }

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

                    $result_key = substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    );
                    $result = $results[$result_key];

                    if (! $result instanceof ParameterResult) {
                        if ($result == ParameterParser::HALT_PARSE) {
                            $this->haltedBy = $this->getParameterClosure($parameter);
                            unset($results[$result_key]);
                            break;
                        }
                    } else {
                        if ($result->shouldHalt()) {
                            $this->haltedBy = $this->getParameterClosure($parameter);
                            if ($result->isHaltOnly()) {
                                unset($results[$result_key]);
                            } else {
                                $results[$result_key] = $result->getValue();    
                            }
                            break;
                        }
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
     * Sets an error handler for the ParameterParser.
     *
     * @param Closure $closure
     */
    public function setErrorHandler(Closure $closure)
    {
        $this->errorHandler = $closure;
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
     * Check if the parsed parameters are valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Retrieves the parameter that halted the execution
     * of the parser, if any. If the parser was not halted
     * null will be returned.
     *
     * @return \ParameterParser\ParameterClosure
     */
    public function haltedBy()
    {
        return $this->haltedBy;
    }

     /**
     * Retrieves name of the parameter that halted the execution
     * of the parser, if any. If the parser was not halted
     * null will be returned.
     *
     * @return \ParameterParser\ParameterClosure
     */
    public function haltedByName()
    {
        return ($this->haltedBy == null)
            ? null
            : $this->haltedBy->parameterName;
    }

    /**
     * Validates the parameter list by verifying that it contains
     * all required parameters. Returns the ParameterClosure if a parameter
     * is missing, else it will return true.
     *
     * @return mixed
     */
    private function validateRequiredParameters()
    {
        $ret = true;
        foreach ($this->parameterCluster->prefixes as $prefix => $parameters) {
            foreach ($parameters as $parameterClosure) {
                if ($parameterClosure->required) {
                    if (! in_array(
                        $parameterClosure
                        ->prefix.
                        $parameterClosure
                        ->parameterName,
                        $this->argv
                    )) {
                        $aliasFound = false;
                        foreach ($parameterClosure->aliases as $prefix => $alias) {
                            if (in_array($prefix.$alias, $this->argv)) {
                                $aliasFound = true;
                                break;
                            }
                        }
                        if (! $aliasFound) {
                            $ret = $parameterClosure;
                            break 2;
                        }
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Initialize the ParameterParser with new data.
     *
     * @param  array            $argv
     * @param  ParameterCluster $parameterCluster
     */
    private function initialize($argv, $parameterCluster)
    {
        $this->valid = true;
        $this->haltedBy = null;
        if ($parameterCluster != null) {
            $this->parameterCluster = $parameterCluster;
            if ($argv != null) {
                $this->preloadAliases($argv);
            }
        }

        if ($argv != null) {
            $this->preloadParameters($argv);
        }
    }

    /**
     * Parses the parameter with the default closure and increments
     * the parameter parser. If '-1' is returned by the default
     * closure, the parameters will be invalidated.
     *
     * @param  int    &$i
     * @param  array  &$results
     * @param  string $parameter
     */
    private function respondDefault(&$i, &$results, $parameter)
    {
        $defaultResult = $this->parameterCluster->default->call(
            $this, $parameter
        );

        if ($defaultResult === -1) {
            $this->valid = false;
        }

        $results[$parameter] = $defaultResult;
        $i++;
    }

    /**
     * Preload alias ParameterClosures into the system.
     */
    private function preloadAliases()
    {
        foreach (array_keys($this->parameterCluster->prefixes) as $prefix) {
            foreach (
                $this->parameterCluster->prefixes[$prefix] as $parameterClosure
            ) {
                foreach ($parameterClosure->aliases as $prefix => $alias) {
                    $aliasClosure = new ParameterClosure(
                        $prefix,
                        $alias,
                        $parameterClosure->parameterClosure
                    );
                    $aliasClosure->parent = $parameterClosure;
                    $this->parameterCluster->add(
                        $aliasClosure
                    );
                }
            }
        }
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
        while (
            $current_argument < $argument_count &&
            count($this->argv) > ($i + 1)
        ) {
            $closure_arguments[] = $this->argv[$i + 1];
            $current_argument += 1;
            $i++;
        }
        $parameterClosure = $this->getParameterClosure($parameter);
        if ($parameterClosure->parent != null) {
            if (count($closure_arguments) == $argument_count) {
                $results[
                    $parameterClosure->parent->parameterName
                ] = $closure(...$closure_arguments);
            } else {
                $this->valid = false;
                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $parameterClosure,
                        'Invalid argument count for parameter closure.'
                    );
                }
            }
        } else {
            if (count($closure_arguments) == $argument_count) {
                $results[
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    )
                ] = $closure(...$closure_arguments);
            } else {
                $this->valid = false;
                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $parameterClosure,
                        'Invalid argument count for parameter closure.'
                    );
                }
            }
        }
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
        $parameterClosure = $this->getParameterClosure($parameter);
        if ($parameterClosure->parent != null) {
            if (count($closure_arguments) > 0) {
                $results[
                    $parameterClosure->parent->parameterName
                ] = $closure(...$closure_arguments);
            } else {
                $this->valid = false;
                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $parameterClosure,
                        'Missing argument for parameter closure.'
                    );
                }
            }
        } else {
            if (count($closure_arguments) > 0) {
                $results[
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    )
                ] = $closure(...$closure_arguments);
            } else {
                $this->valid = false;
                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $parameterClosure,
                        'Missing argument for parameter closure.'
                    );
                }
            }
        }
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
                ]->parameterClosure;
            }
        }

        return $closure;
    }

    /**
     * Get the ParameterClosure object associated with a parameter.
     * If no ParameterClosure is found for the parameter, return null.
     *
     * @param  string $parameter
     *
     * @return ParameterClosure
     */
    private function getParameterClosure($parameter)
    {
        $parameterClosure = null;

        foreach (array_keys($this->parameterCluster->prefixes) as $prefix) {
            if (substr($parameter, 0, strlen($prefix)) == $prefix) {
                @$parameterClosure = $this->parameterCluster->prefixes[$prefix][
                    substr(
                        $parameter,
                        strlen($prefix),
                        strlen($parameter) - strlen($prefix)
                    )
                ];
            }
        }

        return $parameterClosure;
    }
}
