<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date;

use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stubbles\date\span\CustomDatespan;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Year;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\date\*()
 *
 * @group date
 * @since 5.2.0
 */
class FunctionsTest extends TestCase
{
    public function emptyValues(): Generator
    {
        yield [null];
        yield [''];
    }

    /**
     * @test
     * @dataProvider emptyValues
     */
    public function returnsNullForEmptyValues(?string $emptyValue): void
    {
        assertNull(span\parse($emptyValue));
    }

    /**
     * @test
     */
    public function parsesYear(): void
    {
        assertThat(span\parse('2015'), equals(new Year(2015)));
    }

    public function dayValues(): Generator
    {
        yield ['today'];
        yield ['tomorrow'];
        yield ['yesterday'];
        yield ['2015-03-05'];
    }

    /**
     * @test
     * @dataProvider dayValues
     */
    public function parsesDay(string $dayValue): void
    {
        assertThat(span\parse($dayValue), equals(new Day($dayValue)));
    }

    /**
     * @test
     */
    public function parseInvalidDayThrowsInvalidArgumentException(): void
    {
        expect(fn() => span\parse('foo'))
            ->throws(InvalidArgumentException::class);
    }

    public function monthValues(): Generator
    {
        yield ['2015-03'];
    }

    /**
     *
     * @test
     * @dataProvider monthValues
     */
    public function parsesMonth(string $monthValue): void
    {
        assertThat(span\parse($monthValue), equals(Month::fromString($monthValue)));
    }

    public function customDatespanValues(): Generator
    {
        yield [new CustomDatespan('yesterday', 'tomorrow'), 'yesterday,tomorrow'];
        yield [new CustomDatespan('2015-01-01', '2015-12-31'), '2015-01-01,2015-12-31'];
    }

    /**
     * @test
     * @dataProvider customDatespanValues
     */
    public function parsesCustomDatespan(CustomDatespan $expected, string $value): void
    {
        assertThat(span\parse($value), equals($expected));
    }
}
