<?php
/**
 * Color Imag Generator
 *
 * This file provides the actual user interface.
 *
 * @author Leonid Mamchenov <leonid@mamchenkov.net>
 */

/**
 * Include SampleImage class
 */
require_once dirname(__FILE__) . '/SampleImage.php';

define('ACTION_SHOW', 'show');
define('ACTION_DOWNLOAD', 'download');

$color = '';
$width = 0;
$height = 0;
$action = '';

if (!empty($_GET['color'])) { $color = trim(urldecode($_GET['color'])); }
if (!empty($_GET['width'])) { $width = $_GET['width']; }
if (!empty($_GET['height'])) { $height = $_GET['height']; }
if (!empty($_GET['action'])) { $action = (string) $_GET['action'] ; }

try {
	$image = new SampleImage($color, $width, $height);
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

$color = $image->getColor();
$width = $image->getWidth();
$height = $image->getHeight();

$showParams = array(
		'color' => urlencode($color),
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
			fieldset { width: 30%; border: 1px solid #000; }
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
				<label for="color">Color (hex)</label>
				<input type="text" id="color" name="color" value="<?php echo $color; ?>" />
				<br />
				<label for="width">Width (px)</label>
				<input type="text" id="width" name="width" value="<?php echo $width; ?>" />
				<br />
				<label for="height">Height (px)</label>
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
