<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\assert {
    /**
     * returns predicate which tests for equality of dates
     *
     * @param   string  $expected  expected date as string
     * @return  \stubbles\date\assert\DateEquals
     * @since   6.0.0
     */
    function equalsDate(string $expected): DateEquals
    {
        return new DateEquals($expected);
    }
}
namespace stubbles\date\span {
    /**
     * parses given value to a datespan instance
     *
     * If input value is empty return value will be <null>.
     *
     * @param   string|null  $value
     * @return  \stubbles\date\span\Datespan|null
     * @throws  \InvalidArgumentException
     */
    function parse($value)
    {
        if (empty($value)) {
            return null;
        }

        if (ctype_digit($value)) {
            return new Year($value);
        }

        /** @var array<int,int> */
        $chars = count_chars($value);
        if ($chars[ord(',')] === 1) {
            list($start, $end) = explode(',', $value);
            return new CustomDatespan($start, $end);
        }

        if ($chars[ord('-')] === 1) {
            return Month::fromString($value);
        }

        try {
            return new Day($value);
        } catch (\InvalidArgumentException $ex) {
            throw new \InvalidArgumentException('Given value ' . $value . ' can not be parsed as a datespan: ' . $ex->getMessage());
        }
    }
}
