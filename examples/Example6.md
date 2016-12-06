## Index:
* Example 1: Using ParameterParser
* [Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* Example 6: Using Required Parameters

----
### Example 6 : Using Required Parameters

#### Usage: 
    php test.php -call 800-123-4567
#### Output: 
    Error -> 'Missing required argument: name' thrown on argument `name`.
    Usage: -name [name]
    Full Usage: php test.php -name [name] -call [number]
#### Code:
```php
// Create a new ParameterCluster.
$parameters = new ParameterCluster();

// Create a new uniadic closure and associate it with the name parameter.
// The fourth argument which we set to 'true' here 
// means that the parameter is required.
$nameClosure = parameter('-', 'name', function ($name) {
    return $name;
}, true);

// Create a new uniadic closure and associate it with the call parameter.
// Since we are not supplying a fourth argument,
// this parameter is not required.
$callClosure = parameter('-', 'call', function ($number) {
    return $number;
});

// Add the name and call ParameterClosures to the ParameterCluster.
$parameters->addMany([$nameClosure, $callClosure]);

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Set the error handler closure that will be called when an error 
// is encountered while parsing a parameter that exists in the
// pre defined parameter cluster. This function will also be called when
// a required parameter is not supplied.
$parameterParser->setErrorHandler(
    function (
        ParameterClosure $parameter,
        $errorMessage
    ) {
        echo 'Error -> \'' . $errorMessage .
             '\' thrown on argument `' . $parameter->parameterName.'`.';
        echo PHP_EOL;

        echo 'Usage: ' . $parameter->getUsage();
        echo PHP_EOL;
    }
);

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
