<?php

namespace Antoksa\CallableThat\Tests;

use Antoksa\CallableThat\That;
use LogicException;
use PHPUnit\Framework\TestCase;

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
    public function test_correctly_creates_callable($callable, string $expected)
    {
        $this->assertTrue(is_callable($callable));

        $this->assertSame($expected, $callable(new Dummy()));
    }

    public function test_throws_logic_exception_if_not_configured_properly()
    {
        $this->expectException(LogicException::class);

        (new That())();
    }

    public static function callables(): array
    {
        return [
            // Callable method
            [[(new That()), 'toString'], Dummy::DEFAULT],
            [(new That())->call('toString'), Dummy::DEFAULT],

            // Callable method with args
            [[(new That())->withArgs('first', 'second'), 'toString'], Dummy::DEFAULT.'_first_second'],
            [(new That())->call('toString')->withArgs('first', 'second'), Dummy::DEFAULT.'_first_second'],

            // Callable property
            [(new That())->get('value'), Dummy::DEFAULT_PROP],
            [(new That())->value, Dummy::DEFAULT_PROP],
        ];
    }

}

class Dummy
{
    const DEFAULT = 'Dummy';

    const DEFAULT_PROP = 'Dummy_prop';

    public $value = self::DEFAULT_PROP;

    public function toString(...$strings): string
    {
        return implode('_', array_merge([self::DEFAULT], $strings));
    }
}
