<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date;
use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isNotSameAs;
use function stubbles\date\assert\equalsDate;
/**
 * Tests for stubbles\date\DateModifier.
 *
 * @since  1.7.0
 * @group  date
 * @group  bug268
 */
class DateModifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * origin time zone for restoring in tearDown()
     *
     * @type  string
     */
    private $originTimeZone;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->originTimeZone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->originTimeZone);
    }

    /**
     * @test
     */
    public function changeDateReturnsNewDateInstance()
    {
        $date        = Date::now();
        $changedDate = $date->change()->to('+1 day');
        assert($changedDate, isNotSameAs($date));
        assertTrue($changedDate->isAfter($date));
        assertTrue($changedDate->isAfter(new Date('tomorrow')));
    }

    /**
     * @test
     */
    public function changeTimeWithInvalidArgumentThrowsIllegalArgumentException()
    {
        $date = new Date('2011-03-31 01:00:00');
        expect(function() use($date) { $date->change()->timeTo('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function changeTimeWithInvalidValuesThrowsIllegalArgumentException()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM errors in another way, test can be removed once migrated to PHP 7 and typehints for int added');
        }

        $date = new Date('2011-03-31 01:00:00');
        expect(function() use($date) { $date->change()->timeTo('in:val:id'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function changeTimeReturnsNewInstance()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert($date->change()->timeTo('14:13:12'), isNotSameAs($date));
    }

    /**
     * @test
     * @since  5.1.0
     * @group  issue_2
     */
    public function changeTimeToStartOfDayReturnsNewInstance()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert($date->change()->timeToStartOfDay(), isNotSameAs($date));
    }

    /**
     * @test
     * @since  5.1.0
     * @group  issue_2
     */
    public function changeTimeToStartOfDaySetsTime()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->timeToStartOfDay(),
                equalsDate('2011-03-31T00:00:00+00:00')
        );
    }

    /**
     * @test
     * @since  5.1.0
     * @group  issue_2
     */
    public function changeTimeToEndOfDayReturnsNewInstance()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert($date->change()->timeToEndOfDay(), isNotSameAs($date));
    }

    /**
     * @test
     * @since  5.1.0
     * @group  issue_2
     */
    public function changeTimeToEndOfDaySetsTime()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->timeToEndOfDay(),
                equalsDate('2011-03-31T23:59:59+00:00')
        );
    }

    /**
     * @test
     */
    public function changeTimeChangesTimeOnlyButKeepsDate()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->timeTo('14:13:12'),
                equalsDate('2011-03-31T14:13:12+00:00')
        );
    }

    /**
     * @test
     */
    public function changeHourToOnlyChangesHour()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->hourTo(14),
                equalsDate('2011-03-31T14:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByHoursAddsGivenAmountOfHours()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byHours(14),
                equalsDate('2011-03-31T15:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByHoursChangesDateWhenGivenValueExceedsStandardHours()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byHours(24),
                equalsDate('2011-04-01T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByHoursSubtractsNegativeAmountOfHours()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byHours(-24),
                equalsDate('2011-03-30T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeMinuteToOnlyChangesMinutes()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->minuteTo(13),
                equalsDate('2011-03-31T01:13:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMinutesAddsGivenAmountOfMinutes()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byMinutes(14),
                equalsDate('2011-03-31T01:15:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMinutesChangesHoursWhenGivenValueExceedsStandardMinutes()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byMinutes(60),
                equalsDate('2011-03-31T02:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMinutesSubtractsNegativeAmountOfMinutes()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byMinutes(-24),
                equalsDate('2011-03-31T00:37:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeSecondToOnlyChangesSeconds()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->secondTo(12),
                equalsDate('2011-03-31T01:00:12+00:00')
        );
    }

    /**
     * @test
     */
    public function changeBySecondsAddsGivenAmountOfSeconds()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->bySeconds(14),
                equalsDate('2011-03-31T01:01:15+00:00')
        );
    }

    /**
     * @test
     */
    public function changeBySecondsChangesMinutesWhenGivenValueExceedsStandardSeconds()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->bySeconds(60),
                equalsDate('2011-03-31T01:02:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeBySecondsSubtractsNegativeAmountOfSeconds()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->bySeconds(-24),
                equalsDate('2011-03-31T01:00:37+00:00')
        );
    }

    /**
     * @test
     */
    public function changeDateWithInvalidArgumentThrowsIllegalArgumentException()
    {
        $date = new Date('2011-03-31 01:00:00');
        expect(function() use($date) { $date->change()->dateTo('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function changeDateWithInvalidValuesThrowsIllegalArgumentException()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM errors in another way, test can be removed once migrated to PHP 7 and typehints for int added');
        }

        $date = new Date('2011-03-31 01:00:00');
        expect(function() use($date) { $date->change()->dateTo('in-val-id'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function changeDateToReturnsNewInstance()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert($date->change()->dateTo('2012-7-15'), isNotSameAs($date));
    }

    /**
     * @test
     */
    public function changeDateToChangesDateOnlyButKeepsTime()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->dateTo('2012-7-15'),
                equalsDate('2012-07-15T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeYearToOnlyChangesYear()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->yearTo(2012),
                equalsDate('2012-03-31T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByYearsAddsGivenAmountOfYears()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byYears(14),
                equalsDate('2025-03-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByYearsSubtractsNegativeAmountOfYears()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byYears(-11),
                equalsDate('2000-03-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeMonthToOnlyChangesMonth()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->monthTo(7),
                equalsDate('2011-07-31T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMonthsAddsGivenAmountOfMonths()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byMonths(4),
                equalsDate('2011-07-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMonthsChangesYearWhenGivenValueExceedsStandardMonths()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byMonths(12),
                equalsDate('2012-03-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMonthsSubtractsNegativeAmountOfMonths()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byMonths(-10),
                equalsDate('2010-05-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeDayToOnlyChangesDay()
    {
        $date = new Date('2011-03-31 01:00:00');
        assert(
                $date->change()->dayTo(15),
                equalsDate('2011-03-15T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByDaysAddsGivenAmountOfDays()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byDays(4),
                equalsDate('2011-04-04T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByDaysChangesMonthWhenGivenValueExceedsStandardDays()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byDays(40),
                equalsDate('2011-05-10T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByDaysSubtractsNegativeAmountOfDays()
    {
        $date = new Date('2011-03-31 01:01:01');
        assert(
                $date->change()->byDays(-5),
                equalsDate('2011-03-26T01:01:01+00:00')
        );
    }
}
