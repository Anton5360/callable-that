# Callable "that" Proxy

## "That" Philosophy
Lightweight proxy "that" provides ability to create a callable for an object during runtime.

The idea of "that" is to reference the way how you usually create a callable for particular object. 
```php
[$this, 'method'] # For particular object

[that(), 'method'] # For an expected object which is about to be accessed during the loop
```

## Concept
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

Unfortunately, even though we can create objects like `[$this, 'method']`,
but we can't access the object during iteration for runtime (e.g. `array_map()`, `array_filter()` etc.)

Lightweight proxy "that" brings this handy feature:
```php
array_map(
    [that(), 'method'], # static fn (MyClass $object): string => $object->method()
    $objects,
);

array_map(
    [that()->withArgs($arg1, $arg2), 'method'], # static fn (MyClass $object): string => $object->method($arg1, $arg2),
    $objects,
);

array_map(
    that()->property, # static fn (MyClass $object): string => $object->property
    $objects,
);
```


## Usage

### Basic

```php
### Call method
[that(), 'method']
that()->call('method')
that(null, [], 'method') # Not cool, but it changes with php8 named args


### Add arguments
[that()->withArgs($arg1, $arg2), 'method'],
[that(null, [$arg1, $arg2]), 'method'],


### Get Property
that()->property,
that()->get('property'),
that(null, [], '', 'property'), # Not cool, but it changes with php8 named args
```

### Laravel Collection Advantage

Yes, Laravel got `HighOrderedProxy` which allows to the chain of operations conveniently,
but IDE loses the type for it:

```php
collect($objects)
    ->map
    ->method() # Return type is lost
    ->filter
    ->anotherMethod($arg1)
    ->all();
    
collect($objects)
    ->map
    ->existingCollectionMethodWhichReturnsBool() # Even worse, now IDE thinks that it returns bool here
    ->filter # Static analysis tools complains
    ->anotherMethod($arg1)
    ->all();
```

That's why I personally for this case would prefer to avoid `HighOrderedProxy`.
It`s where "that" proxy comes handy:

```php
collect($objects)
    ->map([that(), 'method'])
    ->filter(that()->withArgs($arg1)->call('anotherMethod'))
    ->all(); # Return type never gets lost
```

### Note
First argument (class) **is not** required, however **when provided**, you take advantage of **IDE autocompletion**:
```php
# Partial support; IDE does not suggest method, but it`s clickable as soon as you type it
[that(MyClass::class), 'method']
[that()->setClass(MyClass::class), 'method']


# Full support
that(MyClass::class)->property
that()->setClass(MyClass::class)->property


# No support; However, eventually, you never need that unless
# you want to call method which exists in That class
that(MyClass::class)->call('method')
that(MyClass::class)->get('property')
```
