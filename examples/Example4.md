## Index:
* [Example 1: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* Example 4: Using Aliases

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
// Create a new ParameterCluster.
$parameters = new ParameterCluster();

// Create a new uniadic closure and associate it with the exec parameter.
$execClosure = parameter('exec', function ($arguments) {
    return $arguments;
});

// Add an alias to the exec ParameterClosure using prefix '--'
// and parameter alias 'exec-with'.
// 
// Note: Aliases will always override regular parameters no
// matter what order they are added in. Aliases take precedence.
$execClosure->addAlias('--', 'exec-with');

// Add the exec ParameterClosure to the ParameterCluster.
$parameters->add('-', $execClosure);

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Set a default closer for when no prefixes are found that match
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

// Parse the arguments using the ParameterCluster.
$results = $parameterParser->parse();

// Validate the ParameterParser and if it's invalid, print the usage.
if (! $parameterParser->isValid()) {
    echo 'Usage: php test.php -exec [file]';
    echo PHP_EOL;
} else {
    print_r($results);
}

```