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
    predicate\isNotEmpty,
    predicate\isOfSize,
    predicate\isSameAs
};
/**
 * Tests for stubbles\date\span\Day.
 *
 * @group  date
 * @group  span
 */
class DayTest extends TestCase
{
    /**
     * @test
     */
    public function startDateSetToMidNight(): void
    {
        $day = new Day('2007-04-04');
        assertThat(
                $day->start()->asString(),
                equals('2007-04-04 00:00:00' . $day->start()->offset())
        );
    }

    /**
     * @test
     */
    public function endDateSetToOneSecondBeforeMidNight(): void
    {
        $day = new Day('2007-04-04');
        assertThat(
                $day->end()->asString(),
                equals('2007-04-04 23:59:59' . $day->end()->offset())
        );
    }

    /**
     * @test
     */
    public function amountOfDaysIsAlwaysOne(): void
    {
        $day = new Day('2007-04-04');
        assertThat($day->amountOfDays(), equals(1));
    }

    /**
     * @test
     */
    public function getDaysReturnsListWithSelf(): void
    {
        $day = new Day('2007-05-14');
        assertThat(
                $day->days(),
                isOfSize(1)->and(each(isSameAs($day)))
        );
    }

    /**
     * @test
     */
    public function tomorrowIsNotToday(): void
    {
        $day = new Day('tomorrow');
        assertFalse($day->isToday());
    }

    /**
     * @test
     */
    public function yesterdayIsNotToday(): void
    {
        $day = new Day('yesterday');
        assertFalse($day->isToday());
    }

    /**
     * @test
     */
    public function nowIsToday(): void
    {
        $day = new Day('now');
        assertTrue($day->isToday());
    }

    /**
     * @test
     */
    public function tomorrowIsFuture(): void
    {
        $day = new Day('tomorrow');
        assertTrue($day->isInFuture());
    }

    /**
     * @test
     */
    public function yesterdayIsNotFuture(): void
    {
        $day = new Day('yesterday');
        assertFalse($day->isInFuture());
    }

    /**
     * @test
     */
    public function todayIsNotFuture(): void
    {
        $day = new Day('now');
        assertFalse($day->isInFuture());
        $day = new Day();
        assertFalse($day->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainTheDayBefore(): void
    {
        $day = new Day('2007-04-04');
        assertFalse($day->containsDate(new Date('2007-04-03')));
    }

    /**
     * @test
     */
    public function doesContainTheExactDay(): void
    {
        $day = new Day('2007-04-04');
        assertTrue($day->containsDate(new Date('2007-04-04')));
    }

    /**
     * @test
     */
    public function doesNotContainTheDayAfter(): void
    {
        $day = new Day('2007-04-04');
        assertFalse($day->containsDate(new Date('2007-04-05')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsNameOfDayAndDate(): void
    {
        $day = new Day('2007-04-04');
        assertThat($day->asString(), equals('2007-04-04'));
    }

    /**
     * @test
     */
    public function properStringConversion(): void
    {
        $day = new Day('2007-04-04');
        assertThat((string) $day, equals('2007-04-04'));
    }

    /**
     * @test
     */
    public function asIntReturnsRepresentationOfDayWithinMonth(): void
    {
        $day = new Day('2007-05-14');
        assertThat($day->asInt(), equals(14));
    }

    /**
     * @test
     */
    public function formatReturnsOtherStringRepresentation(): void
    {
        $day = new Day('2007-05-14');
        assertThat($day->format('l, d.m.Y'), equals('Monday, 14.05.2007'));
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function tomorrowCreatesInstanceForTomorrow(): void
    {
        assertThat(
                Day::tomorrow()->asString(),
                equals(date('Y-m-d', strtotime('tomorrow')))
        );
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function yesterdayCreatesInstanceForYesterday(): void
    {
        assertThat(
                Day::yesterday()->asString(),
                equals(date('Y-m-d', strtotime('yesterday')))
        );
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function nextDayRaisesYearForDecember31st(): void
    {
        $day = new Day('2014-12-31');
        assertThat($day->next(), equals('2015-01-01'));
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function beforeDayLowersYearForJanuary1st(): void
    {
        $day = new Day('2014-01-01');
        assertThat($day->before(), equals('2013-12-31'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsDay(): void
    {
        $day = new Day('2014-01-01');
        assertThat($day->type(), equals('day'));
    }
}
