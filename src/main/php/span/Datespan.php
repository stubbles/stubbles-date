<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\date\span;
use stubbles\date\Date;
/**
 * Interface for the datespan classes.
 *
 * @api
 */
interface Datespan
{
    /**
     * returns the start date
     *
     * @return  \stubbles\date\Date
     */
    public function start(): Date;

    /**
     * checks whether datespan starts before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsBefore($date): bool;

    /**
     * checks whether datespan starts after a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsAfter($date): bool;

    /**
     * returns the end date
     *
     * @return  \stubbles\date\Date
     */
    public function end(): Date;

    /**
     * checks whether datespan ends before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsBefore($date): bool;

    /**
     * checks whether datespan ends after a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsAfter($date): bool;

    /**
     * returns formatted date/time string for start date
     *
     * @param   string    $format    format, see http://php.net/date
     * @param   TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     * @since   3.5.0
     */
    public function formatStart(string $format, TimeZone $timeZone = null): string;

    /**
     * returns formatted date/time string for end date
     *
     * @param   string                   $format    format, see http://php.net/date
     * @param   \stubbles\date\TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     * @since   3.5.0
     */
    public function formatEnd(string $format, TimeZone $timeZone = null): string;

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function amountOfDays(): int;

    /**
     * returns list of days
     *
     * @return  iterable
     */
    public function days(): \Iterator;

    /**
     * checks whether the span is in the future compared to current date
     *
     * @return  bool
     */
    public function isInFuture(): bool;

    /**
     * checks whether the span contains the given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     */
    public function containsDate($date): bool;

    /**
     * returns a string representation of the datespan
     *
     * @return  string
     */
    public function asString();

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type();
}
