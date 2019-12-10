<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;
/**
 * Allows iteration over days of a datespan.
 *
 * @since  5.2.0
 * @implements  \Iterator<string,Day>
 */
class Days implements \Iterator, \Countable
{
    /**
     * start date of the iteration
     *
     * @var  \stubbles\date\span\Datespan
     */
    private $datespan;
    /**
     * start date of the span
     *
     * @var  \stubbles\date\span\Day
     */
    private $current;

    /**
     * constructor
     *
     * @param  \stubbles\date\span\Datespan  $datespan
     */
    public function __construct(Datespan $datespan)
    {
        $this->datespan = $datespan;
        $this->rewind();
    }

    /**
     * returns amount of days
     *
     * @return  int
     * @since   7.0.0
     */
    public function count(): int
    {
        return $this->datespan->amountOfDays();
    }

    /**
     * returns current day within iteration
     *
     * @return  \stubbles\date\span\Day
     */
    public function current(): Day
    {
        return $this->current;
    }

    /**
     * returns key for current day, which is it's string representation
     *
     * @return  string
     */
    public function key(): string
    {
        return $this->current->asString();
    }

    /**
     * advances iteration to next day
     */
    public function next(): void
    {
        $this->current = $this->current->next();
    }

    /**
     * returns iteration back to first day of datespan
     */
    public function rewind(): void
    {
        if ($this->datespan instanceof Day) {
            $this->current = $this->datespan;
        } else {
            $this->current = new Day($this->datespan->start());
        }
    }

    /**
     * checks if current entry is valid
     *
     * @return  bool
     */
    public function valid(): bool
    {
        return $this->current->startsBefore($this->datespan->end());
    }
}
