<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\date\Date;

use function bovigo\assert\{
    assertThat,
    assertFalse,
    assertTrue,
    expect,
    predicate\each,
    predicate\equals,
    predicate\isOfSize
};
/**
 * Tests for stubbles\date\span\Month.
 */
#[Group('date')]
#[Group('span')]
class MonthTest extends TestCase
{
    protected function tearDown(): void
    {
        MonthMockDay::$result = null;
    }

    #[Test]
    public function stringRepresentationContainsYearAndMonth(): void
    {
        $month = new Month(2007, 4);
        assertThat($month->asString(), equals('2007-04'));
    }

    #[Test]
    public function stringRepresentationForCorrectMonthNotFixed(): void
    {
        $month = new Month(2007, '04');
        assertThat($month->asString(), equals('2007-04'));
    }

    #[Test]
    public function properStringConversionContainsYearAndMonth(): void
    {
        $month = new Month(2007, 4);
        assertThat((string) $month, equals('2007-04'));
    }

    #[Test]
    public function properStringConversionForCorrectMonthNotFixed(): void
    {
        $month = new Month(2007, '04');
        assertThat((string) $month, equals('2007-04'));
    }

    #[Test]
    public function usesCurrentYearIfNotGiven(): void
    {
        $month = new Month(null, 10);
        assertThat($month->asString(), equals(date('Y') . '-10'));
    }

    #[Test]
    public function usesCurrentMonthIfNotGiven(): void
    {
        $month = new Month(2007);
        assertThat($month->asString(), equals('2007-' . date('m')));
    }

    #[Test]
    public function usesCurrentYearAndMonthIfNotGiven(): void
    {
        $month = new Month();
        assertThat($month->asString(), equals(date('Y') . '-' . date('m')));
    }

    /**
     * @return  array<array<mixed>>
     */
    public static function dayMonth(): array
    {
        return [
            [new Month(2007, 4), 30],
            [new Month(2007, 3), 31],
            [new Month(2007, 2), 28],
            [new Month(2008, 2), 29]
        ];
    }

    #[Test]
    #[DataProvider('dayMonth')]
    public function amountOfDaysIsAlwaysCorrect(Month $month, int $dayCount): void
    {
        assertThat($month->amountOfDays(), equals($dayCount));
        assertThat($month->days(), isOfSize($dayCount));
    }

    /**
     * @return  array<array<mixed>>
     */
    public static function monthDays(): array
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

    #[Test]
    #[DataProvider('monthDays')]
    public function daysReturnsAllDaysInMonth(
        string $dayString,
        string $expectedString,
        Day $day,
        int $expectedDay
    ): void {
        assertThat($dayString, equals($expectedString));
        assertThat($day->asInt(), equals($expectedDay));
    }

    #[Test]
    public function monthInNextYearIsInFuture(): void
    {
        $month = new Month(((int) date('Y')) + 1, 7);
        assertTrue($month->isInFuture());
    }

    #[Test]
    public function monthInLastYearIsNotInFuture(): void
    {
        $month = new Month(((int) date('Y')) - 1, 7);
        assertFalse($month->isInFuture());
    }

    #[Test]
    public function currentMonthIsNotInFuture(): void
    {
        $month = new Month(date('Y'), date('m'));
        assertFalse($month->isInFuture());
    }

    #[Test]
    public function doesNotContainDateFromPreviousMonth(): void
    {
        $month = new Month(2007, 4);
        assertFalse($month->containsDate(new Date('2007-03-31')));
    }

    #[Test]
    public function doesContainAllDatesForThisMonth(): void
    {
        $month = new Month(2007, 4);
        assertThat(
            range(1, 30),
            each(fn($day) => $month->containsDate(new Date('2007-04-' . $day)))
        );
    }

    #[Test]
    public function doesNotContainDateFromLaterMonth(): void
    {
        $month = new Month(2007, 4);
        assertFalse($month->containsDate(new Date('2007-05-01')));
    }

    #[Test]
    public function isCurrentMonthReturnsTrueForCreationWithoutArguments(): void
    {
        $month = new Month();
        assertTrue($month->isCurrentMonth());
    }

    #[Test]
    public function isCurrentMonthReturnsTrueWhenCreatedForCurrentMonth(): void
    {
        $month = new Month(date('Y'), date('m'));
        assertTrue($month->isCurrentMonth());
    }

    #[Test]
    public function isCurrentMonthReturnsFalseForAllOtherMonths(): void
    {
        $month = new Month(2007, 4);
        assertFalse($month->isCurrentMonth());
    }

    /**
     * @since 3.5.1
     */
    #[Test]
    public function lastCreatesInstanceWhichIsNotCurrentMonth(): void
    {
        assertFalse(Month::last()->isCurrentMonth());
    }

    /**
     * @since 3.5.1
     */
    #[Test]
    public function lastCreatesInstanceForPreviousMonth(): void
    {
        if (date('m') === '01') {
            $yearOfLastMonth = date('Y') - 1;
        } else {
            $yearOfLastMonth = date('Y');
        }

        assertThat(
            Month::last()->asString(),
            equals($yearOfLastMonth . '-'. date('m', strtotime('first day of previous month')))
        );
    }

    /**
     * @since 3.5.2
     */
    #[Test]
    public function createFromStringParsesStringToCreateInstance(): void
    {
        assertThat(Month::fromString('2014-05')->asString(), equals('2014-05'));
    }

    /**
     * @return  array<string[]>
     */
    public static function invalidMonthStrings(): array
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
     * @since  3.5.3
     */
    #[Test]
    #[DataProvider('invalidMonthStrings')]
    public function createFromInvalidStringThrowsInvalidArgumentException(
        string $invalid,
        string $expectedExceptionMessage
    ): void {
         expect(fn() => Month::fromString($invalid))
            ->throws(InvalidArgumentException::class)
            ->withMessage($expectedExceptionMessage);
    }

    /**
     * @since 5.2.0
     */
    #[Test]
    public function nextMonthRaisesYearForDecember(): void
    {
        $month = new Month(2014, 12);
        assertThat($month->next(), equals('2015-01'));
    }

    /**
     * @since 5.2.0
     */
    #[Test]
    public function beforeMonthLowersYearForJanuary(): void
    {
        $month = new Month(2014, 01);
        assertThat($month->before(), equals('2013-12'));
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function typeIsMonth(): void
    {
        assertThat(Month::fromString('2014-05')->type(), equals('month'));
    }

    /**
     * @since 5.5.0
     */
    #[Test]
    public function currentOrLastReturnsCurrentWhenTodayIsNotFirstOfMonth(): void
    {
        MonthMockDay::$result = '02';
        assertThat(Month::currentOrLastWhenFirstDay(), equals(new Month()));
    }

    /**
     * @since 7.0.0
     */
    #[Test]
    public function currentOrLastReturnsLastWhenTodayIsFirstOfMonth(): void
    {
        MonthMockDay::$result = '01';
        assertThat(Month::currentOrLastWhenFirstDay(), equals(Month::last()));
    }
}
