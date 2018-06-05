## Index:
* [Example 1: Using ParameterParser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* Example 7: Halting the Parser

----
### Example 6 : Using Required Parameters

#### Usage: 
    php Test.php -load Test -exec 'some code'
#### Output: 
    Halted By: load
    Array
    (
        [load] => Test
    )
#### Code:
```php
// Create a new ParameterCluster.
$parameters = new ParameterCluster;

// Create out first parameter
$loadClosure = parameter('-', 'load', function ($file) {
    // This will return a value for the parameter, and 
    // will then halt the ParameterParser.
    return parameter_result_and_halt($file);
    
    // This will return no value for the parameter
    // but will halt the ParameterParser.
    // return parameter_result_halt();

    // This will simply return a value for the parameter.
    // return parameter_result($file);

    // This will also return a value for the parameter.
    // return $file;
});

// Create a second parameter
$execClosure = parameter('-', 'exec', function ($code) {
    return $code;
});

// Use the ->addMany function to add multiple closures to the ParameterCluster.
$parameters->addMany([
    $loadClosure,
    $execClosure
]);

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Parse the arguments using the ParameterCluster.
$results = $parameterParser->parse();

// Check if the ParameterParser has been halted.
if ($parameterParser->haltedBy() != null) {
    echo 'Halted By: ';
    // You can either use ->haltedBy() or ->haltedByName()
    //     ->haltedBy()     : Will return a ParameterClosure object.
    //     ->haltedByName() : Will return the name of the Parameter.
    echo $parameterParser->haltedByName() . PHP_EOL;
}

// Validate the ParameterParser and if it's invalid, print the usage.
if (! $parameterParser->isValid()) {
    echo 'Usage: ' . $parameters->getFullUsage();
    echo PHP_EOL;
} else {
    print_r($results);
}
```