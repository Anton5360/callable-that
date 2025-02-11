<?php

namespace Antoksa\CallableThat;

if (! function_exists('that')) {
    /**
     * @template T of object
     *
     * @param T|(class-string<T>|null) $class Type (must be classed string; object declaration is for IDE autocomplete)
     * @return That|T|That<T>
     */
    function that(string $class = null, array $args = [], string $method = '', string $property = ''): That {
        return That::make(compact('class', 'method', 'args', 'property'));
    }
}
