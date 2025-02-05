<?php

namespace Antoksa\CallableThat;

use LogicException;

/**
 * @template T of object
 *
 * @mixin T
 */
class That
{
    private $method = null;
    private $property = null;
    private $args = [];

    /**
     * @param object $object
     * @return mixed
     */
    public function __invoke($object = null)
    {
        if ($this->method) {
            return $this->__call($this->method, [$object]);
        } elseif ($this->property) {
            return $object->{$this->property};
        }

        throw new LogicException('Invalidly configured That proxy');
    }

    public function __get(string $name): self
    {
        $this->get($name);

        return $this;
    }

    /**
     * @param string $name
     * @param array{0: object} $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $arguments[0]->$name(...$this->args);
    }

    public function call(string $method, array $args = []): self
    {
        $this->method = $method;

        $this->withArgs(...$args);

        return $this;
    }

    public function get(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function withArgs(...$args): self
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @param array{method: string, property: string, args: array} $config
     *
     * @return self
     */
    public static function make(array $config = []): self
    {
        return (new self())
            ->call($config['method'] ?? '', $config['args'] ?? [])
            ->get($config['property'] ?? '');
    }
}
