## Index:
* [Example 1: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* Example 2: Using ParameterCluster
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)

----
### Example 2 : Using ParameterCluster to parse more advanced parameters.

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
// Create a new ParameterCluster.
$parameters = new ParameterCluster;

// Create new closures for each parameter.
$nameClosure = parameter('name', function ($name) {
    return $name;
});

$inviteClosure = parameter('invite', function ($plusOne, $plusTwo) {
    return [$plusOne, $plusTwo];
});

$joinClosure = parameter('join', function ($stringOne, $stringTwo) {
    return $stringOne . $stringTwo;
});

$minifyClosure = parameter('minify', function() {
    return true;
});

// Apply the ParameterClosures to the ParameterCluster
// and associate them each with a prefix.
$parameters->add('-', $nameClosure);
$parameters->add('+', $minifyClosure);

// Use the ->addMany function to add multiple closures to the same prefix.
$parameters->addMany('--', [$inviteClosure, $joinClosure]);


// Set a default closure for when no prefixes are found that match
// the parameter being parsed. 
// 
// This could be used to toggle certain things on or off, etc.
// In this example, we'll just output an error.
// 
// Note: When using a ParameterCluster you cannot use the setDefault()
// function of ParameterParser unless you execute the parse() function
// after initializing the ParameterParser with the ParameterCluster.
// 
// This is because the setDefault function of ParameterParser simply
// forwards the closure parameter to the setDefault function of the
// ParameterCluster property of the ParameterParser.
// 
// When you initialize the ParameterParser with the ParameterCluster,
// the default function gets overridden with the default function 
// of the new ParameterCluster. 
$parameters->setDefault(function ($parameter) {
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

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Parse the arguments using the ParameterCluster.
$results = $parameterParser->parse();

// Validate the ParameterParser and if it's invalid, print the usage.
if (! $parameterParser->isValid()) {
    echo 'Usage: php test.php -name [name]'.
    ' -invite [name-1] [name-2] -join [string-1] [string-2] +minify';
    echo PHP_EOL;
} else {
    print_r($results);
}
```