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
/**
 * Datespan that represents a single day.
 *
 * @api
 */
class Day extends AbstractDatespan implements Datespan
{
    /**
     * original date
     */
    private Date $date;

    public function __construct(int|string|DateTime|Date|null $day = null)
    {
        $this->date = ((null === $day) ? (Date::now()) : (Date::castFrom($day, 'day')));
        parent::__construct($this->date, $this->date);
    }

    /**
     * create instance for tomorrow
     *
     * @since 3.5.1
     */
    public static function tomorrow(): self
    {
        return new self('tomorrow');
    }

    /**
     * create instance for yesterday
     *
     * @since 3.5.1
     */
    public static function yesterday(): self
    {
        return new self('yesterday');
    }

    /**
     * returns next day
     *
     * @since 5.2.0
     */
    public function next(): self
    {
        return new self($this->start()->change()->byDays(1));
    }

    /**
     * returns the day before
     *
     * @since 5.2.0
     */
    public function before(): self
    {
        return new self($this->start()->change()->byDays(-1));
    }

    /**
     * returns amount of days on this day
     *
     * Well, the amount of days on a day is obviously always one.
     */
    public function amountOfDays(): int
    {
        return 1;
    }

    /**
     * returns list of days
     *
     * @return Iterator<string,Day>
     */
    public function days(): Iterator
    {
        return new Days($this);
    }

    /**
     * checks if it represents the current day
     */
    public function isToday(): bool
    {
        return $this->date->format('Y-m-d') === Date::now($this->date->timezone())->format('Y-m-d');
    }

    /**
     * returns a string representation of the day
     */
    public function asString(): string
    {
        return $this->date->format('Y-m-d');
    }

    /**
     * returns number of current day within month
     */
    public function asInt(): int
    {
        return (int) $this->date->format('d');
    }

    /**
     * returns formatted date/time string
     *
     * Please note that the returned string may also contain a time, depending
     * on your format string.
     *
     * @param   string  $format  format, see http://php.net/date
     */
    public function format($format): string
    {
        return $this->date->format($format);
    }

    /**
     * returns a short type description of the datespan
     *
     * @since 5.3.0
     */
    public function type(): string
    {
        return 'day';
    }
}
