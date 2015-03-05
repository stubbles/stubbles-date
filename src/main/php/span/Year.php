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
     * @param  int  $year   year of the span
     */
    public function __construct($year = null)
    {
        if (null === $year) {
            $year = date('Y');
        }

        $start = new \DateTime();
        $start->setDate($year, 1, 1);
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->setDate($year, 12, $start->format('t'));
        $end->setTime(23, 59, 59);
        parent::__construct(new Date($start), new Date($end));
        $this->year = (int) $year;
    }

    /**
     * returns amount of days in this year
     *
     * @return  int
     */
    public function amountOfDays()
    {
       if ($this->isLeapYear()) {
           return 366;
       }

       return 365;
    }

    /**
     * returns amount of days in this year
     *
     * @return  int
     * @deprecated  since 5.2.0, use amountOfDays() instead, will be removed with 6.0.0
     */
    public function getAmountOfDays()
    {
       return $this->amountOfDays();
    }

    /**
     * returns list of months for this year
     *
     * @return  \stubbles\date\span\Month[]
     */
    public function months()
    {
        return new Months($this);
    }

    /**
     * returns list of months for this year
     *
     * @return  \stubbles\date\span\Month[]
     * @deprecated  since 5.2.0, use months() instead, will be removed with 6.0.0
     */
    public function getMonths()
    {
        return $this->months();
    }

    /**
     * checks whether year is a leap year
     *
     * @return  bool
     */
    public function isLeapYear()
    {
        return $this->formatStart('L') == 1;
    }

    /**
     * checks if represented year is current year
     *
     * @return  bool
     */
    public function isCurrentYear()
    {
        return ((int) date('Y')) === $this->year;
    }

    /**
     * returns int representation of the year
     *
     * @return  int
     * @since   5.2.0
     */
    public function asInt()
    {
        return $this->year;
    }

    /**
     * returns a string representation of the year
     *
     * @return  string
     */
    public function asString()
    {
        return (string) $this->year;
    }
}
