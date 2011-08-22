<?php

require_once 'UoY_Date.php';

echo "Test entire of the 2010-2011-2012 years\n";
UoY_Date::test();

echo "Test today\n";
$term_details = new UoY_Date;
$term_details->setTimestamp(time());
echo $term_details->toString();
