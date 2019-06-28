## Index:
* [Example 1: Using Parameter Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using a Cluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* Example 6: Using Required Parameters
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* [Example 8: Printing Usage](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example8.md)

----
### Example 6 : Using Required Parameters

#### Usage: 
    php test.php -call 800-123-4567
#### Output: 
    Parameters\ParseException: [60005] (parameter: name) : Missing required argument: name

    Parameter Parser v0.0.1

    Description:

        Using Required Parameters Example.

    Usage:

        php test.php -name <name> [-call <number>]

    Parameters:

        Parameter   Properties   Aliases   Description   Required
        -name       <name>                               Yes
        -call       <number>
#### Code:
```php
// Create a new Cluster.
$parameters = new Cluster();

// Create a new uniadic closure and associate it with the name parameter.
// Set it to a required parameter.
$name = parameter('-', 'name', function ($name) {
    return $name;
})->setRequired(true);

// Create a new uniadic closure and associate it with the call parameter.
// Since we are not supplying a fourth argument,
// this parameter is not required.
$call = parameter('-', 'call', function ($number) {
    return $number;
});

// Add the name and call Parameters to the Cluster.
$parameters->addMany([$name, $call]);

// Create a Parser using the Cluster.
$parser = new Parser($argv, $parameters);

// Set the error handler closure that will be called when an error 
// is encountered while parsing a parameter that exists in the
// pre defined parameter cluster. This function will also be called when
// a required parameter is not supplied.
$parser->setErrorHandler(
    function (
        ParseException $e
    ) {
        echo $e->toAnsiString();
    }
);

// Parse the arguments using the Cluster.
$results = $parser->parse();

// Validate the Parser and if it's invalid, print the usage.
// Note that required parameters will be displayed with <> where as
// optional parameters will be displayed with [].
if (! $parser->isValid()) {
    $parameters->printFullUsage(
        "Parameter Parser",
        "Using Required Parameters Example.",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```
