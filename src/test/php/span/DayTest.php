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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\each;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNotEmpty;
use function bovigo\assert\predicate\isOfSize;
use function bovigo\assert\predicate\isSameAs;
/**
 * Tests for stubbles\date\span\Day.
 *
 * @group  date
 * @group  span
 */
class DayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function startDateSetToMidNight()
    {
        $day = new Day('2007-04-04');
        assert(
                $day->start()->asString(),
                equals('2007-04-04 00:00:00' . $day->start()->offset())
        );
    }

    /**
     * @test
     */
    public function endDateSetToOneSecondBeforeMidNight()
    {
        $day = new Day('2007-04-04');
        assert(
                $day->end()->asString(),
                equals('2007-04-04 23:59:59' . $day->end()->offset())
        );
    }

    /**
     * @test
     */
    public function amountOfDaysIsAlwaysOne()
    {
        $day = new Day('2007-04-04');
        assert($day->amountOfDays(), equals(1));
    }

    /**
     * @test
     */
    public function getDaysReturnsListWithSelf()
    {
        $day       = new Day('2007-05-14');
        $dateSpans = $day->days();
        assert(
                $dateSpans,
                isOfSize(1)->and(each(isSameAs($dateSpans['2007-05-14'])))
        );
    }

    /**
     * @test
     */
    public function tomorrowIsNotToday()
    {
        $day = new Day('tomorrow');
        assertFalse($day->isToday());
    }

    /**
     * @test
     */
    public function yesterdayIsNotToday()
    {
        $day = new Day('yesterday');
        assertFalse($day->isToday());
    }

    /**
     * @test
     */
    public function nowIsToday()
    {
        $day = new Day('now');
        assertTrue($day->isToday());
    }

    /**
     * @test
     */
    public function tomorrowIsFuture()
    {
        $day = new Day('tomorrow');
        assertTrue($day->isInFuture());
    }

    /**
     * @test
     */
    public function yesterdayIsNotFuture()
    {
        $day = new Day('yesterday');
        assertFalse($day->isInFuture());
    }

    /**
     * @test
     */
    public function todayIsNotFuture()
    {
        $day = new Day('now');
        assertFalse($day->isInFuture());
        $day = new Day();
        assertFalse($day->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainTheDayBefore()
    {
        $day = new Day('2007-04-04');
        assertFalse($day->containsDate(new Date('2007-04-03')));
    }

    /**
     * @test
     */
    public function doesContainTheExactDay()
    {
        $day = new Day('2007-04-04');
        assertTrue($day->containsDate(new Date('2007-04-04')));
    }

    /**
     * @test
     */
    public function doesNotContainTheDayAfter()
    {
        $day = new Day('2007-04-04');
        assertFalse($day->containsDate(new Date('2007-04-05')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsNameOfDayAndDate()
    {
        $day = new Day('2007-04-04');
        assert($day->asString(), equals('2007-04-04'));
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $day = new Day('2007-04-04');
        assert((string) $day, equals('2007-04-04'));
    }

    /**
     * @test
     */
    public function asIntReturnsRepresentationOfDayWithinMonth()
    {
        $day = new Day('2007-05-14');
        assert($day->asInt(), equals(14));
    }

    /**
     * @test
     */
    public function formatReturnsOtherStringRepresentation()
    {
        $day = new Day('2007-05-14');
        assert($day->format('l, d.m.Y'), equals('Monday, 14.05.2007'));
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function tomorrowCreatesInstanceForTomorrow()
    {
        assert(
                Day::tomorrow()->asString(),
                equals(date('Y-m-d', strtotime('tomorrow')))
        );
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function yesterdayCreatesInstanceForYesterday()
    {
        assert(
                Day::yesterday()->asString(),
                equals(date('Y-m-d', strtotime('yesterday')))
        );
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function nextDayRaisesYearForDecember31st()
    {
        $day = new Day('2014-12-31');
        assert($day->next(), equals('2015-01-01'));
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function beforeDayLowersYearForJanuary1st()
    {
        $day = new Day('2014-01-01');
        assert($day->before(), equals('2013-12-31'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsDay()
    {
        $day = new Day('2014-01-01');
        assert($day->type(), equals('day'));
    }
}
