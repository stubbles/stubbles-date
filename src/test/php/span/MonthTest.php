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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\each;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isOfSize;
/**
 * Tests for stubbles\date\span\Month.
 *
 * @group  date
 * @group  span
 */
class MonthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringRepresentationContainsYearAndMonth()
    {
        $month = new Month(2007, 4);
        assert($month->asString(), equals('2007-04'));
    }

    /**
     * @test
     */
    public function stringRepresentationForCorrectMonthNotFixed()
    {
        $month = new Month(2007, '04');
        assert($month->asString(), equals('2007-04'));
    }

    /**
     * @test
     */
    public function properStringConversionContainsYearAndMonth()
    {
        $month = new Month(2007, 4);
        assert((string) $month, equals('2007-04'));
    }

    /**
     * @test
     */
    public function properStringConversionForCorrectMonthNotFixed()
    {
        $month = new Month(2007, '04');
        assert((string) $month, equals('2007-04'));
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven()
    {
        $month = new Month(null, 10);
        assert($month->asString(), equals(date('Y') . '-10'));
    }

    /**
     * @test
     */
    public function usesCurrentMonthIfNotGiven()
    {
        $month = new Month(2007);
        assert($month->asString(), equals('2007-' . date('m')));
    }

    /**
     * @test
     */
    public function usesCurrentYearAndMonthIfNotGiven()
    {
        $month = new Month();
        assert($month->asString(), equals(date('Y') . '-' . date('m')));
    }

    public function dayMonth(): array
    {
        return [[new Month(2007, 4), 30],
                [new Month(2007, 3), 31],
                [new Month(2007, 2), 28],
                [new Month(2008, 2), 29]
        ];
    }

    /**
     * @param  \stubbles\date\span\Month  $month     month to get days for
     * @param  int                        $dayCount  amount of days in this month
     * @test
     * @dataProvider  dayMonth
     */
    public function amountOfDaysIsAlwaysCorrect(Month $month, int $dayCount)
    {
        assert($month->amountOfDays(), equals($dayCount));
        assert($month->days(), isOfSize($dayCount));
    }

    public function monthDays(): array
    {
        $return      = [];
        $expectedDay = 1;
        foreach ((new Month(2007, 3))->days() as $dayString => $day) {
            $return[] = [
                    $dayString,
                    '2007-03-' . str_pad((string) $expectedDay, 2, '0', STR_PAD_LEFT),
                    $day,
                    $expectedDay++
            ];
        }

        return $return;
    }

    /**
     * @test
     * @dataProvider  monthDays
     */
    public function daysReturnsAllDaysInMonth(
            string $dayString,
            string $expectedString,
            Day $day,
            int $expectedDay
    ) {
        assert($dayString, equals($expectedString));
        assert($day->asInt(), equals($expectedDay));
    }

    /**
     * @test
     */
    public function monthInNextYearIsInFuture()
    {
        $month = new Month(date('Y') + 1, 7);
        assertTrue($month->isInFuture());
    }

    /**
     * @test
     */
    public function monthInLastYearIsNotInFuture()
    {
        $month = new Month(date('Y') - 1, 7);
        assertFalse($month->isInFuture());
    }

    /**
     * @test
     */
    public function currentMonthIsNotInFuture()
    {
        $month = new Month(date('Y'), date('m'));
        assertFalse($month->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousMonth()
    {
        $month = new Month(2007, 4);
        assertFalse($month->containsDate(new Date('2007-03-31')));
    }

    /**
     * @test
     */
    public function doesContainAllDatesForThisMonth()
    {
        $month = new Month(2007, 4);
        assert(
                range(1, 30),
                each(function($day) use($month)
                {
                    return $month->containsDate(new Date('2007-04-' . $day));
                })
        );
    }

    /**
     * @test
     */
    public function doesNotContainDateFromLaterMonth()
    {
        $month = new Month(2007, 4);
        assertFalse($month->containsDate(new Date('2007-05-01')));
    }

    /**
     * @test
     */
    public function isCurrentMonthReturnsTrueForCreationWithoutArguments()
    {
        $month = new Month();
        assertTrue($month->isCurrentMonth());
    }

    /**
     * @test
     */
    public function isCurrentMonthReturnsTrueWhenCreatedForCurrentMonth()
    {
        $month = new Month(date('Y'), date('m'));
        assertTrue($month->isCurrentMonth());
    }

    /**
     * @test
     */
    public function isCurrentMonthReturnsFalseForAllOtherMonths()
    {
        $month = new Month(2007, 4);
        assertFalse($month->isCurrentMonth());
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function lastCreatesInstanceWhichIsNotCurrentMonth()
    {
        assertFalse(Month::last()->isCurrentMonth());
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function lastCreatesInstanceForPreviousMonth()
    {
        assert(
                Month::last()->asString(),
                equals(date('Y') . '-'. date('m', strtotime('first day of previous month')))
        );
    }

    /**
     * @test
     * @since  3.5.2
     */
    public function createFromStringParsesStringToCreateInstance()
    {
        assert(Month::fromString('2014-05')->asString(), equals('2014-05'));
    }

    public function invalidMonthStrings(): array
    {
        return [
                ['invalid', 'Can not parse month from string "invalid", format should be "YYYY-MM"'],
                ['in-10', 'Detected value in for year is not a valid year.'],
                ['2016-in', 'Detected value in for month is not a valid month.'],
                ['2016-0', 'Detected value 0 for month is not a valid month.'],
                ['2016-13', 'Detected value 13 for month is not a valid month.']
        ];
    }

    /**
     * @test
     * @dataProvider  invalidMonthStrings
     * @since  3.5.3
     */
    public function createFromInvalidStringThrowsInvalidArgumentException(
            string $invalid,
            string $expectedExceptionMessage
    ) {
         expect(function() use ($invalid) { Month::fromString($invalid); })
                ->throws(\InvalidArgumentException::class)
                ->withMessage($expectedExceptionMessage);
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function nextMonthRaisesYearForDecember()
    {
        $month = new Month(2014, 12);
        assert($month->next(), equals('2015-01'));
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function beforeMonthLowersYearForJanuary()
    {
        $month = new Month(2014, 01);
        assert($month->before(), equals('2013-12'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsMonth()
    {
        assert(Month::fromString('2014-05')->type(), equals('month'));
    }

    /**
     * @test
     * @since  5.5.0
     */
    public function currentOrLastWhenFirstDay()
    {
        $month = Month::currentOrLastWhenFirstDay();
        if (Date::now()->day() === 1) {
            assert($month, equals(Month::last()));
        } else {
            assert($month, equals(new Month()));
        }
    }
}
