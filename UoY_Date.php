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
require_once 'UoY_Cache.php';

date_default_timezone_set('Europe/London');

/**
 * University date object.
 * 
 * @category UoY
 * @package  UoY
 * 
 * @author   Matt Windsor <mattwindsor@btinternet.com>
 * 
 * @license  ? ?
 * @link     https://github.com/UniversityRadioYork/UoYSocsLib
 */
class UoY_Date extends DateTime
{

    protected $term;
    protected $isBreak;
    protected $week;
    protected $lastEpoch;
    
    /*
        // Type checks
        if (is_integer($year) === false) {
            throw new InvalidArgumentException('Year must be an integer.');
        } else if (is_integer($term) === false) {
            throw new InvalidArgumentException('Term must be an integer.');
        } else if (is_integer($week) === false) {
            throw new InvalidArgumentException('Week must be an integer.');
        //} else if (is_integer($day) === false) {
        //    throw new InvalidArgumentException('Day must be an integer.');
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
        //if ($day < UoY_DateConstants::DAY_LOWER_BOUND) {
        //    throw new OutOfBoundsException('Day ID is too low.');
        //} else if ($day > UoY_DateConstants::DAY_UPPER_BOUND) {
        //    throw new OutOfBoundsException('Day ID is too high.');
        //}
    */
    
    /**
     * Returns the academic year of the given date.
     * 
     * @param integer $date The date, as a Unix timestamp.
     * 
     * @return integer The academic year of the given date, as defined as the
     *                 calendar year upon which Monday Week 1 Autumn falls.
     */
    public function getYear()
    {
        // assumption 01-Sept is the earliest academic year start
        return @date("Y", $this->getTimestamp() - @strtotime("1st September 1970"));
    }

    /**
     * Floors the given date string to the previous Monday. (?)
     * 
     * @param string $datestr A string representing the date.
     * 
     * @return integer A Unix timestamp representing the floored date. 
     */
    protected static function floorMonday($datestr)
    {
        $prevMon = @strtotime("last Monday" . $datestr);
        $m1week = @strtotime($datestr . " -1 week");
        if ($prevMon == $m1week) {
            return @strtotime($datestr);
        } else {
            return $prevMon;
        }
    }

    protected function update()
    {
        $currentEpoch = $this->getTimestamp();
        if ($this->lastEpoch != $currentEpoch)
        {
            $date = $currentEpoch;
            $year = $this->getYear();
            if (!UoY_Cache::yearExists($year, true)) {
                return false;
            }
            $tmpxml = UoY_Cache::cacheHandle();
            $xmlRes = UoY_Cache::getYearResource($tmpxml,$year);
            $feature[] = @strtotime("1st September $year");//inclusive
            $feature[] = @strtotime("1st September " . ($year + 1));//exclusive
            foreach ($xmlRes[0]->term as $t) {
                $feature[] = self::floorMonday($t->start);//inclusive
                $feature[] = @strtotime("next Monday ".($t->end));//exclusive
            }
            sort($feature, SORT_NUMERIC);
            //TODO rename to ??? $term isn't correct
            $term = 0;
            for ($i = 0; $i < count($feature) - 1; $i = $i + 1) {
                if (($date >= $feature[$i]) && ($date < $feature[$i + 1])) {
                    $term = $i;
                    break;
                }
            }
            //0 - $year-1 summer break
            //1 - term 1
            //2 - $year christmas break
            //3 - term 2
            //4 - $year easter break
            //5 - term 3
            //6 - $year summer break
            if ($term != 0) {
                $relativetoterm = $date - $feature[$term];
                $relativetoterm /= 60 * 60 * 24 * 7;
                $week = (int) $relativetoterm + 1;
            } else {
                $start = @strtotime("31st August " . $year);
                $weekdayoffset = @strtotime("last Monday", $start);
                $term_details = new UoY_Date;
                $term_details->setTimestamp($weekdayoffset);
                if (!$term_details->getWeek()) {
                    $week = false; //can't infer any information for the week number
                } else {
                    $relativetoterm = $date - $weekdayoffset;
                    $relativetoterm /= 60 * 60 * 24 * 7;
                    $week = (int) $relativetoterm + $term_details->getWeek();
                }
            }
            $weeknum = $week;
            $termnum = (($term % 2) == 1) ? ($term + 1) / 2 : 0;
            $breaknum = (($term % 2) == 0) ? ($term) / 2 : 0;
            if ($term == 0) {
                $breaknum = 3;
            }
            $yearnum = ($term != 0) ? $year : $year - 1;
            //update values
            $this->term = intval($termnum) === 0 ? intval($breaknum) : intval($termnum);
            $this->isBreak = (intval($termnum) === 0);
            if ($weeknum === false) {
                $this->week = false; 
            } else {
                $this->week = intval($weeknum);
            }
            $this->lastEpoch = $currentEpoch;
        }
        return true;
    }

    /**
     * Gets the term (or break).
     * 
     * @return integer The term (or break).
     */
    public function getTerm()
    {
        if (!$this->update()) return false;
        return $this->term;
    }
    
    /**
     * Gets the term (or break) name.
     * 
     * @return string The term (or break) name.
     */
    public function getTermName()
    {
        if (!$this->update()) return false;
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
				if (!$this->update()) return false;
        return $this->isBreak;
    }
    
    /**
     * Gets the week.
     * 
     * @return integer The week.
     */
    public function getWeek()
    {
        if (!$this->update()) return false;
        return $this->week; //maybe false if update couldn't work it out
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
        return intval(date('N', $this->getTimestamp()));
    }
    
    /**
     * Gets the day name.
     * 
     * @return string The day name.
     */
    public function getDayName()
    {
        switch ($this->getDay()) {
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
        if (!$this->update()) return false;
        $weekvalue = $this->getWeek();
        if ($weekvalue === false) $weekvalue = '??';
        return ($this->getDayName()
                . ' Week ' . $weekvalue
                . ', ' . $this->getTermName()
                . ' ' . $this->getYear() 
                . '/' . (($this->getYear() + 1) % 100)
                );
    }

    /**
     * Function used to test the date handler.
     * 
     * @return nothing.
     */
    public static function test()
    {
        $day = @strtotime("1st September 2010");
        $obj = new UoY_Date;
        $obj->setTimestamp($day);
        for ($i = 0; $i < 365*2; $i++) {
            echo $obj->format( "Y-m-d\n" );
            $val = $obj->toString();
            if ($val === false) {
                echo "not convertable using given data.\n";
            } else {
                echo $val . "\n";
            }
            $obj->modify( '+1 day' );
        }
    }
}
?>
