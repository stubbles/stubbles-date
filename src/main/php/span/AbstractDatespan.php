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
     * @type  Date
     */
    private $start;
    /**
     * end date of the span
     *
     * @type  Date
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
    public function start()
    {
        return $this->start;
    }

    /**
     * returns the start date
     *
     * @return  \stubbles\date\Date
     * @deprecated  since 5.2.0, use start() instead, will be removed with 6.0.0
     */
    public function getStart()
    {
        return $this->start();
    }

    /**
     * checks whether datespan starts before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsBefore($date)
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
    public function startsAfter($date)
    {
        return $this->start->isAfter($date);
    }

    /**
     * returns the end date
     *
     * @return  \stubbles\date\Date
     */
    public function end()
    {
        return $this->end;
    }

    /**
     * returns the end date
     *
     * @return  \stubbles\date\Date
     * @deprecated  since 5.2.0, use end() instead, will be removed with 6.0.0
     */
    public function getEnd()
    {
        return $this->end();
    }

    /**
     * checks whether datespan ends before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsBefore($date)
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
    public function endsAfter($date)
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
    public function formatStart($format, TimeZone $timeZone = null)
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
    public function formatEnd($format, TimeZone $timeZone = null)
    {
        return $this->end->format($format, $timeZone);
    }

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function amountOfDays()
    {
        return $this->end->handle()->diff($this->start->handle())->days + 1;
    }

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     * @deprecated  since 5.2.0, use amountOfDays() instead, will be removed with 6.0.0
     */
    public function getAmountOfDays()
    {
        return $this->amountOfDays();
    }

    /**
     * checks whether the DateSpan is in the future compared to current date
     *
     * @return  bool
     */
    public function isInFuture()
    {
        $today = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
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
    public function containsDate($date)
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
    public function __toString()
    {
        return $this->asString();
    }
}
