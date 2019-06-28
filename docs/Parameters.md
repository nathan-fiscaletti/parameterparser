# Parameter Parser: Parameters

See: [src/Parameters/Parameter.php](../src/Parameters/Parameter.php)

## Creating a Parameter

See [Example 2 : Using a Cluster](../examples/Example2.md)

You can create a parameter with one of two options.

1. Using the `parameter()` helper function.
   
   ```php
   $parameter = parameter('-', 'name', function ($name) {
       return $name;
   });
   ```

   This function takes the following arguments:

   |Argument|Type|Description|
   |---|---|---|
   |`$prefix`|`string`|The prefix for the Parameter.|
   |`$name`|`string`|The name for the Parameter.|
   |`$closure`|`\Closure`|The closure used to process the Parameter.

2. Creating a Parameter using the Parameter constructor.

   ```php
   $parameter = new Parameter('-', 'name', function($name) {
       return $name;
   });
   ```

   > This takes the same arguments as the `parameter()` helper function.

## Configuring the Parameter

Once you have created a Parameter you can configure it using the following options:

|Function|Effect|
|---|---|
|`->setRequired(bool)`|Makes the Parameter a Required Parameter.|
|`->setDescription(string)`|Sets the description for the Parameter. This is used when displaying Parameter usage from a [Cluster](./Clusters.md))|
|`->addAlias(string, string)`|Adds an Alias for this Parameter. The first parameter should be the Prefix for the Alias, and the second parameter should be the name of the Alias. _Note: Only one alias can exist per prefix per Parameter._ |

> The Parameter object implements the [Fluent](https://en.wikipedia.org/wiki/Fluent_interface) design pattern, so you can chain these functions.