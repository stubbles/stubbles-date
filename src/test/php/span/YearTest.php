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
/**
 * Tests for stubbles\date\span\Month.
 *
 * @group  date
 * @group  span
 */
class YearTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringRepresentationContainsYear()
    {
        $year = new Year(2007);
        assert($year->asString(), equals('2007'));
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $year = new Year(2007);
        assert((string) $year, equals('2007'));
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven()
    {
        $year = new Year();
        assert($year->asString(), equals(date('Y')));
    }

    /**
     * @test
     */
    public function amountOfDaysIs366ForLeapYears()
    {
        $year = new Year(2008);
        assert($year->amountOfDays(), equals(366));
    }

    /**
     * @test
     */
    public function amountOfDaysIs365ForNonLeapYears()
    {
        $year = new Year(2007);
        assert($year->amountOfDays(), equals(365));
    }

    /**
     * data provider for getDaysReturnsAllDaysInYear()
     *
     * @return  array
     */
    public function dayYear()
    {
        return [[new Year(2007), 365],
                [new Year(2008), 366]
        ];
    }

    /**
     * @param  \stubbles\date\span\Year  $year      year to get days for
     * @param  int                       $dayCount  amount of days in this year
     * @test
     * @dataProvider  dayYear
     */
    public function daysReturnsAllDaysInYear(Year $year, $dayCount)
    {
        $days = 0;
        foreach ($year->days() as $dayString => $day) {
            assert($day->asString(), equals($dayString));
            $days++;
        }

        assert($days, equals($dayCount));
    }

    /**
     * @test
     */
    public function monthsReturnsAllMonth()
    {
        $year   = new Year(2007);
        $expectedMonth = 0;
        foreach ($year->months() as $monthString => $month) {
            $expectedMonth++;
            assert($month->asString(), equals($monthString));
            assert(
                    $monthString,
                    equals('2007-' . str_pad($expectedMonth, 2, '0', STR_PAD_LEFT))
            );
        }

        assert($expectedMonth, equals(12));
    }

    /**
     * @test
     */
    public function nextYearIsInFuture()
    {
        $year = new Year(date('Y') + 1);
        assertTrue($year->isInFuture());
    }

    /**
     * @test
     */
    public function lastYearIsNotInFuture()
    {
        $year = new Year(date('Y') - 1);
        assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function currentYearIsNotInFuture()
    {
        $year = new Year();
        assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousYear()
    {
        $year = new Year(2007);
        assertFalse($year->containsDate(new Date('2006-12-31')));
    }

    /**
     * @test
     */
    public function doesContainAllDatesForThisYear()
    {
        $year = new Year(2007);
        for ($month = 1; $month <= 12; $month++) {
            $days = $this->createMonth($month)->amountOfDays();
            for ($day = 1; $day <= $days; $day++) {
                assertTrue($year->containsDate(new Date('2007-' . $month . '-' . $day)));
            }
        }
    }

    /**
     * helper method to create a month
     *
     * @param   int  $month
     * @return  Month
     */
    private function createMonth($month)
    {
        return new Month(2007, $month);
    }

    /**
     * @test
     */
    public function doesNotContainDateFromLaterYear()
    {
        $year = new Year(2007);
        assertFalse($year->containsDate(new Date('2008-01-01')));
    }

    /**
     * @test
     */
    public function isLeapYearReturnsTrueForLeapYears()
    {
        $year = new Year(2008);
        assertTrue($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isLeapYearReturnsFalseForNonLeapYears()
    {
        $year = new Year(2007);
        assertFalse($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsTrueForCreationWithoutArguments()
    {
        $year = new Year();
        assertTrue($year->isCurrentYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsTrueWhenCreatedForCurrentYear()
    {
        $year = new Year(date('Y'));
        assertTrue($year->isCurrentYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsFalseForAllOtherYears()
    {
        $year = new Year(2007);
        assertFalse($year->isCurrentYear());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsYear()
    {
        $year = new Year(2007);
        assert($year->type(), equals('year'));
    }
}
