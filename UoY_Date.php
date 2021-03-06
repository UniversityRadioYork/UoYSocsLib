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
    protected $year;
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

		protected static function getTerms($year)
		{
    		if (!UoY_Cache::yearExists($year, true)) {
        		return false;
        }
        $tmpxml = UoY_Cache::cacheHandle();
        $xmlRes = UoY_Cache::getYearResource($tmpxml,$year);
        $feature[] = new UoY_Date("1st September $year");//inclusive
        $feature[] = new UoY_Date("1st September " . ($year + 1));//exclusive
        foreach ($xmlRes[0]->term as $t) {
            //inclusive
            $prevMon = new UoY_Date("last Monday " . ($t->start));
            $m1week = new UoY_Date(($t->start) . " -1 week");
            if ($prevMon == $m1week) {
              $feature[] = new UoY_Date($t->start);
            } else {
              $feature[] = $prevMon;
            }
            //exclusive
            $feature[] = new UoY_Date("next Monday " . ($t->end));
        }
        usort($feature, function($a,$b){
          if ($a->getTimestamp() == $b->getTimestamp()) 
          return 0; 
          return ($a->getTimestamp()) > ($b->getTimestamp()) ? +1 : -1;
        });
				return $feature;
		}
    
    protected function update()
    {
        if ($this->lastEpoch != $this->getTimestamp())
        {
        		// assumption 01-Sept is the earliest academic year start
        		//TODO convert to DateTime
        		$year = @date("Y", $this->getTimestamp() - @strtotime("1st September 1970"));
						$feature = self::getTerms($year);
						if (!$feature) return false;
            //TODO rename to ??? $term isn't correct
            $term = 0;
            for ($i = 0; $i < count($feature) - 1; $i = $i + 1) {
                if (($this >= $feature[$i]) && ($this < $feature[$i + 1])) {
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
                $relativetoterm = $this->diff($feature[$term]);
                $week = ((int) $relativetoterm->days / 7) + 1;
            } else {
                $weekdayoffset = new UoY_Date("31st August " . $year);
                $weekdayoffset->modify("last Monday");
                if (!$weekdayoffset->getWeek()) {
                    $week = false; //can't infer any information for the week number
                } else {
                    $relativetoterm = $this->diff($weekdayoffset);
                    $week = ((int) $relativetoterm->days / 7) + $weekdayoffset->getWeek();
                }
            }
            $weeknum = $week;
            $termnum = ($term == 0) ? 6 : $term;
            $yearnum = ($term != 0) ? $year : $year - 1;
            //update values
            $this->year = $yearnum;
            $this->term = intval($termnum);
            if ($weeknum === false) {
                $this->week = false; 
            } else {
                $this->week = intval($weeknum);
            }
            $this->lastEpoch = $this->getTimestamp();
        }
        return true;
    }

		public function setTermdate($ayear, $term, $week, $day)
		{
				$feature = self::getTerms($ayear);
				$date = $feature[$term];
				$date->modify("+".($week-1)." weeks");
				$date->modify("+".($day-1)." days");
				$this->setTimestamp($date->getTimestamp());
				//TODO check valid weeks, it shouldn't excede $feature[$term+1]
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
    
    public function getYear()	
    {
        if (!$this->update()) return false;
        return $this->year;
    }

    /**
     * Gets the term (or break) name.
     * 
     * @return string The term (or break) name.
     */
    public function getTermName()
    {
        if (!$this->update()) return false;
        switch ($this->term) {
          case UoY_DateConstants::BREAK_WINTER:
              return UoY_DateConstants::NAME_BREAK_WINTER;
          case UoY_DateConstants::BREAK_SPRING:
              return UoY_DateConstants::NAME_BREAK_SPRING;
          case UoY_DateConstants::BREAK_SUMMER:
              return UoY_DateConstants::NAME_BREAK_SUMMER;
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
        if (!$this->update()) 
            throw new Exception('Not enough information');
        return ($term % 2) == 0;
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
        //TODO convert to DateTime
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
