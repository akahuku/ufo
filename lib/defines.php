<?php
define('BMP_CODEPOINTS', 63488);
define('FONT_WIDTH', 8);
define('FONT_HEIGHT', 16);
define('CELL_MARGIN', 8);
define('CELL_WIDTH', CELL_MARGIN + FONT_WIDTH * 2 + CELL_MARGIN);
define('CELL_HEIGHT', CELL_MARGIN + FONT_HEIGHT + CELL_MARGIN);
define('GRID_HORZ_COUNT', 256);

/*
 *
 * ----------------------
 *
 *
 *
 *                                                cap_height
 *              @    @                            |
 *              @    @                            |
 *               @  @                 x height    |
 *    @    @     @  @                 |           |
 *    @    @      @@                  |           |
 *     @  @       @@                  |           |
 *      @@       @  @                 |           |
 *     @  @      @  @                 |           |
 *    @    @    @    @                |           |
 *    @    @    @    @     baseline   |           |
 *                         |
 *                         |
 * ----------------------
 */
$cap_height = 10;
$x_height = 7;
$baseline = 2;

function read_block ($block_filename)
{
	$block_data = array();

	$fp = @fopen($block_filename, "rb");
	if (!$fp) {
		throw new Exception("cannot open the file [$block_filename]. stop.");
	}

	$total = 0;
	while (($line = fgets($fp)) !== false) {
		$line = trim($line);

		if (!preg_match('/^([0-9a-f]+)\.\.([0-9a-f]+);\s*(.+)/i', $line, $re)) {
			continue;
		}

		$item = array(
			"from" => intval($re[1], 16),
			"to" => intval($re[2], 16),
			"name" => $re[3]);

		if ($item["from"] >= 0x10000) {
			break;
		}

		if (count($block_data)) {
			$last_item = $block_data[count($block_data) - 1];
			if ($item["from"] - $last_item["to"] > 1) {
				$block_data[] = array(
					"from" => $last_item["to"] + 1,
					"to" => $item["from"] - 1,
					"index" => count($block_data),
					"name" => "__blackhole__");
			}
		}

		$item["index"] = count($block_data);
		$block_data[] = $item;

		$total += $item["to"] - $item["from"] + 1;
	}
	fwrite(STDERR, sprintf("block total: %d code points.\n", $total));

	fclose($fp);

	return $block_data;
}

function create_cell_image ($width, $height, $is_biwidth)
{
	global $cap_height, $x_height, $baseline;

	$image = ImageCreateTruecolor($width, $height);

	// cell
	ImageFilledrectangle(
		$image,
		0, 0, $width - 2, $height - 2,
		ImageColorAllocate($image, 224, 224, 224));

	// x height
	ImageFilledrectangle(
		$image,
		0, 0,
		$width - 2, CELL_MARGIN + FONT_HEIGHT - $baseline - $x_height - 1,
		ImageColorAllocate($image, 192, 192, 192));

	// cap height
	ImageFilledrectangle(
		$image,
		0, 0,
		$width - 2, CELL_MARGIN + FONT_HEIGHT - $baseline - $cap_height - 1,
		ImageColorAllocate($image, 160, 160, 160));

	// baseline
	ImageFilledrectangle(
		$image,
		0, CELL_MARGIN + FONT_HEIGHT - $baseline,
		$width - 2, $height - 2,
		ImageColorAllocate($image, 192, 192, 192));

	// bounding box
	ImageFilledrectangle(
		$image,
		CELL_MARGIN, CELL_MARGIN,
		CELL_MARGIN + FONT_WIDTH * ($is_biwidth ? 2 : 1) - 1,
		CELL_MARGIN + FONT_HEIGHT - 1,
		ImageColorAllocate($image, 255, 255, 255));

	return $image;
}

function hex2array ($hex)
{
	$result = array();
	$length = strlen($hex);

	for ($i = 0; $i < $length; $i += 2) {
		$result[] = intval($hex[$i], 16) << 4 | intval($hex[$i + 1], 16);
	}

	return $result;
}

function array2hex ($array)
{
	$result = "";
	$length = count($array);

	for ($i = 0; $i < $length; $i++) {
		$result .= sprintf("%02X", $array[$i]);
	}

	return $result;
}

function read_hex_core ($filename, &$buffer)
{
	$fp = @fopen($filename, "rb");
	if (!$fp) {
		throw new Exception("cannot open the file [$filename]. stop.");
	}

	fwrite(STDERR, "\t$filename...");
	$index = 0;
	while (($line = fgets($fp)) !== false) {
		$line = trim($line);

		if (!preg_match('/^([0-9a-f]+):([0-9a-f]+)/i', $line, $re)) {
			continue;
		}

		$code_point = intval($re[1], 16);
		$buffer[$code_point] = hex2array($re[2]);
		$index++;

		if ($index % 256 == 0) {
			fwrite(STDERR, "\r\t$filename...$index");
		}

		/*
		if ($code_point >= 128) {
			break;
		}
		 */
	}

	fwrite(STDERR, "\r\t$filename: $index code points.\n");
	fclose($fp);
}

function read_hex ($input_dir)
{
	$handle = @opendir($input_dir);
	if (!$handle) {
		throw new Exception("cannot open input dir [$input_dir]. stop.");
	}

	fwrite(STDERR, "reading hex file...\n");
	$buffer = array();

	while (($file = readdir($handle)) !== false) {
		if ($file == "." || $file == "..") continue;
		if (!preg_match('/\.hex$/', $file)) continue;
		read_hex_core($input_dir . "/" . $file, $buffer);
	}

	closedir($handle);

	return $buffer;
}

function read_translate_data ($filename)
{
	$translate_data = array();

	$fp = @fopen($filename, "rb");
	if (!$fp) {
		throw new Exception("cannot open the file [$filename]. stop.");
	}

	while (($line = fgets($fp)) !== false) {
		$line = trim($line);

		if (!preg_match('/^0x([0-9A-F]{4})\s+U\+([0-9A-F]{4})\s+/', $line, $re)) {
			continue;
		}

		$jis_code = intval($re[1], 16);
		$codepoint = intval($re[2], 16);

		if (preg_match('/Fullwidth:\s*U\+([0-9A-F]{4})/', $line, $re)) {
			$codepoint = intval($re[1], 16);
		}

		$translate_data[$jis_code] = $codepoint;
	}

	fclose($fp);

	fwrite(STDERR, sprintf(
		"jis->ucs2 translate data: %d entries.\n",
		count($translate_data)));

	return $translate_data;

}

function get_gryph_filename ($block_index, $block_name)
{
	return sprintf("%03d", $block_index) . "-"
		. str_replace(" ", "-", $block_name) . ".png";
}
