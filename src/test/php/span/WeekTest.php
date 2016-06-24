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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isOfSize;
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
        assert($week->amountOfDays(), equals(7));
        assert($week->days(), isOfSize(7));
    }

    /**
     * @return  array
     */
    public function weekDays()
    {
        $return      = [];
        $expectedDay = 14;
        foreach ((new Week('2007-05-14'))->days() as $dayString => $day) {
            $return[] = [
                    $dayString,
                    '2007-05-' . $expectedDay,
                    $day,
                    $expectedDay++
            ];
        }

        return $return;
    }

    /**
     * @test
     * @dataProvider  weekDays
     */
    public function daysReturnsAllSevenDays($dayString, $expectedString, Day $day, $expectedDay)
    {
        assert($dayString, equals($expectedString));
        assert($day->asInt(), equals($expectedDay));
    }

    /**
     * @test
     */
    public function weekWhichStartsAfterTodayIsInFuture()
    {
        $week = new Week('tomorrow');
        assertTrue($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsBeforeTodayIsNotInFuture()
    {
        $week = new Week('yesterday');
        assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsTodayIsNotInFuture()
    {
        $week = new Week('now');
        assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDatesBeforeBeginnOfWeek()
    {
        $week = new Week('2009-01-05');
        assertFalse($week->containsDate(new Date('2009-01-04')));
    }

    /**
     * @test
     */
    public function containsAllDaysOfThisWeek()
    {
        $week = new Week('2009-01-05');
        assertTrue($week->containsDate(new Date('2009-01-05')));
        assertTrue($week->containsDate(new Date('2009-01-06')));
        assertTrue($week->containsDate(new Date('2009-01-07')));
        assertTrue($week->containsDate(new Date('2009-01-08')));
        assertTrue($week->containsDate(new Date('2009-01-09')));
        assertTrue($week->containsDate(new Date('2009-01-10')));
        assertTrue($week->containsDate(new Date('2009-01-11')));
    }

    /**
     * @test
     */
    public function doesNotContainDatesAfterEndOfWeek()
    {
        $week = new Week('2009-01-05');
        assertFalse($week->containsDate(new Date('2009-01-12')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfWeekContainsNumberOfWeek()
    {
        $week = new Week('2007-04-02');
        assert($week->asString(), equals('2007-W14'));
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $week = new Week('2007-04-02');
        assert((string) $week, equals('2007-W14'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function numberReturnsNumberOfWeek()
    {
        $week = new Week('2007-04-02');
        assert($week->number(), equals(14));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function createFromStringParsesStringToCreateInstance()
    {
        assert(Week::fromString('2014-W05')->asString(), equals('2014-W05'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function createFromInvalidStringThrowsInvalidArgumentException()
    {
         expect(function() { Week::fromString('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function createFromInvalidWeekNumberThrowsInvalidArgumentException()
    {
         expect(function() { Week::fromString('2014-W63'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsWeek()
    {
        assert(Week::fromString('2014-W05')->type(), equals('week'));
    }
}
