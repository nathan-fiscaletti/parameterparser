# Parameter Parser: Parsers

See: [src/Parameters/Parser.php](../src/Parameters/Parser.php)

A Parser is an object that will parse the supplied argument array using the [Parameters](./Parameters.md) stored in a [Cluster](./Clusters.md). You can use any array of strings here, however it is most common to use the `$argv` property in PHP.

## Creating a Parser

To create a Parser use the following

```php
$parser = new Parser($argv, $parameters);
```

Where `$argv` is your array of strings to be parsed, and `$parameters` is your instance of [`\Parameters\Cluster`](../src/Parameters/Cluster.php) (see [Clusters](./Clusters.md))/

## Executing the Parser

See [Example 2: Using a Cluster](../examples/Example2.md)

Once you have created a Parser, you can parse the arguments using the following:

```php
$results = $parser->parse();
```

The results of the parser execution will be stored in the array `$results` and the `->isValid()` flag will be set.

## Validating the Parser

See [Example 2: Using a Cluster](../examples/Example2.md)

After you have parsed your arguments using `->parse()` you can check their validity using `->isvalid()` and choose how you want to handle invalid results.

```php
if (! $parser->isValid()) {
    $parameters->printFullUsage(
        "Parameter Parser",
        "An example of Validitation",
        "v0.0.1"
    );
} else {
    print_r($results);
}
```

## Setting Error Handlers

See [Example 5 : Using Error Handlers](../examples/Example5.md)

You can set an error handler that will recieve all instances of `\Parameters\ParseException` that would normally be thrown. If you do not set a error handler, then these exceptions will simply be thrown as normal.

```php
$parser->setErrorHandler(
    function (ParseException $exception) {
        echo $exception->toAnsiString();
    }
);
```

You can either call `->toAnsiString()` on the `\Parameters\ParseException` instance to get the ANSI Stylized String for this exception, or you can treat is as a normal instance of `\Exception`.

The error codes are as follows:

|Code|Description
|---|---|
|`60001`|Invalid argument count while parsing a Uniadic Alias.|
|`60002`|Invalid argument count while parsing a Uniadic Parameter.|
|`60003`|Invalid argument count while parsing a Variadic Alias.|
|`60004`|Invalid argument count while parsing a Variadic Parameter.|
|`60005`|Missing a required parameter.|

## Halting the Parser

See [Example 7: Halting the Parser](../examples/Example7.md)

You can halt the parser when you encounter a specific Parameter. This will cause the parser to parse no further parameters and stop execution, do this by returning one of the following options from the Parameter's closure.

|Option|Effect|
|---|---|
|`parmeter_result_and_halt(value)`|Return the value for the Parameter and then halt the Parser.|
|`parameter_result_halt()`|Return no value for the Parameter and then halt the Parser.|


```php
$load = parameter('-', 'load', function ($file) {
    // This will return a value for the parameter, and 
    // will then halt the Parser.
    return parameter_result_and_halt($file);
    
    // This will return no value for the parameter
    // but will halt the Parser.
    // return parameter_result_halt();

    // This will simply return a value for the parameter.
    // return parameter_result($file);

    // This will also return a value for the parameter.
    // return $file;
});
```

You can check if the Parser was halted using `$parser->haltedBy()` and `$parser->haltedByName()`.

```php
if ($parser->haltedBy() != null) {
    echo 'Halted By: ';
    // You can either use ->haltedBy() or ->haltedByName()
    //     ->haltedBy()     : Will return a Parameter object.
    //     ->haltedByName() : Will return the name of the Parameter.
    echo $parser->haltedByName() . PHP_EOL;
}
```