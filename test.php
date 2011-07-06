<?php

require_once('lib-uoysocs.php');

//$dh = new UoY_DateHandler();
//$dh->test();

echo "Test entire of the 2010-2011 year\n";
UoY_DateHandler::test();

echo "Test today\n";
$term_details = UoY_DateHandler::term_info(time());
print_r($term_details);
