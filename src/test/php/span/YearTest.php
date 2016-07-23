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
namespace stubbles\date\span;
use stubbles\date\Date;

use function bovigo\assert\{
    assert,
    assertFalse,
    assertTrue,
    expect,
    predicate\each,
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isOfSize
};
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
     * @since  7.0.0
     */
    public function constructWithInvalidValueThrowsInvalidArgumentException()
    {
        expect(function() { new Year('nope'); } )
                ->throws(\InvalidArgumentException::class)
                ->withMessage(
                        'Given year "nope" can not be treated as year, should'
                        . ' be something that can be casted to int without data loss'
                );
    }

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
     * @return  array
     */
    public function dayYear()
    {
        return [[new Year(2007), 365],
                [new Year(2008), 366]
        ];
    }

    /**
     * @test
     * @dataProvider  dayYear
     */
    public function daysReturnsAmountOfDaysWithinYear(Year $year, $dayCount)
    {
        assert($year->days(), isOfSize($dayCount));
    }

    /**
     * @test
     */
    public function daysReturnsAllDaysInYear()
    {
        assert((new Year(2008))->days(), each(isInstanceOf(Day::class)));
    }

    public function yearMonths(): array
    {
        $return        = [];
        $expectedMonth = 1;
        foreach ((new Year(2007))->months() as $monthString => $month) {
            $return[] = [
                    $monthString,
                    $month,
                    '2007-' . str_pad((string) $expectedMonth++, 2, '0', STR_PAD_LEFT)
            ];
        }

        return $return;
    }

    /**
     * @test
     * @dataProvider  yearMonths
     */
    public function monthsReturnsAllMonth(
            string $monthString,
            Month $month,
            string $expectedMonth
    ) {
        assert($month->asString(), equals($monthString));
        assert($monthString, equals($expectedMonth));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthIsValidOnStart()
    {
        assertTrue((new Year(2007))->months()->valid());
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthIsInvalidWhenFullyIterated()
    {
        $months = (new Year(2007))->months();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        $months->next();
        assertFalse($months->valid());
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthCurrentReturnsFirstMonthOfYearOnStart()
    {
        assert((new Year(2007))->months()->current(), equals(new Month(2007, 1)));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthKeyIsStringRepresentationOfMonth()
    {
        assert((new Year(2007))->months()->key(), equals('2007-01'));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthCurrentReturnsFirstMonthOfYearAfterRewind()
    {
        $months = (new Year(2007))->months();
        $months->next();
        $months->rewind();
        assert($months->current(), equals(new Month(2007, 1)));
    }

    /**
     * @test
     */
    public function monthReturnsRightAmountOfMonth()
    {
        assert((new Year(2007))->months(), isOfSize(12));
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

    public function allDays(): array
    {
        $return = [];
        $year = new Year(2007);
        for ($month = 1; $month <= 12; $month++) {
            $days = (new Month(2007, $month))->amountOfDays();
            for ($day = 1; $day <= $days; $day++) {
                $return[] = [$year, $month, $day];
            }
        }

        return $return;
    }

    /**
     * @test
     * @dataProvider  allDays
     */
    public function doesContainAllDatesForThisYear(Year $year, int $month, int $day)
    {
        assertTrue($year->containsDate(new Date('2007-' . $month . '-' . $day)));
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
