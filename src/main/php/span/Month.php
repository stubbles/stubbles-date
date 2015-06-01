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
 * Datespan that represents a month.
 *
 * @api
 */
class Month extends CustomDatespan
{
    /**
     * year where month is within
     *
     * @type  int
     */
    private $year;
    /**
     * actual month
     *
     * @type  int
     */
    private $month;
    /**
     * amount of days in this month
     *
     * @type  int
     */
    private $amountOfDays;

    /**
     * constructor
     *
     * If no value for the year is supplied the current year will be used.
     *
     * If no value for the month is supplied the current month will be used.
     *
     * @param  int  $year   year of the span
     * @param  int  $month  month of the span
     */
    public function __construct($year = null, $month = null)
    {
        $start = null;
        if ($year instanceof Date) {
            $start = $year;
            $year  = $start->year();
        } elseif (null === $year) {
            $year = (int) date('Y');
        }

        if (null === $month) {
            if (null !== $start) {
                $month = $start->month();
            } else {
                $month = (int) date('m');
            }
        }

        if (null === $start) {
            $start = new Date($year . '-' . $month . '-01 00:00:00');
        }

        $this->amountOfDays = $start->format('t');
        $this->year         = $year;
        $this->month        = $month;
        parent::__construct(
                $start,
                new Date($year . '-' . $month . '-' . $this->amountOfDays . ' 23:59:59')
        );
    }

    /**
     * create instance from given string, i.e. Month::fromString('2014-05')
     *
     * @param   string  $input
     * @return  \stubbles\date\span\Month
     * @throws  \InvalidArgumentException
     * @since   3.5.2
     */
    public static function fromString($input)
    {
        $data = explode('-', $input);
        if (!isset($data[0]) || !isset($data[1])) {
            throw new \InvalidArgumentException('Can not parse month from string "' . $input . '", format should be "YYYY-MM"');
        }

        list($year, $month) = $data;
        return new self($year, $month);
    }

    /**
     * creates instance for last month regardless of today's date
     *
     * @return  \stubbles\date\span\Month
     * @since   3.5.1
     */
    public static function last()
    {
        $timestamp = strtotime('first day of previous month');
        return new self(date('Y', $timestamp), date('m', $timestamp));
    }

    /**
     * creates instance for current month except when today is the first day of the month
     *
     * @return  \stubbles\date\span\Month
     * @since   5.5.0
     */
    public static function currentOrLastWhenFirstDay()
    {
        $self = new self();
        if ($self->start()->day() === 1) {
            return $self->before();
        }

        return $self;
    }

    /**
     * returns next month
     *
     * @return  \stubbles\date\span\Month
     * @since   5.2.0
     */
    public function next()
    {
        return new self($this->start()->change()->byMonths(1));
    }

    /**
     * returns the month before
     *
     * @return  \stubbles\date\span\Month
     * @since   5.2.0
     */
    public function before()
    {
        return new self($this->start()->change()->byMonths(-1));
    }

    /**
     * returns year month belongs to
     *
     * @return  int
     */
    public function year()
    {
        return $this->year;
    }

    /**
     * returns amount of days in this month
     *
     * @return  int
     */
    public function amountOfDays()
    {
        return $this->amountOfDays;
    }

    /**
     * returns amount of days in this month
     *
     * @return  int
     * @deprecated  since 5.2.0, use amountOfDays() instead, will be removed with 6.0.0
     */
    public function getAmountOfDays()
    {
        return $this->amountOfDays();
    }

    /**
     * checks if it represents the current month
     *
     * @return  bool
     */
    public function isCurrentMonth()
    {
        return $this->asString() === Date::now()->format('Y-m');
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->year . '-' . ((10 > $this->month && strlen($this->month) === 1) ? ('0' . $this->month) : ($this->month));
    }

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type()
    {
        return 'month';
    }
}
