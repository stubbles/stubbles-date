<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var  int
     */
    private $year;
    /**
     * actual month
     *
     * @var  int
     */
    private $month;
    /**
     * amount of days in this month
     *
     * @var  int
     */
    private $amountOfDays;

    /**
     * constructor
     *
     * If no value for the year is supplied the current year will be used.
     *
     * If no value for the month is supplied the current month will be used.
     *
     * @param  int|string|\stubbles\date\Date  $year   year of the span
     * @param  int|string                      $month  month of the span
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

        $this->amountOfDays = (int) $start->format('t');
        $this->year         = (int) $year;
        $this->month        = (int) $month;
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
    public static function fromString(string $input): self
    {
        $data = explode('-', $input);
        if (!isset($data[0]) || !isset($data[1])) {
            throw new \InvalidArgumentException(
                    'Can not parse month from string "' . $input
                    . '", format should be "YYYY-MM"'
            );
        }

        list($year, $month) = $data;
        if (!ctype_digit($year)) {
            throw new \InvalidArgumentException(
                    'Detected value ' . $year . ' for year is not a valid year.'
            );
        }

        if (!ctype_digit($month) || 1 > $month || 12 < $month) {
            throw new \InvalidArgumentException(
                    'Detected value ' . $month . ' for month is not a valid month.'
            );
        }

        return new self($year, $month);
    }

    /**
     * creates instance for last month regardless of today's date
     *
     * @return  \stubbles\date\span\Month
     * @since   3.5.1
     */
    public static function last(): self
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
    public static function currentOrLastWhenFirstDay(): self
    {
        $self = new self();
        if (date('d', time()) === '01') {
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
    public function next(): self
    {
        return new self($this->start()->change()->byMonths(1));
    }

    /**
     * returns the month before
     *
     * @return  \stubbles\date\span\Month
     * @since   5.2.0
     */
    public function before(): self
    {
        return new self($this->start()->change()->byMonths(-1));
    }

    /**
     * returns year month belongs to
     *
     * @return  int
     */
    public function year(): int
    {
        return $this->year;
    }

    /**
     * returns amount of days in this month
     *
     * @return  int
     */
    public function amountOfDays(): int
    {
        return $this->amountOfDays;
    }

    /**
     * checks if it represents the current month
     *
     * @return  bool
     */
    public function isCurrentMonth(): bool
    {
        return $this->asString() === Date::now()->format('Y-m');
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString(): string
    {
        return (string) $this->year . '-' . ((10 > $this->month && strlen((string) $this->month) === 1) ? ('0' . $this->month) : ($this->month));
    }

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type(): string
    {
        return 'month';
    }
}
