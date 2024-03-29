# Changelog

## 9.0.0 (2023-12-31)

### BC breaks

* raised minimum required PHP version to 8.2

## 8.0.1 (2019-12-10)

* fixed type hints in various doc comments

## 8.0.0 (2019-11-30)

### BC breaks

* raised minimum required PHP version to 7.3
* parameter `$expected` of `stubbles\date\assert\equalsDate` now requires a string

### Other changes

* fixed various possible bugs due to incorrect type usage

## 7.0.0 (2016-07-23)

### BC breaks

* raised minimum required PHP version to 7.0.0
* introduced scalar type hints and strict type checking

## 6.0.0 (2016-06-27)

### BC breaks

* dropped support for PHP 5.4 and 5.5
* removed all get*() methods, were deprecated since 5.2.0

### Other changes

* added `stubbles\date\assert\equalsDate()`

## 5.5.1 (2015-06-02)

* fixed `stubbles\date\span\Month::currentOrLastWhenFirstDay()` to return current month for days not the first of month

## 5.5.0 (2015-06-01)

* added `stubbles\date\span\Month::currentOrLastWhenFirstDay()`

## 5.4.0 (2015-06-01)

* ensure output of `stubbles\date\span\CustomDatespan::asString()` is compatible with what `stubbles\date\span\parse()` can parse

## 5.3.0 (2015-05-27)

### BC breaks

* changed string representation of `stubbles\date\span\Week`, is now in format "2015-W05" instead of "05" only

### Other changes

* added `stubbles\date\span\Week::number()`
* added `stubbles\date\span\Week::fromString()`
* added `stubbles\date\span\Datespan::type()`

## 5.2.0 (2015-03-06)

* deprecated all get*() methods and replaced them with *(), all get*() will be removed with 6.0.0
* added `stubbles\date\span\parse()` which parses a string to a datespan instance

## 5.1.0 (2014-11-13)

* implemented #2: helper methods to change date to start or end of day

## 5.0.0 (2014-08-14)

* Removed dependency to stubbles/core.
* All methods which threw a `stubbles\lang\exception\IllegalArgumentException` will now throw a `InvalidArgumentException` instead

## 4.0.0 (2014-07-31)

* Initial release after split off from stubbles/core
