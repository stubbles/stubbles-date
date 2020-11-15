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
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isLessThanOrEqualTo,
    predicate\isNotSameAs,
    predicate\isSameAs
};
use function stubbles\date\assert\equalsDate;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\date\Date.
 *
 * @group  date
 */
class DateTest extends TestCase
{
    /**
     * origin time zone for restoring in tearDown()
     *
     * @var  string
     */
    private $originTimeZone;
    /**
     * current date/time as timestamp
     *
     * @var  int
     */
    private $timestamp;
    /**
     * instance to test
     *
     * @var  \stubbles\date\Date
     */
    private $date;

    /**
     * set up test environment
     */
    protected function setUp(): void
    {
        $this->originTimeZone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $this->timestamp = time();
        $this->date      = new Date($this->timestamp);
    }

    /**
     * clean up test environment
     */
    protected function tearDown(): void
    {
        date_default_timezone_set($this->originTimeZone);
    }

    /**
     * construction should work with time zone as part of a well-formed time string
     *
     * @test
     */
    public function constructorParseWithoutTz(): void
    {
        assertThat(
                new Date('2007-01-01 01:00:00 Europe/Berlin'),
                isInstanceOf(Date::class)
        );
    }

    /**
     * construction should work with a unix timestamp
     *
     * @test
     */
    public function constructorUnixtimestampWithoutTz(): void
    {
        assertThat(
                new Date(1187872547),
                equalsDate('2007-08-23T12:35:47+00:00')
        );
    }

    /**
     * construction should work with a unix timestamp and a specified time zone
     *
     * @test
     */
    public function constructorUnixtimestampWithTz(): void
    {
        assertThat(
                new Date(1187872547, new TimeZone('Europe/Berlin')),
                equalsDate('2007-08-23T12:35:47+00:00')
        );
    }

    /**
     * @return  array<array<mixed>>
     */
    public function constructorTimezones(): array
    {
        return [
            [
                '2007-01-01T00:00:00+00:00',
                'Europe/Berlin',
                '2007-01-01 01:00:00 Europe/Berlin',
                null
            ],
            [
                '2007-01-01T00:00:00+00:00',
                'Europe/Berlin',
                '2007-01-01 01:00:00 Europe/Berlin',
                new TimeZone('Europe/Athens')
            ],
            [
                '2006-12-31T23:00:00+00:00',
                'Europe/Athens',
                '2007-01-01 01:00:00',
                new TimeZone('Europe/Athens')
            ]
        ];
    }

    /**
     * @test
     * @dataProvider  constructorTimezones
     */
    public function constructorParseTz(
            string $expectedTimestamp,
            string $expectedTimeZone,
            string $constructorTimestamp,
            TimeZone $constructorTimeZone = null
    ): void {
        $date = new Date($constructorTimestamp, $constructorTimeZone);
        assertThat($date->timeZone()->name(), equals($expectedTimeZone));
        assertThat($date, equalsDate($expectedTimestamp));
    }

    /**
     * a timezone should not be reported erroneously if it actually could not be
     * parsed out of a string.
     *
     * @test
     */
    public function noDiscreteTimeZone(): void
    {
        $date = new Date('2007-11-04 14:32:00+1000');
        assertThat($date->offset(), equals('+1000'));
        assertThat($date->offsetInSeconds(), equals(36000));
    }

    /**
     * correct time zone should be recognized
     *
     * @test
     */
    public function constructorParseNoTz(): void
    {
        $date= new Date('2007-01-01 01:00:00', new TimeZone('Europe/Athens'));
        assertThat($date->timeZone()->name(), equals('Europe/Athens'));

        $date= new Date('2007-01-01 01:00:00');
        assertThat($date->timeZone()->name(), equals('GMT'));
    }

    /**
     * date handling should work as expected
     *
     * @test
     */
    public function dateHandling(): void
    {
        assertThat($this->date->timestamp(), equals($this->timestamp));
        assertThat($this->date->format('r'), equals(date('r', $this->timestamp)));
        assertTrue($this->date->isAfter(new Date('yesterday')));
        assertTrue($this->date->isBefore(new Date('tomorrow')));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function dateComparisonWithoutDateInstances(): void
    {
        assertTrue($this->date->isAfter('yesterday'));
        assertTrue($this->date->isBefore('tomorrow'));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function isBeforeWithInvalidDate(): void
    {
        expect(function() { $this->date->isBefore(new \stdClass()); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function isAfterWithInvalidDate(): void
    {
        expect(function() { $this->date->isAfter(new \stdClass()); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * dates before unix epoch should be handled
     *
     * @test
     */
    public function preUnixEpoch(): void
    {
        assertThat(
                new Date('31.12.1969 00:00 GMT'),
                equalsDate('1969-12-31T00:00:00+00:00')
        );
    }

    /**
     * dates before the year 1582 are 11 days off, but we do not support this
     *
     * Actually, PHP does not support this and we did not want to build a
     * workaround ourself.
     *
     * Quoting Wikipedia:
     * The last day of the Julian calendar was Thursday October 4, 1582 and this
     * was followed by the first day of the Gregorian calendar, Friday October
     * 15, 1582 (the cycle of weekdays was not affected).
     *
     * @test
     * @see   http://en.wikipedia.org/wiki/Gregorian_calendar
     */
    public function pre1582(): void
    {
        //assertThat(new Date('01.01.1500 00:00 GMT'), equalsDate('1499-12-21T00:00:00+00:00'));
        assertThat(
                new Date('01.01.1500 00:00 GMT'),
                equalsDate('1500-01-01T00:00:00+00:00')
        );
    }

    /**
     * dates before the year 1752 are 11 days off, but we do not support this
     *
     * Actually, PHP does not support this and we did not want to build a
     * workaround ourself.
     *
     * Quoting Wikipedia:
     * The Kingdom of Great Britain and thereby the rest of the British Empire
     * (including the eastern part of what is now the United States) adopted the
     * Gregorian calendar in 1752 under the provisions of the Calendar Act 1750;
     * by which time it was necessary to correct by eleven days (Wednesday,
     * September 2, 1752 being followed by  Thursday, September 14, 1752) to
     * account for February 29, 1700 (Julian).
     *
     * @test
     * @see   http://en.wikipedia.org/wiki/Gregorian_calendar
     */
    public function calendarAct1750(): void
    {
        //assertThat(new Date('01.01.1753 00:00 GMT'), equalsDate('1753-01-01T00:00:00+00:00'));
        //assertThat(new Date('01.01.1752 00:00 GMT'), equalsDate('1751-12-21T00:00:00+00:00'));
        assertThat(
                new Date('01.01.1753 00:00 GMT'),
                equalsDate('1753-01-01T00:00:00+00:00')
        );
        assertThat(
                new Date('01.01.1752 00:00 GMT'),
                equalsDate('1752-01-01T00:00:00+00:00')
        );
    }

    /**
     * @return  array<array<mixed>>
     */
    public function anteAndPostMeridiemTestValues(): array
    {
        return [
                ['May 28 1980 1:00AM', 1, '1:00AM != 1h'],
                ['May 28 1980 12:00AM', 0, '12:00AM != 0h'],
                ['May 28 1980 1:00PM', 13, '13:00PM != 13h'],
                ['May 28 1980 12:00PM', 12, '12:00PM != 12h']
        ];
    }
    /**
     * setting of correct hours when date was given troughthe AM/PM format
     *
     * @test
     * @dataProvider  anteAndPostMeridiemTestValues
     */
    public function anteAndPostMeridiem(string $date, int $expected, string $description): void
    {
        $date = new Date($date);
        assertThat($date->hours(), equals($expected), $description);
    }

    /**
     * @return  array<array<mixed>>
     */
    public function anteAndPostMeridiemInMidageTestValues(): array
    {
        return [
                ['May 28 1580 1:00AM', 1, '1:00AM != 1h'],
                ['May 28 1580 12:00AM', 0, '12:00AM != 0h'],
                ['May 28 1580 1:00PM', 13, '1:00PM != 13h'],
                ['May 28 1580 12:00PM', 12, '12:00PM != 12h']
        ];
    }

    /**
     * setting of correct hours when date was given troughthe AM/PM format
     *
     * @test
     * @dataProvider  anteAndPostMeridiemInMidageTestValues
     */
    public function anteAndPostMeridiemInMidage(
            string $date,
            int $expected,
            string $description
    ): void {
        $date = new Date($date);
        assertThat($date->hours(), equals($expected), $description);
    }

    /**
     * date parsing in different formats in pre 1970 epoch.
     *
     * @test
     */
    public function pre1970(): void
    {
        assertThat(new Date('01.02.1969'), equalsDate('1969-02-01T00:00:00+00:00'));
        assertThat(new Date('1969-02-01'), equalsDate('1969-02-01T00:00:00+00:00'));
        assertThat(new Date('1969-02-01 12:00AM'), equalsDate('1969-02-01T00:00:00+00:00'));
    }

    /**
     * serialize()/unserialize() should preserve all data
     *
     * @test
     */
    public function serialization(): void
    {
        $original = new Date('2007-07-18T09:42:08 Europe/Athens');
        $copy     = unserialize(serialize($original));
        assertThat($copy, equalsDate($original->format('c')));
    }

    /**
     * time zone should be preserved during serialize()/unserialize()
     *
     * @test
     */
    public function timeZoneSerialization(): void
    {
        date_default_timezone_set('Europe/Athens');
        $date = new Date('2007-11-20 21:45:33 Europe/Berlin');
        assertThat($date->timeZone()->name(), equals('Europe/Berlin'));
        assertThat($date->offset(), equals('+0100'));

        $copy = unserialize(serialize($date));
        assertThat($copy->offset(), equals('+0100'));
    }

    /**
     * timezone functionality
     *
     * @test
     */
    public function handlingOfTimezone(): void
    {
        $date = new Date('2007-07-18T09:42:08 Europe/Athens');
        assertThat($date->timeZone()->name(), equals('Europe/Athens'));
        assertThat($date->timeZone()->offsetInSeconds($date), equals(3 * 3600));
    }

    /**
     * representation of string is working deterministicly
     *
     * @test
     */
    public function testTimestamp(): void
    {
        date_default_timezone_set('Europe/Berlin');
        $d1 = new Date('1980-05-28 06:30:00 Europe/Berlin');
        $d2 = new Date(328336200);

        assertThat($d1, equals($d2));
        assertThat(new Date($d2->format('Y-m-d H:i:se')), equals($d2));
    }

    /**
     * dates created with a timestamp are in correct timezone ifa timezone has been passed
     *
     * @test
     */
    public function timestampWithTZ(): void
    {
        $date = new Date(328336200, new TimeZone('Australia/Sydney'));
        assertThat($date->timeZone()->name(), equals('Australia/Sydney'));
    }

    /**
     * string formatting preserves same timezone after serialization
     *
     * @test
     */
    public function stringOutputPreserved(): void
    {
        $date = unserialize(serialize(new Date('2007-11-10 20:15 Europe/Berlin')));
        assertThat($date->format('Y-m-d H:i:sO'), equals('2007-11-10 20:15:00+0100'));
        assertThat(
                $date->format('Y-m-d H:i:sO', new TimeZone()),
                equals('2007-11-10 19:15:00+0000')
        );
    }

    /**
     * now() constructs date with current time
     *
     * @test
     */
    public function nowConstructsCurrentDate(): void
    {
        $date = Date::now();
        assertThat($date, isInstanceOf(Date::class));
        $expected = time();
        assertThat($date->timestamp(), isLessThanOrEqualTo($expected));
    }

    /**
     * @test
     * @since  3.5.0
     * @group  bug267
     */
    public function nowConstructsCurrentDateInGmtTimeZone(): void
    {
        assertThat(Date::now()->timeZone()->name(), equals('GMT'));
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug267
     */
    public function nowConstructsCurrentDateWithTimeZone(): void
    {
        assertThat(
                Date::now(new TimeZone('Europe/London'))->timeZone()->name(),
                equals('Europe/London')
        );
    }

    /**
     * single date and time parts should be returned
     *
     * @test
     */
    public function partsReturned(): void
    {
        // 2007-08-23T12:35:47+00:00
        $date = new Date(1187872547);
        assertThat($date->seconds(), equals(47));
        assertThat($date->minutes(), equals(35));
        assertThat($date->hours(), equals(12));
        assertThat($date->day(), equals(23));
        assertThat($date->month(), equals(8));
        assertThat($date->year(), equals(2007));
    }

    /**
     * same dates should be equal
     *
     * @test
     */
    public function sameDatesShouldBeEqual(): void
    {
        $date = new Date('31.12.1969 00:00 GMT');
        assertFalse($date->equals('foo'));
        assertTrue($date->equals(new Date('1969-12-31T00:00:00+00:00')));
        assertFalse($date->equals(new Date('1969-12-01T00:00:00+00:00')));
    }

    /**
     * handle must be cloned as well
     *
     * @test
     */
    public function cloneClonesHandleAsWell(): void
    {
        $date       = new Date('31.12.1969 00:00 GMT');
        $clonedDate = clone $date;
        assertThat($this->retrieveHandle($clonedDate), isNotSameAs($this->retrieveHandle($date)));
    }

    /**
     * Retrieves date handle from given date.
     *
     * @param  Date $date
     * @return \DateTime
     */
    private function retrieveHandle(Date $date): \DateTime
    {
        $property = (new \ReflectionObject($date))->getProperty('dateTime');
        $property->setAccessible(true);
        return $property->getValue($date);
    }

    /**
     * @test
     */
    public function failingConstructionThrowsIllegalArgumentException(): void
    {
        expect(function() { new Date(null); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function invalidTimestampThrowsIllegalArgumentException(): void
    {
        expect(function() { new Date(0.5); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function invalidDateStringThrowsIllegalArgumentException(): void
    {
        expect(function() { new Date('invalid'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * ensure a readable string representation is created
     *
     * @test
     */
    public function toStringConvertsDateTimePropertyIntoReadableDateRepresentation(): void
    {
        $date = new Date('31.12.1969 00:00 GMT');
        assertThat((string) $date, equals('1969-12-31 00:00:00+0000'));
    }

    /**
     * @test
     */
    public function asStringReturnsStringValue(): void
    {
        $date = new Date('2012-01-21 21:00:00');
        assertThat($date->asString(), equals('2012-01-21 21:00:00' . $date->offset()));
    }

    /**
     * @test
     */
    public function classIsAnnotatedWithXmlTag(): void
    {
        assertTrue(annotationsOf('stubbles\date\Date')->contain('XmlTag'));
    }

    /**
     * @return array<array<string>>
     */
    public function methodsWithXmlIgnore(): array
    {
        return [['handle'],
                ['change'],
                ['timestamp'],
                ['seconds'],
                ['minutes'],
                ['hours'],
                ['day'],
                ['month'],
                ['year'],
                ['offset'],
                ['offsetInSeconds'],
                ['timeZone']
        ];
    }
    /**
     * @test
     * @dataProvider  methodsWithXmlIgnore
     */
    public function methodIsAnnotatedWithXmlIgnore(string $method): void
    {
        assertTrue(
                annotationsOf('stubbles\date\Date', $method)
                        ->contain('XmlIgnore')
        );
    }

    /**
     * @test
     */
    public function asStringIsAnnotatedWithXmlAttribute(): void
    {
        assertTrue(
                annotationsOf('stubbles\date\Date', 'asString')
                        ->contain('XmlAttribute')
        );
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromIntCreatesDateInstance(): void
    {
        assertThat(Date::castFrom(1187872547), equals(new Date(1187872547)));
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromStringCreatesDateInstance(): void
    {
        assertThat(
                Date::castFrom('2007-11-04 14:32:00+1000'),
                equals(new Date('2007-11-04 14:32:00+1000'))
        );
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromDateTimeCreatesDateInstance(): void
    {
        assertThat(
                Date::castFrom(new \DateTime('2007-11-04 14:32:00+1000')),
                equals(new Date('2007-11-04 14:32:00+1000'))
        );
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromDateReturnsSameInstance(): void
    {
        $date = new Date('2007-11-04 14:32:00+1000');
        assertThat(Date::castFrom($date), isSameAs($date));
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromOtherValueThrowsIllegalArgumentException(): void
    {
        expect(function() { Date::castFrom(new \stdClass()); })
                ->throws(\InvalidArgumentException::class);
    }
}
