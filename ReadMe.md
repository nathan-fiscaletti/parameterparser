# Parameter Parser
> **Parameter Parser** is a simple library used to parse intricate parameters from an array of strings.

[![StyleCI](https://styleci.io/repos/73029011/shield?style=flat)](https://styleci.io/repos/73029011)
[![Latest Stable Version](https://poser.pugx.org/nafisc/parameterparser/v/stable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Total Downloads](https://poser.pugx.org/nafisc/parameterparser/downloads?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Latest Unstable Version](https://poser.pugx.org/nafisc/parameterparser/v/unstable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![License](https://poser.pugx.org/nafisc/parameterparser/license?format=flat)](https://packagist.org/packages/nafisc/parameterparser)

[Advanced Code Examples](https://github.com/nathan-fiscaletti/parameterparser/blob/master/Examples.md)

```php
// Initialize a new ParameterCluster
$parameters = new ParameterCluster();

// Add a ParameterClosure to the ParameterCluster
$parameters->add('-', new ParameterClosure('name', function ($name) {
    echo 'Your name is ' . $name . PHP_EOL;
    return $name;
}));

// Create a new parameter parser using the ParameterCluster
$parameterParser = new ParameterParser($argv, $parameters);

// Parse the parameters using the ParameterParser.
$results = $parameterParser->parse();

$name = $results['name'];
```
