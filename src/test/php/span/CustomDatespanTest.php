<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
 */
#[Group('date')]
#[Group('span')]
class CustomDatespanTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function returnsAmountOfDaysInDatespan(): void
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        assertThat($customDatespan->amountOfDays(), equals(14));
    }

    /**
     * @return  array<array<mixed>>
     */
    public static function datespanDays(): array
    {
        $return      = [];
        $expectedDay = 14;
        foreach ((new CustomDatespan('2007-05-14', '2007-05-27'))->days() as $dayString => $day) {
            $return[] = [$dayString, $day, $expectedDay++];
        }

        return $return;
    }

    #[Test]
    #[DataProvider('datespanDays')]
    public function daysReturnsListOfAllDays(string $dayString, Day $day, int $expectedDay): void
    {
        assertThat($dayString, equals('2007-05-' . $expectedDay));
        assertThat($day->asInt(), equals($expectedDay));
    }

    #[Test]
    public function isInFutureIfCurrentDateBeforeStartDate(): void
    {
        $customDatespan = new CustomDatespan('tomorrow', '+3 days');
        assertTrue($customDatespan->isInFuture());
    }

    #[Test]
    public function isNotInFutureIfCurrentDateWithinSpan(): void
    {
        $customDatespan = new CustomDatespan('yesterday', '+3 days');
        assertFalse($customDatespan->isInFuture());
    }

    #[Test]
    public function isNotInFutureIfCurrentDateAfterEndDate(): void
    {
        $customDatespan = new CustomDatespan('-3 days', 'yesterday');
        assertFalse($customDatespan->isInFuture());
    }

    #[Test]
    public function doesNotContainDateBeforeStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertFalse($customDatespan->containsDate(new Date('2006-04-03')));
    }

    #[Test]
    public function containsAllDatesInSpan(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat(
            range(4, 20),
            each(fn($day) => $customDatespan->containsDate(new Date('2006-04-' . $day)))
        );
    }

    #[Test]
    public function doesNotContainDateAfterEndDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertFalse($customDatespan->containsDate(new Date('2006-04-21')));
    }

    #[Test]
    public function serializeAndUnserializeDoesNotDestroyStartDate(): void
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        assertTrue($customDatespan->start()->equals($unserialized->start()));
    }

    #[Test]
    public function serializeAndUnserializeDoesNotDestroyEndDate(): void
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        assertTrue($customDatespan->end()->equals($unserialized->end()));
    }

    #[Test]
    public function stringRepresentationOfDayContainsStartAndEndDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->asString(), equals('2006-04-04,2006-04-20'));
    }

    #[Test]
    public function properStringConversion(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat((string) $customDatespan, equals('2006-04-04,2006-04-20'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function startsBeforeChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->startsBefore('2006-04-05'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function startsAfterChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->startsAfter('2006-04-03'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function endsBeforeChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->endsBefore('2006-04-21'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function endsAfterChecksStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->endsAfter('2006-04-19'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function formatStartReturnsFormattedStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->formatStart('Y-m-d'), equals('2006-04-04'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function formatEndReturnsFormattedStartDate(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->formatEnd('Y-m-d'), equals('2006-04-20'));
    }

    /**
     * @since 3.5.0
     */
    #[Test]
    public function typeIsCustom(): void
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertThat($customDatespan->type(), equals('custom'));
    }
}
