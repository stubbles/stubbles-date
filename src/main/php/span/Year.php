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
 * Datespan that represents a month.
 *
 * @api
 */
class Year extends CustomDatespan
{
    /**
     * the represented year
     *
     * @type  int
     */
    private $year;

    /**
     * constructor
     *
     * If no value for the year is supplied the current year will be used.
     *
     * @param   int|string  $year   year of the span
     * @throws  \InvalidArgumentException
     */
    public function __construct($year = null)
    {
        if (null === $year) {
            $year = date('Y');
        } elseif (!ctype_digit($year)) {
            throw new \InvalidArgumentException(
                    'Given year "' . $year . '" can not be treated as year, should'
                    . ' be something that can be casted to int without data loss'
            );
        }

        $start = new \DateTime();
        $start->setDate((int) $year, 1, 1);
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->setDate((int) $year, 12, (int) $start->format('t'));
        $end->setTime(23, 59, 59);
        parent::__construct(new Date($start), new Date($end));
        $this->year = (int) $year;
    }

    /**
     * returns amount of days in this year
     *
     * @return  int
     */
    public function amountOfDays(): int
    {
       if ($this->isLeapYear()) {
           return 366;
       }

       return 365;
    }

    /**
     * returns list of months for this year
     *
     * @return  \stubbles\date\span\Months
     */
    public function months(): \Iterator
    {
        return new Months($this);
    }

    /**
     * checks whether year is a leap year
     *
     * @return  bool
     */
    public function isLeapYear(): bool
    {
        return $this->formatStart('L') == 1;
    }

    /**
     * checks if represented year is current year
     *
     * @return  bool
     */
    public function isCurrentYear(): bool
    {
        return ((int) date('Y')) === $this->year;
    }

    /**
     * returns int representation of the year
     *
     * @return  int
     * @since   5.2.0
     */
    public function asInt(): int
    {
        return $this->year;
    }

    /**
     * returns a string representation of the year
     *
     * @return  string
     */
    public function asString(): string
    {
        return (string) $this->year;
    }

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type(): string
    {
        return 'year';
    }
}
