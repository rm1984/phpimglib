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
	$filename_image1 = "image1.jpg";
	$realpath_image1 = realpath($filename_image1);
	$image1 = getImage($filename_image1);

	$mimetype = getImageMIMEType($filename_image1);
	list($width, $height) = getImageWidthAndHeight($image1);
?>
			<tr>
				<td class="test_table_row">Image</td>
				<td class="test_table_row"><img src="<?php echo $filename_image1; ?>"/></td>
			</tr>
			<tr>
				<td class="test_table_row">Filename</td>
				<td class="test_table_row"><?php echo $realpath_image1; ?></td>
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
				<td class="test_table_row"><?php getHistogram($image1, LUMINANCE); ?></td>
			</tr>
			<tr>
				<td class="test_table_row">Histogram (based on black and white)</td>
				<td class="test_table_row"><?php getHistogram($image1, BLACKWHITE); ?></td>
			</tr>
		</table>
	</body>
</html>
