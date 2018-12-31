## Index:
* [Example 1: Using ParameterParser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* Example 5: Using Error Handlers
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* [Example 8: Printing Usage](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example8.md)

----
### Example 5 : Using Error Handlers

#### Usage: 
    php test.php -name
#### Output:
    Error -> 'Invalid argument count for parameter closure.' thrown on argument `name`.
    Usage: [-name <name>]
    
    Parameter Parser v0.0.1
    
    Description:
    
    	Using Error Handlers Example.
    
    Usage:
    
    	php test.php [-name <name>]
    
    Parameters:
    
    	Parameter      Properties      Aliases      Description                    Required
    	-name          <name>                       Displays the name passed.
#### Code:
```php
// Create a new ParameterCluster.
$parameters = new ParameterCluster();

// Create a new uniadic closure and associate it with the name parameter.
$nameClosure = parameter('-', 'name', function ($name) {
    return $name;
});

// Set the description for the parameter.
$nameClosure->setDescription('Displays the name passed.');

// Add the name ParameterClosure to the ParameterCluster.
$parameters->add($nameClosure);

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
    // The default closure will always return -1 unless
    // you directly override it.
    return -1;
});

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
    $parameters->printFullUsage(
        "Parameter Parser",
        "Using Error Handlers Example.",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```