<?php

namespace Parameters;

class FullUsageStyle
{
    /**
     * Retrieve which values from a Parameter
     * you would like displayed in the
     * full usage output.
     *
     * @param int $columnPadding
     *
     * @return array
     */
    public static function all($columnPadding)
    {
        return self::allExcept([], $columnPadding);
    }

    /**
     * Retrieve which values from a Parameter
     * you would like displayed in the
     * full usage output.
     *
     * Any key in the $except array will be excluded.
     *
     * @param array $except
     * @param int $columnPadding
     *
     * @return array
     */
    public static function allExcept($except, $columnPadding)
    {
        $result = [
            'parameter' => [
                // 9 = Length of the word 'Parameter'
                'longest' => 9 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->prefix.$parameter->name;
                },
            ],

            'properties' => [
                // 10 = Length of the word 'Properties'
                'longest' => 10 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->getPropertiesAsString();
                },
            ],

            'aliases' => [
                // 7 = Length of the word 'Aliases'
                'longest' => 7 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->getAliasUsage(false);
                },
            ],

            'description' => [
                // 11 = Length of the word 'Description'
                'longest' => 11 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->description;
                },
            ],

            'required' => [
                // 8 = Length of the word 'Required'
                'longest' => 8 + $columnPadding,
                'values' => [],
                'fetch' => function ($parameter) {
                    return $parameter->required ? 'Yes' : '';
                },
            ],
        ];

        // Remove the exceptions
        foreach ($except as $exceptKey) {
            unset($result[$exceptKey]);
        }

        return $result;
    }
}
