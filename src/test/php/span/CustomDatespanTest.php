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
    predicate\each,
    predicate\equals,
    predicate\isInstanceOf
};
/**
 * Tests for stubbles\date\span\CustomDatespan.
 *
 * @group  date
 * @group  span
 */
class CustomDatespanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function startDateCreatedFromStringInput()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $startDate      = $customDatespan->start();
        assert($startDate, isInstanceOf(Date::class));
        assert(
                $startDate->asString(),
                equals('2006-04-04 00:00:00' . $startDate->offset())
        );
    }

    /**
     * @test
     */
    public function endDateCreatedFromStringInput()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $endDate        = $customDatespan->end();
        assert($endDate, isInstanceOf(Date::class));
        assert(
                $endDate->asString(),
                equals('2006-04-20 23:59:59' . $endDate->offset())
        );
    }

    /**
     * @test
     */
    public function startDateIsSetToMidnight()
    {
        $customDatespan = new CustomDatespan(
                new Date('2006-04-04'),
                new Date('2006-04-20')
        );
        $startDate = $customDatespan->start();
        assert(
                $startDate->asString(),
                equals('2006-04-04 00:00:00' . $startDate->offset())
        );
    }

    /**
     * @test
     */
    public function endDateIsSetToMidnight()
    {
        $customDatespan = new CustomDatespan(
                new Date('2006-04-04'),
                new Date('2006-04-20')
        );
        $endDate = $customDatespan->end();
        assert(
                $endDate->asString(),
                equals('2006-04-20 23:59:59' . $endDate->offset())
        );
    }

    /**
     * @test
     */
    public function returnsAmountOfDaysInDatespan()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        assert($customDatespan->amountOfDays(), equals(14));
    }

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
    public function daysReturnsListOfAllDays(string $dayString, Day $day, int $expectedDay)
    {
        assert($dayString, equals('2007-05-' . $expectedDay));
        assert($day->asInt(), equals($expectedDay));
    }

    /**
     * @test
     */
    public function isInFutureIfCurrentDateBeforeStartDate()
    {
        $customDatespan = new CustomDatespan('tomorrow', '+3 days');
        assertTrue($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateWithinSpan()
    {
        $customDatespan = new CustomDatespan('yesterday', '+3 days');
        assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateAfterEndDate()
    {
        $customDatespan = new CustomDatespan('-3 days', 'yesterday');
        assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateBeforeStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertFalse($customDatespan->containsDate(new Date('2006-04-03')));
    }

    /**
     * @test
     */
    public function containsAllDatesInSpan()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assert(
                range(4, 20),
                each(function($day) use($customDatespan)
                {
                    return $customDatespan->containsDate(new Date('2006-04-' . $day));
                })
        );
    }

    /**
     * @test
     */
    public function doesNotContainDateAfterEndDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertFalse($customDatespan->containsDate(new Date('2006-04-21')));
    }

    /**
     * @test
     */
    public function serializeAndUnserializeDoesNotDestroyStartDate()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        assertTrue($customDatespan->start()->equals($unserialized->start()));
    }

    /**
     * @test
     */
    public function serializeAndUnserializeDoesNotDestroyEndDate()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        assertTrue($customDatespan->end()->equals($unserialized->end()));
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsStartAndEndDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assert($customDatespan->asString(), equals('2006-04-04,2006-04-20'));
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assert((string) $customDatespan, equals('2006-04-04,2006-04-20'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function startsBeforeChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->startsBefore('2006-04-05'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function startsAfterChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->startsAfter('2006-04-03'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function endsBeforeChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->endsBefore('2006-04-21'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function endsAfterChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assertTrue($customDatespan->endsAfter('2006-04-19'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function formatStartReturnsFormattedStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assert($customDatespan->formatStart('Y-m-d'), equals('2006-04-04'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function formatEndReturnsFormattedStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assert($customDatespan->formatEnd('Y-m-d'), equals('2006-04-20'));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function typeIsCustom()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        assert($customDatespan->type(), equals('custom'));
    }
}
