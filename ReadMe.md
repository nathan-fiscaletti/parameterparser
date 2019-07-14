# Parameter Parser
> **Parameter Parser** is a simple library used to parse intricate parameters from an array of strings.

> **Hint**: Parameter Parser is available through [Composer](https://getcomposer.org). `composer require nafisc/parameterparser`.

[![StyleCI](https://styleci.io/repos/73029011/shield?style=flat)](https://styleci.io/repos/73029011)
[![Latest Stable Version](https://poser.pugx.org/nafisc/parameterparser/v/stable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Total Downloads](https://poser.pugx.org/nafisc/parameterparser/downloads?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![Latest Unstable Version](https://poser.pugx.org/nafisc/parameterparser/v/unstable?format=flat)](https://packagist.org/packages/nafisc/parameterparser)
[![License](https://poser.pugx.org/nafisc/parameterparser/license?format=flat)](https://packagist.org/packages/nafisc/parameterparser)

[Documentation](./docs/) - [Advanced Code Examples](./examples/Example1.md) - [Looking for the Python version?](https://github.com/nathan-fiscaletti/parameterparser-py)

### Features
* Parse command line parameters.
* Assign aliases to parameters.
* Custom closures for each command line parameter.
* Variadic closure support for arguments taking more than one value.
* Customize the way the command line is parsed.

### Example Usage
```php
// Initialize a new Cluster
$parameters = new Cluster();

// Add a Parameter to the Cluster
$parameter = parameter('-', 'name', function ($name) {
    return $name;
});

$parameter->setRequired(true)
          ->setDescription('Your name.');

$parameters->add($parameter);

// Create a new Parser using the Cluster
$parser = new Parser($argv, $parameters);

// Parse the parameters using the Parser.
$results = $parser->parse();

// Verify that the parameters were valid after parsing.
if (! $parser->isValid()) {

    // Since it was not valid, output usage.
    $parameters->printFullUsage(
        "Parameter Parser",
        "An advanced parameter parser for PHP",
        "v1.0.0"
    );

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
