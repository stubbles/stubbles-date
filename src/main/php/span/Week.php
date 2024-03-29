<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\span;

use DateTime;
use InvalidArgumentException;
use stubbles\date\Date;
/**
 * Datespan that represents a week.
 *
 * @api
 */
class Week extends CustomDatespan
{
    public function __construct(int|string|DateTime|Date $startOfWeek)
    {
        $date = Date::castFrom($startOfWeek);
        parent::__construct($date, $date->change()->to('+ 6 days'));
    }

    /**
     * create instance from given string, i.e. Week::fromString('2014-W05')
     *
     * @throws InvalidArgumentException
     * @since  5.3.0
     */
    public static function fromString(string $input): self
    {
        $data = explode('-', $input);
        $week = strtotime($input);
        if (!isset($data[0]) || !isset($data[1]) || false === $week) {
            throw new InvalidArgumentException(
                'Can not parse week from string "' . $input . '", format should be "YYYY-Www"'
            );
        }

        return new self($week);
    }

    /**
     * returns number of the week
     */
    public function number(): int
    {
        return (int) $this->formatStart('W');
    }

    /**
     * returns amount of days in this datespan
     */
    public function amountOfDays(): int
    {
        return 7;
    }

    /**
     * returns a string representation for the week
     */
    public function asString(): string
    {
        return $this->formatStart('Y-\WW');
    }

    /**
     * returns a short type description of the datespan
     *
     * @since 5.3.0
     */
    public function type(): string
    {
        return 'week';
    }
}
