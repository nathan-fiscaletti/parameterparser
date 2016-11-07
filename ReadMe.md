# Parameter Parser
> **Parameter Parser** is a simple library used to parse intricate parameters from an array of strings.

[![StyleCI](https://styleci.io/repos/73029011/shield?style=flat)](https://styleci.io/repos/73029011)
[![Latest Stable Version](https://poser.pugx.org/nafisc/parameterparser/v/stable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Total Downloads](https://poser.pugx.org/nafisc/parameterparser/downloads?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Latest Unstable Version](https://poser.pugx.org/nafisc/parameterparser/v/unstable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![License](https://poser.pugx.org/nafisc/parameterparser/license?format=flat)](https://packagist.org/packages/nafisc/parameterparser)

[More Code Examples](https://github.com/nathan-fiscaletti/parameterparser/blob/master/Examples.md)

```php
// Create a new prefix cluster to use for parsing the arguments.
// We will only be using the default closure of the cluster
// for this example.
$prefixes = new \ParameterParser\PrefixCluster;

// Set the default closure of the prefix cluster.
// In this example, we will just have two parameters that can be set.
$prefixes->setDefault(function ($parameter) {
    switch($parameter) {
        case 'color' : {
            echo 'Color has been enabled.';
            break;
        }

        case 'silent' : {
            echo 'Silent mode has been enabled.';
            break;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
        }
    }

    echo PHP_EOL;
});

// Parse the parameters using the parameter parser.
(new \ParameterParser\ParameterParser($argv, $prefixes))->parse();
```
