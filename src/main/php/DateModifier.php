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
/**
 * Class for date/time modifications.
 *
 * @since 1.7.0
 */
class DateModifier
{
    public function __construct(private Date $originalDate) { }

    /**
     * returns a new date instance which represents the changed date
     *
     * @api
     * @param string $target relative format accepted by strtotime()
     */
    public function to(string $target): Date
    {
        $modifiedHandle = clone $this->originalDate->handle();
        $modifiedHandle->modify($target);
        return new Date($modifiedHandle);
    }

    /**
     * returns a new date instance with same date but changed time
     *
     * @api
     * @param  string $time time representation in format HH:MM:SS
     * @throws InvalidArgumentException
     */
    public function timeTo(string $time): Date
    {
        $times = explode(':', $time);
        if (count($times) != 3) {
            throw new InvalidArgumentException(
                'Given time "' . $time . '" does not follow required format HH:MM:SS'
            );
        }

        list($hour, $minute, $second) = $times;
        if (!ctype_digit($hour) || 0 > $hour || 23 < $hour) {
            throw new InvalidArgumentException(
                'Given value ' . $hour . ' for hour not suitable for changing the time.'
            );
        }

        if (!ctype_digit($minute) || 0 > $minute || 59 < $minute) {
            throw new InvalidArgumentException(
                'Given value ' . $minute . ' for minute not suitable for changing the time.'
            );
        }

        if (!ctype_digit($second) || 0 > $second || 59 < $second) {
            throw new InvalidArgumentException(
                'Given value ' . $second . ' for second not suitable for changing the time.'
            );
        }

        return $this->createDateWithNewTime((int) $hour, (int) $minute, (int) $second);
    }

    /**
     * returns a new date instance with same date but time at 00:00:00
     *
     * @api
     * @since 5.1.0
     */
    public function timeToStartOfDay(): Date
    {
        return $this->createDateWithNewTime(0, 0, 0);
    }

    /**
     * returns a new date instance with same date but time at 23:59:59
     *
     * @api
     * @since 5.1.0
     */
    public function timeToEndOfDay(): Date
    {
        return $this->createDateWithNewTime(23, 59, 59);
    }

    /**
     * returns a new date instance with same date, minute and second but changed hour
     *
     * @api
     */
    public function hourTo(int $hour): Date
    {
        return $this->createDateWithNewTime(
            $hour,
            $this->originalDate->minutes(),
            $this->originalDate->seconds()
        );
    }

    /**
     * changes date by given amount of hours
     *
     * @api
     */
    public function byHours(int $hours): Date
    {
        return $this->hourTo($this->originalDate->hours() + $hours);
    }

    /**
     * returns a new date instance with same date, hour and second but changed minute
     *
     * @api
     */
    public function minuteTo(int $minute): Date
    {
        return $this->createDateWithNewTime(
            $this->originalDate->hours(),
            $minute,
            $this->originalDate->seconds()
        );
    }

    /**
     * changes date by given amount of minutes
     *
     * @api
     */
    public function byMinutes(int $minutes): Date
    {
        return $this->minuteTo($this->originalDate->minutes() + $minutes);
    }

    /**
     * returns a new date instance with same date, hour and minute but changed second
     *
     * @api
     */
    public function secondTo(int $second): Date
    {
        return $this->createDateWithNewTime(
            $this->originalDate->hours(),
            $this->originalDate->minutes(),
            $second
        );
    }

    /**
     * changes date by given amount of seconds
     *
     * @api
     */
    public function bySeconds(int $seconds): Date
    {
        return $this->secondTo($this->originalDate->seconds() + $seconds);
    }

    /**
     * creates new date instance with changed time
     */
    private function createDateWithNewTime(int $hour, int $minute, int $second): Date
    {
        return new Date($this->originalDate->handle()->setTime($hour, $minute, $second));
    }

    /**
     * returns a new date instance with changed date but same time
     *
     * @api
     * @param  string $date date representation in format YYYY-MM-DD
     * @throws InvalidArgumentException
     */
    public function dateTo(string $date): Date
    {
        $dates = explode('-', $date);
        if (count($dates) != 3) {
            throw new InvalidArgumentException(
                'Given date "' . $date . '" does not follow required format YYYY-MM-DD'
            );
        }

        list($year, $month, $day) = $dates;
        if (!ctype_digit($year)) {
            throw new InvalidArgumentException(
                'Given value ' . $year . ' for year not suitable for changing the date.'
            );
        }

        if (!ctype_digit($month) || 1 > $month || 12 < $month) {
            throw new InvalidArgumentException(
                'Given value ' . $month . ' for month not suitable for changing the date.'
            );
        }

        if (!ctype_digit($day) || 1 > $day || 31 < $day) {
            throw new InvalidArgumentException(
                'Given value ' . $day . ' for day not suitable for changing the date.'
            );
        }

        return $this->createNewDateWithExistingTime((int) $year, (int) $month, (int) $day);
    }

    /**
     * returns a new date instance with changed year but same time, month and day
     *
     * @api
     */
    public function yearTo(int $year): Date
    {
        return $this->createNewDateWithExistingTime(
            $year,
            $this->originalDate->month(),
            $this->originalDate->day()
        );
    }

    /**
     * changes date by given amount of years
     *
     * @api
     */
    public function byYears(int $years): Date
    {
        return $this->yearTo($this->originalDate->year() + $years);
    }

    /**
     * returns a new date instance with changed month but same time, year and day
     *
     * @api
     */
    public function monthTo(int $month): Date
    {
        return $this->createNewDateWithExistingTime(
            $this->originalDate->year(),
            $month,
            $this->originalDate->day()
        );
    }

    /**
     * changes date by given amount of months
     *
     * @api
     */
    public function byMonths(int $months): Date
    {
        return $this->monthTo($this->originalDate->month() + $months);
    }

    /**
     * returns a new date instance with changed day but same time, year and month
     *
     * @api
     */
    public function dayTo(int $day): Date
    {
        return $this->createNewDateWithExistingTime(
            $this->originalDate->year(),
            $this->originalDate->month(),
            $day
        );
    }

    /**
     * changes date by given amount of days
     *
     * @api
     */
    public function byDays(int $days): Date
    {
        return $this->dayTo($this->originalDate->day() + $days);
    }

    /**
     * creates new date instance with changed date but same time
     */
    private function createNewDateWithExistingTime(int $year, int $month, int $day): Date
    {
        return new Date($this->originalDate->handle()->setDate($year, $month, $day));
    }
}
