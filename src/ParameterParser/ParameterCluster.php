<?php

namespace ParameterParser;

use Closure;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

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
     * @param string $showRequiredFirst
     * @param string $customBinary
     * @param string $customScript
     *
     * @return string
     */
    public function getUsage(
        $showRequiredFirst = true,
        $customBinary = null,
        $customScript = null
    ) {
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
            if ($showRequiredFirst) {
                usort($parameters, function ($p1, $p2) {
                    if ($p1->required && $p2->required) {
                        return 0;
                    }

                    if ($p1->required && ! $p2->required) {
                        return -1;
                    }

                    if ($p2->required && ! $p1->required) {
                        return 1;
                    }
                });
            }

            foreach ($parameters as $parameter) {
                if ($parameter->parent == null) {
                    $fullUsage .= $parameter->getUsage().' ';
                }
            }
        }

        return $fullUsage;
    }

    /**
     * Print the full usage along with each commands
     * individual usage and descripition.
     *
     * @param string $applicationName
     * @param string $description
     * @param string $applicationVersion
     * @param bool   $showRequiredFirst
     * @param string $customBinary
     * @param string $customScript
     * @param int    $columnPadding
     */
    public function printFullUsage(
        $applicationName,
        $description = null,
        $applicationVersion = null,
        $showRequiredFirst = true,
        $customBinary = null,
        $customScript = null,
        $columnPadding = 5
    ) {
        // Create Ansi Instance
        $ansi = new \Bramus\Ansi\Ansi();

        $ansi->color([SGR::COLOR_FG_BLUE_BRIGHT])
             ->bold()
             ->text(PHP_EOL.$applicationName)
             ->noStyle();

            
        $ansi->text(
            ($applicationVersion !== null ? ' '.$applicationVersion : '').PHP_EOL
        );
        echo PHP_EOL;

        if ($description != null) {
            $ansi->color([SGR::COLOR_FG_BLUE_BRIGHT])
                 ->bold()
                 ->text('Description:')
                 ->noStyle();
            echo PHP_EOL.PHP_EOL."\t".$description.PHP_EOL.PHP_EOL;
        }

        $ansi->color([SGR::COLOR_FG_BLUE_BRIGHT])
                 ->bold()
                 ->text('Usage:')
                 ->noStyle();
        echo PHP_EOL.PHP_EOL."\t".
             $this->getUsage(
                 $showRequiredFirst,
                 $customBinary,
                 $customScript
             ).PHP_EOL;
        echo PHP_EOL;

        $parameterCount = 0;
        $values = [
            'parameter' => [
                // 9 = Length of the word 'Parameter'
                'longest' => 9 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->prefix.$parameter->parameterName;
                },
            ],

            'properties' => [
                // 10 = Length of the word 'Properties'
                'longest' => 10 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->getPropertiesAsString();
                },
            ],

            'aliases' => [
                // 7 = Length of the word 'Aliases'
                'longest' => 7 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->getAliasUsage(false);
                },
            ],

            'description' => [
                // 11 = Length of the word 'Description'
                'longest' => 11 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->description;
                },
            ],

            'required' => [
                // 8 = Length of the word 'Required'
                'longest' => 8 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->required ? 'Yes' : '';
                },
            ],
        ];

        foreach ($this->prefixes as $prefix => $parameters) {
            foreach ($parameters as $parameter) {
                if ($parameter->parent === null) {
                    $parameterCount += 1;
                    foreach ($values as $mappedValueName => $mappedValue) {
                        $nVal = $mappedValue['fetch']($parameter);
                        $nValSize = strlen($nVal);
                        if (
                            $nValSize +
                            $columnPadding
                            > $values[$mappedValueName]['longest']
                        ) {
                            $values[$mappedValueName]['longest'] =
                                $nValSize +
                                $columnPadding;
                        }

                        $values[$mappedValueName]['values'][] = $nVal;
                    }
                }
            }
        }

        $ansi->color([SGR::COLOR_FG_BLUE_BRIGHT])
                 ->bold()
                 ->text('Parameters:')
                 ->noStyle();
        echo PHP_EOL.PHP_EOL;

        $headerFormat = "\t";
        $columnNames = [];

        $parameterFormat = '';
        $parameterValues = [];

        foreach ($values as $mappedValueName => $mappedValue) {
            $headerFormat .= '%-'.$mappedValue['longest'].'s ';
            $columnNames[] = ucwords($mappedValueName);
        }

        for ($i = 0; $i < $parameterCount; $i++) {
            $newFormat = "\t";
            foreach ($values as $mappedValueName => $mappedValue) {
                $newFormat .= '%-'.$mappedValue['longest'].'s ';
                $parameterValues[] = $mappedValue['values'][$i];
            }
            $newFormat .= PHP_EOL;
            $parameterFormat .= $newFormat;
        }

        vprintf($headerFormat.PHP_EOL, $columnNames);
        vprintf($parameterFormat.PHP_EOL, $parameterValues);
    }
}
