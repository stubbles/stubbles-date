<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function bovigo\assert\{
    assertThat,
    assertTrue,
    expect,
    predicate\isNotSameAs
};
use function stubbles\date\assert\equalsDate;
/**
 * Tests for stubbles\date\DateModifier.
 *
 * @since 1.7.0
 * @group date
 * @group bug268
 */
class DateModifierTest extends TestCase
{
    /**
     * origin time zone for restoring in tearDown()
     */
    private string $originTimeZone;

    protected function setUp(): void
    {
        $this->originTimeZone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->originTimeZone);
    }

    /**
     * @test
     */
    public function changeDateReturnsNewDateInstance(): void
    {
        $date        = Date::now();
        $changedDate = $date->change()->to('+1 day');
        assertThat($changedDate, isNotSameAs($date));
        assertTrue($changedDate->isAfter($date));
        assertTrue($changedDate->isAfter(new Date('tomorrow')));
    }

    /**
     * @since  7.0.0
     * @return array<string[]>
     */
    public static function invalidArgumentsForTime(): array
    {
        return [
            ['invalid', 'Given time "invalid" does not follow required format HH:MM:SS'],
            ['in:23:45', 'Given value in for hour not suitable for changing the time.'],
            ['-1:23:45', 'Given value -1 for hour not suitable for changing the time.'],
            ['24:23:45', 'Given value 24 for hour not suitable for changing the time.'],
            ['12:in:00', 'Given value in for minute not suitable for changing the time.'],
            ['12:-1:59', 'Given value -1 for minute not suitable for changing the time.'],
            ['12:60:45', 'Given value 60 for minute not suitable for changing the time.'],
            ['12:00:in', 'Given value in for second not suitable for changing the time.'],
            ['12:59:-1', 'Given value -1 for second not suitable for changing the time.'],
            ['12:23:60', 'Given value 60 for second not suitable for changing the time.']
        ];
    }

    /**
     * @test
     * @dataProvider invalidArgumentsForTime
     */
    public function changeTimeWithInvalidArgumentThrowsIllegalArgumentException(
        string $invalid,
        string $exceptionMessage
    ): void {
        $date = new Date('2011-03-31 01:00:00');
        expect(fn() => $date->change()->timeTo($invalid))
            ->throws(InvalidArgumentException::class)
            ->withMessage($exceptionMessage);
    }

    /**
     * @test
     */
    public function changeTimeReturnsNewInstance(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat($date->change()->timeTo('14:13:12'), isNotSameAs($date));
    }

    /**
     * @test
     * @since 5.1.0
     * @group issue_2
     */
    public function changeTimeToStartOfDayReturnsNewInstance(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat($date->change()->timeToStartOfDay(), isNotSameAs($date));
    }

    /**
     * @test
     * @since 5.1.0
     * @group issue_2
     */
    public function changeTimeToStartOfDaySetsTime(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->timeToStartOfDay(),
            equalsDate('2011-03-31T00:00:00+00:00')
        );
    }

    /**
     * @test
     * @since 5.1.0
     * @group issue_2
     */
    public function changeTimeToEndOfDayReturnsNewInstance(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat($date->change()->timeToEndOfDay(), isNotSameAs($date));
    }

    /**
     * @test
     * @since 5.1.0
     * @group issue_2
     */
    public function changeTimeToEndOfDaySetsTime(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->timeToEndOfDay(),
            equalsDate('2011-03-31T23:59:59+00:00')
        );
    }

    /**
     * @test
     */
    public function changeTimeChangesTimeOnlyButKeepsDate(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->timeTo('14:13:12'),
            equalsDate('2011-03-31T14:13:12+00:00')
        );
    }

    /**
     * @test
     */
    public function changeHourToOnlyChangesHour(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->hourTo(14),
            equalsDate('2011-03-31T14:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByHoursAddsGivenAmountOfHours(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byHours(14),
            equalsDate('2011-03-31T15:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByHoursChangesDateWhenGivenValueExceedsStandardHours(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byHours(24),
            equalsDate('2011-04-01T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByHoursSubtractsNegativeAmountOfHours(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byHours(-24),
            equalsDate('2011-03-30T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeMinuteToOnlyChangesMinutes(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->minuteTo(13),
            equalsDate('2011-03-31T01:13:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMinutesAddsGivenAmountOfMinutes(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byMinutes(14),
            equalsDate('2011-03-31T01:15:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMinutesChangesHoursWhenGivenValueExceedsStandardMinutes(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byMinutes(60),
            equalsDate('2011-03-31T02:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMinutesSubtractsNegativeAmountOfMinutes(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byMinutes(-24),
            equalsDate('2011-03-31T00:37:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeSecondToOnlyChangesSeconds(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->secondTo(12),
            equalsDate('2011-03-31T01:00:12+00:00')
        );
    }

    /**
     * @test
     */
    public function changeBySecondsAddsGivenAmountOfSeconds(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->bySeconds(14),
            equalsDate('2011-03-31T01:01:15+00:00')
        );
    }

    /**
     * @test
     */
    public function changeBySecondsChangesMinutesWhenGivenValueExceedsStandardSeconds(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->bySeconds(60),
            equalsDate('2011-03-31T01:02:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeBySecondsSubtractsNegativeAmountOfSeconds(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->bySeconds(-24),
            equalsDate('2011-03-31T01:00:37+00:00')
        );
    }

    /**
     * since   7.0.0
     * @return array<string[]>
     */
    public static function invalidDates(): array
    {
        return [
            ['invalid', 'Given date "invalid" does not follow required format YYYY-MM-DD'],
            ['in-05-22', 'Given value in for year not suitable for changing the date.'],
            ['2016-in-22', 'Given value in for month not suitable for changing the date.'],
            ['2016-00-22', 'Given value 00 for month not suitable for changing the date.'],
            ['2016-13-22', 'Given value 13 for month not suitable for changing the date.'],
            ['2016-01-in', 'Given value in for day not suitable for changing the date.'],
            ['2016-12-00', 'Given value 00 for day not suitable for changing the date.'],
            ['2016-04-32', 'Given value 32 for day not suitable for changing the date.']
        ];
    }

    /**
     * @test
     * @dataProvider invalidDates
     */
    public function changeDateWithInvalidArgumentThrowsIllegalArgumentException(
        string $invalid,
        string $expectedExceptionMessage
    ): void {
        $date = new Date('2011-03-31 01:00:00');
        expect(fn() => $date->change()->dateTo($invalid))
            ->throws(InvalidArgumentException::class)
            ->withMessage($expectedExceptionMessage);
    }

    /**
     * @test
     */
    public function changeDateToReturnsNewInstance(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat($date->change()->dateTo('2012-7-15'), isNotSameAs($date));
    }

    /**
     * @test
     */
    public function changeDateToChangesDateOnlyButKeepsTime(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->dateTo('2012-7-15'),
            equalsDate('2012-07-15T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeYearToOnlyChangesYear(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->yearTo(2012),
            equalsDate('2012-03-31T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByYearsAddsGivenAmountOfYears(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byYears(14),
            equalsDate('2025-03-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByYearsSubtractsNegativeAmountOfYears(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byYears(-11),
            equalsDate('2000-03-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeMonthToOnlyChangesMonth(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->monthTo(7),
            equalsDate('2011-07-31T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMonthsAddsGivenAmountOfMonths(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byMonths(4),
            equalsDate('2011-07-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMonthsChangesYearWhenGivenValueExceedsStandardMonths(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byMonths(12),
            equalsDate('2012-03-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByMonthsSubtractsNegativeAmountOfMonths(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byMonths(-10),
            equalsDate('2010-05-31T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeDayToOnlyChangesDay(): void
    {
        $date = new Date('2011-03-31 01:00:00');
        assertThat(
            $date->change()->dayTo(15),
            equalsDate('2011-03-15T01:00:00+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByDaysAddsGivenAmountOfDays(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byDays(4),
            equalsDate('2011-04-04T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByDaysChangesMonthWhenGivenValueExceedsStandardDays(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byDays(40),
            equalsDate('2011-05-10T01:01:01+00:00')
        );
    }

    /**
     * @test
     */
    public function changeByDaysSubtractsNegativeAmountOfDays(): void
    {
        $date = new Date('2011-03-31 01:01:01');
        assertThat(
            $date->change()->byDays(-5),
            equalsDate('2011-03-26T01:01:01+00:00')
        );
    }
}
