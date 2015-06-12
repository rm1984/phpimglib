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

/*
 * Gets the dominant color of an image.
 * [COOL TIP] To boost up the colors in a photo, get its dominant color and then, with a tool like PhotoShop or the GIMP, create a new layer filled with that color, invert it and set the blend mode to "Overlay".
 * 
 * @param resource $image The image resource
 * @return array (r, g, b)
 */
function getDominantColor($image)
{
	$rTotal = 0;
	$gTotal = 0;
	$bTotal = 0;
	$total = 0;

    for ($x = 0; $x < imagesx($image); $x++)
    {
        for ($y = 0; $y < imagesy($image); $y++)
        {
            $rgb = imagecolorat($image, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $rTotal += $r;
            $gTotal += $g;
            $bTotal += $b;
            $total++;
        }
    }

    $rAverage = round($rTotal / $total);
    $gAverage = round($gTotal / $total);
    $bAverage = round($bTotal / $total);

	return array($rAverage, $gAverage, $bAverage);
}

function createImageFilledWithColor($width, $height, $r, $g, $b)
{
	$image = imagecreatetruecolor($width, $height);
	$color = imagecolorallocate($image, $r, $g, $b);

	imagefill($image, 0, 0, $color);

	return $image;
}

function overlay($lower_layer_image, $upper_layer_image)
{
	$width = imagesx($lower_layer_image);
	$height = imagesy($lower_layer_image);
	$overlay = imagecreatetruecolor($width, $height);

    for ($x = 0; $x < $width; $x++)
    {
        for ($y = 0; $y < $height; $y++)
        {
            $lower_rgb = imagecolorat($lower_layer_image, $x, $y);
            $lower_r = ($lower_rgb >> 16) & 0xFF;
            $lower_g = ($lower_rgb >> 8) & 0xFF;
            $lower_b = $lower_rgb & 0xFF;

            $upper_rgb = imagecolorat($upper_layer_image, $x, $y);
            $upper_r = ($upper_rgb >> 16) & 0xFF;
            $upper_g = ($upper_rgb >> 8) & 0xFF;
            $upper_b = $upper_rgb & 0xFF;

			// red channel
			if ($lower_r > 127.5)
			{
				$value_unit_r = (255 - $lower_r) / 127.5;
				$min_value_r = $lower_r - (255 - $lower_r);
				$overlay_r = ($upper_r * $value_unit_r) + $min_value_r;
			}
			else if ($lower_r < 127.5)
			{
				$value_unit_r = $lower_r / 127.5;
				$overlay_r = ($upper_r * $value_unit_r);
			}

			// green channel
			if ($lower_g > 127.5)
			{
				$value_unit_g = (255 - $lower_g) / 127.5;
				$min_value_g = $lower_g - (255 - $lower_g);
				$overlay_g = ($upper_g * $value_unit_g) + $min_value_g;
			}
			else if ($lower_g < 127.5)
			{
				$value_unit_g = $lower_g / 127.5;
				$overlay_g = ($upper_g * $value_unit_g);
			}

			// blue channel
			if ($lower_b > 127.5)
			{
				$value_unit_b = (255 - $lower_b) / 127.5;
				$min_value_b = $lower_b - (255 - $lower_b);
				$overlay_b = ($upper_b * $value_unit_b) + $min_value_b;
			}
			else if ($lower_b < 127.5)
			{
				$value_unit_b = $lower_b / 127.5;
				$overlay_b = ($upper_b * $value_unit_b);
			}

			$pixel = imagecolorallocate($overlay, $overlay_r, $overlay_g, $overlay_b);

			imagesetpixel($overlay, $x, $y, $pixel);
        }
    }

/*
	ob_start();

	imagejpeg($overlay, NULL, 100);
	imagedestroy($overlay);

	$i = ob_get_clean();

	echo "<img src='data:image/jpeg;base64," . base64_encode($i) . "'/>";
*/

	return $overlay;
}

function boost($image)
{
	list($width, $height) = getImageWidthAndHeight($image);
	list($r, $g, $b) = getDominantColor($image);

	$colored_image = createImageFilledWithColor($width, $height, $r, $g, $b);
	$overlay = overlay($image, $colored_image);

	ob_start();

	imagejpeg($overlay, NULL, 100);
	imagedestroy($overlay);

	$i = ob_get_clean();

	echo "<img src='data:image/jpeg;base64," . base64_encode($i) . "'/>";
}


?>
