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
$parameterParser = new ParameterParser($argv);

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
$nameClosure = new ParameterClosure('name', function ($name) {
    return $name;
});

$inviteClosure = new ParameterClosure('invite', function ($plusOne, $plusTwo) {
    return [$plusOne, $plusTwo];
});

$joinClosure = new ParameterClosure('join', function ($stringOne, $stringTwo) {
    return $stringOne . $stringTwo;
});

$minifyClosure = new ParameterClosure('minify', function() {
    return true;
});

// Apply the ParameterClosures to the ParameterCluster
// and associate them each with a prefix.
$parameters->add('-', $nameClosure);
$parameters->add('+', $minifyClosure);

// Use the ->addMany function to add multiple closures to the same prefix.
$parameters->addMany('--', [$inviteClosure, $joinClosure]);


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
    echo 'Unknown parameter \'' . $parameter .'\'' . PHP_EOL;
});

// Parse the arguments using the ParameterCluster.
$results = (new ParameterParser($argv, $parameters))->parse();

print_r($results);
```
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
$loadClosure = new ParameterClosure('load', function (... $arguments) {
    return $arguments;
});

$execClosure = new ParameterClosure('exec', function (...$arguments) {
    return $arguments;
});

// Create a new uniadic closure with regular parameter.
// This will make any parameter passed to this prefix closure only take
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
    echo 'Unknown parameter \'' . $parameter .'\'' . PHP_EOL;
});

// Parse the arguments using the ParameterCluster.
$results = (new ParameterParser($argv, $parameters))->parse();

print_r($results);
```
