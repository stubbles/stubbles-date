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
 * Datespan that represents a week.
 *
 * @api
 */
class Week extends CustomDatespan
{
    /**
     * constructor
     *
     * @param  int|string|\DateTime|\stubbles\date\Date  $startOfWeek  first day of the week
     */
    public function __construct($startOfWeek)
    {
        $date = Date::castFrom($startOfWeek);
        parent::__construct($date, $date->change()->to('+ 6 days'));
    }

    /**
     * create instance from given string, i.e. Week::fromString('2014-W05')
     *
     * @param   string  $input
     * @return  \stubbles\date\span\Week
     * @throws  \InvalidArgumentException
     * @since   5.3.0
     */
    public static function fromString(string $input): self
    {
        $data = explode('-', $input);
        $week = strtotime($input);
        if (!isset($data[0]) || !isset($data[1]) || false === $week) {
            throw new \InvalidArgumentException('Can not parse week from string "' . $input . '", format should be "YYYY-Www"');
        }

        $self = new self($week);
        return $self;
    }

    /**
     * returns number of the week
     *
     * @return  int
     */
    public function number(): int
    {
        return (int) $this->formatStart('W');
    }

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function amountOfDays(): int
    {
        return 7;
    }

    /**
     * returns a string representation for the week
     *
     * @return  string
     */
    public function asString(): string
    {
        return $this->formatStart('Y-\WW');
    }

    /**
     * returns a short type description of the datespan
     *
     * @return  string
     * @since   5.3.0
     */
    public function type(): string
    {
        return 'week';
    }
}
