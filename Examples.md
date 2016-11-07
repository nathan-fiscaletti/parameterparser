## Examples

### Example 1 : Using the Parameter Parser to parse simple parameters.

#### Usage: 
    php test.php silent color
#### Output: 
    Silent mode has been enabled.
    Color has been enabled.
#### Code:
```php
// Create a new prefix cluster to use for parsing the arguments.
// We will only be using the default closure of the cluster
// for this example.
$prefixes = new \ParameterParser\PrefixCluster;

// Set the default closure of the prefix cluster.
// In this example, we will just have two parameters that can be set.
$prefixes->setDefault(function ($parameter) {
    switch($parameter) {
        case 'color' : {
            echo 'Color has been enabled.';
            break;
        }

        case 'silent' : {
            echo 'Silent mode has been enabled.';
            break;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
        }
    }

    echo PHP_EOL;
});

// Parse the parameters using the parameter parser.
(new \ParameterParser\ParameterParser($argv, $prefixes))->parse();
```

### Example 2 : Using prefixes to parse more advanced parameters.

#### Usage: 
    php test.php -name Nathan +minify
#### Output:
    Name has been set to Nathan
    Minification has been enabled.
#### Code:
```php
// Create a new prefix cluster.
$prefixes = new \ParameterParser\PrefixCluster;

// Create a new closure for the dash prefix.
$dashPrefixClosure = function ($parameter, $value) {
    switch($parameter) {
        case 'name' : {
            echo 'Name has been set to ' . $value;
            break;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
        }
    }

    echo PHP_EOL;
};

// Create a new closure for the plus prefix.
$plusPrefixClosure = function ($parameter) {
    switch ($parameter) {
        case 'minify' : {
            echo 'Minification has been enabled.';
            break;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
        }
    }

    echo PHP_EOL;
};

// Apply the prefix closures and the prefixes to the prefix cluster.
$prefixes->add('-', $dashPrefixClosure);
$prefixes->add('+', $plusPrefixClosure);

// Set a default closer for when no prefixes are found that match
// the parameter being parsed. 
// 
// This could be used to toggle certain things on or off, etc.
// In this example, we'll just output an error.
$prefixes->setDefault(function ($parameter) {
    echo 'Unknown parameter \'' . $parameter .'\'' . PHP_EOL;
});

// Parse the arguments using the prefix cluster.
(new \ParameterParser\ParameterParser($argv, $prefixes))->parse();
```