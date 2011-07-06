<?php

date_default_timezone_set('Europe/London');

//variables to change
$file = 'uoy-term-dates.xml';
$url = 'localhost';
$localdir = '/var/www/html';
$bootloader = 'http://ury.york.ac.uk/xml/uoy-term-dates.xml';

function bootload_file() {
  global $localdir, $file, $bootloader; 
  $dest = $localdir.'/'.$file;
  $res = touch ($dest, 0);
  if (!$res) {
    return false; //local file doesn't exist
  }
  return copy($bootloader, $dest);
}

function get_years($xml) {
  $years = $xml->xpath('/uoytermdates/termdates/year');
  $res = array();
  foreach ($years as $y) {
    $res[] = (integer) $y;
  }
  sort($res, SORT_NUMERIC);
  return $res;
}

function get_updated_time($xml) {
  $res = $xml->xpath('/uoytermdates/updated[1]');
  return @strtotime($res[0]);
}

function add_year_to_cache($xml,$year,$tmpxml) {
  $res = $xml->xpath("/uoytermdates/termdates[year=${year}]");
  $data = dom_import_simplexml($res[0]);
  $dom = dom_import_simplexml($tmpxml);
  $dom = $dom->ownerDocument;
  $node = $dom->importNode($data, true);
  $dom->documentElement->appendChild($node);
  $tmpxml = simplexml_import_dom($dom);
}

function get_trusted_sources($tmpxml) {
  $res = $tmpxml->xpath('/uoytermdates/source[trusted="yes"]/url');
  $result = array();
  foreach ($res as $r) {
    $result[] = $r;
  }
  return $result;
}

function get_sources($xml) {
  $res = $tmpxml->xpath('/uoytermdates/source/url');
  $result = array();
  foreach ($res as $r) {
    $result[] = $r;
  }
  return $result;
}

function change_update_time_of_cache($time, $tmpxml) {
  $tmpxml->updated[0] = @date('Y-m-d\TH:i:sP', $time);
}

function write_to_cache($tmpxml) {
  global $file, $localdir;
  return file_put_contents($localdir.'/'.$file, $tmpxml->asXML());
}

function add_source_to_cache($url, $trust, $tmpxml){
  //TODO
}

function update_cache() {
  global $file, $url, $localdir, $tmpxml;
  $cache = array('public' => "http://${url}/${file}", 'local' => "${localdir}/${file}");
  
  if (!file_exists($cache['local'])){
    bootload_file();
  }

  $tmpxml = simplexml_load_file($cache['local']);

  $sources = get_trusted_sources($tmpxml);
  $lastupdate = get_updated_time($tmpxml);
  $localyears = get_years($tmpxml);
  
  $sourceslist = $sources;
  $updated = false;
  foreach ($sources as $f){
    if ($f != $cache['public']){
      $xml = @simplexml_load_file($f);
      if (!$xml) break; //remote file doesn't exist
      $utime = get_updated_time($xml);
      //find newer version
      if ($lastupdate < $utime) {
        //update sources
        $sourcesremote = get_sources($xml);
        $sourcestoupdate = array_diff($sourcesremote,$sourceslist);
        foreach ($sourcestoupdate as $s) {
          add_source_to_cache($s,false,$tmpxml);
          $sourcelist[] = $s;
        }
        //update termdates
        $yearremote = get_years($xml);
        $yearstoupdate = array_diff($yearremote, $localyears);
        foreach ($yearstoupdate as $year) {
          add_year_to_cache($xml,$year,$tmpxml);
          $yearlocal[] = $year;
        }
        //update timestamp
        if (count($yearstoupdate) != 0) {
          change_update_time_of_cache($utime,$tmpxml);
          $lastupdate = $utime;
          $updated = true;
        }
      }
    }
  }
  if ($updated){
    return write_to_cache($tmpxml);
  }
  return true;
}

function is_year_in_xml($year,$update) {
  global $file, $localdir;
  $tmpxml = simplexml_load_file("${localdir}/${file}");
  $res = $tmpxml->xpath("/uoytermdates/termdates[year=${year}]");
  if ((count($res[0]) == 0) && $update) {
    update_cache();
    $res = $tmpxml->xpath("/uoytermdates/termdates[year=${year}]");
  }
  return count($res[0]) != 0;//no year exist in xml even after update
}



//assumption 01-Sept is the earliest academic year start
function get_academic_year_start($date) {
  return @date("Y", $date - @strtotime("1st September 1970"));
}

function get_uoy_term_info($date){
  global $file, $localdir;
  $year = get_academic_year_start($date);
  if (!is_year_in_xml($year,true)){
    return false;
  }
  $tmpxml = simplexml_load_file("${localdir}/${file}");
  $res = $tmpxml->xpath("/uoytermdates/termdates[year=${year}]");
  $feature[] = @strtotime("31st August ${year}");
  $feature[] = @strtotime("1st September ".($year+1)."");
  foreach ($res[0]->term as $t) {
    $feature[] = @strtotime($t->start);
    $feature[] = @strtotime($t->end);
  }
  sort($feature,SORT_NUMERIC);
  $term = 0; 
  for ($i = 0; $i < count($feature)-1; $i = $i+1){
    if ($i % 2 == 0) {
      //Break (exclusive dates)
      if (($date > $feature[$i] + 60*60*24) && ($date < $feature[$i+1])) {
        $term = $i;
        break;
      }
    }else{
      //Term (inclusive dates)
      if (($date >= $feature[$i]) && ($date <= $feature[$i+1] + 60*60*24)) {
        $term = $i;
        break;
      }
    }
  }
  //0 - $year-1 summer break
  //1 - term 1
  //2 - $year christmas break
  //3 - term 2
  //4 - $year easter break
  //5 - term 3
  //6 - $year summer break
  if (($term % 2) == 1){
    $weekdayoffset = @strtotime("last Monday",$feature[$term]);
    $relativetoterm = $date - $weekdayoffset;
    $relativetoterm /= 60*60*24*7;
    $week = (int)$relativetoterm; 
  } else {
    $week = 0;
  }
  $result['weeknum'] = $week;
  $result['termnum'] = (($term % 2) == 1)?($term+1)/2:0;
  $result['yearnum'] = ($term != 0)?$year:$year-1;
  switch($term){
    case 0: case 6: $result['termname'] = 'Summer Break'; break;
    case 1: $result['termname'] = 'Autumn Term'; break;
    case 2: $result['termname'] = 'Winter Break'; break;
    case 3: $result['termname'] = 'Spring Term'; break;
    case 4: $result['termname'] = 'Spring Break'; break;
    case 5: $result['termname'] = 'Summer Term'; break;
  }
  $result['yearname'] = $result['yearnum'].'-'.$result['yearnum']+1;

  return $result;
}

for ($i=0; $i<365; $i++) {
  $day = @strtotime("1st September 2010") + $i * 60 * 60 * 24;
  echo @date("Y-m-d",$day)."\n";
  print_r(get_uoy_term_info($day));
}



