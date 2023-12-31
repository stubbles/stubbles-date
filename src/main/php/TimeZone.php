<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
/**
 * Class for time zone handling.
 *
 * Shameless rip from the XP framework. ;-) Wraps PHP's internal time zone
 * functions for ease of use.
 *
 * @api
 */
class TimeZone
{
    /**
     * internal time zone handle
     */
    protected DateTimeZone $timeZone;

    public function __construct(string|DateTimeZone $timeZone = null)
    {
        if (is_string($timeZone)) {
            $timeZone = @timezone_open($timeZone);
            if (!($timeZone instanceof DateTimeZone)) {
                throw new InvalidArgumentException(
                    'Invalid time zone identifier ' . var_export($timeZone, true)
                );
            }
        } elseif (null === $timeZone) {
            $timeZone = timezone_open(date_default_timezone_get());
        }

        $this->timeZone = $timeZone;
    }

    /**
     * returns internal time zone handle
     */
    public function handle(): DateTimeZone
    {
        return clone $this->timeZone;
    }

    /**
     * returns name of time zone
     */
    public function name(): string
    {
        return $this->timeZone->getName();
    }

    /**
     * returns offset of the time zone
     *
     * If no date is passed 'now' will be used.
     */
    public function offset(int|string|DateTime|Date $date = null): string
    {
        $offset  = $this->offsetInSeconds($date);
        $hours   = intval(abs($offset) / 3600);
        $minutes = (abs($offset)- ($hours * 3600)) / 60;
        return sprintf('%s%02d%02d', ($offset < 0 ? '-' : '+'), $hours, $minutes);
    }

    /**
     * returns offset to given date in seconds
     *
     * If no date is passed 'now' will be used.
     *
     * Because a timezone may have different offsets when its in DST or non-DST
     * mode, a date object must be given which is used to determine whether DST
     * or non-DST offset should be returned.
     */
    public function offsetInSeconds(int|string|DateTime|Date $date = null): int
    {
        if (null === $date) {
            return $this->timeZone->getOffset(new DateTime('now'));
        }

        return $this->timeZone->getOffset(Date::castFrom($date)->handle());
    }

    /**
     * checks whether time zone as dst mode or not
     */
    public function hasDst(): bool
    {
        // if there is at least one transition the time zone has a dst mode
        return count($this->timeZone->getTransitions()) > 0;
    }

    /**
     * translates a date from one timezone to a date of this timezone
     *
     * A new date instance will be returned while the given date is not changed.
     */
    public function translate(int|string|DateTime|Date $date): Date
    {
        $handle = Date::castFrom($date)->handle();
        $handle->setTimezone($this->timeZone);
        return new Date($handle);
    }

    /**
     * checks whether a value is equal to the class
     */
    public function equals(mixed $compare): bool
    {
        if ($compare instanceof self) {
            return $this->name() === $compare->name();
        }

        return false;
    }

    /**
     * returns a string representation of the class
     */
    public function __toString(): string
    {
        return $this->timeZone->getName();
    }
}
