<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\assert;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Exporter\Exporter;
use stubbles\date\Date;

use function bovigo\assert\{
    assertThat,
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
class DateEqualsTest extends TestCase
{
    /**
     * @type  DateEquals
     */
    private $equalsDate;

    protected function setUp(): void
    {
        $this->equalsDate = equalsDate('2007-08-23T12:35:47+00:00');
    }

    /**
     * @test
     */
    public function invalidExpectedDateThrowsInvalidArgumentException()
    {
        expect(function() { equalsDate('this is not a valid date'); })
            ->throws(\InvalidArgumentException::class)
            ->withMessage('Given value for expected "this is not a valid date" is not a valid date.');
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
      assertThat(
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
        assertThat(
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
        assertThat(
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
        assertThat(
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
      assertThat(
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
      assertThat(
            $this->equalsDate->describeValue(new Exporter(), 'foo'),
            equals("'foo'")
        );
    }
}
