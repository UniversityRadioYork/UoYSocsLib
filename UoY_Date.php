<?php
/**
 * UoY_Date class
 * 
 * Part of the University of York Society Common Library
 * 
 * PHP version 5.3
 * 
 * @category UoY
 * @package  UoY
 * 
 * @author   Gareth Andrew Lloyd <gareth@ignition-web.co.uk>
 * @author   Matt Windsor <mattwindsor@btinternet.com>
 * 
 * @license  ? ?
 * @link     https://github.com/UniversityRadioYork/UoYSocsLib
 */

require_once 'UoY_DateConstants.php';

/**
 * University date object.
 * 
 * @category UoY
 * @package  UoY
 * 
 * @author   Matt Windsor <mattwindsor@btinternet.com>
 * 
 * @license  ? ?
 * @link     github.com/UniversityRadioYork/University-of-York-Society-Common-Library
 */
class UoY_Date
{
    protected $year;
    protected $term;
    protected $isBreak;
    protected $week;
    protected $day;
    
    /**
     * Constructs a new date.
     * 
     * @param integer $year    The year of the date.
     * @param integer $term    The term or break of the date.
     * @param boolean $isBreak Whether or not the date belongs to a break
     *                         instead of a term.
     * @param integer $week    The week of the term or break.
     * @param integer $day     The day of the week.
     * 
     * @throws InvalidArgumentException if the types are incorrect.
     * @throws OutOfBoundsException     if any variable is outside its 
     *                                  expected range.
     */
    public function __construct($year, $term, $isBreak, $week, $day)
    {
        // Type checks
        if (is_integer($year) === false) {
            throw new InvalidArgumentException('Year must be an integer.');
        } else if (is_integer($term) === false) {
            throw new InvalidArgumentException('Term must be an integer.');
        } else if (is_integer($week) === false) {
            throw new InvalidArgumentException('Week must be an integer.');
        } else if (is_integer($day) === false) {
            throw new InvalidArgumentException('Day must be an integer.');
        } else if (is_bool($isBreak) === false) {
            throw new InvalidArgumentException('isBreak must be an boolean.');
        }
     
        // Range checks
        if ($isBreak) {
            if ($term < UoY_DateConstants::BREAK_LOWER_BOUND) {
                throw new OutOfBoundsException('Break ID is too low.');
            } else if ($term > UoY_DateConstants::BREAK_UPPER_BOUND) {
                throw new OutOfBoundsException('Break ID is too high.');
            }
        } else {
            if ($term < UoY_DateConstants::TERM_LOWER_BOUND) {
                throw new OutOfBoundsException('Term ID is too low.');
            } else if ($term > UoY_DateConstants::TERM_UPPER_BOUND) {
                throw new OutOfBoundsException('Term ID is too high.');
            }
        }
        if ($week < 1) {
            throw new OutOfBoundsException('Week must be positive.');
        }
        if ($day < UoY_DateConstants::DAY_LOWER_BOUND) {
            throw new OutOfBoundsException('Day ID is too low.');
        } else if ($day > UoY_DateConstants::DAY_UPPER_BOUND) {
            throw new OutOfBoundsException('Day ID is too high.');
        }
        
        $this->year = $year;
        $this->term = $term;
        $this->isBreak = $isBreak;
        $this->week = $week;
        $this->day = $day;
    }
    
    /**
     * Gets the year.
     * 
     * The year is the calendar year on which Monday, Week 1 Autumn falls
     * (ie, the first of the two years in 'Year xx/yy'; for Year 2010/11, this
     * would return 2010).
     * 
     * @return integer The year.
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Gets the term (or break).
     * 
     * @return integer The term (or break).
     */
    public function getTerm()
    {
        return $this->term;
    }
    
    /**
     * Gets the term (or break) name.
     * 
     * @return string The term (or break) name.
     */
    public function getTermName()
    {
        if ($this->isInBreak()) {
            switch ($this->term) {
            case UoY_DateConstants::BREAK_WINTER:
                return UoY_DateConstants::NAME_BREAK_WINTER;
            case UoY_DateConstants::BREAK_SPRING:
                return UoY_DateConstants::NAME_BREAK_SPRING;
            case UoY_DateConstants::BREAK_SUMMER:
                return UoY_DateConstants::NAME_BREAK_SUMMER;
            default:
                throw new LogicException('Invalid term stored in date.');
            }
        } else {
            switch ($this->term) {
            case UoY_DateConstants::TERM_AUTUMN:
                return UoY_DateConstants::NAME_TERM_AUTUMN;
            case UoY_DateConstants::TERM_SPRING:
                return UoY_DateConstants::NAME_TERM_SPRING;
            case UoY_DateConstants::TERM_SUMMER:
                return UoY_DateConstants::NAME_TERM_SUMMER;
            default:
                throw new LogicException('Invalid term stored in date.');
            }
        }
    }

    /**
     * Gets whether or not the date is in a break (vacation).
     * 
     * If the date is in a break, the term returned is a constant in the 
     * UoY_DateConstants::BREAK_xxx series.
     * 
     * @return boolean true if the date refers to a break (and thus the week
     *                 value refers to weeks after the end of the term); false
     *                 otherwise.
     */
    public function isInBreak()
    {
        return $this->isBreak;
    }
    
    /**
     * Gets the week.
     * 
     * @return integer The week.
     */
    public function getWeek()
    {
        return $this->week;
    }
    
    /**
     * Gets the day.
     * 
     * @return integer The day, which is a value in the range 
     *                 UoY_DateConstants::DAY_MONDAY through 
     *                 UoY_DateConstants::DAY_SUNDAY inclusive.
     */
    public function getDay()
    {
        return $this->day;
    }
    
    /**
     * Gets the day name.
     * 
     * @return string The day name.
     */
    public function getDayName()
    {
        switch ($this->day) {
        case UoY_DateConstants::DAY_MONDAY:
            return 'Monday';
        case UoY_DateConstants::DAY_TUESDAY:
            return 'Tuesday';
        case UoY_DateConstants::DAY_WEDNESDAY:
            return 'Wednesday';
        case UoY_DateConstants::DAY_THURSDAY:
            return 'Thursday';
        case UoY_DateConstants::DAY_FRIDAY:
            return 'Friday';
        case UoY_DateConstants::DAY_SATURDAY:
            return 'Saturday';
        case UoY_DateConstants::DAY_SUNDAY:
            return 'Sunday';
        default:
            throw new LogicException('Invalid day stored in date.');
        }
    }
    
    /**
     * Gets a string representation of the date.
     * 
     * @return string The string representation, which is always of the format
     * '{DAY NAME} Week {WEEK}, {TERM NAME} {YEAR}/{YEAR + 1}'.
     */
    public function toString()
    {
        return ($this->getDayName()
                . ' Week ' . $this->getWeek()
                . ', ' . $this->getTermName()
                . ' ' . $this->getYear() 
                . '/' . (($this->getYear() + 1) % 100)
                );
    }
}
?>
