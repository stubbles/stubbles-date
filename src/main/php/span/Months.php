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
/**
 * Allows iteration over months of a datespan.
 *
 * @since  5.2.0
 */
class Months implements \Iterator
{
    /**
     * start date of the iteration
     *
     * @type  int
     */
    private $year;
    /**
     * start date of the span
     *
     * @type  \stubbles\date\span\Month
     */
    private $currentMonth;

    /**
     * constructor
     *
     * @param  \stubbles\date\span\Year  $year
     */
    public function __construct(Year $year)
    {
        $this->year         = $year->asInt();
        $this->currentMonth = new Month($this->year, 1);
    }

    /**
     * returns current day within iteration
     *
     * @return  \stubbles\date\span\Month
     */
    public function current()
    {
        return $this->currentMonth;
    }

    /**
     * returns key for current day, which is it's string representation
     *
     * @return  string
     */
    public function key()
    {
        return $this->currentMonth->asString();
    }

    /**
     * advances iteration to next day
     */
    public function next()
    {
        $this->currentMonth = $this->currentMonth->next();
    }

    /**
     * returns iteration back to first day of datespan
     */
    public function rewind()
    {
        $this->currentMonth = new Month($this->year, 1);
    }

    /**
     * checks if current entry is valid
     *
     * @return  bool
     */
    public function valid()
    {
        return $this->currentMonth->year() == $this->year;
    }
}
