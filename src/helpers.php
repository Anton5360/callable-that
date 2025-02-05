<?php

namespace Antoksa\CallableThat;

if (! function_exists('that')) {
    /**
     * @template T of object
     * @template H of class-string<T>|null
     *
     * @param H $class
     * @return (H is null ? That : That<T>)
     */
    function that(string $class = null, array $args = [], string $method = '', string $property = ''): That {
        return That::make(compact('method', 'args', 'property'));
    }
}
