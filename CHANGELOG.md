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
