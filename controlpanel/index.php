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
	<meta name="author" content="Gareth Andrew Lloyd <gareth@ignition-web.co.uk>">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
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
		text-align:right;
  }
	th {
    font-weight:bold;
  }
	input {
		width:100%;
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
								<th style="width:6%" class="ui-widget-header">Year</th>
								<th style="width:12%" class="ui-widget-header">Autumn start</th>
								<th style="width:12%" class="ui-widget-header">Autumn end</th>
								<th style="width:12%" class="ui-widget-header">Spring start</th>
								<th style="width:12%" class="ui-widget-header">Spring end</th>
								<th style="width:12%" class="ui-widget-header">Summer start</th>
								<th style="width:12%" class="ui-widget-header">Summer end</th>
								<th style="width:22%" class="ui-widget-header">Options</th>
							</tr>
						</thead>
            <tbody>
<?php
	$xml = UoY_Cache::cacheHandle();
	function ordByYear($a, $b) {
		if (((int)$a->year) == ((int)$b->year)) return 0;
		return ((int)$a->year) < ((int)$b->year)?-1:1;
	}
	$res = $xml->xpath("/uoytermdates/termdates");
	usort($res,"ordByYear");
	foreach ($res as $td ) {
		$row = array();
		$row[] = (int)$td->year;
		$res2 = $td->term;
		foreach ($res2 as $t) {
			$row[] = $t->start;
			$row[] = $t->end;
		}
?>
							<tr id="y<?php echo $row[0]; ?>">
								<td class="ui-waidget-content"><?php echo $row[0]; ?></td>
								<td class="ui-waidget-content"><input value="<?php echo $row[1]; ?>"/></td>
								<td class="ui-waidget-content"><input value="<?php echo $row[2]; ?>"/></td>
								<td class="ui-waidget-content"><input value="<?php echo $row[3]; ?>"/></td>
								<td class="ui-waidget-content"><input value="<?php echo $row[4]; ?>"/></td>
								<td class="ui-waidget-content"><input value="<?php echo $row[5]; ?>"/></td>
								<td class="ui-waidget-content"><input value="<?php echo $row[6]; ?>"/></td>
								<td class="ui-waidget-content"><a class="update" href="#">Update</a><a class="del" href="#">Delete</a></td>
							</tr>
<?php
 }
?>
							<tr id="ynew">
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><input/></td>
								<td class="ui-waidget-content"><a class="add" href="#">Add</a></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div id="tabs-2">
					<table id="sources" class="ui-widget">
						<thead>
							<tr>
								<th style="width:70%" class="ui-widget-header">URL</th>
								<th style="width:12%" class="ui-widget-header">Trusted?</th>
								<th style="width:18%" class="ui-widget-header">Options</th>
							</tr>
						</thead>
            <tbody>
<?php

?>

<?php

?>
						</tbody>
					</table>
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
