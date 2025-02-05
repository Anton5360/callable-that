<?php

namespace Antoksa\CallableThat;

if (! function_exists('that')) {
    function that(array $args = [], string $method = '', string $property = ''): That {
        return That::make(compact('method', 'args', 'property'));
    }
}
