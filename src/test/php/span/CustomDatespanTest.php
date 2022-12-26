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
    predicate\each,
    predicate\equals,
    predicate\isInstanceOf
};
/**
 * Tests for stubbles\date\span\CustomDatespan.
 *
 * @group date
 * @group span
 */
class CustomDatespanTest extends TestCase
{
    /**
     * @test
     */
    public function startDateCreatedFromStringInput(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $startDate      = $customDatespan->start();
        assertThat($startDate, isInstanceOf(Date::class));
        assertThat(
            $startDate->asString(),
            equals('2006-04-04 00:00:00' . $startDate->offset())
        );
    }

    /**
     * @test
     */
    public function endDateCreatedFromStringInput(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $endDate        = $customDatespan->end();
        assertThat($endDate, isInstanceOf(Date::class));
        assertThat(
            $endDate->asString(),
            equals('2006-04-20 23:59:59' . $endDate->offset())
        );
    }

    /**
     * @test
     */
    public function startDateIsSetToMidnight(): void
    {
        $customDatespan = new CustomDatespan(
            new Date('2006-04-04'),
            new Date('2006-04-20')
        );
        $startDate = $customDatespan->start();
        assertThat(
            $startDate->asString(),
            equals('2006-04-04 00:00:00' . $startDate->offset())
        );
    }

    /**
     * @test
     */
    public function endDateIsSetToMidnight(): void
    {
        $customDatespan = new CustomDatespan(
            new Date('2006-04-04'),
            new Date('2006-04-20')
        );
        $endDate = $customDatespan->end();
        assertThat(
            $endDate->asString(),
            equals('2006-04-20 23:59:59' . $endDate->offset())
        );
    }

    /**
     * @test
     */
    public function returnsAmountOfDaysInDatespan(): void
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        assertThat($customDatespan->amountOfDays(), equals(14));
    }

    /**
     * @return  array<array<mixed>>
     */
    public function datespanDays(): array
    {
        $return      = [];
        $expectedDay = 14;
        foreach ((new CustomDatespan('2007-05-14', '2007-05-27'))->days() as $dayString => $day) {
            $return[] = [$dayString, $day, $expectedDay++];
        }

        return $return;
    }

    /**
     * @test
     * @dataProvider  datespanDays
     */
    public function daysReturnsListOfAllDays(string $dayString, Day $day, int $expectedDay): void
    {
        assertThat($dayString, equals('2007-05-' . $expectedDay));
        assertThat($day->asInt(), equals($expectedDay));
    }

    /**
     * @test
     */
    public function isInFutureIfCurrentDateBeforeStartDate(): void
    {
        $customDatespan = new CustomDatespan('tomorrow', '+3 days');
        assertTrue($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateWithinSpan(): void
    {
        $customDatespan = new CustomDatespan('yesterday', '+3 days');
        assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateAfterEndDate(): void
    {
        $customDatespan = new CustomDatespan('-3 days', 'yesterday');
        assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateBeforeStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertFalse($customDatespan->containsDate(new Date('2006-04-03')));
    }

    /**
     * @test
     */
    public function containsAllDatesInSpan(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat(
            range(4, 20),
            each(fn($day) => $customDatespan->containsDate(new Date('2006-04-' . $day)))
        );
    }

    /**
     * @test
     */
    public function doesNotContainDateAfterEndDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertFalse($customDatespan->containsDate(new Date('2006-04-21')));
    }

    /**
     * @test
     */
    public function serializeAndUnserializeDoesNotDestroyStartDate(): void
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        assertTrue($customDatespan->start()->equals($unserialized->start()));
    }

    /**
     * @test
     */
    public function serializeAndUnserializeDoesNotDestroyEndDate(): void
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        assertTrue($customDatespan->end()->equals($unserialized->end()));
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsStartAndEndDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->asString(), equals('2006-04-04,2006-04-20'));
    }

    /**
     * @test
     */
    public function properStringConversion(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat((string) $customDatespan, equals('2006-04-04,2006-04-20'));
    }

    /**
     * @test
     * @since 3.5.0
     */
    public function startsBeforeChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->startsBefore('2006-04-05'));
    }

    /**
     * @test
     * @since 3.5.0
     */
    public function startsAfterChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->startsAfter('2006-04-03'));
    }

    /**
     * @test
     * @since 3.5.0
     */
    public function endsBeforeChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->endsBefore('2006-04-21'));
    }

    /**
     * @test
     * @since 3.5.0
     */
    public function endsAfterChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->endsAfter('2006-04-19'));
    }

    /**
     * @test
     * @since 3.5.0
     */
    public function formatStartReturnsFormattedStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->formatStart('Y-m-d'), equals('2006-04-04'));
    }

    /**
     * @test
     * @since 3.5.0
     */
    public function formatEndReturnsFormattedStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->formatEnd('Y-m-d'), equals('2006-04-20'));
    }

    /**
     * @test
     * @since 7.0.0
     */
    public function typeIsCustom(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->type(), equals('custom'));
    }
}
