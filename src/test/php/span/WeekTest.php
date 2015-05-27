<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date\span;
use stubbles\date\Date;
/**
 * Tests for stubbles\date\span\Week.
 *
 * @group  date
 * @group  span
 */
class WeekTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function amountOfDaysIsAlwaysSeven()
    {
        $week = new Week('2007-05-14');
        $this->assertEquals(7, $week->amountOfDays());
    }

    /**
     * @test
     */
    public function daysReturnsAllSevenDays()
    {
        $week = new Week('2007-05-14');
        $days = 0;
        $expectedDay = 14;
        foreach ($week->days() as $dayString => $day) {
            $this->assertEquals(
                    '2007-05-' . str_pad($expectedDay, 2, '0', STR_PAD_LEFT),
                    $dayString
            );
            $this->assertEquals($expectedDay, $day->asInt());
            $expectedDay++;
            $days++;
        }

        $this->assertEquals(7, $days);
    }

    /**
     * @test
     */
    public function weekWhichStartsAfterTodayIsInFuture()
    {
        $week = new Week('tomorrow');
        $this->assertTrue($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsBeforeTodayIsNotInFuture()
    {
        $week = new Week('yesterday');
        $this->assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsTodayIsNotInFuture()
    {
        $week = new Week('now');
        $this->assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDatesBeforeBeginnOfWeek()
    {
        $week = new Week('2009-01-05');
        $this->assertFalse($week->containsDate(new Date('2009-01-04')));
    }

    /**
     * @test
     */
    public function containsAllDaysOfThisWeek()
    {
        $week = new Week('2009-01-05');
        $this->assertTrue($week->containsDate(new Date('2009-01-05')));
        $this->assertTrue($week->containsDate(new Date('2009-01-06')));
        $this->assertTrue($week->containsDate(new Date('2009-01-07')));
        $this->assertTrue($week->containsDate(new Date('2009-01-08')));
        $this->assertTrue($week->containsDate(new Date('2009-01-09')));
        $this->assertTrue($week->containsDate(new Date('2009-01-10')));
        $this->assertTrue($week->containsDate(new Date('2009-01-11')));
    }

    /**
     * @test
     */
    public function doesNotContainDatesAfterEndOfWeek()
    {
        $week = new Week('2009-01-05');
        $this->assertFalse($week->containsDate(new Date('2009-01-12')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfWeekContainsNumberOfWeek()
    {
        $week = new Week('2007-04-02');
        $this->assertEquals('2007-W14', $week->asString());
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $week = new Week('2007-04-02');
        $this->assertEquals('2007-W14', (string) $week);
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function numberReturnsNumberOfWeek()
    {
        $week = new Week('2007-04-02');
        $this->assertEquals(14, $week->number());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function createFromStringParsesStringToCreateInstance()
    {
        $this->assertEquals(
                '2014-W05',
                Week::fromString('2014-W05')->asString()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  5.3.0
     */
    public function createFromInvalidStringThrowsInvalidArgumentException()
    {
         Week::fromString('invalid');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  5.3.0
     */
    public function createFromInvalidWeekNumberThrowsInvalidArgumentException()
    {
         Week::fromString('2014-W63');
    }
}
