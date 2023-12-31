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
 * Tests for stubbles\date\span\Week.
 *
 * @group date
 * @group span
 */
class WeekTest extends TestCase
{
    /**
     * @test
     */
    public function amountOfDaysIsAlwaysSeven(): void
    {
        $week = new Week('2007-05-14');
        assertThat($week->amountOfDays(), equals(7));
        assertThat($week->days(), isOfSize(7));
    }

    /**
     * @return  array<array<mixed>>
     */
    public static function weekDays(): array
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
     * @dataProvider weekDays
     */
    public function daysReturnsAllSevenDays(
        string $dayString,
        string $expectedString,
        Day $day,
        int $expectedDay
    ): void {
        assertThat($dayString, equals($expectedString));
        assertThat($day->asInt(), equals($expectedDay));
    }

    /**
     * @test
     */
    public function weekWhichStartsAfterTodayIsInFuture(): void
    {
        $week = new Week('tomorrow');
        assertTrue($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsBeforeTodayIsNotInFuture(): void
    {
        $week = new Week('yesterday');
        assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsTodayIsNotInFuture(): void
    {
        $week = new Week('now');
        assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDatesBeforeBeginnOfWeek(): void
    {
        $week = new Week('2009-01-05');
        assertFalse($week->containsDate(new Date('2009-01-04')));
    }

    /**
     * @test
     */
    public function containsAllDaysOfThisWeek(): void
    {
        $week = new Week('2009-01-05');
        assertThat(
            range(5, 11),
            each(fn($day) => $week->containsDate(new Date('2009-01-' . $day)))
        );
    }

    /**
     * @test
     */
    public function doesNotContainDatesAfterEndOfWeek(): void
    {
        $week = new Week('2009-01-05');
        assertFalse($week->containsDate(new Date('2009-01-12')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfWeekContainsNumberOfWeek(): void
    {
        $week = new Week('2007-04-02');
        assertThat($week->asString(), equals('2007-W14'));
    }

    /**
     * @test
     */
    public function properStringConversion(): void
    {
        $week = new Week('2007-04-02');
        assertThat((string) $week, equals('2007-W14'));
    }

    /**
     * @test
     * @since 5.3.0
     */
    public function numberReturnsNumberOfWeek(): void
    {
        $week = new Week('2007-04-02');
        assertThat($week->number(), equals(14));
    }

    /**
     * @test
     * @since 5.3.0
     */
    public function createFromStringParsesStringToCreateInstance(): void
    {
        assertThat(Week::fromString('2014-W05')->asString(), equals('2014-W05'));
    }

    /**
     * @test
     * @since 5.3.0
     */
    public function createFromInvalidStringThrowsInvalidArgumentException(): void
    {
         expect(function() { Week::fromString('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since 5.3.0
     */
    public function createFromInvalidWeekNumberThrowsInvalidArgumentException(): void
    {
         expect(fn() => Week::fromString('2014-W63'))
            ->throws(InvalidArgumentException::class);
    }

    /**
     * @test
     * @since 5.3.0
     */
    public function typeIsWeek(): void
    {
        assertThat(Week::fromString('2014-W05')->type(), equals('week'));
    }
}
