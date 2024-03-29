<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\assert;

use bovigo\assert\predicate\Equals;
use bovigo\assert\predicate\Predicate;
use DateTime;
use InvalidArgumentException;
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
    private DateTime $expectedDate;
    private ?string $lastFailureDiff = null;

    /**
     * @throws InvalidArgumentException in case given date can not be parsed
     */
    public function __construct(private string $expected)
    {
        $expectedDate = date_create($expected);
        if (false === $expectedDate) {
            throw new InvalidArgumentException(
                'Given value for expected "' . $expected . '" is not a valid date.'
            );
        }

        $this->expectedDate = $expectedDate;
    }

    /**
     * evaluates predicate against given value
     */
    public function test(mixed $value): bool
    {
        if (!($value instanceof Date)) {
            throw new InvalidArgumentException(
                'Value to test is not an instance of ' . Date::class
            );
        }

        // compare unix timestamp, as rfc formatted date contains tinmezones and
        // strings may differ, even if both point to the exact same point in time
        $equals = equals(date_format($this->expectedDate, 'U'));
        if ($equals->test(date_format($value->handle(), 'U'))) {
            return true;
        }

        // get a diff based on a human readable form, as the diff from
        // comparison above would just contain unix timestamps which nobody can
        // really differentiate what is wrong about them
        $getDiff = new Equals(date_format($this->expectedDate, 'r'));
        $getDiff->test(date_format($value->handle(), 'r'));
        $this->lastFailureDiff = $getDiff->diffForLastFailure();
        return false;
    }

    /**
     * returns string representation of predicate
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
     */
    public function hasDiffForLastFailure(): bool
    {
        return !empty($this->lastFailureDiff);
    }

    /**
     * returns diff for last failure
     */
    public function diffForLastFailure(): ?string
    {
        return $this->lastFailureDiff;
    }

    /**
     * returns a textual description of given value
     */
    public function describeValue(Exporter $exporter, $value): string
    {
        if ($value instanceof Date) {
            return 'date ' . $value->format('c');
        }

        return parent::describeValue($exporter, $value);
    }
}
