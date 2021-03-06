<?php
/**
 * Layout header and footer
 * 
 * @package ShowBot-backend
 * @author Latif Khalifa <latifer@streamgrid.net>
 * @copyright Copyright(c) 2014, Latif Khalifa
 * @license http://opensource.org/licenses/MIT
 */
class Layout
{
	static function header()
	{
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>ShowBot</title>
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
	<style>
	body{
		font-family: "Verdana", sans-serif;
		font-size: 12px; 
		color: #eee;
		background-color: #111;
	}
	
@media
only screen and (-webkit-min-device-pixel-ratio: 2),
only screen and (   min--moz-device-pixel-ratio: 2),
only screen and (     -o-min-device-pixel-ratio: 2/1),
only screen and (        min-device-pixel-ratio: 2),
only screen and (                min-resolution: 192dpi),
only screen and (                min-resolution: 1.2dppx) { 
  
	body {
		font-size: 24px;
	}

}

table {
    border-spacing: 0px;
}

table.jtable tr:hover, table.jtable th {
	background: #003147 url(images/ui-bg_highlight-soft_33_003147_1x100.png) 50% 50% repeat-x;
	color: #ffffff;
}

table.jtable tr:hover {
	cursor: pointer;
}

table.jtable {
	border: 1px solid #005880; 
	background-color: #1e1e1e;
	color: #c5c5c5;
	border-radius: 4px;
}

table.jtable a:visited {
        color: #888;
}

table.noborder {
    margin: 1px;
    border: none;
}

td, th {
    text-align: left;
    margin: 0;
    padding: 6px;
    vertical-align: top;
    display: table-cell;
    border: none;
}

.allcaps {
	text-transform: uppercase;
}

	</style>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="jquery.color-2.1.2.min.js"></script>
	<link href="showbot.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
</head>
<body>
	<div>
		<div style="display: inline-block; margin-bottom: 10px;">
			<img src="images/showbot.png" alt="ShowBot by sebgonz" />
		</div>
	</div>
<?php		
	}

	static function footer()
	{
?>
</body>
</html><?php		
	}
}
