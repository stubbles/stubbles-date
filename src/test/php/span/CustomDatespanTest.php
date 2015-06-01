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
        $this->assertInstanceOf('stubbles\date\Date', $startDate);
        $this->assertEquals(
                '2006-04-04 00:00:00' . $startDate->offset(),
                $startDate->asString()
        );
    }

    /**
     * @test
     */
    public function endDateCreatedFromStringInput()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $endDate        = $customDatespan->end();
        $this->assertInstanceOf('stubbles\date\Date', $endDate);
        $this->assertEquals(
                '2006-04-20 23:59:59' . $endDate->offset(),
                $endDate->asString()
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
        $startDate      = $customDatespan->start();
        $this->assertEquals(
                '2006-04-04 00:00:00' . $startDate->offset(),
                $startDate->asString()
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
        $endDate        = $customDatespan->end();
        $this->assertEquals(
                '2006-04-20 23:59:59' . $endDate->offset(),
                $endDate->asString()
        );
    }

    /**
     * @test
     */
    public function returnsAmountOfDaysInDatespan()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $this->assertEquals(14, $customDatespan->amountOfDays());
    }

    /**
     * @test
     */
    public function daysReturnsListOfAllDays()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $expectedDay    = 14;
        foreach ($customDatespan->days() as $dayString => $day) {
            $this->assertEquals(
                    '2007-05-' . str_pad($expectedDay, 2, '0', STR_PAD_LEFT),
                    $dayString
            );
            $this->assertEquals($expectedDay, $day->asInt());
            $expectedDay++;
        }
    }

    /**
     * @test
     */
    public function isInFutureIfCurrentDateBeforeStartDate()
    {
        $customDatespan = new CustomDatespan('tomorrow', '+3 days');
        $this->assertTrue($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateWithinSpan()
    {
        $customDatespan = new CustomDatespan('yesterday', '+3 days');
        $this->assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateAfterEndDate()
    {
        $customDatespan = new CustomDatespan('-3 days', 'yesterday');
        $this->assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateBeforeStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertFalse($customDatespan->containsDate(new Date('2006-04-03')));
    }

    /**
     * @test
     */
    public function containsAllDatesInSpan()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        for ($day = 4; $day <= 20; $day++) {
            $this->assertTrue($customDatespan->containsDate(new Date('2006-04-' . $day)));
        }
    }

    /**
     * @test
     */
    public function doesNotContainDateAfterEndDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertFalse($customDatespan->containsDate(new Date('2006-04-21')));
    }

    /**
     * @test
     */
    public function serializeAndUnserializeDoesNotDestroyStartAndEndDate()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        $this->assertTrue($customDatespan->start()->equals($unserialized->start()));
        $this->assertTrue($customDatespan->end()->equals($unserialized->end()));
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsStartAndEndDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertEquals('2006-04-04,2006-04-20', $customDatespan->asString());
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertEquals('2006-04-04,2006-04-20', (string) $customDatespan);
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function startsBeforeChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertTrue($customDatespan->startsBefore('2006-04-05'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function startsAfterChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertTrue($customDatespan->startsAfter('2006-04-03'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function endsBeforeChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertTrue($customDatespan->endsBefore('2006-04-21'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function endsAfterChecksStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertTrue($customDatespan->endsAfter('2006-04-19'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function formatStartReturnsFormattedStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertEquals('2006-04-04', $customDatespan->formatStart('Y-m-d'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function formatEndReturnsFormattedStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertEquals('2006-04-20', $customDatespan->formatEnd('Y-m-d'));
    }
}
