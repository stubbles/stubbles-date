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
        $this->assertEquals('2007', $year->asString());
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $year = new Year(2007);
        $this->assertEquals('2007', (string) $year);
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven()
    {
        $year = new Year();
        $this->assertEquals(date('Y'), $year->asString());
    }

    /**
     * @test
     */
    public function amountOfDaysIs366ForLeapYears()
    {
        $year = new Year(2008);
        $this->assertEquals(366, $year->amountOfDays());
    }

    /**
     * @test
     */
    public function amountOfDaysIs365ForNonLeapYears()
    {
        $year = new Year(2007);
        $this->assertEquals(365, $year->amountOfDays());
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
            $this->assertEquals($dayString, $day->asString());
            $days++;
        }

        $this->assertEquals($dayCount, $days);
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
            $this->assertEquals(
                    $monthString,
                    $month->asString()
            );
            $this->assertEquals(
                    '2007-' . str_pad($expectedMonth, 2, '0', STR_PAD_LEFT),
                    $monthString
            );
        }

        $this->assertEquals(12, $expectedMonth);
    }

    /**
     * @test
     */
    public function nextYearIsInFuture()
    {
        $year = new Year(date('Y') + 1);
        $this->assertTrue($year->isInFuture());
    }

    /**
     * @test
     */
    public function lastYearIsNotInFuture()
    {
        $year = new Year(date('Y') - 1);
        $this->assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function currentYearIsNotInFuture()
    {
        $year = new Year();
        $this->assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousYear()
    {
        $year = new Year(2007);
        $this->assertFalse($year->containsDate(new Date('2006-12-31')));
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
                $this->assertTrue($year->containsDate(new Date('2007-' . $month . '-' . $day)));
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
        $this->assertFalse($year->containsDate(new Date('2008-01-01')));
    }

    /**
     * @test
     */
    public function isLeapYearReturnsTrueForLeapYears()
    {
        $year = new Year(2008);
        $this->assertTrue($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isLeapYearReturnsFalseForNonLeapYears()
    {
        $year = new Year(2007);
        $this->assertFalse($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsTrueForCreationWithoutArguments()
    {
        $year = new Year();
        $this->assertTrue($year->isCurrentYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsTrueWhenCreatedForCurrentYear()
    {
        $year = new Year(date('Y'));
        $this->assertTrue($year->isCurrentYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsFalseForAllOtherYears()
    {
        $year = new Year(2007);
        $this->assertFalse($year->isCurrentYear());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsYear()
    {
        $year = new Year(2007);
        $this->assertEquals('year', $year->type());
    }
}
