<?php

require_once 'UoY_DateHandler.php';

echo "Test entire of the 2010-2011-2012 years\n";
UoY_DateHandler::test();

echo "Test today\n";
$term_details = UoY_DateHandler::termInfo(time());
echo $term_details->toString();
