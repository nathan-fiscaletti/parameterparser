## Index:
* [Example 1: Using Parameter Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using a Cluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
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
    Parameters\ParseException: [60002] (parameter: name) : Invalid argument count. Expecting 1 but recieved 0.

    Parameter Parser v0.0.1

    Description:

        Using Error Handlers Example.

    Usage:

        php test.php [-name <name>]

    Parameters:

        Parameter   Properties   Aliases   Description                 Required
        -name       <name>                 Displays the name passed.
#### Code:
```php
// Create a new Cluster.
$parameters = new Cluster();

// Create a new uniadic closure and associate it with the name parameter.
$name = parameter('-', 'name', function ($name) {
    return $name;
});

// Set the description for the parameter.
$name->setDescription('Displays the name passed.');

// Add the name Parameter to the Cluster.
$parameters->add($name);

// Create a Parser using the Cluster.
$parser = new Parser($argv, $parameters);

// Set a default closer for when no prefixes are found that match
// the parameter being parsed. 
// 
// This will handle any error that is not related to a prefixed
// parameter.
$parser->setDefault(function ($parameter) {
    // Always return -1 if no valid parameter is found.
    // This will invalidate the parameters.
    // 
    // After parsing, use $parser->isValid()
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
$parser->setErrorHandler(
    function (
        ParseException $exception
    ) {
        echo $exception->toAnsiString();
    }
);

// Parse the arguments using the Cluster.
$results = $parser->parse();

// Validate the Parser and if it's invalid, print the usage.
if (! $parser->isValid()) {
    $parameters->printFullUsage(
        "Parameter Parser",
        "Using Error Handlers Example.",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```