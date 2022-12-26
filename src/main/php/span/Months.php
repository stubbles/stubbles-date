<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;

use Countable;
use Iterator;
/**
 * Allows iteration over months of a datespan.
 *
 * @since 5.2.0
 * @implements  Iterator<string,Month>
 */
class Months implements Iterator, Countable
{
    /**
     * start date of the iteration
     */
    private int $year;
    /**
     * start date of the span
     */
    private Month $currentMonth;

    public function __construct(Year $year)
    {
        $this->year         = $year->asInt();
        $this->currentMonth = new Month($this->year, 1);
    }

    /**
     * returns amount of month (duh!)
     *
     * @since 7.0.0
     */
    public function count(): int
    {
        return 12;
    }

    /**
     * returns current day within iteration
     */
    public function current(): Month
    {
        return $this->currentMonth;
    }

    /**
     * returns key for current day, which is it's string representation
     */
    public function key(): string
    {
        return $this->currentMonth->asString();
    }

    /**
     * advances iteration to next day
     */
    public function next(): void
    {
        $this->currentMonth = $this->currentMonth->next();
    }

    /**
     * returns iteration back to first day of datespan
     */
    public function rewind(): void
    {
        $this->currentMonth = new Month($this->year, 1);
    }

    /**
     * checks if current entry is valid
     */
    public function valid(): bool
    {
        return $this->currentMonth->year() == $this->year;
    }
}
