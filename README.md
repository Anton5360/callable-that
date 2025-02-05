# Callable "that" Proxy

Proxy provides ability to create a callable for an object during runtime

How often did you need to perform `array_map()` like this?

```php
array_map(
    static function (MyClass $object): string {
        return $object->method();
    },
    $objects,
);

array_map(
    static fn (MyClass $object): string => $object->method(),
    $objects,
);

array_map(
    static fn (MyClass $object): string => $object->method($arg1, $arg2),
    $objects,
);

array_map(
    static fn (MyClass $object): string => $object->property,
    $objects,
);
```

Unfortunately, even though we can create objects like `[$this, 'method']` or `[$this->method(...)]`,
but we can't access the object during iteration for runtime (e.g. `array_map()`, `array_filter()` etc.)

Lightweight proxy "that" is to represent an object during runtime:
```php
array_map(
    [that(), 'method'],
# Or   that()->method(...),
# Or   that()->call('method'),
    $objects,
);

array_map(
    [that()->withArgs($arg1, $arg2)->method(...)],
# Or   that(args: [$arg1, $arg2])->method(...),
# Or   [that(args: [$arg1, $arg2]), 'method'],
# Or   [that()->withArgs($arg1, $arg2), 'method'],
    $objects,
);

array_map(
    that()->property,
#  Or  that()->get('property'),
    $objects,
);
```


## Laravel Collection advantage

Yes, Laravel got `HighOrderedProxy` which allows to the chain of operations conveniently,
but IDE loses the type for it:

```php
collect($objects)
    ->map
    ->method() # Return type is lost
    ->filter
    ->anotherMethod($arg1)
    ->all();
```

That's why I personally for this case would prefer to avoid `HighOrderedProxy`,
 but what we can do with `that()` proxy now:

```php
collect($objects)
    ->map(that()->method(...))
    ->filter([that()->withArgs($arg1), 'anotherMethod'])
    ->all(); # Return type never gets lost
```
