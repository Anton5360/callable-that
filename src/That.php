<?php

namespace Antoksa\CallableThatProxy;

use LogicException;

/**
 * @template T of object
 *
 * @mixin T
 */
class That
{
    private $method;
    private $property;
    private $args;

    /**
     * @param T|class-string<T>|null $class Type (must be classed string; object declaration is for IDE autocomplete)
     */
    public function __construct(
        string $class = null,
        array $args = [],
        string $method = '',
        string $property = ''
    ) {
        $this->args = $args;
        $this->method = $method;
        $this->property = $property;
    }

    /**
     * @param T $object
     * @return mixed
     */
    public function __invoke($object)
    {
        if ($this->method) {
            return $this->__call($this->method, [$object]);
        } elseif ($this->property) {
            return $object->{$this->property};
        }

        throw new LogicException('Invalidly configured That proxy (must have either method or property)');
    }

    public function __get(string $name): self
    {
        return $this->get($name);
    }

    /**
     * @param array{0: T} $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $arguments[0]->$name(...$this->args);
    }

    /**
     * @return T|That<T>
     */
    public function call(string $method, array $args = []): self
    {
        $this->method = $method;

        return $this->withArgs(...$args);
    }

    /**
     * @return T|That<T>
     */
    public function get(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return T|That<T>
     */
    public function withArgs(...$args): self
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Pseudo method used for IDE autocompletion
     *
     * @param T $class
     * @return T|That<T>
     */
    public function setClass($class)
    {
        return $this;
    }

    /**
     * @param array{class: T, method: string, property: string, args: array} $config
     *
     * @return T|That<T>
     */
    public static function make(array $config = []): self
    {
        return (new self($config['class'] ?? null))
            ->call($config['method'] ?? '', $config['args'] ?? [])
            ->get($config['property'] ?? '')
            ->setClass($config['class'] ?? null);
    }
}
