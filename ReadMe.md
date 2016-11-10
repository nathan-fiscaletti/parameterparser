# Parameter Parser
> **Parameter Parser** is a simple library used to parse intricate parameters from an array of strings.

[parameterparser.io](http://parameterparser.io/)

[![StyleCI](https://styleci.io/repos/73029011/shield?style=flat)](https://styleci.io/repos/73029011)
[![Latest Stable Version](https://poser.pugx.org/nafisc/parameterparser/v/stable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Total Downloads](https://poser.pugx.org/nafisc/parameterparser/downloads?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Latest Unstable Version](https://poser.pugx.org/nafisc/parameterparser/v/unstable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![License](https://poser.pugx.org/nafisc/parameterparser/license?format=flat)](https://packagist.org/packages/nafisc/parameterparser)

[Advanced Code Examples](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)

### Features
* Parse command line parameters.
* Assign aliases to parameters.
* Custom closures for each command line parameter.
* Customize the way the command line is parsed.

### Example Usage
```php
// Initialize a new ParameterCluster
$parameters = new ParameterCluster();

// Add a ParameterClosure to the ParameterCluster
$parameters->add('-', parameter('name', function ($name) {
    return $name;
}));

// Create a new parameter parser using the ParameterCluster
$parameterParser = new ParameterParser($argv, $parameters);

// Parse the parameters using the ParameterParser.
$results = $parameterParser->parse();

// Verify that the parameters were valid after parsing.
if (! $parameterParser->isValid()) {

    // Since it was not valid, output usage.
    echo 'Usage: php test.php -name [name]' . PHP_EOL;

} else {

    // Retrieve the name from the results
    $name = $results['name'];

    // Output the name
    echo 'Your name is ' . $name . PHP_EOL;

}
```

### Output
```
~/ php test.php -name 'Nathan Fiscaletti'

   Your name is Nathan Fiscaletti
```
