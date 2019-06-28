<?php

namespace Parameters;

use Closure;
use ReflectionFunction;

class Parser
{
    /**
     * The constant value that is used to halt the parser.
     */
    public const HALT_PARSE = 'parameter_parser_halt_parser';

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
    private $cluster = null;

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
     * @param Cluster $prefixes
     */
    public function __construct(
        $argv = null,
        Cluster $cluster = null
    ) {
        $this->cluster = new Cluster();
        $this->initialize($argv, $cluster);
    }

    /**
     * Parse the arguments.
     *
     * @param array   $argv
     * @param Cluster $cluster
     *
     * @return array
     */
    public function parse(
        $argv = null,
        Cluster $cluster = null
    ) {
        $this->initialize($argv, $cluster);

        return $this->checkValidityAndContinueParse();
    }

    /**
     * Sets an error handler for the Parser.
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
        $this->cluster->setDefault($closure);
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
     * @return \Parameters\Parameter
     */
    public function haltedBy()
    {
        return $this->haltedBy;
    }

    /**
     * Retrieves the name of the parameter that halted the execution
     * of the parser, if any. If the parser was not halted
     * null will be returned.
     *
     * @return \Parameters\Parameter
     */
    public function haltedByName()
    {
        return ($this->haltedBy == null)
            ? null
            : $this->haltedBy->name;
    }

    /**
     * Validates the parameters passed to the initializer
     * and continues the parse if it sees fit.
     *
     * @return array
     */
    private function checkValidityAndContinueParse()
    {
        $valid = $this->validateRequiredParameters();
        if ($valid !== true) {
            $error = new ParseException(
                $valid,
                'Missing required argument: '.$valid->name,
                ParseException::MISSING_REQUIRED_ARGUMENT
            );
            if ($this->errorHandler != null) {
                $this->errorHandler->call(
                    $this,
                    $error
                );
            } else {
                throw $error;
            }
            $this->valid = false;

            return [];
        }

        return $this->parseEvery();
    }

    /**
     * Parse every element in the loaded parameters.
     *
     * @return array
     */
    private function parseEvery()
    {
        $results = [];

        $i = 0;
        while ($i < count($this->argv)) {
            $parameter = $this->argv[$i];
            if ($this->parseSingle($i, $parameter, $results) === false) {
                break;
            }
        }

        return $results;
    }

    /**
     * Parse a single parameter and increment the parser.
     *
     * If this function returns 'false', it means that
     * the parse was halted by one of the parameters.
     *
     * @param int    &$i
     * @param string  $parameter
     * @param array  &$results
     *
     * @return bool
     */
    private function parseSingle(&$i, $parameter, &$results)
    {
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

                $result_key = $this->getRealName($parameter);
                $result = @$results[$result_key];

                if (! $result instanceof Result) {
                    if ($result === self::HALT_PARSE) {
                        $this->haltedBy = $this->getParameter($parameter);
                        unset($results[$result_key]);

                        return false;
                    }
                } else {
                    if ($result->shouldHalt()) {
                        $this->haltedBy = $this->getParameter($parameter);
                        if ($result->isHaltOnly()) {
                            unset($results[$result_key]);
                        } else {
                            $results[$result_key] = $result->getValue();
                        }

                        return false;
                    }
                }
            } else {
                $this->respondDefault($i, $results, $parameter);
            }
        } else {
            $this->respondDefault($i, $results, $parameter);
        }

        return true;
    }

    /**
     * Validates the parameter list by verifying that it contains
     * all required parameters. Returns the Parameter if a parameter
     * is missing, else it will return true.
     *
     * @return mixed
     */
    private function validateRequiredParameters()
    {
        $ret = true;
        foreach ($this->cluster->prefixes as $prefix => $parameters) {
            foreach ($parameters as $parameterObj) {
                if ($parameterObj->required) {
                    if (! in_array(
                        $parameterObj
                        ->prefix.
                        $parameterObj
                        ->name,
                        $this->argv
                    )) {
                        $aliasFound = false;
                        foreach ($parameterObj->aliases as $prefix => $alias) {
                            if (in_array($prefix.$alias, $this->argv)) {
                                $aliasFound = true;
                                break;
                            }
                        }
                        if (! $aliasFound) {
                            $ret = $parameterObj;
                            break 2;
                        }
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Initialize the Parser with new data.
     *
     * @param  array   $argv
     * @param  Cluster $cluster
     */
    private function initialize($argv, $cluster)
    {
        $this->valid = true;
        $this->haltedBy = null;
        if ($cluster != null) {
            $this->cluster = $cluster;
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
        $defaultResult = $this->cluster->default->call(
            $this, $parameter
        );

        if ($defaultResult === -1) {
            $this->valid = false;
        }

        $results[$parameter] = $defaultResult;
        $i++;
    }

    /**
     * Preload alias Parameters into the system.
     */
    private function preloadAliases()
    {
        foreach (array_keys($this->cluster->prefixes) as $prefix) {
            foreach (
                $this->cluster->prefixes[$prefix] as $parameterObj
            ) {
                foreach ($parameterObj->aliases as $prefix => $alias) {
                    $aliasClosure = new Parameter(
                        $prefix,
                        $alias,
                        $parameterObj->closure
                    );
                    $aliasClosure->parent = $parameterObj;
                    $this->cluster->add(
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

        $parameterObj = $this->getParameter($parameter);
        if ($parameterObj->parent != null) {
            if (count($closure_arguments) == $argument_count) {
                $results[
                    $parameterObj->parent->name
                ] = $closure(...$closure_arguments);
            } else {
                $this->valid = false;
                $error = new ParseException(
                    $parameterObj,
                    'Invalid argument count. Expecting '.$argument_count.' but recieved '.count($closure_arguments).'.',
                    ParseException::INVALID_ARGUMENT_COUNT_ALIAS
                );
                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $error
                    );
                } else {
                    throw $error;
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
                $error = new ParseException(
                    $parameterObj,
                    'Invalid argument count. Expecting '.$argument_count.' but recieved '.count($closure_arguments).'.',
                    ParseException::INVALID_ARGUMENT_COUNT_PARAMETER
                );
                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $error
                    );
                } else {
                    throw $error;
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
        $parameterObj = $this->getParameter($parameter);
        if ($parameterObj->parent != null) {
            if (count($closure_arguments) > 0) {
                $results[
                    $parameterObj->parent->name
                ] = $closure(...$closure_arguments);
            } else {
                $this->valid = false;
                $error = new ParseException(
                    $parameterObj,
                    'Invalid argument count. Expecting 1+ but recieved '.count($closure_arguments).'.',
                    ParseException::INVALID_ARGUMENT_COUNT_VARIADIC_ALIAS
                );

                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $error
                    );
                } else {
                    throw $error;
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
                $error = new ParseException(
                    $parameterObj,
                    'Invalid argument count. Expecting 1+ but recieved '.count($closure_arguments).'.',
                    ParseException::INVALID_ARGUMENT_COUNT_VARIADIC_PARAMETER
                );

                if ($this->errorHandler != null) {
                    $this->errorHandler->call(
                        $this,
                        $error
                    );
                } else {
                    throw $error;
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
        return $this->getPrefix($parameter) != null;
    }

    /**
     * Retrieves the real name that will be displayed in
     * the results for a parameter.
     *
     * @return string
     */
    private function getRealName($param)
    {
        $parameterObj = $this->getParameter($param);
        if ($parameterObj->parent != null) {
            return $parameterObj->parent->name;
        } else {
            return $parameterObj->name;
        }
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
        $closure = $this->getParameter($parameter);

        return ! is_null($closure) ? $closure->closure : null;
    }

    /**
     * Attempts to find the prefix associated with the parameter.
     * If no prefix is found, null will be returned.
     *
     * @param string $parameter
     *
     * @return string|null
     */
    private function getPrefix($parameter)
    {
        $lastprefix = null;

        foreach (array_keys($this->cluster->prefixes) as $prefix) {
            if (substr($parameter, 0, strlen($prefix)) == $prefix) {
                if ($lastprefix == null) {
                    $lastprefix = $prefix;
                } else {
                    if (strlen($lastprefix) < strlen($prefix)) {
                        $lastprefix = $prefix;
                    }
                }
            }
        }

        return $lastprefix;
    }

    /**
     * Get the Parameter object associated with a parameter.
     * If no Parameter is found for the parameter, return null.
     *
     * @param  string $parameter
     *
     * @return Parameter
     */
    private function getParameter($parameter)
    {
        $closure = null;
        $lastprefix = null;
        $parameter_parsed = null;

        foreach (array_keys($this->cluster->prefixes) as $prefix) {
            if (substr($parameter, 0, strlen($prefix)) == $prefix) {
                $parameter_without_prefix = substr(
                    $parameter,
                    strlen($prefix),
                    strlen($parameter) - strlen($prefix)
                );

                if ($lastprefix == null) {
                    if (array_key_exists($parameter_without_prefix, $this->cluster->prefixes[$prefix])) {
                        $lastprefix = $prefix;
                        $parameter_parsed = $parameter_without_prefix;
                    }
                } else {
                    if (array_key_exists($parameter_without_prefix, $this->cluster->prefixes[$prefix])) {
                        if (strlen($lastprefix) < strlen($prefix)) {
                            $lastprefix = $prefix;
                            $parameter_parsed = $parameter_without_prefix;
                        }
                    }
                }
            }
        }

        return @$this->cluster->prefixes[$lastprefix][$parameter_parsed];
    }
}
