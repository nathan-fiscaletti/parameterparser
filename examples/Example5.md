## Index:
* [Example 1: Using ParameterParser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* Example 5: Using Error Handlers

----
### Example 5 : Using Error Handlers

#### Usage: 
    php test.php -name
#### Output:
    Invalid usage of parameter 'name'
    Usage: -name [name]
    Full Usage: php test.php -name [name]
#### Code:
```php
// Create a new ParameterCluster.
$parameters = new ParameterCluster();

// Create a new uniadic closure and associate it with the exec parameter.
$execClosure = parameter('-', 'name', function ($name) {
    return $argument;
});

// Set the description for the parameter.
$execClosure->setDescription('Displays the name passed.');

// Add the exec ParameterClosure to the ParameterCluster.
$parameters->add($execClosure);

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Set a default closer for when no prefixes are found that match
// the parameter being parsed. 
// 
// This will handle any error that is not related to a prefixed
// parameter.
$parameterParser->setDefault(function ($parameter) {
    // Always return -1 if no valid parameter is found.
    // This will invalidate the parameters.
    // 
    // After parsing, use $parameterParser->isValid()
    // to check validity.
    // 
    // By default, the default closure will always return -1 unless
    // you directly override it.
    return -1;
});

// Set the error handler closure that will be called when an error 
// is encountered while parsing a parameter that exists in the
// pre defined parameter cluster.
$parameterParser->setErrorHandler(function (ParameterClosure $parameter) {
    echo 'Invalid usage of parameter \'' . $parameter->parameterName . '\'';
    echo PHP_EOL;

    echo 'Usage: ' . $parameter->getUsage();
    echo PHP_EOL;
});

// Parse the arguments using the ParameterCluster.
$results = $parameterParser->parse();

// Validate the ParameterParser and if it's invalid, print the usage.
if (! $parameterParser->isValid()) {
    echo 'Full Usage: ' . $parameters->getFullUsage();
    echo PHP_EOL;
} else {
    print_r($results);
}
```