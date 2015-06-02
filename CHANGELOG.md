5.5.1 (2015-06-02)
------------------

  * fixed `stubbles\date\span\Month::currentOrLastWhenFirstDay()` to return current month for days not the first of month


5.5.0 (2015-06-01)
------------------

  * added `stubbles\date\span\Month::currentOrLastWhenFirstDay()`


5.4.0 (2015-06-01)
------------------

  * ensure output of `stubbles\date\span\CustomDatespan::asString()` is compatible with what `stubbles\date\span\parse()` can parse


5.3.0 (2015-05-27)
------------------

### BC breaks

  * changed string representation of `stubbles\date\span\Week`, is now in format "2015-W05" instead of "05" only


### Other changes

  * added `stubbles\date\span\Week::number()`
  * added `stubbles\date\span\Week::fromString()`
  * added `stubbles\date\span\Datespan::type()`



5.2.0 (2015-03-06)
------------------

  * deprecated all get*() methods and replaced them with *(), all get*() will be removed with 6.0.0
  * added `stubbles\date\span\parse()` which parses a string to a datespan instance


5.1.0 (2014-11-13)
------------------

  * implemented #2: helper methods to change date to start or end of day


5.0.0 (2014-08-14)
------------------

  * Removed dependency to stubbles/core.
  * All methods which threw a `stubbles\lang\exception\IllegalArgumentException` will now throw a `InvalidArgumentException` instead


4.0.0 (2014-07-31)
------------------

  * Initial release after split off from stubbles/core
