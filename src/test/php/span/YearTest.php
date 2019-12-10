<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;
use PHPUnit\Framework\TestCase;
use stubbles\date\Date;

use function bovigo\assert\{
    assertThat,
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
class YearTest extends TestCase
{
    /**
     * @test
     * @since  7.0.0
     */
    public function constructWithInvalidValueThrowsInvalidArgumentException(): void
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
    public function stringRepresentationContainsYear(): void
    {
        $year = new Year(2007);
        assertThat($year->asString(), equals('2007'));
    }

    /**
     * @test
     */
    public function properStringConversion(): void
    {
        $year = new Year(2007);
        assertThat((string) $year, equals('2007'));
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven(): void
    {
        $year = new Year();
        assertThat($year->asString(), equals(date('Y')));
    }

    /**
     * @test
     */
    public function amountOfDaysIs366ForLeapYears(): void
    {
        $year = new Year(2008);
        assertThat($year->amountOfDays(), equals(366));
    }

    /**
     * @test
     */
    public function amountOfDaysIs365ForNonLeapYears(): void
    {
        $year = new Year(2007);
        assertThat($year->amountOfDays(), equals(365));
    }

    /**
     * @return  array<array<mixed>>
     */
    public function dayYear(): array
    {
        return [[new Year(2007), 365],
                [new Year(2008), 366]
        ];
    }

    /**
     * @test
     * @dataProvider  dayYear
     */
    public function daysReturnsAmountOfDaysWithinYear(Year $year, int $dayCount): void
    {
        assertThat($year->days(), isOfSize($dayCount));
    }

    /**
     * @test
     */
    public function daysReturnsAllDaysInYear(): void
    {
        assertThat((new Year(2008))->days(), each(isInstanceOf(Day::class)));
    }

    /**
     * @return  array<array<mixed>>
     */
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
    ): void {
        assertThat($month->asString(), equals($monthString));
        assertThat($monthString, equals($expectedMonth));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthIsValidOnStart(): void
    {
        assertTrue((new Year(2007))->months()->valid());
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthIsInvalidWhenFullyIterated(): void
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
    public function yearMonthCurrentReturnsFirstMonthOfYearOnStart(): void
    {
        assertThat((new Year(2007))->months()->current(), equals(new Month(2007, 1)));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthKeyIsStringRepresentationOfMonth(): void
    {
        assertThat((new Year(2007))->months()->key(), equals('2007-01'));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function yearMonthCurrentReturnsFirstMonthOfYearAfterRewind(): void
    {
        $months = (new Year(2007))->months();
        $months->next();
        $months->rewind();
        assertThat($months->current(), equals(new Month(2007, 1)));
    }

    /**
     * @test
     */
    public function monthReturnsRightAmountOfMonth(): void
    {
        assertThat((new Year(2007))->months(), isOfSize(12));
    }

    /**
     * @test
     */
    public function nextYearIsInFuture(): void
    {
        $year = new Year(((int) date('Y')) + 1);
        assertTrue($year->isInFuture());
    }

    /**
     * @test
     */
    public function lastYearIsNotInFuture(): void
    {
        $year = new Year(((int) date('Y')) - 1);
        assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function currentYearIsNotInFuture(): void
    {
        $year = new Year();
        assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousYear(): void
    {
        $year = new Year(2007);
        assertFalse($year->containsDate(new Date('2006-12-31')));
    }

    /**
     * @return  array<array<mixed>>
     */
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
    public function doesContainAllDatesForThisYear(Year $year, int $month, int $day): void
    {
        assertTrue($year->containsDate(new Date('2007-' . $month . '-' . $day)));
    }

    /**
     * @test
     */
    public function doesNotContainDateFromLaterYear(): void
    {
        $year = new Year(2007);
        assertFalse($year->containsDate(new Date('2008-01-01')));
    }

    /**
     * @test
     */
    public function isLeapYearReturnsTrueForLeapYears(): void
    {
        $year = new Year(2008);
        assertTrue($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isLeapYearReturnsFalseForNonLeapYears(): void
    {
        $year = new Year(2007);
        assertFalse($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsTrueForCreationWithoutArguments(): void
    {
        $year = new Year();
        assertTrue($year->isCurrentYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsTrueWhenCreatedForCurrentYear(): void
    {
        $year = new Year(date('Y'));
        assertTrue($year->isCurrentYear());
    }

    /**
     * @test
     */
    public function isCurrentYearReturnsFalseForAllOtherYears(): void
    {
        $year = new Year(2007);
        assertFalse($year->isCurrentYear());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsYear(): void
    {
        $year = new Year(2007);
        assertThat($year->type(), equals('year'));
    }
}
