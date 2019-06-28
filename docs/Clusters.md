# Parameter Parser: Clusters

See: [src/Parameters/Cluster.php](../src/Parameters/Cluster.php)

A Cluster is a group of Parameters packaged together.

## Creating a Parameter Cluster

See [Example 2 : Using a Cluster](../examples/Example2.md)

```php
$parameters = new Cluster;
```

## Adding a Parameter to a Cluster

To add a Parameter to a Cluster, first [create your parameter](./Parameters.md), and then use one of the following functions:

|Function|Effect|
|---|---|
|`add(Parameter)`|Adds a Parameter to the Cluster.|
|`addMany(array[Parameter])`|Adds multiple Parameters to the Cluster at once.|


You can also remove a Parameter from the cluster using one of the following functions:

|Function|Effect|
|---|---|
|`remove(string, string)`|Removes a Parameter from the cluster. The first argument is the Prefix for the Parameter and the second is the name of the Parameter.|

## Setting the Default Handler

See [Example 1: Using Parameter Parser](../examples/Example1.md)

If an argument is found during parsing that does not match any of the configured Parameters the Default Handler will be called. You can configure how this performs using the following:

```php
$parameters->setDefault(function($argument) {
    // handle the argument
});
```

> The Cluster object implements the [Fluent](https://en.wikipedia.org/wiki/Fluent_interface) design pattern, so you can chain these functions.

## Printing Usage

See: [Example 8: Printing Usage](../examples/Example8.md)

You can print the usage for your Cluster using the following:

```php
$parameters->printFullUsage(
    
    /* Required parameters */

    "Parameter Parser",        // Application name
    "Printing Usage Example.", // Applicatio Description
    "v0.0.1",                  // Application Version

    /* Optional parameters */

    true,                      // Show required parameters first in 
                               // Usage section (defaults to true).

    null,                      // If not null, will replace the `php` 
                               // binary in the Usage section (defaults to null).

    null,                      // If not null, will replace the script name in 
                               // the Usage section (defaults to null).

    2,                         // The amount of padding to add to each column 
                               // after the longest word. (Default 2)    

    ['required']               // You can specify which elements you would like 
                               // hidden from the output here. This example hides 
                               // the "required" output. You can also define a custom one.
                               // By default, if you leave this null, 
                               // FullUsageStyle::all() is used.
);
```