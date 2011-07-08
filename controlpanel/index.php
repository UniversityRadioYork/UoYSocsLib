<?php

//Authentication code here

require_once '../UoY_Cache.php';

?><!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class=""> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title></title>
	<meta name="description" content="">
	<meta name="author" content="">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<!--<link type="text/css" rel="stylesheet" href="css/style.css?v=2" />-->
	<link type="text/css" rel="stylesheet" href="css/smoothness/jquery-ui-1.8.14.custom.css" />

	<script type="text/javascript" src="js/libs/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="js/libs/jquery-ui-1.8.14.custom.min.js"></script>
	<script type="text/javascript" src="js/libs/modernizr-1.7.min.js"></script>
</head>
<style type="text/css">
  table {
		border-collapse:collapse;
    font-size:1em;
		width:100%;
  }
  th,td {
    padding:0.5em;
		border: 1px solid #ddd;
  }
	th {
    font-weight:bold;
  }
</style>
<body>
	<div id="container">
		<header>

		</header>

		<div id="main" role="main">
			<div id="tabs">
				<ul>
					<li><a href="#tabs-1">Term Dates</a></li>
					<li><a href="#tabs-2">Update Sources</a></li>
					<li><a href="#tabs-3">Help</a></li>
				</ul>
				<div id="tabs-1">
					<table id="termdates" class="ui-widget">
						<thead>
							<tr>
								<th class="ui-widget-header">Year</th>
								<th class="ui-widget-header">Autumn start</th>
								<th class="ui-widget-header">Autumn end</th>
								<th class="ui-widget-header">Spring start</th>
								<th class="ui-widget-header">Spring end</th>
								<th class="ui-widget-header">Summer start</th>
								<th class="ui-widget-header">Summer end</th>
								<th class="ui-widget-header">Options</th>
							</tr>
						</thead>
            <tbody>
<?php
	$xml = UoY_Cache::cacheHandle();
	$res = $xml->xpath("/uoytermdates/termdates");
	foreach ($res as $td ) {
		$row = array();
		$row[] = (string )$td->year;
		$res2 = $td->term;
		foreach ($res2 as $t) {
			$row[] = $t->start;
			$row[] = $t->end;
		}
?>
							<tr>
								<td class="ui-waidget-content"><?php echo $row[0]; ?></td>
								<td class="ui-waidget-content"><?php echo $row[1]; ?></td>
								<td class="ui-waidget-content"><?php echo $row[2]; ?></td>
								<td class="ui-waidget-content"><?php echo $row[3]; ?></td>
								<td class="ui-waidget-content"><?php echo $row[4]; ?></td>
								<td class="ui-waidget-content"><?php echo $row[5]; ?></td>
								<td class="ui-waidget-content"><?php echo $row[6]; ?></td>
								<td class="ui-waidget-content">&nbsp;</td>
							</tr>
<?php
 }
?>
						</tbody>
					</table>
				</div>
				<div id="tabs-2">
					<p>Tab 2 content</p>
				</div>
				<div id="tabs-3">
					<p>Tab 3 content</p>
				</div>
			</div>
		</div>

		<footer>

		</footer>
	</div>

	<script type="text/javascript" src="js/plugins.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script> DD_belatedPNG.fix('img, .png_bg');</script>
	<![endif]-->
</body>
</html>
