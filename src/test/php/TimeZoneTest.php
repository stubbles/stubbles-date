<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date;
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
 *
 * @group  date
 */
class TimeZoneTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\date\TimeZone
     */
    private $timeZone;

    /**
     * set up test environment
     */
    protected function setUp(): void
    {
        $this->timeZone = new TimeZone('Europe/Berlin');
    }

    /**
     * @test
     */
    public function nameReturnsTimezoneName()
    {
        assertThat($this->timeZone->name(), equals('Europe/Berlin'));
    }

    /**
     * @test
     */
    public function offsetDstIsTwoHours()
    {
        $date = new Date('2007-08-21');
        assertThat($this->timeZone->offset($date), equals('+0200'));
        assertThat($this->timeZone->offsetInSeconds($date), equals(7200));
    }

    /**
     * @test
     */
    public function offsetNoDstIsOneHour()
    {
        $date = new Date('2007-01-21');
        assertThat($this->timeZone->offset($date), equals('+0100'));
        assertThat($this->timeZone->offsetInSeconds($date), equals(3600));
    }

    /**
     * offset in seconds for current date is 3600 seconds or 7200 seconds, depending
     * whether we are in dst or not
     *
     * @test
     */
    public function offsetForCurrentDateIs3600SecondsOr7200SecondsDependingWhetherInDstOrNot()
    {
        $offset = $this->timeZone->offsetInSeconds();
        assertTrue((3600 === $offset || 7200 === $offset));
    }

    /**
     * offset for some time zones is just an half hour more
     *
     * @test
     */
    public function offsetWithHalfHourDST()
    {
        $timeZone = new TimeZone('Australia/Adelaide');
        assertThat($timeZone->offset(new Date('2007-01-21')), equals('+1030'));
    }

    /**
     * offset for some time zones is just an half hour more
     *
     * @test
     */
    public function offsetWithHalfHourNoDST()
    {
        $timeZone = new TimeZone('Australia/Adelaide');
        assertThat($timeZone->offset(new Date('2007-08-21')), equals('+0930'));
    }

    /**
     * a date should be translatable into a date of our current time zone
     *
     * @test
     */
    public function translate()
    {
        $date = new Date('2007-01-01 00:00 Australia/Sydney');
        assertThat(
                $this->timeZone->translate($date),
                equals(new Date('2006-12-31 14:00:00 Europe/Berlin'))
        );
    }

    /**
     * @test
     */
    public function timeZonesHavingDstShouldBeMarkedAsSuch()
    {
        assertTrue($this->timeZone->hasDst());
    }

    /**
     * @test
     */
    public function timeZonesAreEqualsIfTheyRepresentTheSameTimeZoneString()
    {
        assertTrue($this->timeZone->equals($this->timeZone));
        assertTrue($this->timeZone->equals(new TimeZone('Europe/Berlin')));
        assertFalse($this->timeZone->equals(new TimeZone('Australia/Adelaide')));
        assertFalse($this->timeZone->equals(new \stdClass()));
    }

    /**
     * @test
     */
    public function invalidTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction()
    {
        expect(function() { new TimeZone(500); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function nonExistingTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction()
    {
        expect(function() { new TimeZone('Europe/Karlsruhe'); })->throws(
                defined('HHVM_VERSION') ? \Exception::class : \InvalidArgumentException::class
        );
    }

    /**
     * @test
     */
    public function toStringConversionCreatesReadableRepresentation()
    {
        assertThat((string) $this->timeZone, equals('Europe/Berlin'));
    }
}
