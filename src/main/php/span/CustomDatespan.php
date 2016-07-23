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
     * @return  iterable
     */
    public function days(): \Iterator
    {
        return new Days($this);
    }

    /**
     * returns a string representation of the datespan
     *
     * @return  string
     */
    public function asString(): string
    {
        return $this->formatStart('Y-m-d') . ',' . $this->formatEnd('Y-m-d');
    }

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type(): string
    {
        return 'custom';
    }
}
