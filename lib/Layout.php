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
<html lang="us">
<head>
	<meta charset="utf-8">
	<title>ShowBot</title>
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
	/*background: #003147 url(images/ui-bg_highlight-hard_20_0972a5_1x100.png) 50% 50% repeat-x;*/
	background-color: #003147;
	color: #ffffff;
}

table.jtable tr:hover {
	cursor: pointer;
}

table.jtable {
	border: 1px solid #005880; 
	/* border: 1px solid #358; */
	/*background: #1e1e1e url(images/ui-bg_flat_50_5c5c5c_40x100.png) 50% 50% repeat-x;*/
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
	</style>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link href="showbot.css" rel="stylesheet">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
</head>
<body>
	<div>
		<div style="display: inline-block">
			<img src="images/logo.png" title="Logo" />
		</div>
		<div style="float: right;">
			<span style="font-size: 200%;">SHOWBOT</span>
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
