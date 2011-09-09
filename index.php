<?php
/**
 * Color Imag Generator
 *
 * This file provides the actual user interface.
 *
 * @author Leonid Mamchenov <leonid@mamchenkov.net>
 */

ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');

/**
 * Include SampleImage class
 */
require_once dirname(__FILE__) . '/SampleImage.php';

define('ACTION_SHOW', 'show');
define('ACTION_DOWNLOAD', 'download');

$colors = array('#0000ff');
$width = 0;
$height = 0;
$action = '';

if (!empty($_GET['colors'])) { 
	$colors = $_GET['colors']; 
}
if (!empty($_GET['width'])) { $width = $_GET['width']; }
if (!empty($_GET['height'])) { $height = $_GET['height']; }
if (!empty($_GET['action'])) { $action = (string) $_GET['action'] ; }

try {
	$image = new SampleImage($colors, $width, $height);
}
catch (Exception $e) {
	print "<html><body>";
	print "<strong>Fatal error:</strong> $e";
	print "</body></html>";
	exit();
}

switch ($action) {
	case ACTION_DOWNLOAD:
		$image->download();
		exit();
		break;
	case ACTION_SHOW:
		$image->show();
		exit();
		break;
	default:
		// Non-exit scenario continues to HTML
		break;
}

$emptyColors = array('', '', '', '');
$colors = array_merge($image->getColors(), $emptyColors);
$width = $image->getWidth();
$height = $image->getHeight();

$showParams = array(
		'colors' => $colors,
		'width' => $width,
		'height' => $height,
		'action' => ACTION_SHOW,
	);
$showQuery = '?' . http_build_query($showParams);

$downloadParams = $showParams;
$downloadParams['action'] = ACTION_DOWNLOAD;
$downloadQuery = '?' . http_build_query($downloadParams);

?>
<html>
	<head>
		<title>Color Image Generator</title>
		<style>
			body { font-family: "Arial"; }
			fieldset { width: 50%; border: 1px solid #000; }
			legend { border: 1px solid #000; padding: 5px; font-size: 120%; }
			form { display: inline; float: left; margin-right: 20px; border-right: 1px dotted #000; padding: 20px; }
			label { display: block;  font-size: 80%; }
			input[type=text] { margin-bottom: 10px; }
			img { border: 1px solid #000; }
			address { font-color: #cccccc; font-size: 80%; margin-top: 10px; }
			fieldset p { text-align: center; font-size: 80%; }
		</style>
	</head>
	<body>
		<fieldset>
		<legend>Color Image Generator</legend>
			<form method="get">
				<label for="color1">First Color (hex)</label>
				<input type="text" id="color1" name="colors[0]" value="<?php echo $colors[0]; ?>" />
				<br />
				<label for="color2">Second Color (hex)</label>
				<input type="text" id="color2" name="colors[1]" value="<?php echo $colors[1]; ?>" />
				<br />
				<label for="color3">Third Color (hex)</label>
				<input type="text" id="color3" name="colors[2]" value="<?php echo $colors[2]; ?>" />
				<br />
				<label for="color4">Fourth Color (hex)</label>
				<input type="text" id="color4" name="color[3]" value="<?php echo $colors[3]; ?>" />
				<br />

				<label for="width">Image Width (px)</label>
				<input type="text" id="width" name="width" value="<?php echo $width; ?>" />
				<br />
				<label for="height">Image Height (px)</label>
				<input type="text" id="height"  name="height" value="<?php echo $height; ?>" />
				<br />
				<input type="submit" value="Create Image" />
			</form>
			<p>
				(click image to download)<br /><br />
				<a href="<?php echo $downloadQuery; ?>"><img src="<?php echo $showQuery; ?>" /></a>
			</p>
		</fieldset>
		<address>&copy; Copyright <?php echo date('Y'); ?>, <a href="http://mamchenkov.net">Leonid Mamchenkov</a></address>
	</body>
</html>
