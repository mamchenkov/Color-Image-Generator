<?php
/**
 * Color Image Generator
 *
 * Generate an image of specified size, filled with specified color(s).
 * Provide support for display and download of the image.
 *
 * @author Leonid Mamchenkov <leonid@mamchenkov.net>
 */
class SampleImage {

	const DEFAULT_COLOR = '#0000ff';
	const DEFAULT_WIDTH = 200;
	const DEFAULT_HEIGHT = 200;

	const MAX_WIDTH = 1000;
	const MAX_HEIGHT = 1000;

	protected $colors;
	protected $width;
	protected $height;
	protected $fileName;
	protected $convert = '/usr/bin/convert';
	protected $tempDir = '/tmp/';

	/**
	 * Constructor
	 *
	 * - Make sure we have all we need
	 * - Set the values to work with
	 *
	 * @param array $colors List of color hex codes
	 * @param integer $width Width in pixels
	 * @param integer $height Height in pixels
	 */
	public function __construct($colors, $width, $height) {

		// No reason to continue at all, if ImageMagick is not around
		if (!file_exists($this->convert)) {
			throw new Exception("ImageMagick convert not found! Expecting at: " . $this->convert);
		}

		// Temporary directory is not a directory or is not writable
		if (!file_exists($this->tempDir) || !is_dir($this->tempDir) || !is_writable($this->tempDir)) {
			throw new Exception("Temporary directory is missing or is not writable! Checking: " . $this->tempDir);
		}

		$this->setColors($colors);
		$this->setWidth($width);
		$this->setHeight($height);
	}

	/**
	 * Destructor
	 *
	 * - Remove temporary filename
	 *
	 */
	public function __destruct() {
		if (file_exists($this->fileName)) {
			unlink($this->fileName);
		}
	}

	/**
	 * Validate, clean, and set the color(s)
	 *
	 * This also updates the filename that we are using for
	 * both temporary image storage and attachment name when
	 * downloading
	 *
	 * @param array $colors Color hex codes
	 */
	public function setColors($colors) {
		$validColors = array();

		foreach ($colors as $color) {
			$color = urldecode($color);
			$color = trim($color);
			$color = strtolower($color);

			if (!preg_match('/^#/', $color)) {
				$color = "#{$color}";
			}

			if (preg_match('/^#?(([a-fA-F0-9]){3}){1,2}$/', $color)) {
				$validColors[] = $color;
			}

		}

		if (!empty($validColors)) {
			$this->colors = $validColors;
		}
		else {
			$this->colors = array(self::DEFAULT_COLOR);
		}

		// Our filename is based on color, so reset it every time color changes
		$this->setFileName();
	}

	/**
	 * Get current color
	 *
	 * @return string Color hex code
	 */
	public function getColors() {
		return $this->colors;
	}

	/**
	 * Validate and set the width
	 *
	 * @param integer $width Width in pixels
	 */
	public function setWidth($width) {
		$width = (int) $width;
		$this->width = (($width > 0) && ($width <= self::MAX_WIDTH)) ? $width : self::DEFAULT_WIDTH;
	}

	/**
	 * Get current width
	 *
	 * @return integer
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * Validate and set the height
	 *
	 * @param integer $height Height in pixels
	 */
	public function setHeight($height) {
		$height = (int) $height;
		$this->height = (($height > 0) && ($height <= self::MAX_HEIGHT)) ? $height : self::DEFAULT_HEIGHT;
	}

	/**
	 * Get current height
	 *
	 * @return integer
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Set filename
	 *
	 * Filename is based on chosen color.
	 */
	public function setFileName() {
		$name = 'color';
		foreach ($this->colors as $color) {
			$name .= '_' . strtolower(preg_replace('/^#/', '', $color));
		}
		$name .= '.png';
		$this->fileName = $this->tempDir . $name;
	}

	/**
	 * Generate image file
	 *
	 * Executes shell command that generates a file on disk.
	 */
	private function create() {
		$command = sprintf("%s -size %dx%d xc:%s %s", 
			$this->convert, 
			$this->width, 
			$this->height, 
			$this->colors[0], 
			$this->fileName
		);
		exec($command);
	}	

	/**
	 * Display generated image
	 *
	 * Output the image from disk, setting proper headers
	 */
	public function show() {
		$this->create();
		header("Content-Type: image/png");
		ob_clean();
		flush();
		readfile($this->fileName);
	}

	/**
	 * Download generated image
	 *
	 * Output the image from disk as attachment downloader.
	 */
	public function download() {
		header('Content-Disposition: attachment; filename="' . basename($this->fileName) . '"');
		$this->show();
	}
}
?>
