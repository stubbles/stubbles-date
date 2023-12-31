stubbles/date
=============

Handling dates and date spans in a immutable way, using a beautiful API.


Build status
------------

![Tests](https://github.com/stubbles/stubbles-date/workflows/Tests/badge.svg)

[![Latest Stable Version](https://poser.pugx.org/stubbles/date/version.png)](https://packagist.org/packages/stubbles/date) [![Latest Unstable Version](https://poser.pugx.org/stubbles/date/v/unstable.png)](//packagist.org/packages/stubbles/date)


Installation
------------

_stubbles/date_ is distributed as [Composer](https://getcomposer.org/)
package. To install it as a dependency of your package use the following
command:

    composer require "stubbles/date": "^9.0"


Requirements
------------

_stubbles/date_ requires at least PHP 8.2.



Introduction
------------

The origin of the classes in this library are from the days when PHP's object-
oriented date/time handling classes were not immutable. In the meantime immutable
versions of them have been added to PHP, but we like the API of the classes here
more, as they lead to a better grammar in the code in which they are used.

The underlaying implementation makes use of PHP's object-oriented date/time
handling classes, but abstracts them such that the _stubbles/date_ instances are immutable.
Every change to a date will result in a new instance. Although the date class
exposes the underlaying handle, the user will only receive a clone of that, so
modifying such a handle will not mutate the date instance from which the handle
was obtained.


`stubbles\date\Date`
--------------------

Please note that this class not only covers a date, but an exact point in time.
Each instance also has a time part. If you are interested in a concrete day only
please use `stubbles\date\span\Day` (see below).


### Create a new instance

When creating a new date instance there are different ways to set the actual date:

* integer, interpreted as timestamp: `new Date(1187872547)`
* string, parsed into a date:  `new Date('2007-01-01 01:00:00 Europe/Berlin')`
* `\DateTime` object, will be used as is: `new Date(new \DateTime())`
* no argument,creates a date representing the current time: `new Date()`,
  equivalent to `Date::now()`

The second argument to the constructor is an optional timezone. Timezone
assignment works through these rules:

* If the time is given as string and contains a parseable timezone identifier
  that one is used.
* If no timezone could be determined, the timezone given by the second constructor
  parameter is used.
* If no timezone has been given as second parameter, the default timezone of the
  system is used.


### Liberal type hinting

If you have a method which accepts an instance of `stubbles\date\Date` you can
choose to be liberal in what you accept. Instead of type hinting against the
concrete type your method can cast the provided value into an instance:

```php
/**
 * does something cool
 *
 * @param  int|string|stubbles\date\Date  $date
 */
function doSomething($date)
{
    $date = Date::castFrom($date);
    // now do something with $date, which is an instance of stubbles\date\Date
}
```

The `Date::castFrom();` accepts four different value types:

* integer, interpreted as unix timestamp: `Date:castFrom(1187872547)`
* string, parsed into a date: `Date:castFrom('2007-01-01 01:00:00 Europe/Berlin')`
* `\DateTime` object: `Date:castFrom(new \DateTime())`
* instances of `stubbles\date\Date` itself

An instance of `stubbles\date\Date` will always be returned as is, the other
allowed values will result in the creation of a `stubbles\date\Date` instance.
Passing any other value will result in a `\InvalidArgumentException`.


### Change a date

To change a date the `Date::change()` method can be called. This will return an
instance of `stubbles\date\DateModifier` which provides several different methods
to change the date and/or time. All changes will result in a new `stubbles\date\Date`
instance, the instance on which `Date::change()` is originally called remains
unchanged:

```php
$currentDate = Date::now();
// create new date with current time but 48 hours ago, this will not change $currentDate
$newDate = $currentDate->change()->byHours(-48);
```

Here's a list of methods offered by `stubbles\date\DateModifier`:

* `to($target)`: change date by relative format accepted by [strtotime()](http://php.net/strtotime)
* `timeTo($time)`: keep date, i.e. day, month and year, but change time to given
   value, must be in format _HH:mm:ss_
* `createDateWithNewTime($hour, $minute, $second)`: same as above, but with
  separate parameters for all values
* `timeToStartOfDay()`: alias for `timeTo('00:00:00')`
* `timeToEndOfDay()`: alias for `timeTo('23:59:59')`
* `hourTo($hour)`; keep day, month, year, minutes and seconds, but change hour
   to given value
* `byHours($hours)`: add given amount of hours to current date and time. A negative
  value will subtract the hours
* `minuteTo($minute)`: keep day, month, year, hours and seconds, but change
   minutes to given value
* `byMinutes($minutes)`: add given amount of minutes to current date and time. A
  negative value will subtract the minutes.
* `secondTo($second)`: keep day, month, year, hours and minutes, but change
  seconds to given value
* `bySeconds($seconds)`: add given amount of seconds to current date and time. A
  negative value will subtract the seconds.
* `dateTo($date)`: change date but keep time, given date must be in format _YYYY-MM-DD_
* `yearTo($year)`: keep day, month, hours, minutes and seconds, but change year
   to given value
* `byYears($years)`: add given amount of years to current date and time. A
  negative value will subtract the years.
* `monthTo($month)`: keep day, year, hours, minutes and seconds, but change month
  to given value
* `byMonths($months)`: add given amount of months to current date and time. A
  negative value will subtract the months.
* `dayTo($day)`: keep month, year, hours, minutes and seconds, but change day
  to given value
* `byDays($days)`: add given amount of days to current date and time. A negative
   value will subtract the days.


### Comparing dates

Instances of `stubbles\date\Date` can be compared with each other:

```php
$date = Date::now();
if ($date->isBefore('2017-01-01 00:00:00')) {
    // execute when current date is before 2017
}
```

```php
$date = Date::now();
if ($date->isAfter('2017-01-01 00:00:00')) {
    // execute when current date is after beginning of 2017
}
```

Comparison is done based on the unix timestamp.

Both `isBefore()` and `isAfter()` accept all values that are accepted by
`Date::castFrom()`, see above.


### Date formatting

The date can be displayed as a string by formatting:

```php
echo 'Current date and time in system timezone: ' . Date::now()->format('Y-m-d H:i:s') . PHP_EOL;
echo 'Current date and time in timezone Europe/Berlin: ' . Date::now()->format('Y-m-d H:i:s', new TimeZone('Europe/Berlin')) . PHP_EOL;
```

When an instances is casted to a string, the output format will be _Y-m-d H:i:sO_.


Date spans
----------

Sometimes it is necessary to not cover a specific date only, but a span between
two points in time. Most notably these are things like a single day, months,
weeks or even a year. As it is impractical to always carry the starting and
ending point of such a span, _stubbles/date_  provides the `stubbles\date\span\Datespan`
interface and various implementations.


### Default methods of each date span

* `start()`: returns the exact starting point of the span
* `startsBefore($date)`: checks if the date span starts before the given point
* `startsAfter($date)`: checks if the date span starts after the given point
* `end()`: returns exact ending point of the span
* `endsBefore($date)`: checks if date span ends before the given point
* `endsAfter($date)`: checks if date span ends after the given point
* `formatStart($format, TimeZone $timeZone = null)`: format start point in given format
* `formatEnd($format, TimeZone $timeZone = null)`: format end point in given format
* `amountOfDays()`: returns amount of days that are covered by the date span
* `days()`: returns an iterator which allows to iterate over each single day
  within this date span
* `isInFuture()`: checks whether date span is completely in the future based on
  current date and time
* `containsDate($date)`: checks if given date and time are contained in the date span

All methods which have a `$date` parameter accept all values that are accepted
by `Date::castFrom()`, see above.

### List of provided date span implementations


#### `stubbles\dates\span\Day`

Covers a whole day, starting at _00:00:00_ and ending at _23:59:59_.

```php
// create without argument always points to current day
$today = new Day();

// create with given date
$another = new Day('2016-06-27');

// create day from given stubbles\date\Date instance
$oneMore = new Day(new Date('2013-05-28'));

// creates a new instance representing tomorrow
$tomorrow = Day::tomorrow();

// creates a new instance representing yesterday
$yesterday = Day::yesterday();
```

Additional methods:

* `next()`: creates a new instance with the day after the represented day
* `before()`: create a new instance with the day before the represented day
* `isToday()`: checks if the day represents the current day


#### `stubbles\dates\span\Week`

Covers a whole week, starting on the given date at _00:00:00_ and ending at
seven days later at _23:59:59_.

```php
// create a week starting today
$week1 = new Week(Date::now());

// create a week which starts tomorrow
$week2 = new Week('tomorrow');

// create a week which represents the 5th calender week of 2016
$week3 = Week::fromString('2016-W05')
```

Additional methods:

* `number()`: returns the week number


#### `stubbles\dates\span\Month`

Covers a month, starting at the first of the month at _00:00:00_ and ending at
the last day of the month at _23:59:59_.

```php
// creates instance representing the current month
$currentMonth = new Month();

// creates instance with current month but in the year 2014-05
$currentMonthIn2015 = new Month(2015);

// create instance representing June 2016
$exactMonth = new Month(2016, 6);

// create instance representing month given as string, format must be YYYY-MM
$otherMonth = Month::fromString('2016-07');

// creates instance representing the month before current month
$lastMonth = Month::last();

// creates instance for current month execpt when today is the first day of a
// month, the the instance represents the month before
// ideally suited when creating reports, as most often the report created on the
// first month of a day should be for the last month instead of for the current
// month
$reportingMonth = Month::currentOrLastWhenFirstDay()
```

Additional methods:

* `next()`: creates instance with month after the currently represented month
* `before()`: creates instance with month before the currently represented month
* `year()`: returns the year in which the month is
* `isCurrentMonth()`: checks whether month instance represents the current month


#### `stubbles\dates\span\Year`

Covers a year, starting at _January 01 00:00:00_ and ending at _December 31 23:59:59_.

```php
// create instance representing the current year
$currentYear = new Year();

// creates instance representing the year 2015
$year2015 = new Year(2015);
```

Additional methods:

* `months()`: returns an iterator which contains all instances of `stubbles\dates\span\Month`
  for the year
* `isLeapYear()`: checks whether year is a leap year
* `isCurrentYear()`: checks whether year represents the current year


#### `stubbles\dates\span\CustomDatespan`

Covers a custom date span starting at the given date at _00:00:00_ and ending at
the given date at 23:59:59.

```php
// create a span from 2006-04-04 00:00:00 to 2006-04-20 23:59:59
$custom = new CustomDatespan('2006-04-04', '2006-04-20');
```

Constructor parameters accept all values that are accepted by `Date::castFrom()`,
see above. Please note that the time for the start is always set to _00:00:00_
and for the end is always set to _23:59:59_. It is not possible to change this
to another time.


Integration with _bovigo/assert_
--------------------------------

In case you want to unit test your code and need to test for date equality you
can use [_bovigo/assert_](https://github.com/mikey179/bovigo-assert)
for the assertions. _stubbles/date_ provides the predicate `stubbles\date\assert\equalsDate()`
which can be used to check for equality of dates. It can take any argument that
`stubbles\date\Date` accepts, and compares the unix timestamp with the actual
value. In case they don't refer to the same point in time the error message will
contain a diff with both dates in human readable form.
