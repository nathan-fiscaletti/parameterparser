## Index:
* [Example 1: Using ParameterParser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using ParameterCluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* Example 4: Using Aliases
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)

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
$execClosure = parameter('-', 'exec', function ($file) {
    return $file;
});

// Add an alias to the exec ParameterClosure using prefix '--'
// and parameter alias 'exec-with'.
// 
// Note: Aliases will always override regular parameters no
// matter what order they are added in. Aliases take precedence.
// 
// Note: You can also define aliases with no prefix and the 
// alias will use it's parent parameter's prefix.
$execClosure->addAlias('exec-with', '--');

// Add the exec ParameterClosure to the ParameterCluster.
$parameters->add($execClosure);

// Create a ParameterParser using the ParameterCluster.
$parameterParser = new ParameterParser($argv, $parameters);

// Parse the arguments using the ParameterCluster.
$results = $parameterParser->parse();

// Validate the ParameterParser and if it's invalid, print the usage.
// Note: Aliases do not currently show in ParameterCluster::getFullUsage()
if (! $parameterParser->isValid()) {
    echo 'Usage: ' . $parameters->getFullUsage();
    echo PHP_EOL;
} else {
    print_r($results);
}

```