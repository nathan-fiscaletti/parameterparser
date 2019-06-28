## Index:
* [Example 1: Using Parameter Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using a Cluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* Example 4: Using Aliases
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* [Example 8: Printing Usage](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example8.md)

----
### Example 4 : Using aliases with ParameterClosures

#### Usage: 
    php test.php --exec-with 'Main File.sh'

    -- or --

    php test.php -exec 'Main File.sh'
#### Output:
    Array
    (
        [exec] => Main File.sh
    )
#### Code:
```php
// Create a new Cluster.
$parameters = new Cluster();

// Create a new uniadic closure and associate it with the exec parameter.
$execClosure = parameter('-', 'exec', function ($file) {
    return $file;
});

// Add an alias to the exec Parameter using prefix '--'
// and parameter alias 'exec-with'.
// 
// Note: Aliases will always override regular parameters no
// matter what order they are added in. Aliases take precedence.
// 
// Note: You can also define aliases with no prefix and the 
// alias will use it's parent parameter's prefix.
$execClosure->addAlias('exec-with', '--');

// Add the exec Parameter to the Cluster.
$parameters->add($execClosure);

// Create a Parser using the Cluster.
$parser = new Parser($argv, $parameters);

// Parse the arguments using the Cluster.
$results = $parser->parse();

// Validate the Parser and if it's invalid, print the usage.
// Note: Aliases do not currently show in Cluster::printFullUsage()
if (! $parser->isValid()) {
    $parameters->printFullUsage(
        "Parameter Parser",
        "Using aliases with Parameter Example.",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```