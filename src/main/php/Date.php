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
/**
 * Class for date/time handling.
 *
 * Shameless rip from the XP framework. ;-) Wraps PHP's internal date/time
 * functions for ease of use.
 *
 * @api
 * @XmlTag(tagName='date')
 */
class Date
{
    /**
     * internal date/time handle
     *
     * @type  \DateTime
     */
    protected $dateTime;

    /**
     * constructor
     *
     * Creates a new date object through either a
     * <ul>
     *   <li>integer - interpreted as timestamp</li>
     *   <li>string - parsed into a date</li>
     *   <li>DateTime object - will be used as is</li>
     *   <li>NULL - creates a date representing the current time</li>
     *  </ul>
     *
     * Timezone assignment works through these rules:
     * <ul>
     *   <li>If the time is given as string and contains a parseable timezone
     *       identifier that one is used.</li>
     *   <li>If no timezone could be determined, the timezone given by the
     *       second parameter is used.</li>
     *   <li>If no timezone has been given as second parameter, the system's
     *       default timezone is used.</li>
     *
     * @param   int|string|\DateTime    $dateTime  initial date
     * @param   \stubbles\date\TimeZone  $timeZone  initial timezone
     * @throws  \InvalidArgumentException
     */
    public function __construct($dateTime = null, TimeZone $timeZone = null)
    {
        if (is_numeric($dateTime)) {
            $this->dateTime = date_create('@' . $dateTime, timezone_open('UTC'));
            if (false !== $this->dateTime) {
                date_timezone_set($this->dateTime, (null === $timeZone) ? (new \DateTimeZone(date_default_timezone_get())) : ($timeZone->getHandle()));
            }
        } elseif (is_string($dateTime)) {
            try {
                if (null === $timeZone) {
                    $this->dateTime = new \DateTime($dateTime);
                } else {
                    $this->dateTime = new \DateTime($dateTime, $timeZone->getHandle());
                }
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Given datetime string ' . $dateTime . ' is not a valid date string: ' . $e->getMessage(), $e->getCode(), $e);
            }
        } else {
            $this->dateTime = $dateTime;
        }

        if (!($this->dateTime instanceof \DateTime)) {
            throw new \InvalidArgumentException('Datetime must be either unix timestamp, well-formed timestamp or instance of DateTime, but was ' . gettype($dateTime) . ' ' . $dateTime);
        }
    }

    /**
     * returns current date/time
     *
     * @param   \stubbles\date\TimeZone  $timeZone  initial timezone
     * @return  \stubbles\date\Date
     */
    public static function now(TimeZone $timeZone = null)
    {
        return new self(time(), $timeZone);
    }

    /**
     * casts given value to an instance of Date
     *
     * @param   int|string|\DateTime|Date  $value
     * @param   string                     $name
     * @return  \stubbles\date\Date
     * @throws  \InvalidArgumentException
     * @since   3.4.4
     */
    public static function castFrom($value, $name = 'Date')
    {
        if (is_int($value) || is_string($value) || $value instanceof \DateTime) {
            return new self($value);
        }

        if (!($value instanceof Date)) {
            throw new \InvalidArgumentException($name . ' must be a timestamp, a string containing time info or an instance of \DateTime or stubbles\date\Date, but was ' . gettype($value));
        }

        return $value;
    }

    /**
     * returns internal date/time handle
     *
     * @return  \DateTime
     * @XmlIgnore
     */
    public function handle()
    {
        return clone $this->dateTime;
    }

    /**
     * returns internal date/time handle
     *
     * @return  \DateTime
     * @XmlIgnore
     * @deprecated  since 5.2.0, use handle() instead, will be removed with 6.0.0
     */
    public function getHandle()
    {
        return $this->handle();
    }

    /**
     * returns way to change the date to another
     *
     * @return  \stubbles\date\DateModifier
     * @XmlIgnore
     */
    public function change()
    {
        return new DateModifier($this);
    }

    /**
     * returns timestamp for this date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function timestamp()
    {
        return (int) $this->dateTime->format('U');
    }

    /**
     * returns timestamp for this date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use timestamp() instead, will be removed with 6.0.0
     */
    public function getTimestamp()
    {
        return $this->timestamp();
    }

    /**
     * returns seconds of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function seconds()
    {
        return (int) $this->dateTime->format('s');
    }

    /**
     * returns seconds of current date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use seconds() instead, will be removed with 6.0.0
     */
    public function getSeconds()
    {
        return $this->seconds();
    }

    /**
     * returns minutes of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function minutes()
    {
        return (int) $this->dateTime->format('i');
    }

    /**
     * returns minutes of current date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use minutes() instead, will be removed with 6.0.0
     */
    public function getMinutes()
    {
        return $this->minutes();
    }

    /**
     * returns hours of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function hours()
    {
        return (int) $this->dateTime->format('G');
    }

    /**
     * returns hours of current date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use hours() instead, will be removed with 6.0.0
     */
    public function getHours()
    {
        return $this->hours();
    }

    /**
     * returns day of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function day()
    {
        return (int) $this->dateTime->format('d');
    }

    /**
     * returns day of current date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use day() instead, will be removed with 6.0.0
     */
    public function getDay()
    {
        return $this->day();
    }

    /**
     * returns month of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function month()
    {
        return (int) $this->dateTime->format('m');
    }

    /**
     * returns month of current date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use month() instead, will be removed with 6.0.0
     */
    public function getMonth()
    {
        return $this->month();
    }

    /**
     * returns year of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function year()
    {
        return (int) $this->dateTime->format('Y');
    }

    /**
     * returns year of current date/time
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use year() instead, will be removed with 6.0.0
     */
    public function getYear()
    {
        return $this->year();
    }

    /**
     * returns offset to UTC in "+MMSS" notation
     *
     * @return  string
     * @XmlIgnore
     */
    public function offset()
    {
        return $this->dateTime->format('O');
    }

    /**
     * returns offset to UTC in "+MMSS" notation
     *
     * @return  string
     * @XmlIgnore
     * @deprecated  since 5.2.0, use offset() instead, will be removed with 6.0.0
     */
    public function getOffset()
    {
        return $this->offset();
    }

    /**
     * returns offset to UTC in seconds
     *
     * @return  int
     * @XmlIgnore
     */
    public function offsetInSeconds()
    {
        return (int) $this->dateTime->format('Z');
    }

    /**
     * returns offset to UTC in seconds
     *
     * @return  int
     * @XmlIgnore
     * @deprecated  since 5.2.0, use offsetInSeconds() instead, will be removed with 6.0.0
     */
    public function getOffsetInSeconds()
    {
        return $this->offsetInSeconds();
    }

    /**
     * checks whether this date is before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     */
    public function isBefore($date)
    {
        return $this->timestamp() < self::castFrom($date, 'date')->timestamp();
    }

    /**
     * checks whether this date is after a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     */
    public function isAfter($date)
    {
        return $this->timestamp() > self::castFrom($date, 'date')->timestamp();
    }

    /**
     * returns time zone of this date
     *
     * @return  \stubbles\date\TimeZone
     * @XmlIgnore
     */
    public function timeZone()
    {
        return new TimeZone($this->dateTime->getTimezone());
    }

    /**
     * returns time zone of this date
     *
     * @return  \stubbles\date\TimeZone
     * @XmlIgnore
     * @deprecated  since 5.2.0, use timeZone() instead, will be removed with 6.0.0
     */
    public function getTimeZone()
    {
        return $this->timeZone();
    }

    /**
     * returns date as string
     *
     * @return  string
     * @XmlAttribute(attributeName='value')
     */
    public function asString()
    {
        return $this->format('Y-m-d H:i:sO');
    }

    /**
     * returns formatted date/time string
     *
     * @param   string                   $format    format, see http://php.net/date
     * @param   \stubbles\date\TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     */
    public function format($format, TimeZone $timeZone = null)
    {
        if (null !== $timeZone) {
            return $timeZone->translate($this)->format($format);
        }

        return $this->dateTime->format($format);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($this->timestamp() === $compare->timestamp());
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->asString();
    }

    /**
     * make sure handle is cloned as well
     */
    public function __clone()
    {
        $this->dateTime = clone $this->dateTime;
    }
}
