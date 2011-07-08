<?php
class UoY_Config 
{
  // Cache file //
  const CACHE_FILE_NAME = 'UoYCache.xml';
  const CACHE_URL_DIR = 'localhost';
  const CACHE_LOCAL_DIR = 'xml';
  const CACHE_BOOTLOADER = 'http://ury.york.ac.uk/xml/uoy-term-dates.xml';

  public static function localFile() 
  {
		return dirname(__FILE__).'/'.self::CACHE_LOCAL_DIR .'/'. self::CACHE_FILE_NAME;
  }

  public static function urlFile()
  {
    return "http://" . self::CACHE_URL_DIR . '/' . self::CACHE_FILE_NAME;
  }
}
?>
