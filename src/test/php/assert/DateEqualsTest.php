<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\date\assert;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
};
/**
 * Tests for stubbles\date\assert\DateEquals.
 *
 * @since 7.0.0
 */
#[Group('assert')]
class DateEqualsTest extends TestCase
{
    private DateEquals $equalsDate;

    protected function setUp(): void
    {
        $this->equalsDate = equalsDate('2007-08-23T12:35:47+00:00');
    }

    #[Test]
    public function invalidExpectedDateThrowsInvalidArgumentException(): void
    {
        expect(fn() => equalsDate('this is not a valid date'))
            ->throws(InvalidArgumentException::class)
            ->withMessage('Given value for expected "this is not a valid date" is not a valid date.');
    }

    #[Test]
    public function hasNoDiffByDefault(): void
    {
        assertFalse($this->equalsDate->hasDiffForLastFailure());
    }

    #[Test]
    public function lastFailureDiffIsNullByDefault(): void
    {
        assertNull($this->equalsDate->diffForLastFailure());
    }

    #[Test]
    public function stringRepresentationContainsExpectedValue(): void
    {
      assertThat(
            (string) $this->equalsDate,
            equals('is equal to date 2007-08-23T12:35:47+00:00')
        );
    }

    #[Test]
    public function testAgainstNonDateThrowsInvalidArgumentException(): void
    {
        expect(fn() => $this->equalsDate->test(303))
            ->throws(InvalidArgumentException::class);
    }

    #[Test]
    public function testAgainstEqualDateReturnsTrue(): void
    {
        assertTrue($this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00')));
    }

    #[Test]
    public function hasNoDiffWhenTestWasSuccessful(): void
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00'));
        assertFalse($this->equalsDate->hasDiffForLastFailure());
    }

    #[Test]
    public function lastFailureDiffIsNullWhenTestWasSuccessful(): void
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00'));
        assertNull($this->equalsDate->diffForLastFailure());
    }

    #[Test]
    public function stringRepresentationContainsExpectedValueOnlyAfterSuccessfulTest(): void
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+00:00'));
        assertThat(
            (string) $this->equalsDate,
            equals('is equal to date 2007-08-23T12:35:47+00:00')
        );
    }

    #[Test]
    public function testAgainstUnequalDateReturnsFalse(): void
    {
        assertFalse($this->equalsDate->test(new Date('2007-08-23T12:35:47+01:00')));
    }

    #[Test]
    public function hasDiffWhenTestFailed(): void
    {
        $this->equalsDate->test(new Date('2007-08-23T12:35:47+01:00'));
        assertTrue($this->equalsDate->hasDiffForLastFailure());
    }

    #[Test]
    public function lastFailureDiffContainsDiffBetweenExpectedAndTestedWhenTestFailed(): void
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

    #[Test]
    public function stringRepresentationContainsDiffWhenTestFailed(): void
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

    #[Test]
    public function describeValueForDateReturnsFormattedDateString(): void
    {
      assertThat(
        $this->equalsDate->describeValue(
            new Exporter(),
            new Date('2007-08-23T12:35:47+01:00')
        ),
        equals('date 2007-08-23T12:35:47+01:00')
        );
    }

    #[Test]
    public function describeValueForNonDateReturnsParentFormat(): void
    {
        assertThat(
            $this->equalsDate->describeValue(new Exporter(), 'foo'),
            equals("'foo'")
        );
    }
}
