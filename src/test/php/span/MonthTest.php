<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date\span;
use stubbles\date\Date;
/**
 * Tests for stubbles\date\span\Month.
 *
 * @group  date
 * @group  span
 */
class MonthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringRepresentationContainsYearAndMonth()
    {
        $month = new Month(2007, 4);
        $this->assertEquals('2007-04', $month->asString());
    }

    /**
     * @test
     */
    public function stringRepresentationForCorrectMonthNotFixed()
    {
        $month = new Month(2007, '04');
        $this->assertEquals('2007-04', $month->asString());
    }

    /**
     * @test
     */
    public function properStringConversionContainsYearAndMonth()
    {
        $month = new Month(2007, 4);
        $this->assertEquals('2007-04', (string) $month);
    }

    /**
     * @test
     */
    public function properStringConversionForCorrectMonthNotFixed()
    {
        $month = new Month(2007, '04');
        $this->assertEquals('2007-04', (string) $month);
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven()
    {
        $month = new Month(null, 10);
        $this->assertEquals(date('Y') . '-10', $month->asString());
    }

    /**
     * @test
     */
    public function usesCurrentMonthIfNotGiven()
    {
        $month = new Month(2007);
        $this->assertEquals('2007-' . date('m'), $month->asString());
    }

    /**
     * @test
     */
    public function usesCurrentYearAndMonthIfNotGiven()
    {
        $month = new Month();
        $this->assertEquals(date('Y') . '-' . date('m'), $month->asString());
    }

    /**
     * data provider for amountOfDaysIsAlwaysCorrect() and getDaysReturnsAllDaysInMonth()
     *
     * @return  array
     */
    public function dayMonth()
    {
        return [[new Month(2007, 4), 30],
                [new Month(2007, 3), 31],
                [new Month(2007, 2), 28],
                [new Month(2008, 2), 29]
        ];
    }

    /**
     * @param  \stubbles\date\span\Month  $month     month to get days for
     * @param  int                        $dayCount  amount of days in this month
     * @test
     * @dataProvider  dayMonth
     */
    public function amountOfDaysIsAlwaysCorrect(Month $month, $dayCount)
    {
        $this->assertEquals($dayCount, $month->amountOfDays());
    }

    /**
     * @param  \stubbles\date\span\Month  $month     month to get days for
     * @param  int                        $dayCount  amount of days in this month
     * @test
     * @dataProvider  dayMonth
     */
    public function daysReturnsAllDaysInMonth(Month $month, $dayCount)
    {
        $days        = 0;
        $expectedDay = 1;
        foreach ($month->days() as $dayString => $day) {
            $this->assertEquals(
                    $month->asString() . '-' . str_pad($expectedDay, 2, '0', STR_PAD_LEFT),
                    $dayString
            );
            $this->assertEquals($expectedDay, $day->asInt());
            $expectedDay++;
            $days++;
        }

        $this->assertEquals($dayCount, $days);
    }

    /**
     * @test
     */
    public function monthInNextYearIsInFuture()
    {
        $month = new Month(date('Y') + 1, 7);
        $this->assertTrue($month->isInFuture());
    }

    /**
     * @test
     */
    public function monthInLastYearIsNotInFuture()
    {
        $month = new Month(date('Y') - 1, 7);
        $this->assertFalse($month->isInFuture());
    }

    /**
     * @test
     */
    public function currentMonthIsNotInFuture()
    {
        $month = new Month(date('Y'), date('m'));
        $this->assertFalse($month->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousMonth()
    {
        $month = new Month(2007, 4);
        $this->assertFalse($month->containsDate(new Date('2007-03-31')));
    }

    /**
     * @test
     */
    public function doesContainAllDatesForThisMonth()
    {
        $month = new Month(2007, 4);
        for ($day = 1; $day < 31; $day++) {
            $this->assertTrue($month->containsDate(new Date('2007-04-' . $day)));
        }
    }

    /**
     * @test
     */
    public function doesNotContainDateFromLaterMonth()
    {
        $month = new Month(2007, 4);
        $this->assertFalse($month->containsDate(new Date('2007-05-01')));
    }

    /**
     * @test
     */
    public function isCurrentMonthReturnsTrueForCreationWithoutArguments()
    {
        $month = new Month();
        $this->assertTrue($month->isCurrentMonth());
    }

    /**
     * @test
     */
    public function isCurrentMonthReturnsTrueWhenCreatedForCurrentMonth()
    {
        $month = new Month(date('Y'), date('m'));
        $this->assertTrue($month->isCurrentMonth());
    }

    /**
     * @test
     */
    public function isCurrentMonthReturnsFalseForAllOtherMonths()
    {
        $month = new Month(2007, 4);
        $this->assertFalse($month->isCurrentMonth());
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function lastCreatesInstanceWhichIsNotCurrentMonth()
    {
        $this->assertFalse(Month::last()->isCurrentMonth());
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function lastCreatesInstanceForPreviousMonth()
    {
        $this->assertEquals(
                date('Y') . '-'. date('m', strtotime('first day of previous month')),
                Month::last()->asString()
        );
    }

    /**
     * @test
     * @since  3.5.2
     */
    public function createFromStringParsesStringToCreateInstance()
    {
        $this->assertEquals('2014-05', Month::fromString('2014-05')->asString());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  3.5.3
     */
    public function createFromInvalidStringThrowsInvalidArgumentException()
    {
         Month::fromString('invalid');
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function nextMonthRaisesYearForDecember()
    {
        $month = new Month(2014, 12);
        $this->assertEquals('2015-01', $month->next());
    }

    /**
     * @test
     * @since  5.2.0
     */
    public function beforeMonthLowersYearForJanuary()
    {
        $month = new Month(2014, 01);
        $this->assertEquals('2013-12', $month->before());
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function typeIsWeek()
    {
        $this->assertEquals('month', Month::fromString('2014-05')->type());
    }
}
