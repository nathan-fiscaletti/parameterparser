----
### Example 1 : Using the Parameter Parser to parse simple parameters.

#### Usage: 
    php test.php silent color
#### Output: 
    Silent mode has been enabled.
    Color has been enabled.
#### Code:
```php
// Create a new parameter parser using the default PHP arguments.
$parameterParser = new \ParameterParser\ParameterParser($argv);

// Set the default closure of ParameterParser
// In this example, we will just have two parameters that can be set.
$parameterParser->setDefault(function ($parameter) {
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
$parameterParser->parse();
```
----
### Example 2 : Using PrefixCluster to parse more advanced parameters.

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
// Create a new prefix cluster.
$prefixes = new \ParameterParser\PrefixCluster;

// Create a new closure for the dash prefix.
$dashPrefixClosure = function ($parameter, $value) {
    switch ($parameter) {
        case 'name' : {
            return $value;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
            echo PHP_EOL;
        }
    }
};

// Create a new closure for the double dash prefix.
// Note: You cannot use the splat operator if you are using
// the ->parse() function. Please see 'Example 3' for information
// on using the splat operator to extend the functionality of closures.
$doubleDashPrefixClosure = function ($parameter, $value1, $value2) {
    switch ($parameter) {
        case 'invite' : {
            return [$value1, $value2];
        }

        case 'join' : {
            return $value1.$value2;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
            echo PHP_EOL;
        }
    }
};

// Create a new closure for the plus prefix.
$plusPrefixClosure = function ($parameter) {
    switch ($parameter) {
        case 'minify' : {
            return true;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
            echo PHP_EOL;
        }
    }
};


// Apply the prefix closures and the prefixes to the prefix cluster.
$prefixes->add('-', $dashPrefixClosure);
$prefixes->add('--', $doubleDashPrefixClosure);
$prefixes->add('+', $plusPrefixClosure);


// Set a default closer for when no prefixes are found that match
// the parameter being parsed. 
// 
// This could be used to toggle certain things on or off, etc.
// In this example, we'll just output an error.
// 
// Note: When using PrefixCluster you cannot use the setDefault()
// function of ParameterParser unless you execute the parse() function
// after initializing the ParameterParser with the PrefixCluster.
// 
// This is because the setDefault function of ParameterParser simply
// forwards the closure parameter to the setDefault function of the
// PrefixCluster property of the ParameterParser.
// 
// When you initialize the ParameterParser with the PrefixCluster,
// the default function gets overridden with the default function 
// of the new PrefixCluster. 
$prefixes->setDefault(function ($parameter) {
    echo 'Unknown parameter \'' . $parameter .'\'' . PHP_EOL;
});

// Parse the arguments using the prefix cluster.
$results = (new \ParameterParser\ParameterParser($argv, $prefixes))->parse();

print_r($results);
```
----
### Example 3 : Using PrefixCluster and parsePrefixOnly() function with splat operator.

#### Usage: 
    php test.php -load 'Main Library.so' File2.so -exec 'Pre Load.sh' Initialize.sh start.sh
#### Output:
    Array
    (
        [load] => Array
            (
                [0] => Main Library.so
                [1] => File2.so
            )

        [exec] => Array
            (
                [0] => Pre Load.sh
                [1] => Initialize.sh
                [2] => start.sh
            )

    )
#### Code:
```php
// Create a new prefix cluster.
$prefixes = new \ParameterParser\PrefixCluster;

// Create a new closure for the dash prefix using the splat operator (...)
// This will allow our closure to take all parameters between this prefix
// and the next prefix in the parameter list.
$dashPrefixClosure = function ($parameter, ...$values) {
    switch ($parameter) {
        case 'load' : {
            return $values;
        }

        case 'exec' : {
            return $values;
        }

        default : {
            echo 'Unknown parameter \'' . $parameter . '\'';
            echo PHP_EOL;
        }
    }
};


// Apply the prefix closures and the prefixes to the prefix cluster.
$prefixes->add('-', $dashPrefixClosure);


// Set a default closer for when no prefixes are found that match
// the parameter being parsed. 
// 
// This could be used to toggle certain things on or off, etc.
// In this example, we'll just output an error.
// 
// Note: When using PrefixCluster you cannot use the setDefault()
// function of ParameterParser unless you execute the parse() function
// after initializing the ParameterParser with the PrefixCluster.
// 
// This is because the setDefault function of ParameterParser simply
// forwards the closure parameter to the setDefault function of the
// PrefixCluster property of the ParameterParser.
$prefixes->setDefault(function ($parameter) {
    echo 'Unknown parameter \'' . $parameter .'\'' . PHP_EOL;
});

// Parse the arguments using the prefix cluster.
// 
// Note: Since we are using the parsePrefixOnly() function instead of parse() all
// prefix closures can use the splat operator to define as many parameters
// as you'd like. The parser will take all arguments before the next prefix
// and pass them as parameters to your closure.
$results = (new \ParameterParser\ParameterParser($argv, $prefixes))->parsePrefixOnly();

print_r($results);
```