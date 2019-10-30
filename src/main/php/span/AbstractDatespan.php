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
namespace stubbles\date\span;
use stubbles\date\Date;
use stubbles\date\TimeZone;
/**
 * Datespan with a custom start and end date.
 *
 * @internal
 */
abstract class AbstractDatespan implements Datespan
{
    /**
     * start date of the span
     *
     * @type  \stubbles\date\Date
     */
    private $start;
    /**
     * end date of the span
     *
     * @type  \stubbles\date\Date
     */
    private $end;

    /**
     * constructor
     *
     * @param  int|string|\DateTime|\stubbles\date\Date  $start  start date of the span
     * @param  int|string|\DateTime|\stubbles\date\Date  $end    end date of the span
     */
    public function __construct($start, $end)
    {
        $this->start = Date::castFrom($start, 'start')->change()->timeTo('00:00:00');
        $this->end   = Date::castFrom($end, 'end')->change()->timeTo('23:59:59');
    }

    /**
     * returns the start date
     *
     * @return  \stubbles\date\Date
     */
    public function start(): Date
    {
        return $this->start;
    }

    /**
     * checks whether datespan starts before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsBefore($date): bool
    {
        return $this->start->isBefore($date);
    }

    /**
     * checks whether datespan starts after a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsAfter($date): bool
    {
        return $this->start->isAfter($date);
    }

    /**
     * returns the end date
     *
     * @return  \stubbles\date\Date
     */
    public function end(): Date
    {
        return $this->end;
    }

    /**
     * checks whether datespan ends before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsBefore($date): bool
    {
        return $this->end->isBefore($date);
    }

    /**
     * checks whether datespan ends after a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsAfter($date): bool
    {
        return $this->end->isAfter($date);
    }

    /**
     * returns formatted date/time string for start date
     *
     * @param   string                   $format    format, see http://php.net/date
     * @param   \stubbles\date\TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     * @since   3.5.0
     */
    public function formatStart(string $format, TimeZone $timeZone = null): string
    {
        return $this->start->format($format, $timeZone);
    }

    /**
     * returns formatted date/time string for end date
     *
     * @param   string                   $format    format, see http://php.net/date
     * @param   \stubbles\date\TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     * @since   3.5.0
     */
    public function formatEnd(string $format, TimeZone $timeZone = null): string
    {
        return $this->end->format($format, $timeZone);
    }

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function amountOfDays(): int
    {
        return $this->end->handle()->diff($this->start->handle())->days + 1;
    }

    /**
     * checks whether the DateSpan is in the future compared to current date
     *
     * @return  bool
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
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     */
    public function containsDate($date): bool
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
     *
     * @return  string
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}
