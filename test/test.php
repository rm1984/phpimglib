<?php
	require('../functions.php');
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css"/>
	</head>
	<body>
		<table class="test_table">
			<tr>
				<th colspan="2" class="test_table_row">Test 1</th>
			</tr>
<?php
	$filename_image = "image1.jpg";
	$realpath_image = realpath($filename_image);
	$image = getImage($filename_image);

	$mimetype = getImageMIMEType($filename_image);
	list($width, $height) = getImageWidthAndHeight($image);
	list($dom_r, $dom_g, $dom_b) = getDominantColor($image);
?>
			<tr>
				<td class="test_table_row">Image</td>
				<td class="test_table_row"><img src="<?php echo $filename_image; ?>"/></td>
			</tr>
			<tr>
				<td class="test_table_row">Filename</td>
				<td class="test_table_row"><?php echo $realpath_image; ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Width x Height (in pixels)</td>
				<td class="test_table_row"><?php echo $width .  " x " . $height; ?></td>
			</tr>
			<tr>
				<td class="test_table_row">MIME type</td>
				<td class="test_table_row"><?php echo $mimetype; ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Histogram (based on luminance)</td>
				<td class="test_table_row"><?php getHistogram($image, LUMINANCE); ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Histogram (based on black and white)</td>
				<td class="test_table_row"><?php getHistogram($image, BLACKWHITE); ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Dominant color</td>
				<td class="test_table_row"><div class="foo" style="background-color: <?php echo "rgb(" . $dom_r . ", " . $dom_g . ", " . $dom_b . ");"; ?>"><?php echo "RGB (" . $dom_r . ", " . $dom_g . ", " . $dom_b . ")"; ?></div></td>
			</tr>
			<tr>
				<td class="test_table_row">Boosted image</td>
				<td class="test_table_row"><?php boost($image); ?></td>
			</tr>
		</table>
		<br/>
		<table class="test_table">
			<tr>
				<th colspan="2" class="test_table_row">Test 2</th>
			</tr>
<?php
	$filename_image = "image2.png";
	$realpath_image = realpath($filename_image);
	$image = getImage($filename_image);

	$mimetype = getImageMIMEType($filename_image);
	list($width, $height) = getImageWidthAndHeight($image);
	list($dom_r, $dom_g, $dom_b) = getDominantColor($image);
?>
			<tr>
				<td class="test_table_row">Image</td>
				<td class="test_table_row"><img src="<?php echo $filename_image; ?>"/></td>
			</tr>
			<tr>
				<td class="test_table_row">Filename</td>
				<td class="test_table_row"><?php echo $realpath_image; ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Width x Height (in pixels)</td>
				<td class="test_table_row"><?php echo $width .  " x " . $height; ?></td>
			</tr>
			<tr>
				<td class="test_table_row">MIME type</td>
				<td class="test_table_row"><?php echo $mimetype; ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Histogram (based on luminance)</td>
				<td class="test_table_row"><?php getHistogram($image, LUMINANCE); ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Histogram (based on black and white)</td>
				<td class="test_table_row"><?php getHistogram($image, BLACKWHITE); ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Dominant color</td>
				<td class="test_table_row"><div class="foo" style="background-color: <?php echo "rgb(" . $dom_r . ", " . $dom_g . ", " . $dom_b . ");"; ?>"><?php echo "RGB (" . $dom_r . ", " . $dom_g . ", " . $dom_b . ")"; ?></div></td>
			</tr>
			<tr>
				<td class="test_table_row">Boosted image</td>
				<td class="test_table_row"><?php boost($image); ?></td>
			</tr>
		</table>
	</body>
</html>
