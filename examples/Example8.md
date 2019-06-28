## Index:
* [Example 1: Using Parameter Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example1.md)
* [Example 2: Using a Cluster](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example2.md)
* [Example 3: Using Variadic Closures (...)](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example3.md)
* [Example 4: Using Aliases](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example4.md)
* [Example 5: Using Error Handlers](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example5.md)
* [Example 6: Using Required Parameters](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example6.md)
* [Example 7: Halting the Parser](https://github.com/nathan-fiscaletti/parameterparser/blob/master/examples/Example7.md)
* Example 8: Printing Usage

----
### Example 8 : Printing Usage

#### Usage: 
    php test.php
#### Output: 
    Parameter Parser v0.0.1

    Description:

        Printing Usage Example.

    Usage:

        php test.php --read ( -rf ) <file> [--load ( -lf ) <file>]

    Parameters:

        Parameter   Properties   Aliases   Description
        --load      <file>       -lf       Will load a file.
        --read      <file>       -rf       Will read a file.
#### Code:
```php
// Create a new Cluster.
$parameters = new Cluster;

// Set up some parameters
$load = parameter('--', 'load', function ($file) {
    return 'loaded';
});
$load->addAlias('lf', '-');
$load->setDescription('Will load a file.');


$read = parameter('--', 'read', function ($file) {
    return 'read';
});
$read->setRequired(true);
$read->addAlias('rf', '-');
$read->setDescription('Will read a file.');

// Use the ->addMany function to add multiple closures to the Cluster.
$parameters->addMany([
    $load,
    $read
]);

// Print the usage for the cluster

$parameters->printFullUsage(
    
    /* Required parameters */

    "Parameter Parser",        // Application name
    "Printing Usage Example.", // Applicatio Description
    "v0.0.1",                  // Application Version

    /* Optional parameters */

    true,                      // Show required parameters first in Usage section (defaults to true).
    null,                      // If not null, will replace the `php` binary in the Usage section (defaults to null).
    null,                      // If not null, will replace the script name in the Usage section (defaults to null).
    2,                         // The amount of padding to add to each column after the longest word. (Default 2)    

    ['required']               // You can specify which elements you would like hidden from the output here.
                               // This example hides the "required" output. You can also define a custom one.
                               // By default, if you leave this null, FullUsageStyle::all() is used.
);
```
