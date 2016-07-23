<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\date
 */
namespace stubbles\date\assert;
use SebastianBergmann\Exporter\Exporter;
use stubbles\date\Date;

use function bovigo\assert\{
    assert,
    assertFalse,
    assertNull,
    assertTrue,
    expect,
    predicate\equals,
    predicate\isInstanceOf,
    predicate\isLessThanOrEqualTo,
    predicate\isNotSameAs,
    predicate\isSameAs
};
/**
 * Tests for stubbles\date\assert\DateEquals.
 *
 * @group  assert
 * @since  7.0.0
 */
class DateEqualsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  DateEquals
     */
    private $equalsDate;

    public function setUp()
    {
        $this->equalsDate = equalsDate('2007-08-23T12:35:47+00:00');
    }

    /**
     * @test
     */
    public function hasNoDiffByDefault()
    {
        assertFalse($this->equalsDate->hasDiffForLastFailure());
    }

    /**
     * @test
     */
    public function lastFailureDiffIsNullByDefault()
    {
        assertNull($this->equalsDate->diffForLastFailure());
    }

    /**
     * @test
     */
    public function stringRepresentationContainsExpectedValue()
    {
        assert(
                (string) $this->equalsDate,
                equals('is equal to date 2007-08-23T12:35:47+00:00')
        );
    }

    /**
     * @test
     */
    public function testAgainstNonDateThrowsInvalidArgumentException()
    {
        expect(function() { $this->equalsDate->test(303); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function testAgainstEqualDateReturnsTrue()
    {
        assertTrue($this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00')));
    }

    /**
     * @test
     */
    public function hasNoDiffWhenTestWasSuccessful()
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00'));
        assertFalse($this->equalsDate->hasDiffForLastFailure());
    }

    /**
     * @test
     */
    public function lastFailureDiffIsNullWhenTestWasSuccessful()
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00'));
        assertNull($this->equalsDate->diffForLastFailure());
    }

    /**
     * @test
     */
    public function stringRepresentationContainsExpectedValueOnlyAfterSuccessfulTest()
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00'));
        assert(
                (string) $this->equalsDate,
                equals('is equal to date 2007-08-23T12:35:47+00:00')
        );
    }

    /**
     * @test
     */
    public function testAgainstUnequalDateReturnsFalse()
    {
        assertFalse($this->equalsDate->test(new Date('2007-08-23T12:35:47+01:00')));
    }

    /**
     * @test
     */
    public function hasDiffWhenTestFailed()
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+01:00'));
        assertTrue($this->equalsDate->hasDiffForLastFailure());
    }

    /**
     * @test
     */
    public function lastFailureDiffContainsDiffBetweenExpectedAndTestedWhenTestFailed()
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+01:00'));
        assert(
                $this->equalsDate->diffForLastFailure(),
                equals("
--- Expected
+++ Actual
@@ @@
-'Thu, 23 Aug 2007 12:35:47 +0000'
+'Thu, 23 Aug 2007 12:35:47 +0100'
")
        );
    }

    /**
     * @test
     */
    public function stringRepresentationContainsDiffWhenTestFailed()
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+01:00'));
        assert(
                (string) $this->equalsDate,
                equals("is equal to date 2007-08-23T12:35:47+00:00.
--- Expected
+++ Actual
@@ @@
-'Thu, 23 Aug 2007 12:35:47 +0000'
+'Thu, 23 Aug 2007 12:35:47 +0100'
")
        );
    }

    /**
     * @test
     */
    public function describeValueForDateReturnsFormattedDateString()
    {
        assert(
            $this->equalsDate->describeValue(
                    new Exporter(),
                    new Date('2007-08-23T12:35:47+01:00')
            ),
            equals('date 2007-08-23T12:35:47+01:00')
        );
    }

    /**
     * @test
     */
    public function describeValueForNonDateReturnsParentFormat()
    {
        assert(
            $this->equalsDate->describeValue(new Exporter(), 'foo'),
            equals("'foo'")
        );
    }
}
