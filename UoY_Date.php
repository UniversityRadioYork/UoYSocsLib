<?php
/**
 * University date object.
 * 
 * @author Matt Windsor <mattwindsor@btinternet.com>
 */
class UoY_Date
{
    protected $_year;
    protected $_term;
    protected $_isBreak;
    protected $_week;
    protected $_day;
    
    /**
     * Constructs a new date.
     * 
     * @param integer $year    The year of the date.
     * @param integer $term    The term or break of the date.
     * @param boolean $isBreak Whether or not the date belongs to a break
     *                         instead of a term.
     * @param integer $week    The week of the term or break.
     * @param integer $day     The day of the week.
     */
    public function __construct($year, $term, $isBreak, $week, $day)
    {
        $this->_year = $year;
        $this->_term = $term;
        $this->_isBreak = $isBreak;
        $this->_week = $week;
        $this->_day = $day;
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
        return $this->_year;
    }

    /**
     * Gets the term (or break).
     * 
     * @return integer The term (or break).
     */
    public function getTerm()
    {
        return $this->_term;
    }
    
    /**
     * Gets the term (or break) name.
     * 
     * @return string The term (or break) name.
     */
    public function getTermName()
    {
        if ($this->isInBreak()) {
            switch ($this->_term) {
            case UoY_DateHandler::BREAK_WINTER:
                return 'Winter Break';
            case UoY_DateHandler::BREAK_SPRING:
                return 'Spring Break';
            case UoY_DateHandler::BREAK_SUMMER:
                return 'Summer Break';
            default:
                throw new LogicException('Invalid term stored in date.');
            }
        } else {
            switch ($this->_term) {
            case UoY_DateHandler::TERM_AUTUMN:
                return 'Autumn Term';
            case UoY_DateHandler::TERM_SPRING:
                return 'Spring Term';
            case UoY_DateHandler::TERM_SUMMER:
                return 'Summer Term';
            default:
                throw new LogicException('Invalid term stored in date.');
            }
        }
    }

    /**
     * Gets whether or not the date is in a break (vacation).
     * 
     * If the date is in a break, the term returned is a constant in the 
     * UoY_DateHandler::BREAK_xxx series.
     * 
     * @return boolean true if the date refers to a break (and thus the week
     *                 value refers to weeks after the end of the term); false
     *                 otherwise.
     */
    public function isInBreak()
    {
        return $this->_isBreak;
    }
    
    /**
     * Gets the week.
     * 
     * @return integer The week.
     */
    public function getWeek()
    {
        return $this->_week;
    }
    
    /**
     * Gets the day.
     * 
     * @return integer The day, which is a value in the range 
     *                 UoY_DateHandler::DAY_MONDAY through 
     *                 UoY_DateHandler::DAY_SUNDAY inclusive.
     */
    public function getDay()
    {
        return $this->_day;
    }
    
    /**
     * Gets the day name.
     * 
     * @return string The day name.
     */
    public function getDayName()
    {
        switch ($this->_day) {
        case UoY_DateHandler::DAY_MONDAY:
            return 'Monday';
        case UoY_DateHandler::DAY_TUESDAY:
            return 'Tuesday';
        case UoY_DateHandler::DAY_WEDNESDAY:
            return 'Wednesday';
        case UoY_DateHandler::DAY_THURSDAY:
            return 'Thursday';
        case UoY_DateHandler::DAY_FRIDAY:
            return 'Friday';
        case UoY_DateHandler::DAY_SATURDAY:
            return 'Saturday';
        case UoY_DateHandler::DAY_SUNDAY:
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
