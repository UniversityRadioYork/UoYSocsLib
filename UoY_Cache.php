<?php

require_once 'UoY_Config.php';

class UoY_Cache 
{
    /**
     * Bootloads the system by copying a trusted 
     * 
     * @return boolean true if the bootload was successful; false otherwise.
     */
    protected static function bootloadCache()
    {
        $dest = UoY_Config::localFile();
        if (!file_exists(UoY_Config::CACHE_LOCAL_DIR)) {
            $res = mkdir(UoY_Config::CACHE_LOCAL_DIR, 0770, true);
            if (!$res) {
                return false;
            }
        }
        $res = touch($dest, 0);
        if (!$res) {
            return false; //local file doesn't exist
        }
        return copy(UoY_Config::CACHE_BOOTLOADER, $dest);
    }

    /**
     * Gets a list of the years in the given term date set.
     * 
     * @param object $xml The SimpleXMLElement object representing the term date set.
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
     * @param object $xml The SimpleXMLElement object representing the term date set.
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
     * @param object  $src  The source SimpleXMLElement object. (?)
     * @param integer $year The year to copy over.
     * @param object  $dest The destination SimpleXMLElement object. (?) 
     * 
     * @return mixed Nothing.
     */
    protected static function copyYearData($src, $year, $dest)
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
     * @param object $xml The SimpleXMLElement object representing the term date set.
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
     * @param object $xml The SimpleXMLElement object representing the term date set.
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
    
    public static function getYearResource($xml, $year)
    {
        return $xml->xpath("/uoytermdates/termdates[year=$year]");
    }

    /**
     * Changes the update time of the cache.
     *
     * @param integer $time The new update time of the cache.
     * 
     * @param object  $xml  The SimpleXMLElement object of the cache.
     * 
     * @return mixed Nothing.
     */
    protected static function setUpdatedTime($time, $xml)
    {
        //MAYBE rename to set_updated_time
        $xml->updated[0] = @date('Y-m-d\TH:i:sP', $time);
    }

    /**
     * Writes to the cache file.
     * 
     * @param object $tmpxml The cache's SimpleXMLElement object.
     * 
     * @return boolean true if the cache was written to successfully. (?)  
     */
    protected static function write($tmpxml)
    {
        return file_put_contents(
            UoY_Config::localFile(), $tmpxml->asXML()
        );
    }

    /**
     * Adds a source to the cache.
     * 
     * @param type $trust  ?
     * @param object $tmpxml The cache's SimpleXMLElement object.
     * 
     * @return mixed Nothing.
     */
    protected static function addSource($url, $trust, $tmpxml)
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
    public static function cacheExists()
    {
        if (!file_exists(UoY_Config::localFile())) {
            return self::bootloadCache();
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
        
        if (!self::cacheExists()) {
            return false; //cache file missing and can't be made
        }

        $tmpxml = simplexml_load_file(UoY_Config::localFile());

        $sources = self::getTrustedSources($tmpxml);
        $lastupdate = self::getUpdatedTime($tmpxml);
        $localyears = self::getYears($tmpxml);

        $sourceslist = $sources;
        $updated = false;
        foreach ($sources as $f) {
            if ($f != UoY_Config::urlFile()) {
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
                        self::addSource($s, false, $tmpxml);
                        $sourcelist[] = $s;
                    }
                    //update termdates
                    $yearremote = self::getYears($xml);
                    $yearstoupdate = array_diff($yearremote, $localyears);
                    foreach ($yearstoupdate as $year) {
                        self::copyYearData($xml, $year, $tmpxml);
                        $yearlocal[] = $year;
                    }
                    //update timestamp
                    if (count($yearstoupdate) != 0) {
                        self::setUpdatedTime($utime, $tmpxml);
                        $lastupdate = $utime;
                        $updated = true;
                    }
                }
            }
        }
        if ($updated) {
            return self::write($tmpxml);
        }
        return true;
    }
    
    public static function cacheHandle()
    {
        return simplexml_load_file(UoY_Config::localFile());
    }

}

?>
