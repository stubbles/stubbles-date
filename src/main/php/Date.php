<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * @var  \DateTime
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
            $date = date_create('@' . $dateTime, timezone_open('UTC'));
            if (false === $date) {
                throw new \InvalidArgumentException('Can not create date from timestamp ' . (string) $dateTime);
            }

            $this->dateTime = $date;
            date_timezone_set(
                $this->dateTime,
                (null === $timeZone) ? (new \DateTimeZone(date_default_timezone_get())) : ($timeZone->handle())
            );
        } elseif (is_string($dateTime)) {
            try {
                if (null === $timeZone) {
                    $this->dateTime = new \DateTime($dateTime);
                } else {
                    $this->dateTime = new \DateTime($dateTime, $timeZone->handle());
                }
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                        'Given datetime string ' . $dateTime
                        . ' is not a valid date string: ' . $e->getMessage(),
                        $e->getCode(),
                        $e
                );
            }
        } else {
            $this->dateTime = $dateTime;
        }

        if (!($this->dateTime instanceof \DateTime)) {
            throw new \InvalidArgumentException(
                    'Datetime must be either unix timestamp, well-formed timestamp'
                    . ' or instance of \DateTime, but was ' . gettype($dateTime)
            );
        }
    }

    /**
     * returns current date/time
     *
     * @param   \stubbles\date\TimeZone  $timeZone  initial timezone
     * @return  \stubbles\date\Date
     */
    public static function now(TimeZone $timeZone = null): self
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
    public static function castFrom($value, string $name = 'Date'): self
    {
        if (is_int($value) || is_string($value) || $value instanceof \DateTime) {
            return new self($value);
        }

        if (!($value instanceof Date)) {
            throw new \InvalidArgumentException(
                    $name . ' must be a timestamp, a string containing time info'
                    . ' or an instance of \DateTime or ' . __CLASS__
                    . ', but was ' . gettype($value)
            );
        }

        return $value;
    }

    /**
     * returns internal date/time handle
     *
     * @return  \DateTime
     * @XmlIgnore
     */
    public function handle(): \DateTime
    {
        return clone $this->dateTime;
    }

    /**
     * returns way to change the date to another
     *
     * @return  \stubbles\date\DateModifier
     * @XmlIgnore
     */
    public function change(): DateModifier
    {
        return new DateModifier($this);
    }

    /**
     * returns timestamp for this date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function timestamp(): int
    {
        return (int) $this->dateTime->format('U');
    }

    /**
     * returns seconds of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function seconds(): int
    {
        return (int) $this->dateTime->format('s');
    }

    /**
     * returns minutes of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function minutes(): int
    {
        return (int) $this->dateTime->format('i');
    }

    /**
     * returns hours of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function hours(): int
    {
        return (int) $this->dateTime->format('G');
    }

    /**
     * returns day of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function day(): int
    {
        return (int) $this->dateTime->format('d');
    }

    /**
     * returns month of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function month(): int
    {
        return (int) $this->dateTime->format('m');
    }

    /**
     * returns year of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function year(): int
    {
        return (int) $this->dateTime->format('Y');
    }

    /**
     * returns offset to UTC in "+MMSS" notation
     *
     * @return  string
     * @XmlIgnore
     */
    public function offset(): string
    {
        return $this->dateTime->format('O');
    }

    /**
     * returns offset to UTC in seconds
     *
     * @return  int
     * @XmlIgnore
     */
    public function offsetInSeconds(): int
    {
        return (int) $this->dateTime->format('Z');
    }

    /**
     * checks whether this date is before a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     */
    public function isBefore($date): bool
    {
        return $this->timestamp() < self::castFrom($date, 'date')->timestamp();
    }

    /**
     * checks whether this date is after a given date
     *
     * @param   int|string|\DateTime|\stubbles\date\Date  $date
     * @return  bool
     */
    public function isAfter($date): bool
    {
        return $this->timestamp() > self::castFrom($date, 'date')->timestamp();
    }

    /**
     * returns time zone of this date
     *
     * @return  \stubbles\date\TimeZone
     * @XmlIgnore
     */
    public function timeZone(): TimeZone
    {
        return new TimeZone($this->dateTime->getTimezone());
    }

    /**
     * returns date as string
     *
     * @return  string
     * @XmlAttribute(attributeName='value')
     */
    public function asString(): string
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
    public function format(string $format, TimeZone $timeZone = null): string
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
    public function equals($compare): bool
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
    public function __toString(): string
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
