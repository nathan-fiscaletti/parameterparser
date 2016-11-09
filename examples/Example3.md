> [< Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md) ...... [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)

----
### Example 3 : Using ParameterCluster and the splat operator `...` (aka. Variadic Closures)

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
// Create a new ParameterCluster.
$parameters = new ParameterCluster();

// Create a new variadic closures for the load and exec parameters using
// the splat operator (...). This will allow the closure to take all
// parameters between this initial 'load' parameter and the next 
// prefixed parameter in the parameter list.
$loadClosure = parameter('load', function (...$arguments) {
    return $arguments;
});

$execClosure = parameter('exec', function (...$arguments) {
    return $arguments;
});

// Create a new uniadic closure with regular parameter.
// This will make any parameter passed to the associated closure only take
// the next parameter (or the next few, depending on the number of arguments
// in the closure definition) as (an) argument(s), and not care where the next 
// prefix is in the list of arguments.
$configureWithClosure = new ParameterClosure('configurewith', function ($file) {
    return $file;
});


// Apply the ParameterClosures to the ParameterCluster
// and associate them each with a prefix.
$parameters->add('+', $configureWithClosure);

// Use the ->addMany function to add multiple closures to the same prefix.
$parameters->addMany('-', [$loadClosure, $execClosure]);


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

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Parse the arguments using the ParameterCluster.
$results = $parameterParser->parse();

// Validate the ParameterParser and if it's invalid, print the usage.
if (! $parameterParser->isValid()) {
    echo 'Usage: php test.php -load [files...]'.
         ' -exec [...files] +configurewith [file]';
    echo PHP_EOL;
} else {
    print_r($results);
}

```
> [< Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md) ...... [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
