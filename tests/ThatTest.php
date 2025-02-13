<?php

namespace Antoksa\CallableThatProxy\Tests;

use Antoksa\CallableThatProxy\That;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ThatTest extends TestCase
{
    public function test_make_method()
    {
        $that = That::make([
            'method' => 'methodName',
            'property' => 'propertyName',
            'args' => ['one', 'two'],
        ]);

        $expectedThat = (new That())
            ->call('methodName')
            ->get('propertyName')
            ->withArgs('one', 'two');

        $this->assertEquals($expectedThat, $that);
    }

    /**
     * @dataProvider callables
     */
    public function test_correctly_creates_callable(mixed $callable, string $expected)
    {
        $this->assertTrue(is_callable($callable));

        $this->assertSame($expected, $callable(new Dummy()));
    }

    public function test_correctly_iterates_using_that_proxy()
    {
        $this->assertSame(
            expected: [1, 2, 3],
            actual: array_map(
                (new That())->call('count'),
                [new Dummy(), new Dummy(), new Dummy()]
            )
        );
    }

    public function test_throws_logic_exception_if_not_configured_properly()
    {
        $this->expectException(LogicException::class);

        (new That())(new stdClass());
    }

    public static function callables(): array
    {
        $cases = [
            // Callable method
            [[(new That()), 'toString'], Dummy::DEFAULT],
            [(new That())->call('toString'), Dummy::DEFAULT],
            [(new That(method: 'toString')), Dummy::DEFAULT],

            // Callable method with args
            [[(new That())->withArgs('first', 'second'), 'toString'], Dummy::DEFAULT.'_first_second'],
            [(new That())->call('toString')->withArgs('first', 'second'), Dummy::DEFAULT.'_first_second'],
            [(new That())->call('toString', ['first', 'second']), Dummy::DEFAULT.'_first_second'],
            [(new That(args: ['first', 'second'], method: 'toString')), Dummy::DEFAULT.'_first_second'],

            // Callable property
            [(new That())->get('value'), Dummy::DEFAULT_PROP],
            [(new That())->value, Dummy::DEFAULT_PROP],
            [(new That(property: 'value')), Dummy::DEFAULT_PROP],
        ];

        if (version_compare(PHP_VERSION, '8.1', '>=')) {
            array_push(
                $cases,

                // Callable method
                [(new That())->toString(...), Dummy::DEFAULT],

                // Callable method with args
                [(new That())->withArgs('first', 'second')->toString(...), Dummy::DEFAULT.'_first_second']
            );
        }

        return $cases;
    }

}

class Dummy
{
    const DEFAULT = 'Dummy';

    const DEFAULT_PROP = 'Dummy_prop';

    public $value = self::DEFAULT_PROP;

    public static $count = 0;

    public function toString(...$strings): string
    {
        return implode('_', array_merge([self::DEFAULT], $strings));
    }

    public function count(): int
    {
        return ++static::$count;
    }
}
