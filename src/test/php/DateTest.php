<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date;
use stubbles\lang\reflect;
/**
 * Helper class for the test.
 */
class DateHandleDeliverer extends Date
{
    /**
     * returns handle
     *
     * @return  \DateTime
     */
    public static function deliverHandle(Date $date)
    {
        return $date->dateTime;
    }
}
/**
 * Tests for stubbles\date\Date.
 *
 * @group  date
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * origin time zone for restoring in tearDown()
     *
     * @type  string
     */
    private $originTimeZone;
    /**
     * current date/time as timestamp
     *
     * @type  int
     */
    private $timestamp;
    /**
     * instance to test
     *
     * @type  \stubbles\date\Date
     */
    private $date;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->originTimeZone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $this->timestamp = time();
        $this->date      = new Date($this->timestamp);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->originTimeZone);
    }

    /**
     * helper assertion for the test
     *
     * @param  string    $expected  expected date as string
     * @param  stubbles\lang\types\Date  $date      date to check for equality to expected date
     */
    protected function assertDateEquals($expected, Date $date)
    {
        $this->assertEquals(
                date_format(date_create($expected), 'U'),
                date_format($date->getHandle(), 'U'),
                'Expected ' . $expected . ' but got ' . $date->format('c')
        );
    }

    /**
     * construction should work with time zone as part of a well-formed time string
     *
     * @test
     */
    public function constructorParseWithoutTz()
    {
        $this->assertTrue(
                new Date('2007-01-01 01:00:00 Europe/Berlin') instanceof Date
        );
    }

    /**
     * construction should work with a unix timestamp
     *
     * @test
     */
    public function constructorUnixtimestampWithoutTz()
    {
        $this->assertDateEquals(
                '2007-08-23T12:35:47+00:00',
                new Date(1187872547)
        );
    }

    /**
     * construction should work with a unix timestamp and a specified time zone
     *
     * @test
     */
    public function constructorUnixtimestampWithTz()
    {
        $this->assertDateEquals(
                '2007-08-23T12:35:47+00:00',
                new Date(1187872547, new TimeZone('Europe/Berlin'))
        );
    }

    public function constructorTimezones()
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
    public function constructorParseTz($expectedTimestamp, $expectedTimeZone, $constructorTimestamp, $constructorTimeZone)
    {
        $date = new Date($constructorTimestamp, $constructorTimeZone);
        $this->assertEquals($expectedTimeZone, $date->timeZone()->name());
        $this->assertDateEquals($expectedTimestamp, $date);
    }

    /**
     * a timezone should not be reported erroneously if it actually could not be
     * parsed out of a string.
     *
     * @test
     */
    public function noDiscreteTimeZone()
    {
        $date = new Date('2007-11-04 14:32:00+1000');
        $this->assertEquals('+1000', $date->offset());
        $this->assertEquals(36000, $date->offsetInSeconds());
    }

    /**
     * correct time zone should be recognized
     *
     * @test
     */
    public function constructorParseNoTz()
    {
        $date= new Date('2007-01-01 01:00:00', new TimeZone('Europe/Athens'));
        $this->assertEquals('Europe/Athens', $date->timeZone()->name());

        $date= new Date('2007-01-01 01:00:00');
        $this->assertEquals('GMT', $date->timeZone()->name());
    }

    /**
     * date handling should work as expected
     *
     * @test
     */
    public function dateHandling()
    {
        $this->assertEquals($this->timestamp, $this->date->getTimestamp());
        $this->assertEquals(date('r', $this->timestamp), $this->date->format('r'));
        $this->assertTrue($this->date->isAfter(new Date('yesterday')));
        $this->assertTrue($this->date->isBefore(new Date('tomorrow')));
    }

    /**
     * @test
     * @since  3.5.0
     */
    public function dateComparisonWithoutDateInstances()
    {
        $this->assertTrue($this->date->isAfter('yesterday'));
        $this->assertTrue($this->date->isBefore('tomorrow'));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  3.5.0
     */
    public function isBeforeWithInvalidDate()
    {
        $this->assertTrue($this->date->isBefore(new \stdClass()));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  3.5.0
     */
    public function isAfterWithInvalidDate()
    {
        $this->assertTrue($this->date->isAfter(new \stdClass()));
    }

    /**
     * dates before unix epoch should be handled
     *
     * @test
     */
    public function preUnixEpoch()
    {
        $this->assertDateEquals(
                '1969-12-31T00:00:00+00:00',
                new Date('31.12.1969 00:00 GMT')
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
    public function pre1582()
    {
        //$this->assertDateEquals('1499-12-21T00:00:00+00:00', new Date('01.01.1500 00:00 GMT'));
        $this->assertDateEquals(
                '1500-01-01T00:00:00+00:00',
                new Date('01.01.1500 00:00 GMT')
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
    public function calendarAct1750()
    {
        //$this->assertDateEquals('1753-01-01T00:00:00+00:00', new Date('01.01.1753 00:00 GMT'));
        //$this->assertDateEquals('1751-12-21T00:00:00+00:00', new Date('01.01.1752 00:00 GMT'));
        $this->assertDateEquals(
                '1753-01-01T00:00:00+00:00',
                new Date('01.01.1753 00:00 GMT')
        );
        $this->assertDateEquals(
                '1752-01-01T00:00:00+00:00',
                new Date('01.01.1752 00:00 GMT')
        );
    }

    /**
     * setting of correct hours when date was given troughthe AM/PM format
     *
     * @test
     */
    public function anteAndPostMeridiem()
    {
        $date = new Date('May 28 1980 1:00AM');
        $this->assertEquals(1, $date->hours(), '1:00AM != 1h');
        $date = new Date('May 28 1980 12:00AM');
        $this->assertEquals(0, $date->hours(), '12:00AM != 0h');
        $date = new Date('May 28 1980 1:00PM');
        $this->assertEquals(13, $date->hours(), '1:00PM != 13h');
        $date = new Date('May 28 1980 12:00PM');
        $this->assertEquals(12, $date->hours(), '12:00PM != 12h');
    }

    /**
     * setting of correct hours when date was given troughthe AM/PM format
     *
     * @test
     */
    public function anteAndPostMeridiemInMidage()
    {
        $date = new Date('May 28 1580 1:00AM');
        $this->assertEquals(1, $date->hours(), '1:00AM != 1h');
        $date = new Date('May 28 1580 12:00AM');
        $this->assertEquals(0, $date->hours(), '12:00AM != 0h');
        $date = new Date('May 28 1580 1:00PM');
        $this->assertEquals(13, $date->hours(), '1:00PM != 13h');
        $date = new Date('May 28 1580 12:00PM');
        $this->assertEquals(12, $date->hours(), '12:00PM != 12h');
    }

    /**
     * date parsing in different formats in pre 1970 epoch.
     *
     * @test
     */
    public function pre1970()
    {
        $this->assertDateEquals('1969-02-01T00:00:00+00:00', new Date('01.02.1969'));
        $this->assertDateEquals('1969-02-01T00:00:00+00:00', new Date('1969-02-01'));
        $this->assertDateEquals('1969-02-01T00:00:00+00:00', new Date('1969-02-01 12:00AM'));
    }

    /**
     * serialize()/unserialize() should preserve all data
     *
     * @test
     */
    public function serialization()
    {
        $original = new Date('2007-07-18T09:42:08 Europe/Athens');
        $copy     = unserialize(serialize($original));
        $this->assertDateEquals($original->format('c'), $copy);
    }

    /**
     * time zone should be preserved during serialize()/unserialize()
     *
     * @test
     */
    public function timeZoneSerialization()
    {
        date_default_timezone_set('Europe/Athens');
        $date = new Date('2007-11-20 21:45:33 Europe/Berlin');
        $this->assertEquals('Europe/Berlin', $date->timeZone()->name());
        $this->assertEquals('+0100', $date->offset());

        $copy = unserialize(serialize($date));
        $this->assertEquals('+0100', $copy->offset());
    }

    /**
     * timezone functionality
     *
     * @test
     */
    public function handlingOfTimezone()
    {
        $date = new Date('2007-07-18T09:42:08 Europe/Athens');
        $this->assertEquals('Europe/Athens', $date->timeZone()->name());
        $this->assertEquals(3 * 3600, $date->timeZone()->offsetInSeconds($date));
    }

    /**
     * representation of string is working deterministicly
     *
     * @test
     */
    public function testTimestamp()
    {
        date_default_timezone_set('Europe/Berlin');
        $d1 = new Date('1980-05-28 06:30:00 Europe/Berlin');
        $d2 = new Date(328336200);

        $this->assertEquals($d1, $d2);
        $this->assertEquals($d2, new Date($d2->format('Y-m-d H:i:se')));
    }

    /**
     * dates created with a timestamp are in correct timezone ifa timezone has been passed
     *
     * @test
     */
    public function timestampWithTZ()
    {
        $date = new Date(328336200, new TimeZone('Australia/Sydney'));
        $this->assertEquals('Australia/Sydney', $date->timeZone()->name());
    }

    /**
     * string formatting preserves same timezone after serialization
     *
     * @test
     */
    public function stringOutputPreserved()
    {
        $date = unserialize(serialize(new Date('2007-11-10 20:15+0100')));
        $this->assertEquals('2007-11-10 20:15:00+0100', $date->format('Y-m-d H:i:sO'));
        $this->assertEquals('2007-11-10 19:15:00+0000', $date->format('Y-m-d H:i:sO', new TimeZone()));
    }

    /**
     * now() constructs date with current time
     *
     * @test
     */
    public function nowConstructsCurrentDate()
    {
        $date = Date::now();
        $this->assertInstanceOf('stubbles\date\Date', $date);
        $this->assertLessThanOrEqual(time(), $date->timestamp());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug267
     */
    public function nowConstructsCurrentDateInUtcTimeZone()
    {
        if (version_compare(phpversion(), '5.5.9', '>')) {
            $this->markTestSkipped('Test only works until 5.5.9, see http://evertpot.com/php-5-5-10-timezone-changes/');
        }

        $this->assertEquals(
                'UTC',
                Date::now()->timeZone()->name()
        );
    }

    /**
     * @test
     * @since  3.5.0
     * @group  bug267
     * @requires PHP 5.5.10
     */
    public function nowConstructsCurrentDateInGmtTimeZone()
    {
        $this->assertEquals(
                'GMT',
                Date::now()->timeZone()->name()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug267
     */
    public function nowConstructsCurrentDateWithTimeZone()
    {
        $this->assertEquals(
                'Europe/London',
                Date::now(new TimeZone('Europe/London'))->timeZone()->name()
        );
    }

    /**
     * single date and time parts should be returned
     *
     * @test
     */
    public function partsReturned()
    {
        // 2007-08-23T12:35:47+00:00
        $date = new Date(1187872547);
        $this->assertEquals(47, $date->seconds());
        $this->assertEquals(35, $date->minutes());
        $this->assertEquals(12, $date->hours());
        $this->assertEquals(23, $date->day());
        $this->assertEquals(8, $date->month());
        $this->assertEquals(2007, $date->year());
    }

    /**
     * same dates should be equal
     *
     * @test
     */
    public function sameDatesShouldBeEqual()
    {
        $date = new Date('31.12.1969 00:00 GMT');
        $this->assertFalse($date->equals('foo'));
        $this->assertTrue($date->equals(new Date('1969-12-31T00:00:00+00:00')));
        $this->assertFalse($date->equals(new Date('1969-12-01T00:00:00+00:00')));
    }

    /**
     * handle must be cloned as well
     *
     * @test
     */
    public function cloneClonesHandleAsWell()
    {
        $date       = new Date('31.12.1969 00:00 GMT');
        $clonedDate = clone $date;
        $this->assertNotSame(
                DateHandleDeliverer::deliverHandle($date),
                DateHandleDeliverer::deliverHandle($clonedDate)
        );
    }

    /**
     * failing constructoon throws a illegal argument exception
     *
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function failingConstructionThrowsIllegalArgumentException()
    {
        new Date(null);
    }

    /**
     * failing constructoon throws a illegal argument exception
     *
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidDateStringhrowsIllegalArgumentException()
    {
        new Date('invalid');
    }

    /**
     * ensure a readable string representation is created
     *
     * @test
     */
    public function toStringConvertsDateTimePropertyIntoReadableDateRepresentation()
    {
        $date = new Date('31.12.1969 00:00 GMT');
        $this->assertEquals(
                '1969-12-31 00:00:00+0000',
                (string) $date
        );
    }

    /**
     * @test
     */
    public function asStringReturnsStringValue()
    {
        $date = new Date('2012-01-21 21:00:00');
        $this->assertEquals(
                '2012-01-21 21:00:00' . $date->getOffset(),
                $date->asString()
        );
    }

    /**
     * creates reflection instance for class under test
     *
     * @return  \stubbles\lang\reflect\ReflectionClass
     */
    private function getDateReflectionClass()
    {
        return \stubbles\lang\reflect('stubbles\date\Date');
    }

    /**
     * @test
     */
    public function classIsAnnotatedWithXmlTag()
    {
        $this->assertTrue(
                reflect\annotationsOf('stubbles\date\Date')->contain('XmlTag')
        );
    }

    /**
     * returns list of methods which should be annotated with XmlIgnore
     *
     * @return  array
     */
    public function getXmlIgnoredMethods()
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
     * @dataProvider  getXmlIgnoredMethods
     */
    public function methodIsAnnotatedWithXmlIgnore($method)
    {
        $this->assertTrue(
                reflect\annotationsOf('stubbles\date\Date', $method)
                        ->contain('XmlIgnore')
        );
    }

    /**
     * @test
     */
    public function asStringIsAnnotatedWithXmlAttribute()
    {
        $this->assertTrue(
                reflect\annotationsOf('stubbles\date\Date', 'asString')
                        ->contain('XmlAttribute')
        );
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromIntCreatesDateInstance()
    {
        $this->assertEquals(new Date(1187872547), Date::castFrom(1187872547));
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromStringCreatesDateInstance()
    {
        $this->assertEquals(
                new Date('2007-11-04 14:32:00+1000'),
                Date::castFrom('2007-11-04 14:32:00+1000')
        );
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromDateTimeCreatesDateInstance()
    {
        $this->assertEquals(
                new Date('2007-11-04 14:32:00+1000'),
                Date::castFrom(new \DateTime('2007-11-04 14:32:00+1000'))
        );
    }

    /**
     * @test
     * @since  3.4.4
     */
    public function castFromDateReturnsSameInstance()
    {
        $date = new Date('2007-11-04 14:32:00+1000');
        $this->assertSame($date, Date::castFrom($date));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  3.4.4
     */
    public function castFromOtherValueThrowsIllegalArgumentException()
    {
        Date::castFrom(new \stdClass());
    }
}
