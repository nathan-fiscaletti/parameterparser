## Index:
* [Example 1: Using Parameter Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using a Cluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* Example 3: Using Variadic Closures (...)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* [Example 8: Printing Usage](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example8.md)

----
### Example 3 : Using a Cluster and the splat operator `...` (aka. Variadic Closures)

#### Usage: 
    php test.php -load 'Main Library.so' File2.so +configurewith 'Main Library.so' -exec 'Pre Load.sh' Initialize.sh start.sh
#### Output:
    Array
    (
        [load] => Array
            (
                [0] => Main Library.so
                [1] => File2.so
            )

        [configurewith] => Main Library.so

        [exec] => Array
            (
                [0] => Pre Load.sh
                [1] => Initialize.sh
                [2] => start.sh
            )

    )
#### Code:
```php
// Create a new Cluster.
$parameters = new Cluster;

// Create a new variadic closures for the load and exec parameters using
// the splat operator (...). This will allow the closure to take all
// parameters between this initial 'load' parameter and the next 
// prefixed parameter in the parameter list.
$loadClosure = parameter('-', 'load', function (...$arguments) {
    return $arguments;
});

$execClosure = parameter('-', 'exec', function (...$arguments) {
    return $arguments;
});

// Create a new uniadic closure with regular parameter.
// This will make any parameter passed to the associated closure only take
// the next parameter (or the next few, depending on the number of arguments
// in the closure definition) as (an) argument(s), and not care where the next 
// prefix is in the list of arguments.
$configureWithClosure = parameter('+', 'configurewith', function ($file) {
    return $file;
});


// Use the ->addMany function to add multiple closures to the Cluster.
$parameters->addMany([
    $loadClosure,
    $execClosure,
    $configureWithClosure
]);

// Create a Parser using the Cluster.
$parser = new Parser($argv, $parameters);

// Parse the arguments using the Cluster.
$results = $parser->parse();

// Validate the Parser and if it's invalid, print the usage.
if (! $parser->isValid()) {
    $parameters->printFullUsage(
        "Parameter Parser",
        "Using Cluster and the splat operator `...` (aka. Variadic Closures) Example.",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```
