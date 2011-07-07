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

require_once 'UoY_Date.php';

date_default_timezone_set('Europe/London');

/**
 * Class for handling University of York term dates.
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
class UoY_DateHandler
{
    // Term identifiers
    /** Autumn term. */
    const TERM_AUTUMN = 1;
    /** Spring term. */
    const TERM_SPRING = 2;
    /** Summer term. */
    const TERM_SUMMER = 3;

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
  
    // Break identifiers
    /** Winter break. */
    const BREAK_WINTER = 1;
    /** Spring break. */
    const BREAK_SPRING = 2;
    /** Summer break. */
    const BREAK_SUMMER = 3;
    
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

    // Day identifiers
    /** Monday. */
    const DAY_MONDAY = 1;
    /** Tuesday. */
    const DAY_TUESDAY = 2;
    /** Wednesday. */
    const DAY_WEDNESDAY = 3;
    /** Thursday. */
    const DAY_THURSDAY = 4;
    /** Friday. */
    const DAY_FRIDAY = 5;
    /** Saturday. */
    const DAY_SATURDAY = 6;
    /** Sunday. */
    const DAY_SUNDAY = 7;
    
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
    
    //variables to change
    protected static $file = 'uoy-term-dates.xml';
    protected static $url = 'localhost';
    protected static $localdir = 'xml';
    protected static $bootloader = 'http://ury.york.ac.uk/xml/uoy-term-dates.xml';

    /**
     * Constructs a new UoY_DateHandler.
     * 
     * TODO: This is a static class, so this should be moved somewhere else.
     */
    public function __construct()
    {
        if (!defined('STDIN')) {
            self::$localdir = $_SERVER['DOCUMENT_ROOT'] . '/xml';
        }
    }

    /**
     * Bootloads the system by copying a trusted 
     * 
     * @return boolean true if the bootload was successful; false otherwise.
     */
    protected static function bootloadFile()
    {
        $dest = self::$localdir . '/' . self::$file;
        if (!file_exists(self::$localdir)) {
            $res = mkdir(self::$localdir, 0770, true);
            if (!$res) {
                return false;
            }
        }
        $res = touch($dest, 0);
        if (!$res) {
            return false; //local file doesn't exist
        }
        return copy(self::$bootloader, $dest);
    }

    /**
     * Gets a list of the years in the given term date set.
     * 
     * @param object $xml The XML object representing the term date set.
     * 
     * @return array A sorted list of years.
     */
    protected static function getYears($xml)
    {
        $years = $xml->xpath('/uoytermdates/termdates/year');
        $res = array();
        foreach ($years as $y) {
            $res[] = (integer) $y;
        }
        sort($res, SORT_NUMERIC);
        return $res;
    }

    /**
     * Gets the time at which the term date set being used was last updated.
     * 
     * @param object $xml The XML object representing the term date set.
     * 
     * @return integer The update time, as a Unix timestamp. (?)
     */
    protected static function getUpdatedTime($xml)
    {
        $res = $xml->xpath('/uoytermdates/updated[1]');
        return @strtotime($res[0]);
    }

    /**
     * Adds a year from $src to $dest. (?)
     * 
     * @param object  $src  The source XML object. (?)
     * @param integer $year The year to copy over.
     * @param object  $dest The destination XML object. (?) 
     * 
     * @return mixed Nothing.
     */
    protected static function addYearToCache($src, $year, $dest)
    {
        //TODO add validation
        //MAYBE rename to copy_year_data
        $res = $src->xpath("/uoytermdates/termdates[year=$year]");
        $data = dom_import_simplexml($res[0]);
        $dom = dom_import_simplexml($dest);
        $dom = $dom->ownerDocument;
        $node = $dom->importNode($data, true);
        $dom->documentElement->appendChild($node);
        $dest = simplexml_import_dom($dom);
    }

    /**
     * Gets a list of trusted sources from the term date set. (?)
     * 
     * @param object $xml The XML object representing the term date set.
     * 
     * @return array A list of trusted sources. (?)
     */
    protected static function getTrustedSources($xml)
    {
        $res = $xml->xpath('/uoytermdates/source[trusted="yes"]/url');
        $result = array();
        foreach ($res as $r) {
            $result[] = $r;
        }
        return $result;
    }

    /**
     * Gets a list of sources from the term date set. (?)
     * 
     * @param object $xml The XML object representing the term date set.
     * 
     * @return array A list of all sources. (?)
     */
    protected static function getSources($xml)
    {
        $res = $xml->xpath('/uoytermdates/source/url');
        $result = array();
        foreach ($res as $r) {
            $result[] = $r;
        }
        return $result;
    }

    /**
     * Changes the update time of the cache.
     *
     * @param integer $time The new update time of the cache.
     * 
     * @param object  $xml  The XML object of the cache.
     * 
     * @return mixed Nothing.
     */
    protected static function changeUpdateTimeOfCache($time, $xml)
    {
        //MAYBE rename to set_updated_time
        $xml->updated[0] = @date('Y-m-d\TH:i:sP', $time);
    }

    /**
     * Writes to the cache.
     * 
     * @param object $tmpxml The cache's XML object.
     * 
     * @return boolean true if the cache was written to successfully. (?)  
     */
    protected static function writeToCache($tmpxml)
    {
        return file_put_contents(
            self::$localdir . '/' . self::$file, $tmpxml->asXML()
        );
    }

    /**
     * Adds a source to the cache.
     * 
     * @param type $trust  ?
     * @param type $tmpxml ?
     * 
     * @return mixed Nothing.
     */
    protected static function addSourceToCache($trust, $tmpxml)
    {
        //TODO
    }

    /**
     * Checks to see whether or not the cache exists.
     * 
     * If the cache does not exist, the function will try to create it. (?)
     * 
     * @return boolean True if the cache exists; false otherwise.
     */
    protected static function cacheExists()
    {
        $file = self::$file;
        $localdir = self::$localdir;
        if (!file_exists("$localdir/$file")) {
            return self::bootloadFile();
        }
        return true;
    }

    /**
     * Updates the cache.
     * 
     * @return boolean True if the cache was updated. (?)
     */
    public static function updateCache()
    {
        $url = self::$url;
        $file = self::$file;
        $localdir = self::$localdir;
        
        if (!self::cacheExists()) {
            return false; //cache file missing and can't be made
        }

        $tmpxml = simplexml_load_file("$localdir/$file");

        $sources = self::getTrustedSources($tmpxml);
        $lastupdate = self::getUpdatedTime($tmpxml);
        $localyears = self::getYears($tmpxml);

        $sourceslist = $sources;
        $updated = false;
        foreach ($sources as $f) {
            if ($f != "http://$url/$file") {
                $xml = @simplexml_load_file($f);
                if (!$xml) {
                    break; //remote file doesn't exist
                }
                $utime = self::getUpdatedTime($xml);
                //find newer version
                if ($lastupdate < $utime) {
                    //update sources
                    $sourcesremote = self::getSources($xml);
                    $sourcestoupdate = array_diff($sourcesremote, $sourceslist);
                    foreach ($sourcestoupdate as $s) {
                        self::addSourceToCache($s, false, $tmpxml);
                        $sourcelist[] = $s;
                    }
                    //update termdates
                    $yearremote = self::getYears($xml);
                    $yearstoupdate = array_diff($yearremote, $localyears);
                    foreach ($yearstoupdate as $year) {
                        self::addYearToCache($xml, $year, $tmpxml);
                        $yearlocal[] = $year;
                    }
                    //update timestamp
                    if (count($yearstoupdate) != 0) {
                        self::changeUpdateTimeOfCache($utime, $tmpxml);
                        $lastupdate = $utime;
                        $updated = true;
                    }
                }
            }
        }
        if ($updated) {
            return self::writeToCache($tmpxml);
        }
        return true;
    }

    /**
     * Checks whether or not the given academic year exists in the system.
     * 
     * Data for the year existing in the system is a necessary prerequisite
     * for the 
     * 
     * @param integer $year   The year to look up.
     * @param boolean $update If true, the system will update itself. (?)
     * 
     * @return boolean Whether or not the year exists in the system.
     */
    public static function yearExists($year, $update = false)
    {
        if (!self::cacheExists()) {
            return false; //cache file missing and can't be made
        }
        $ld = self::$localdir;
        $file = self::$file;
        $tmpxml = simplexml_load_file("$ld/$file");
        $res = $tmpxml->xpath("/uoytermdates/termdates[year=$year]");
        if (($res == array()) && $update) {
            self::updateCache();
            $res = $tmpxml->xpath("/uoytermdates/termdates[year=$year]");
        }
        return $res != array(); //no year exist in xml even after update
    }

    /**
     * Returns the academic year of the given date.
     * 
     * @param integer $date The date, as a Unix timestamp.
     * 
     * @return integer The academic year of the given date, as defined as the
     *                 calendar year upon which Monday Week 1 Autumn falls.
     */
    public static function yearNumber($date)
    {
        // assumption 01-Sept is the earliest academic year start
        return @date("Y", $date - @strtotime("1st September 1970"));
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

    /**
     * Converts a date in Unix timestamp format to the format used by the
     * University of York.
     * 
     * @param integer $date The date to convert, as a Unix timestamp.
     * 
     * @return UoY_Date The date, in University of York date format. 
     */
    public static function termInfo($date)
    {
        $ld = self::$localdir;
        $file = self::$file;
        $year = self::yearNumber($date);
        if (!self::yearExists($year, true)) {
            return false;
        }
        $tmpxml = simplexml_load_file("$ld/$file");
        $res = $tmpxml->xpath("/uoytermdates/termdates[year=$year]");
        $feature[] = @strtotime("1st September $year");//inclusive
        $feature[] = @strtotime("1st September " . ($year + 1));//exclusive
        foreach ($res[0]->term as $t) {
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
            $term_details = self::termInfo($weekdayoffset);
            if (!$term_details) {
                return false; //can't infer any information for the week number
            }
            $relativetoterm = $date - $weekdayoffset;
            $relativetoterm /= 60 * 60 * 24 * 7;
            $week = (int) $relativetoterm + $term_details->getWeek();
        }
        $result['weeknum'] = $week;
        $result['termnum'] = (($term % 2) == 1) ? ($term + 1) / 2 : 0;
        $result['breaknum'] = (($term % 2) == 0) ? ($term) / 2 : 0;
        if ($term == 0) {
            $result['breaknum'] = 3;
        }
        $result['yearnum'] = ($term != 0) ? $year : $year - 1;

        return new UoY_Date(
            $result['yearnum'],
            $result['termnum'] === 0 ? $result['breaknum'] : $result['termnum'],
            ($result['termnum'] === 0), // Whether or not this is a break
            $result['weeknum'],
            intval(date('N', $date)) // Day
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
        for ($i = 0; $i < 365*2; $i++) {
            echo @date("Y-m-d", $day) . "\n";
            if (self::termInfo($day) === false) {
                echo "not convertable using given data.\n";
            } else {
                echo self::termInfo($day)->toString() . "\n";
            }
            $day = @strtotime(@date("Y-m-d", $day) . " +1 day");
        }
    }

}

?>
