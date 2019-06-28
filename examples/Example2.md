## Index:
* [Example 1: Using Parameter Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* Example 2: Using a Cluster
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* [Example 8: Printing Usage](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example8.md)

----
### Example 2 : Using a Cluster to parse more advanced parameters.

#### Usage: 
    php test.php -name "Nathan Fiscaletti" +minify --join 'foo bar' apples --invite 'Mr. Foo' 'Mr. Bar'
#### Output:
    Array
    (
        [name] => Nathan Fiscaletti
        [minify] => 1
        [join] => foo barapples
        [invite] => Array
            (
                [0] => Mr. Foo
                [1] => Mr. Bar
            )
    )
#### Code:
```php
// Create a new Cluster.
$parameters = new Cluster;

// Create new closures for each parameter.
$name = parameter('-', 'name', function ($name) {
    return $name;
});

$invite = parameter('--', 'invite', function ($name1, $name2) {
    return [$name1, $name2];
});

$join = parameter('--', 'join', function ($string1, $string2) {
    return $string1 . $string2;
});

$minify = parameter('+', 'minify', function() {
    return true;
});

// Use the ->addMany function to add multiple closures to the Cluster.
$parameters->addMany([
    $name,
    $minify,
    $invite,
    $join,
]);


// Set a default closure for when no prefixes are found that match
// the parameter being parsed. 
// 
// This could be used to toggle certain things on or off, etc.
// In this example, we'll just output an error.
// 
// Note: When using a Cluster you cannot use the setDefault()
// function of Parser unless you execute the parse() function
// after initializing the Parser with the Cluster.
// 
// This is because the setDefault function of Parser simply
// forwards the closure parameter to the setDefault function of the
// Cluster property of the Parser.
// 
// When you initialize the Parser with the Cluster,
// the default function gets overridden with the default function 
// of the new Cluster. 
//
// See: https://bit.ly/2AmIChU
//
$parameters->setDefault(function ($parameter) {
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

// Create a Parser using the Cluster.
$parser = new Parser($argv, $parameters);

// Parse the arguments using the Cluster.
$results = $parser->parse();

// Validate the ParameterParser and if it's invalid, print the usage.
if (! $parser->isValid()) {
    $parameters->printFullUsage(
        "Parameter Parser",
        "Using ParameterCluster to parse more advanced parameters Example.",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```
