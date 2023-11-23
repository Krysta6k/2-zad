<?php
class Images
{
	private function __construct()
	{ /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone()
	{ /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup()
	{ /* ... @return ClassName */ }  // We protect from creation through "unserialize"
	public static function upload($input, $output, $type = 'jpeg', $new_height = null, $new_width = null)
	{
		$get_directory = explode('/', $output);
		$directory = ROOT . '/' . $get_directory[7] . '/' . $get_directory[8];

		if (!file_exists($directory)) {
			mkdir($directory, 0755, true);
			mkdir($directory . '/fullsize', 0755, true);
			mkdir($directory . '/thumb', 0755, true);
			mkdir($directory . '/results', 0755, true);
		}

		if (in_array($type, ['svg', 'svg+xml'])) {
			if (move_uploaded_file($input, $output))
				Notify::createSuccess('The file is correct and was uploaded successfully');
			else
				Notify::createError('Possible file upload attack!');
		} else {
			$im = new Imagick($input);
			// $im->setImageResolution(1250, 1250);

			$imageprops = $im->getImageGeometry();
			$original_width = $imageprops['width'];
			$original_height = $imageprops['height'];

			if (in_array($type, ['jpeg', 'jpg'])) {
				$im->setImageColorspace(Imagick::COLORSPACE_SRGB);
			}

			if (in_array($type, ['webp', 'png', 'ico'])) {
				$im->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
				$im->setBackgroundColor(new ImagickPixel('transparent'));
			}

			if ($new_height) {
				if ($original_height > $new_height) {
					$newWidth = ($new_height / $original_height) * $original_width;
					$im->resizeImage($newWidth, $new_height, Imagick::FILTER_LANCZOS, 0.9, true);
				}
			}

			if ($new_width) {
				if ($original_width > $new_width) {
					$newHeight = $new_width * ($original_height / $original_width);
					$im->resizeImage($new_width, $newHeight, Imagick::FILTER_LANCZOS, 0.9, true);
				}
			}

			$im->setImageFormat($type);
			$im->writeImage($output);
			$im->clear();
			$im->destroy();
		}
	}

	public static function get($image_path, bool $set_default = true)
	{
		if (file_exists(ROOT . DS . $image_path))
			return DS . $image_path;
		else
			return '/img/splash.jpg';

		if ($set_default)
			return '/img/splash.jpg';
	}

	public static function remove($image_path)
	{
		foreach (glob(ROOT . explode('.', $image_path)[0] . ".*") as $filename) {
			if (!unlink($filename)) trigger_error("Удаление изображения: Невозможно удалить изображение", E_USER_ERROR);
		}
	}

	public static function save_image_from_url($input, $output, $type, $h = null)
	{
		$get_directory = explode('/', $output);
		$directory = ROOT . '/' . $get_directory[7] . '/' . $get_directory[8];

		if (!file_exists($directory)) mkdir($directory, 0755, true);

		$im = new Imagick($input);
		// $im->setImageResolution(1250,1250);
		$im->setImageColorspace(255);
		$im->setCompression(Imagick::COMPRESSION_JPEG);
		$im->setCompressionQuality(50);
		$im->setImageFormat('jpeg');

		if ($h) {
			$imageprops = $im->getImageGeometry();
			$width = $imageprops['width'];
			$height = $imageprops['height'];

			if ($height > $h) {
				// if($width > $height) {
				// 	$newHeight = $h;
				// 	$newWidth = ($h / $height) * $width;
				// } else {
				// 	$newWidth = $h;
				// 	$newHeight = ($h / $width) * $height;
				// }

				$newHeight = $h;
				$newWidth = ($h / $height) * $width;

				$im->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 0.9, true);
			}
		}

		$im->writeImage($output);
		$im->clear();
		$im->destroy();
	}

	public static function get_image_style(object $item)
	{
		if (!$item->img_prop)
			return false;

		$parts = explode(' ', $item->img_prop);
		$style = 'background-position: ' . $parts[0] . ' ' . $parts[1] . ';background-size: ' . $parts[2];

		$parts = array_map(function ($item) {
			return str_replace('%', '', $item);
		}, $parts);

		return [
			'range_slider' => [
				'margL' => $parts[0],
				'margT' => $parts[1],
				'sizeImg' => $parts[2],
			],
			'style' => $style
		];
	}
}