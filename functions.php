<?php
require('constants.php');

/*
 * Gets an image resource from an image file.
 *
 * @param string $filename The filename of the image (including full path)
 * @return resource
 */
function getImage($filename)
{
	if (exif_imagetype($filename) == IMAGETYPE_GIF)
	{
		return imagecreatefromgif($filename);
	}
	else if (exif_imagetype($filename) == IMAGETYPE_JPEG)
	{
		return imagecreatefromjpeg($filename);
	}
	else if (exif_imagetype($filename) == IMAGETYPE_PNG)
	{
		return imagecreatefrompng($filename);
	}
}

/*
 * Gets the MIME type of an image file.
 *
 * @param string $filename The filename of the image (including full path)
 * @return string
 */
function getImageMIMEType($filename)
{
	return image_type_to_mime_type(exif_imagetype($filename));
}

/*
 * Gets the width (in pixels) of an image resource.
 *
 * @param resource $image The image resource
 * @return integer
 */
function getImageWidth($image)
{
	$width = imagesx($image);

	return $width;
}

/*
 * Gets the height (in pixels) of an image resource.
 *
 * @param resource $image The image resource
 * @return integer
 */
function getImageHeight($image)
{
	$height = imagesy($image);

	return $height;
}

/*
 * Gets both the width and height (in pixels) of an image resource as an array.
 *
 * @param resource $image The image resource
 * @return array (width, height)
 */
function getImageWidthAndHeight($image)
{
	return array(getImageWidth($image), getImageHeight($image));
}

function getPixelRGB($image, $x, $y)
{
	$colors = imagecolorsforindex($image, imagecolorat($image, $x, $y));

	return array($colors['red'], $colors['green'], $colors['blue']);
}

function getPixelRGBA($image, $x, $y)
{
	$colors = imagecolorsforindex($image, imagecolorat($image, $x, $y));

	return array($colors['red'], $colors['green'], $colors['blue'], $colors['alpha']);
}

function getPixelBlackWhite($image, $x, $y)
{
	list($r, $g, $b) = getPixelRGB($image, $x, $y);

	$bw = round(($r + $g + $b) / 3);

	return $bw;
}

function getPixelLuminance($image, $x, $y)
{
	list($r, $g, $b) = getPixelRGB($image, $x, $y);

	$luminance = round((0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b));

	return $luminance;
}

function getHistogram($image, $type = LUMINANCE)
{
	$width = getImageWidth($image);
	$height = getImageHeight($image);

	for ($i = 0; $i <= 255; $i++)
	{
		$h[$i] = 0;
	}

	for ($y = 0; $y < $height; $y++)
	{
		for ($x = 0; $x < $width; $x++)
		{
			if ($type == BLACKWHITE)
			{
				$p = getPixelBlackWhite($image, $x, $y);
			}
			else
			{
				$p = getPixelLuminance($image, $x, $y);
			}

			$h[$p]++;
		}
	}

	$peak = max($h);
	$factor = $peak / 255;

	for ($i = 0; $i <= 255; $i++)
	{
		$h[$i] = round($h[$i] / $factor);
	}

	$histogram = imagecreatetruecolor(256, HISTOGRAM_HEIGHT);
	$black = imagecolorallocate($histogram, 0, 0, 0);
	$white = imagecolorallocate($histogram, 255, 255, 255);

	for ($x = 0; $x <= 255; $x++)
	{
		for ($y = 0; $y <= (HISTOGRAM_HEIGHT - 1); $y++)
		{
			if ($y < ((HISTOGRAM_HEIGHT - 1) - $h[$x]))
			{
				imagesetpixel($histogram, $x, $y, $white);
			}
			else
			{
				imagesetpixel($histogram, $x, $y, $black);
			}
		}
	}

	ob_start();

	imagejpeg($histogram, NULL, 100);
	imagedestroy($histogram);

	$i = ob_get_clean();

	echo "<img src='data:image/jpeg;base64," . base64_encode($i) . "'/>";
}
?>
