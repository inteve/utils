<?php

	namespace Inteve\Utils;

	use Imagick;
	use ImagickDraw;
	use ImagickPixel;
	use Nette\Utils\FileSystem;
	use Nette\Utils\Image;
	use Nette\Utils\Validators;


	class ImagickHelper
	{
		/** {@link resize()} only shrinks images */
		const SHRINK_ONLY = Image::SHRINK_ONLY;

		/** {@link resize()} will ignore aspect ratio */
		const STRETCH = Image::STRETCH;

		/** {@link resize()} fits in given area so its dimensions are less than or equal to the required dimensions */
		const FIT = Image::FIT;

		/** {@link resize()} fills given area so its dimensions are greater than or equal to the required dimensions */
		const FILL = Image::FILL;

		/** {@link resize()} fills given area exactly */
		const EXACT = Image::EXACT;


		public function __construct()
		{
			throw new \Inteve\Utils\StaticClassException('This is static class.');
		}


		/**
		 * @param  int $width
		 * @param  int $height
		 * @param  string|NULL $background
		 * @return Imagick
		 */
		public static function newImage($width, $height, $background = NULL)
		{
			Validators::assert($width, 'number', '$width');
			Validators::assert($height, 'number', '$height');
			Validators::assert($background, 'string:6|null', '$background');

			$image = new Imagick;
			$image->newImage($width, $height, new ImagickPixel(isset($background) ? strtoupper("#{$background}FF") : 'transparent'));
			$image->setColorSpace(Imagick::COLORSPACE_SRGB);
			$image->setOption('png:color-type', '6'); // http://www.imagemagick.org/script/command-line-options.php#define + http://stackoverflow.com/a/36417545
			$image->setImageFormat('png32');
			return $image;
		}


		/**
		 * @param  string $path
		 * @return Imagick
		 */
		public static function openImage($path)
		{
			Validators::assert($path, 'string', '$path');

			$image = new Imagick;
			$image->setBackgroundColor(new ImagickPixel('transparent'));
			$image->readImage($path);
			return $image;
		}


		/**
		 * @param  string $path
		 * @return void
		 */
		public static function saveImage(Imagick $image, $path)
		{
			Validators::assert($path, 'string', '$path');

			try {
				FileSystem::createDir(dirname($path));

			} catch (\Nette\IOException $e) {
				if (!is_dir(dirname($path))) {
					$er = error_get_last();

					if ($er === NULL) {
						throw new InvalidStateException("Trying to create directory failed with unknow error", 0, $e);
					}

					throw new InvalidStateException("Trying to create directory failed with error '" . $er['message'] . "'", $er['type'], $e);
				}
			}
			$image->writeImage($path);
		}


		/**
		 * @param  int $x
		 * @param  int $y
		 * @return void
		 */
		public static function composite(Imagick $destination, Imagick $source, $x, $y)
		{
			Validators::assert($x, 'number', '$x');
			Validators::assert($y, 'number', '$y');

			$localX = $destination->getImageWidth() / 2;
			$localY = $destination->getImageHeight() / 2;
			$halfWidth = $source->getImageWidth() / 2;
			$halfHeight = $source->getImageHeight() / 2;
			$localX += $x - $halfWidth;
			$localY += $y - $halfHeight;
			$destination->compositeImage($source, Imagick::COMPOSITE_DEFAULT, (int) round($localX), (int) round($localY));
		}


		/**
		 * @param  float $scale
		 * @return void
		 */
		public static function scale(Imagick $image, $scale)
		{
			$width = $image->getImageWidth() * $scale;
			$height = $image->getImageHeight() * $scale;
			$image->resizeImage((int) round($width), (int) round($height), Imagick::FILTER_LANCZOS, 1, FALSE);
		}


		/**
		 * @param  int|string|NULL $width
		 * @param  int|string|NULL $height
		 * @param  Image::SHRINK_ONLY|Image::STRETCH|Image::FIT|Image::FILL|Image::EXACT $flags
		 * @return void
		 */
		public static function resize(Imagick $image, $width, $height, $flags = self::FIT)
		{
			if ($flags & self::EXACT) {
				if ($width === NULL || $height === NULL) {
					throw new InvalidArgumentException('Missing width or height for EXACT resize.');
				}

				self::resize($image, $width, $height, self::FILL);
				self::crop($image, '50%', '50%', $width, $height);
				return;
			}

			list($newWidth, $newHeight) = Image::calculateSize($image->getImageWidth(), $image->getImageHeight(), $width, $height, $flags);

			if ($newWidth !== $image->getImageWidth() || $newHeight !== $image->getImageHeight()) { // resize
				$image->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1, FALSE);
			}

			if ($width < 0 || $height < 0) {
				if ($width < 0) {
					$image->flopImage();
				}

				if ($height < 0) {
					$image->flipImage();
				}
			}
		}


		/**
		 * @return void
		 */
		public static function autocrop(Imagick $image)
		{
			$image->trimImage(0);
			$image->setImagePage(0, 0, 0, 0);
		}


		/**
		 * @param  int|string  $left in pixels or percent
		 * @param  int|string  $top in pixels or percent
		 * @param  int|string  $width in pixels or percent
		 * @param  int|string  $height in pixels or percent
		 * @return void
		 */
		public static function crop(Imagick $image, $left, $top, $width, $height)
		{
			list($x, $y, $width, $height) = Image::calculateCutout($image->getImageWidth(), $image->getImageHeight(), $left, $top, $width, $height);
			$image->cropImage($width, $height, $x, $y);
			$image->setImagePage(0, 0, 0, 0);
		}


		/**
		 * @param  Imagick|ImagickDraw $resource
		 * @return void
		 */
		public static function destroyResource($resource)
		{
			Validators::assert($resource, 'object', '$resource');

			$resource->clear();
			$resource->destroy();
		}
	}
