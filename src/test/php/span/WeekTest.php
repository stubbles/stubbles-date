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
 * Tests for stubbles\date\span\Week.
 */
#[Group('date')]
#[Group('span')]
class WeekTest extends TestCase
{
    #[Test]
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

    #[Test]
    #[DataProvider('weekDays')]
    public function daysReturnsAllSevenDays(
        string $dayString,
        string $expectedString,
        Day $day,
        int $expectedDay
    ): void {
        assertThat($dayString, equals($expectedString));
        assertThat($day->asInt(), equals($expectedDay));
    }

    #[Test]
    public function weekWhichStartsAfterTodayIsInFuture(): void
    {
        $week = new Week('tomorrow');
        assertTrue($week->isInFuture());
    }

    #[Test]
    public function weekWhichStartsBeforeTodayIsNotInFuture(): void
    {
        $week = new Week('yesterday');
        assertFalse($week->isInFuture());
    }

    #[Test]
    public function weekWhichStartsTodayIsNotInFuture(): void
    {
        $week = new Week('now');
        assertFalse($week->isInFuture());
    }

    #[Test]
    public function doesNotContainDatesBeforeBeginnOfWeek(): void
    {
        $week = new Week('2009-01-05');
        assertFalse($week->containsDate(new Date('2009-01-04')));
    }

    #[Test]
    public function containsAllDaysOfThisWeek(): void
    {
        $week = new Week('2009-01-05');
        assertThat(
            range(5, 11),
            each(fn($day) => $week->containsDate(new Date('2009-01-' . $day)))
        );
    }

    #[Test]
    public function doesNotContainDatesAfterEndOfWeek(): void
    {
        $week = new Week('2009-01-05');
        assertFalse($week->containsDate(new Date('2009-01-12')));
    }

    #[Test]
    public function stringRepresentationOfWeekContainsNumberOfWeek(): void
    {
        $week = new Week('2007-04-02');
        assertThat($week->asString(), equals('2007-W14'));
    }

    #[Test]
    public function properStringConversion(): void
    {
        $week = new Week('2007-04-02');
        assertThat((string) $week, equals('2007-W14'));
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function numberReturnsNumberOfWeek(): void
    {
        $week = new Week('2007-04-02');
        assertThat($week->number(), equals(14));
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function createFromStringParsesStringToCreateInstance(): void
    {
        assertThat(Week::fromString('2014-W05')->asString(), equals('2014-W05'));
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function createFromInvalidStringThrowsInvalidArgumentException(): void
    {
         expect(function() { Week::fromString('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function createFromInvalidWeekNumberThrowsInvalidArgumentException(): void
    {
         expect(fn() => Week::fromString('2014-W63'))
            ->throws(InvalidArgumentException::class);
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function typeIsWeek(): void
    {
        assertThat(Week::fromString('2014-W05')->type(), equals('week'));
    }
}
