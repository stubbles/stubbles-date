<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;

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
    predicate\isOfSize,
    predicate\isSameAs
};
/**
 * Tests for stubbles\date\span\Day.
 */
#[Group('date')]
#[Group('span')]
class DayTest extends TestCase
{
    #[Test]
    public function startDateSetToMidNight(): void
    {
        $day = new Day('2007-04-04');
        assertThat(
            $day->start()->asString(),
            equals('2007-04-04 00:00:00' . $day->start()->offset())
        );
    }

    #[Test]
    public function endDateSetToOneSecondBeforeMidNight(): void
    {
        $day = new Day('2007-04-04');
        assertThat(
            $day->end()->asString(),
            equals('2007-04-04 23:59:59' . $day->end()->offset())
        );
    }

    #[Test]
    public function amountOfDaysIsAlwaysOne(): void
    {
        $day = new Day('2007-04-04');
        assertThat($day->amountOfDays(), equals(1));
    }

    #[Test]
    public function getDaysReturnsListWithSelf(): void
    {
        $day = new Day('2007-05-14');
        assertThat(
            $day->days(),
            isOfSize(1)->and(each(isSameAs($day)))
        );
    }

    #[Test]
    public function tomorrowIsNotToday(): void
    {
        $day = new Day('tomorrow');
        assertFalse($day->isToday());
    }

    #[Test]
    public function yesterdayIsNotToday(): void
    {
        $day = new Day('yesterday');
        assertFalse($day->isToday());
    }

    #[Test]
    public function nowIsToday(): void
    {
        $day = new Day('now');
        assertTrue($day->isToday());
    }

    #[Test]
    public function tomorrowIsFuture(): void
    {
        $day = new Day('tomorrow');
        assertTrue($day->isInFuture());
    }

    #[Test]
    public function yesterdayIsNotFuture(): void
    {
        $day = new Day('yesterday');
        assertFalse($day->isInFuture());
    }

    #[Test]
    public function todayIsNotFuture(): void
    {
        $day = new Day('now');
        assertFalse($day->isInFuture());
        $day = new Day();
        assertFalse($day->isInFuture());
    }

    #[Test]
    public function doesNotContainTheDayBefore(): void
    {
        $day = new Day('2007-04-04');
        assertFalse($day->containsDate(new Date('2007-04-03')));
    }

    #[Test]
    public function doesContainTheExactDay(): void
    {
        $day = new Day('2007-04-04');
        assertTrue($day->containsDate(new Date('2007-04-04')));
    }

    #[Test]
    public function doesNotContainTheDayAfter(): void
    {
        $day = new Day('2007-04-04');
        assertFalse($day->containsDate(new Date('2007-04-05')));
    }

    #[Test]
    public function stringRepresentationOfDayContainsNameOfDayAndDate(): void
    {
        $day = new Day('2007-04-04');
        assertThat($day->asString(), equals('2007-04-04'));
    }

    #[Test]
    public function properStringConversion(): void
    {
        $day = new Day('2007-04-04');
        assertThat((string) $day, equals('2007-04-04'));
    }

    #[Test]
    public function asIntReturnsRepresentationOfDayWithinMonth(): void
    {
        $day = new Day('2007-05-14');
        assertThat($day->asInt(), equals(14));
    }

    #[Test]
    public function formatReturnsOtherStringRepresentation(): void
    {
        $day = new Day('2007-05-14');
        assertThat($day->format('l, d.m.Y'), equals('Monday, 14.05.2007'));
    }

    /**
     * @since 3.5.1
     */
    #[Test]
    public function tomorrowCreatesInstanceForTomorrow(): void
    {
        assertThat(
            Day::tomorrow()->asString(),
            equals(date('Y-m-d', strtotime('tomorrow')))
        );
    }

    /**
     * @since 3.5.1
     */
    #[Test]
    public function yesterdayCreatesInstanceForYesterday(): void
    {
        assertThat(
            Day::yesterday()->asString(),
            equals(date('Y-m-d', strtotime('yesterday')))
        );
    }

    /**
     * @since 5.2.0
     */
    #[Test]
    public function nextDayRaisesYearForDecember31st(): void
    {
        $day = new Day('2014-12-31');
        assertThat($day->next(), equals('2015-01-01'));
    }

    /**
     * @since 5.2.0
     */
    #[Test]
    public function beforeDayLowersYearForJanuary1st(): void
    {
        $day = new Day('2014-01-01');
        assertThat($day->before(), equals('2013-12-31'));
    }

    /**
     * @since 5.3.0
     */
    #[Test]
    public function typeIsDay(): void
    {
        $day = new Day('2014-01-01');
        assertThat($day->type(), equals('day'));
    }
}
