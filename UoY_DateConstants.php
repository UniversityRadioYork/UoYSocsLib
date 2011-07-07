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
 * @link     github.com/UniversityRadioYork/University-of-York-Society-Common-Library
 */

/**
 * Constants for the date subsystem.
 * 
 * @category UoY
 * @package  UoY
 * 
 * @author   Matt Windsor <mattwindsor@btinternet.com>
 * 
 * @license  ? ?
 * @link     https://github.com/UniversityRadioYork/UoYSocsLib
 */
class UoY_DateConstants
{
    // TERMS //
    
    /** Identifier for Autumn term. */
    const TERM_AUTUMN = 1;
    /** Name for Autumn term. */
    const NAME_TERM_AUTUMN = 'Autumn Term';
    
    /** Identifier for Spring term. */
    const TERM_SPRING = 2;
    /** Name for Spring term. */
    const NAME_TERM_SPRING = 'Spring Term';
    
    /** Identifier for Summer term. */
    const TERM_SUMMER = 3;
    /** Name for Summer term. */
    const NAME_TERM_SUMMER = 'Summer Term';

    /** 
     * Lowest valid term identifier.  
     * 
     * Any number below this is not a valid term identifier.
     */
    const TERM_LOWER_BOUND = self::TERM_AUTUMN;
  
    /**
     * Highest valid term identifier.
     * 
     * Any number above this is not a valid term identifier.
     */
    const TERM_UPPER_BOUND = self::TERM_SUMMER;
  
    
    // BREAKS //
    
    /** Identifier for Winter break. */
    const BREAK_WINTER = 1;
    /** Name for Winter break. */
    const NAME_BREAK_WINTER = 'Winter Break';
    
    /** Identifier for Spring break. */
    const BREAK_SPRING = 2;
    /** Name for Spring break. */
    const NAME_BREAK_SPRING = 'Spring Break';
    
    /** Identifier for Summer break. */
    const BREAK_SUMMER = 3;
    /** Name for Summer break. */
    const NAME_BREAK_SUMMER = 'Summer Break';
    
    /** 
     * Lowest valid break identifier.  
     * 
     * Any number below this is not a valid break identifier.
     */
    const BREAK_LOWER_BOUND = self::BREAK_WINTER;
  
    /**
     * Highest valid term identifier.
     * 
     * Any number above this is not a valid break identifier.
     */
    const BREAK_UPPER_BOUND = self::BREAK_SUMMER;

    
    // DAYS //
    
    /** Identifier for Nonday. */
    const DAY_MONDAY = 1;
    /** Name for Monday. */
    const NAME_DAY_MONDAY = 'Monday';
    
    /** Identifier for Tuesday. */
    const DAY_TUESDAY = 2;
    /** Name for Tuesday. */
    const NAME_DAY_TUESDAY = 'Tuesday';
    
    /** Identifier for Wednesday. */
    const DAY_WEDNESDAY = 3;
    /** Name for Wednesday. */
    const NAME_DAY_WEDNESDAY = 'Wednesday';
    
    /** Identifier for Thursday. */
    const DAY_THURSDAY = 4;
    /** Name for Thursday. */
    const NAME_DAY_THURSDAY = 'Thursday';
    
    /** Identifier for Friday. */
    const DAY_FRIDAY = 5;
    /** Name for Friday. */
    const NAME_DAY_FRIDAY = 'Friday';
    
    /** Identifier for Saturday. */
    const DAY_SATURDAY = 6;
    /** Name for Saturday. */
    const NAME_DAY_SATURDAY = 'Saturday';
    
    /** Identifier for Sunday. */
    const DAY_SUNDAY = 7;
    /** Name for Sunday. */
    const NAME_DAY_SUNDAY = 'Sunday';
    
    /** 
     * Lowest valid day identifier.  
     * 
     * Any number below this is not a valid day identifier.
     */
    const DAY_LOWER_BOUND = self::DAY_MONDAY;
  
    /**
     * Highest valid day identifier.
     * 
     * Any number above this is not a valid day identifier.
     */
    const DAY_UPPER_BOUND = self::DAY_SUNDAY;
}

?>
