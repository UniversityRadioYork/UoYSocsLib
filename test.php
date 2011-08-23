<?php

require_once 'UoY_Date.php';
require_once 'UoY_DateConstants.php';

echo "Test entire of the 2010-2011-2012 years\n";
UoY_Date::test();

echo "Test today\n";
$term_details = new UoY_Date;
$term_details->setTimestamp(time());
echo $term_details->toString()."\n";

echo "Test Friday Week 3 Summer Term 2011\n";
$term_details = new UoY_Date;
$term_details->setTermdate(
		2011, 
		UoY_DateConstants::TERM_SUMMER,
		3,
		UoY_DateConstants::DAY_FRIDAY);
echo $term_details->toString()."\n";
