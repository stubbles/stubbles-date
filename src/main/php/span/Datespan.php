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
use Iterator;
use stubbles\date\Date;
use stubbles\date\TimeZone;
/**
 * Interface for the datespan classes.
 *
 * @api
 */
interface Datespan
{
    /**
     * returns the start date
     */
    public function start(): Date;

    /**
     * checks whether datespan starts before a given date
     *
     * @since 3.5.0
     */
    public function startsBefore(int|string|DateTime|Date $date): bool;

    /**
     * checks whether datespan starts after a given date
     *
     * @since 3.5.0
     */
    public function startsAfter(int|string|DateTime|Date $date): bool;

    /**
     * returns the end date
     */
    public function end(): Date;

    /**
     * checks whether datespan ends before a given date
     *
     * @since 3.5.0
     */
    public function endsBefore(int|string|DateTime|Date $date): bool;

    /**
     * checks whether datespan ends after a given date
     *
     * @since 3.5.0
     */
    public function endsAfter(int|string|DateTime|Date $date): bool;

    /**
     * returns formatted date/time string for start date
     *
     * @param   string    $format    format, see http://php.net/date
     * @since 3.5.0
     */
    public function formatStart(string $format, TimeZone $timeZone = null): string;

    /**
     * returns formatted date/time string for end date
     *
     * @param   string                   $format    format, see http://php.net/date
     * @since 3.5.0
     */
    public function formatEnd(string $format, TimeZone $timeZone = null): string;

    /**
     * returns amount of days in this datespan
     */
    public function amountOfDays(): int;

    /**
     * returns list of days
     *
     * @return Iterator<string,Day>
     */
    public function days(): Iterator;

    /**
     * checks whether the span is in the future compared to current date
     */
    public function isInFuture(): bool;

    /**
     * checks whether the span contains the given date
     */
    public function containsDate(int|string|DateTime|Date $date): bool;

    /**
     * returns a string representation of the datespan
     */
    public function asString(): string;

    /**
     * returns a short type description of the datespan
     *
     * @since   5.3.0
     */
    public function type(): string;
}
