<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date;
use stubbles\date\span\CustomDatespan;
use stubbles\date\span\Day;
use stubbles\date\span\Month;
use stubbles\date\span\Year;

use function bovigo\assert\assert;
use function bovigo\assert\assertNull;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\date\*()
 *
 * @group  date
 * @since  5.2.0
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function emptyValues(): array
    {
        return [[null], ['']];
    }

    /**
     * @param  mixed  $emptyValue
     * @test
     * @dataProvider  emptyValues
     */
    public function returnsNullForEmptyValues($emptyValue)
    {
        assertNull(span\parse($emptyValue));
    }

    /**
     * @test
     */
    public function parsesYear()
    {
        assert(span\parse('2015'), equals(new Year(2015)));
    }

    public function dayValues(): array
    {
        return [['today'], ['tomorrow'], ['yesterday'], ['2015-03-05']];
    }

    /**
     *
     * @param  string  $dayValue
     * @test
     * @dataProvider  dayValues
     */
    public function parsesDay(string $dayValue)
    {
        assert(span\parse($dayValue), equals(new Day($dayValue)));
    }

    public function monthValues(): array
    {
        return [['2015-03']];
    }

    /**
     *
     * @param  string  $monthValue
     * @test
     * @dataProvider  monthValues
     */
    public function parsesMonth(string $monthValue)
    {
        assert(span\parse($monthValue), equals(Month::fromString($monthValue)));
    }

    public function customDatespanValues(): array
    {
        return [
                [new CustomDatespan('yesterday', 'tomorrow'), 'yesterday,tomorrow'],
                [new CustomDatespan('2015-01-01', '2015-12-31'), '2015-01-01,2015-12-31']
        ];
    }

    /**
     *
     * @param  \stubbles\date\span\CustomDatespan  $expected
     * @param  string                              $value
     * @test
     * @dataProvider  customDatespanValues
     */
    public function parsesCustomDatespan(CustomDatespan $expected, string $value)
    {
        assert(span\parse($value), equals($expected));
    }
}
