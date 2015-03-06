<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date\span {
    /**
     * parses given value to a datespan instance
     *
     * If input value is empty return value will be <null>.
     *
     * @param   string  $value
     * @return  \stubbles\date\span\Datespan
     * @throws  \InvalidArgumentException
     */
    function parse($value)
    {
        if (empty($value)) {
            return null;
        }

        if (strlen((int) $value) == strlen($value)) {
            return new Year($value);
        }

        $chars = count_chars($value);
        if ($chars[ord(',')] === 1) {
            list($start, $end) = explode(',', $value);
            return new CustomDatespan($start, $end);
        }

        if ($chars[ord('-')] === 1) {
            try {
                return Month::fromString($value);
            } catch (\InvalidArgumentException $ex) {
                // skip, propably not a month
            }
        }

        try {
            return new Day($value);
        } catch (\InvalidArgumentException $ex) {
            // skip, propably not a day
        }

        throw new \InvalidArgumentException('Given value ' . $value . ' can not be parsed as a datespan');
    }
}