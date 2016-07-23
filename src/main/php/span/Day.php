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
/**
 * Datespan that represents a single day.
 *
 * @api
 */
class Day extends AbstractDatespan implements Datespan
{
    /**
     * original date
     *
     * @type  \stubbles\date\Date
     */
    private $date;

    /**
     * constructor
     *
     * @param  int|string|\DateTime|\stubbles\date\Date  $day  day that the span covers
     */
    public function __construct($day = null)
    {
        $this->date = ((null === $day) ? (Date::now()) : (Date::castFrom($day, 'day')));
        parent::__construct($this->date, $this->date);
    }

    /**
     * create instance for tomorrow
     *
     * @return  \stubbles\date\span\Day
     * @since   3.5.1
     */
    public static function tomorrow(): self
    {
        return new self('tomorrow');
    }

    /**
     * create instance for yesterday
     *
     * @return  \stubbles\date\span\Day
     * @since   3.5.1
     */
    public static function yesterday(): self
    {
        return new self('yesterday');
    }

    /**
     * returns next day
     *
     * @return  \stubbles\date\span\Day
     * @since   5.2.0
     */
    public function next(): self
    {
        return new self($this->start()->change()->byDays(1));
    }

    /**
     * returns the day before
     *
     * @return  \stubbles\date\span\Day
     * @since   5.2.0
     */
    public function before(): self
    {
        return new self($this->start()->change()->byDays(-1));
    }

    /**
     * returns amount of days on this day
     *
     * Well, the amount of days on a day is obviously always one.
     *
     * @return  int
     */
    public function amountOfDays(): int
    {
        return 1;
    }

    /**
     * returns list of days
     *
     * @return  \stubbles\date\span\Day[]
     */
    public function days()
    {
        return [$this->asString() => $this];
    }

    /**
     * checks if it represents the current day
     *
     * @return  bool
     */
    public function isToday(): bool
    {
        return $this->date->format('Y-m-d') === Date::now($this->date->timezone())->format('Y-m-d');
    }

    /**
     * returns a string representation of the day
     *
     * @return  string
     */
    public function asString(): string
    {
        return $this->date->format('Y-m-d');
    }

    /**
     * returns number of current day within month
     *
     * @return  int
     */
    public function asInt()
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
     * @return  string
     */
    public function format($format)
    {
        return $this->date->format($format);
    }

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type()
    {
        return 'day';
    }
}
