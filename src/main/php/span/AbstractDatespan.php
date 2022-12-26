<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.

 */
namespace stubbles\date\span;

use DateTime;
use stubbles\date\Date;
use stubbles\date\TimeZone;
/**
 * Datespan with a custom start and end date.
 *
 * @internal
 */
abstract class AbstractDatespan implements Datespan
{
    private Date $start;
    private Date $end;

    public function __construct(
        int|string|DateTime|Date $start,
        int|string|DateTime|Date $end
    ) {
        $this->start = Date::castFrom($start, 'start')->change()->timeTo('00:00:00');
        $this->end   = Date::castFrom($end, 'end')->change()->timeTo('23:59:59');
    }

    /**
     * returns the start date
     */
    public function start(): Date
    {
        return $this->start;
    }

    /**
     * checks whether datespan starts before a given date
     *
     * @since 3.5.0
     */
    public function startsBefore(int|string|DateTime|Date $date): bool
    {
        return $this->start->isBefore($date);
    }

    /**
     * checks whether datespan starts after a given date
     *
     * @since 3.5.0
     */
    public function startsAfter(int|string|DateTime|Date $date): bool
    {
        return $this->start->isAfter($date);
    }

    /**
     * returns the end date
     */
    public function end(): Date
    {
        return $this->end;
    }

    /**
     * checks whether datespan ends before a given date
     *
     * @since 3.5.0
     */
    public function endsBefore(int|string|DateTime|Date $date): bool
    {
        return $this->end->isBefore($date);
    }

    /**
     * checks whether datespan ends after a given date
     *
     * @since 3.5.0
     */
    public function endsAfter(int|string|DateTime|Date $date): bool
    {
        return $this->end->isAfter($date);
    }

    /**
     * returns formatted date/time string for start date
     *
     * @param   string                   $format    format, see http://php.net/date
     * @since 3.5.0
     */
    public function formatStart(string $format, TimeZone $timeZone = null): string
    {
        return $this->start->format($format, $timeZone);
    }

    /**
     * returns formatted date/time string for end date
     *
     * @param   string                   $format    format, see http://php.net/date
     * @since 3.5.0
     */
    public function formatEnd(string $format, TimeZone $timeZone = null): string
    {
        return $this->end->format($format, $timeZone);
    }

    /**
     * returns amount of days in this datespan
     */
    public function amountOfDays(): int
    {
        // as \DateInterval is created by \DateTime::diff() it is an int,
        // but psalm doesn't know this, so we cast it to silence it
        // see https://www.php.net/manual/en/class.dateinterval.php#dateinterval.props.days
        return ((int) $this->end->handle()->diff($this->start->handle())->days) + 1;
    }

    /**
     * checks whether the DateSpan is in the future compared to current date
     */
    public function isInFuture(): bool
    {
        $today = mktime(23, 59, 59, (int) date('m'), (int) date('d'), (int) date('Y'));
        if ($this->start->timestamp() > $today) {
            return true;
        }

        return false;
    }

    /**
     * checks whether the span contains the given date
     */
    public function containsDate(int|string|DateTime|Date $date): bool
    {
        $date = Date::castFrom($date);
        if (!$this->start->isBefore($date) && !$this->start->equals($date)) {
            return false;
        }

        if (!$this->end->isAfter($date) && !$this->end->equals($date)) {
            return false;
        }

        return true;
    }

    /**
     * returns string representation of datespan
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}
