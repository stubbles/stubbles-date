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
namespace stubbles\date;
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
     *
     * @type  \DateTimeZone
     */
    protected $timeZone;

    /**
     * constructor
     *
     * Time zone can be a string like 'Europe/Berlin', a DateTimeZone instance
     * or null.
     *
     * @param   string|\DateTimeZone  $timeZone  initial timezone handle
     * @throws  \InvalidArgumentException
     */
    public function __construct($timeZone = null)
    {
        if (is_string($timeZone)) {
            $this->timeZone = @timezone_open($timeZone);
        } elseif (null === $timeZone) {
            $this->timeZone = timezone_open(date_default_timezone_get());
        } else {
            $this->timeZone = $timeZone;
        }

        if (!($this->timeZone instanceof \DateTimeZone)) {
            throw new \InvalidArgumentException(
                    'Invalid time zone identifier ' . $timeZone
            );
        }
    }

    /**
     * returns internal time zone handle
     *
     * @return  \DateTimeZone
     */
    public function handle(): \DateTimeZone
    {
        return clone $this->timeZone;
    }

    /**
     * returns name of time zone
     *
     * @return  string
     */
    public function name(): string
    {
        return $this->timeZone->getName();
    }

    /**
     * returns offset of the time zone
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date  defaults to current date
     * @return  string
     */
    public function offset($date = null): string
    {
        $offset  = $this->offsetInSeconds($date);
        $hours   = intval(abs($offset) / 3600);
        $minutes = (abs($offset)- ($hours * 3600)) / 60;
        return sprintf('%s%02d%02d', ($offset < 0 ? '-' : '+'), $hours, $minutes);
    }

    /**
     * returns offset to given date in seconds
     *
     * Because a timezone may have different offsets when its in DST or non-DST
     * mode, a date object must be given which is used to determine whether DST
     * or non-DST offset should be returned.
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date  defaults to current date
     * @return  int
     */
    public function offsetInSeconds($date = null): int
    {
        if (null === $date) {
            return $this->timeZone->getOffset(new \DateTime('now'));
        }

        return $this->timeZone->getOffset(Date::castFrom($date)->handle());
    }

    /**
     * checks whether time zone as dst mode or not
     *
     * @return  bool
     */
    public function hasDst(): bool
    {
        // if there is at least one transition the time zone has a dst mode
        return (count($this->timeZone->getTransitions()) > 0);
    }

    /**
     * translates a date from one timezone to a date of this timezone
     *
     * A new date instance will be returned while the given date is not changed.
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  \stubbles\date\Date
     */
    public function translate($date): Date
    {
        $handle = Date::castFrom($date)->handle();
        $handle->setTimezone($this->timeZone);
        return new Date($handle);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare): bool
    {
        if ($compare instanceof self) {
            return ($this->name() === $compare->name());
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString(): string
    {
        return $this->timeZone->getName();
    }
}
