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
 * Datespan with a custom start and end date.
 *
 * @api
 */
class CustomDatespan extends AbstractDatespan
{
    /**
     * returns list of days within this datespan
     *
     * @return  \stubbles\date\span\Day[]
     */
    public function days()
    {
        $days         = [];
        $start        = $this->start();
        $endTimestamp = $this->end()->format('U');
        while ($start->format('U') <= $endTimestamp) {
            $days[] =  new Day(clone $start);
            $start   = $start->change()->to('+1 day');
        }

        return $days;
    }

    /**
     * returns list of days within this datespan
     *
     * @return  \stubbles\date\span\Day[]
     * @deprecated  since 5.2.0, use days() instead, will be removed with 6.0.0
     */
    public function getDays()
    {
        return $this->days();
    }

    /**
     * returns a string representation of the datespan
     *
     * @return  string
     */
    public function asString()
    {
        return $this->getStart()->format('d.m.Y') . ' - ' . $this->getEnd()->format('d.m.Y');
    }
}
