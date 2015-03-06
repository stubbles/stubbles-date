<?php
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
/**
 * Tests for stubbles\date\*()
 *
 * @group  date
 * @since  5.2.0
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function emptyValues()
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
        $this->assertNull(span\parse($emptyValue));
    }

    /**
     * @test
     */
    public function parsesYear()
    {
        $this->assertEquals(
                new Year(2015),
                span\parse('2015')
        );
    }

    /**
     * @return  array
     */
    public function dayValues()
    {
        return [['today'], ['tomorrow'], ['yesterday'], ['2015-03-05']];
    }

    /**
     *
     * @param  string  $dayValue
     * @test
     * @dataProvider  dayValues
     */
    public function parsesDay($dayValue)
    {
        $this->assertEquals(
                new Day($dayValue),
                span\parse($dayValue)
        );
    }

    /**
     * @return  array
     */
    public function monthValues()
    {
        return [['2015-03']];
    }

    /**
     *
     * @param  string  $monthValue
     * @test
     * @dataProvider  monthValues
     */
    public function parsesMonth($monthValue)
    {
        $this->assertEquals(
                Month::fromString($monthValue),
                span\parse($monthValue)
        );
    }

    /**
     * @return  array
     */
    public function customDatespanValues()
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
    public function parsesCustomDatespan(CustomDatespan $expected, $value)
    {
        $this->assertEquals(
                $expected,
                span\parse($value)
        );
    }
}
