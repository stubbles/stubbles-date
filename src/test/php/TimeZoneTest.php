<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function bovigo\assert\{
    assertThat,
    assertFalse,
    assertTrue,
    expect,
    predicate\equals
};
/**
 * Tests for stubbles\date\TimeZone.
 */
#[Group('date')]
class TimeZoneTest extends TestCase
{
    private TimeZone $timeZone;

    protected function setUp(): void
    {
        $this->timeZone = new TimeZone('Europe/Berlin');
    }

    #[Test]
    public function nameReturnsTimezoneName(): void
    {
        assertThat($this->timeZone->name(), equals('Europe/Berlin'));
    }

    #[Test]
    public function offsetDstIsTwoHours(): void
    {
        $date = new Date('2007-08-21');
        assertThat($this->timeZone->offset($date), equals('+0200'));
        assertThat($this->timeZone->offsetInSeconds($date), equals(7200));
    }

    #[Test]
    public function offsetNoDstIsOneHour(): void
    {
        $date = new Date('2007-01-21');
        assertThat($this->timeZone->offset($date), equals('+0100'));
        assertThat($this->timeZone->offsetInSeconds($date), equals(3600));
    }

    /**
     * offset in seconds for current date is 3600 seconds or 7200 seconds, depending
     * whether we are in dst or not
     */
    #[Test]
    public function offsetForCurrentDateIs3600SecondsOr7200SecondsDependingWhetherInDstOrNot(): void
    {
        $offset = $this->timeZone->offsetInSeconds();
        assertTrue((3600 === $offset || 7200 === $offset));
    }

    /**
     * offset for some time zones is just an half hour more
     */
    #[Test]
    public function offsetWithHalfHourDST(): void
    {
        $timeZone = new TimeZone('Australia/Adelaide');
        assertThat($timeZone->offset(new Date('2007-01-21')), equals('+1030'));
    }

    /**
     * offset for some time zones is just an half hour more
     */
    #[Test]
    public function offsetWithHalfHourNoDST(): void
    {
        $timeZone = new TimeZone('Australia/Adelaide');
        assertThat($timeZone->offset(new Date('2007-08-21')), equals('+0930'));
    }

    /**
     * a date should be translatable into a date of our current time zone
     */
    #[Test]
    public function translate(): void
    {
        $date = new Date('2007-01-01 00:00 Australia/Sydney');
        assertThat(
            $this->timeZone->translate($date),
            equals(new Date('2006-12-31 14:00:00 Europe/Berlin'))
        );
    }

    #[Test]
    public function timeZonesHavingDstShouldBeMarkedAsSuch(): void
    {
        assertTrue($this->timeZone->hasDst());
    }

    #[Test]
    public function timeZonesAreEqualsIfTheyRepresentTheSameTimeZoneString(): void
    {
        assertTrue($this->timeZone->equals($this->timeZone));
        assertTrue($this->timeZone->equals(new TimeZone('Europe/Berlin')));
        assertFalse($this->timeZone->equals(new TimeZone('Australia/Adelaide')));
        assertFalse($this->timeZone->equals(new \stdClass()));
    }

    #[Test]
    public function nonExistingTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction(): void
    {
        expect(fn() => new TimeZone('Europe/Karlsruhe'))
            ->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function toStringConversionCreatesReadableRepresentation(): void
    {
        assertThat((string) $this->timeZone, equals('Europe/Berlin'));
    }
}
