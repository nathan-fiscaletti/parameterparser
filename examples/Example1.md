## Index:
* Example 1: Using Parameter Parser
* [Example 2: Using a Cluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* [Example 8: Printing Usage](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example8.md)

----
### Example 1 : Using Parameter Parser to parse simple parameters.

#### Usage: 
    php test.php silent color
#### Output: 
    Array
    (
        [color] => 1
        [silent] => 1
    )
#### Code:
```php
// Create a new parameter parser using the default PHP arguments.
$parser = new Parser($argv);

// Set the default closure of Parser
// In this example, we will just have two parameters that can be set.
$parser->setDefault(function ($parameter) {
    switch($parameter) {
        case 'color' : {
            return true;
        }

        case 'silent' : {
            return true;
        }

        // Always return -1 if no valid parameter is found.
        // This will invalidate the parameters.
        // 
        // After parsing, use $parser->isValid()
        // to check validity.
        // 
        // The default closure will always return -1 unless
        // you directly override it.
        default : {
            return -1;
        }
    }
});

// Parse the parameters using the parameter parser.
$results = $parser->parse();

// Validate the Parser and if it's invalid, print the usage.
if (! $parser->isValid()) {
    echo 'Usage: php test.php [color] [silent]';
    echo PHP_EOL;
} else {
    print_r($results);
}
```
