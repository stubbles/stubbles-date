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
     * @type  string
     */
    private $lastFailureDiff;

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
    public function test($value): bool
    {
        if (!($value instanceof Date)) {
            throw new \InvalidArgumentException(
                    'Value to test is not an instance of ' . Date::class
            );
        }

        // compare unix timestamp, as rfc formatted date contains tinmezones and
        // strings may differ, even if both point to the exact same point in time
        $equals = equals(date_format(date_create($this->expected), 'U'));
        if ($equals->test(date_format($value->handle(), 'U'))) {
            return true;
        }

        // get a diff based on a human readable form, as the diff from
        // comparison above would just contain unix timestamps which nobody can
        // really differentiate what is wrong about them
        $getDiff = equals(date_format(date_create($this->expected), 'r'));
        $getDiff->test(date_format($value->handle(), 'r'));
        $this->lastFailureDiff = $getDiff->diffForLastFailure();
        return false;
    }

    /**
     * returns string representation of predicate
     *
     * @return  string
     */
    public function __toString(): string
    {
        $result = 'is equal to date ' . $this->expected;
        if ($this->hasDiffForLastFailure()) {
            return $result . '.' . $this->diffForLastFailure();
        }

        return $result;
    }

    /**
     * checks if a diff is available for the last failure
     *
     * @return  bool
     */
    public function hasDiffForLastFailure(): bool
    {
        return !empty($this->lastFailureDiff);
    }

    /**
     * returns diff for last failure
     *
     * @return  string
     */
    public function diffForLastFailure(): string
    {
        return $this->lastFailureDiff;
    }

    /**
     * returns a textual description of given value
     *
     * @param   \SebastianBergmann\Exporter\Exporter  $exporter
     * @param   mixed                                 $value
     * @return  string
     */
    public function describeValue(Exporter $exporter, $value): string
    {
        if ($value instanceof Date) {
            return 'date ' . $value->format('c');
        }

        return parent::describeValue($exporter, $value);
    }
}
