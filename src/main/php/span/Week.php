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
 * Datespan that represents a week.
 *
 * @api
 */
class Week extends CustomDatespan
{
    /**
     * constructor
     *
     * @param  int|string|\DateTime|Date  $date  first day of the week
     */
    public function __construct($date)
    {
        $date = Date::castFrom($date);
        parent::__construct($date, $date->change()->to('+ 6 days'));
    }

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function getAmountOfDays()
    {
        return 7;
    }

    /**
     * returns a string representation for the week
     *
     * @return  string
     */
    public function asString()
    {
        return $this->formatStart('W');
    }
}
