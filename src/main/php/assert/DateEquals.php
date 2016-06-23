<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date\assert;
use bovigo\assert\predicate\Predicate;
use SebastianBergmann\Exporter\Exporter;
use stubbles\date\Date;

use function bovigo\assert\predicate\equals;
/**
 * Predicate to test for date equality with bovigo/assert.
 *
 * @since  6.0.0
 */
class DateEquals extends Predicate
{
    /**
     * the expected value
     *
     * @type  mixed
     */
    private $expected;

    /**
     * constructor
     *
     * @param  mixed   $expected  value to which test values must be equal
     */
    public function __construct($expected)
    {
        $this->expected = $expected;
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        if (!($value instanceof Date)) {
            throw new \InvalidArgumentException(
                    'Value to test is not an instance of ' . Date::class
            );
        }

        return equals(date_format(date_create($this->expected), 'U'))
                ->test(date_format($value->handle(), 'U'));
    }

    /**
     * returns string representation of predicate
     *
     * @return  string
     */
    public function __toString()
    {
        return 'is equal to date ' . $this->expected;
    }

    /**
     * returns a textual description of given value
     *
     * @param   \SebastianBergmann\Exporter\Exporter  $exporter
     * @param   mixed                                 $value
     * @return  string
     */
    public function describeValue(Exporter $exporter, $value)
    {
        if ($value instanceof Date) {
            return 'date ' . $value->format('c');
        }

        return parent::describeValue($exporter, $value);
    }
}
