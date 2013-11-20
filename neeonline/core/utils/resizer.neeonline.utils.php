<?
	require_once(str_replace('/utils', '', __DIR__) . '/dynamic.neeonline.class.php');
	
	class NeeonlineResizerUtils extends NeeonlineDynamicClass
	{
		protected static $_localInstance;
		
		private $_image;
	    private $_width;
	    private $_height;
		private $_imageResized;
		
		function __construct($fileName)
		{
			parent::__construct();
		}
		
		public static function getInstance()
		{
			if (is_null(self::$_localInstance)) self::$_localInstance = new self();
			
			return self::$_localInstance;
		}
		
		public function load($fileName)
		{
			$this->_image = $this->_openImage($fileName);
			
		    $this->_width  = imagesx($this->_image);
		    $this->_height = imagesy($this->_image);
		}
		
		private function _openImage($file)
		{
			$extension = strtolower(strrchr($file, '.'));

			switch($extension)
			{
				case '.jpg':
				case '.jpeg':
					$img = @imagecreatefromjpeg($file);
				break;
				case '.gif':
					$img = @imagecreatefromgif($file);
				break;
				case '.png':
					$img = @imagecreatefrompng($file);
					
					imagealphablending($img, true);
				break;
				default:
					$img = false;
					break;
			}
			
			return $img;
		}
		
		private function _getDimensions($newWidth, $newHeight, $option)
		{

		   switch ($option)
			{
				case 'exact':
					$optimalWidth = $newWidth;
					$optimalHeight= $newHeight;
				break;
				case 'portrait':
					$optimalWidth = $this->_getWidthByFixedHeight($newHeight);
					$optimalHeight= $newHeight;
				break;
				case 'landscape':
					$optimalWidth = $newWidth;
					$optimalHeight= $this->_getHeightByFixedWidth($newWidth);
				break;
				case 'auto':
					$optionArray = $this->_getAutoDimensions($newWidth, $newHeight);
					$optimalWidth = $optionArray['optimalWidth'];
					$optimalHeight = $optionArray['optimalHeight'];
					break;
				case 'crop':
					$optionArray = $this->_getCropDimensions($newWidth, $newHeight);
					$optimalWidth = $optionArray['optimalWidth'];
					$optimalHeight = $optionArray['optimalHeight'];
				break;
			}
			
			return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
		}

		private function _getWidthByFixedHeight($newHeight)
		{
			$ratio = $this->_width / $this->_height;
			$newWidth = $newHeight * $ratio;
			
			return $newWidth;
		}

		private function _getHeightByFixedWidth($newWidth)
		{
			$ratio = $this->_height / $this->_width;
			$newHeight = $newWidth * $ratio;
			
			return $newHeight;
		}

		private function _getAutoDimensions($newWidth, $newHeight)
		{
			if ($this->_height < $this->_width)
			{
				// landscape
				
				$optimalWidth = $newWidth;
				$optimalHeight= $this->_getHeightByFixedWidth($newWidth);
			}
			elseif ($this->_height > $this->_width)
			{
				// portrait
				
				$optimalWidth = $this->_getWidthByFixedHeight($newHeight);
				$optimalHeight= $newHeight;
			}
			else
			{
				// square
				
				if ($newHeight < $newWidth)
				{
					$optimalWidth = $newWidth;
					$optimalHeight= $this->_getHeightByFixedWidth($newWidth);
				}
				else if ($newHeight > $newWidth)
				{
					$optimalWidth = $this->_getWidthByFixedHeight($newHeight);
					$optimalHeight= $newHeight;
				}
				else
				{
					$optimalWidth = $newWidth;
					$optimalHeight= $newHeight;
				}
			}

			return array(
				'optimalWidth'	=> $optimalWidth,
				'optimalHeight'	=> $optimalHeight
			);
		}

		private function _getCropDimensions($newWidth, $newHeight)
		{
			$heightRatio = $this->_height / $newHeight;
			$widthRatio  = $this->_width /  $newWidth;

			if ($heightRatio < $widthRatio)
			{
				$optimalRatio = $heightRatio;
			}
			else
			{
				$optimalRatio = $widthRatio;
			}

			$optimalHeight = $this->_height / $optimalRatio;
			$optimalWidth  = $this->_width  / $optimalRatio;

			return array(
				'optimalWidth'	=> $optimalWidth,
				'optimalHeight'	=> $optimalHeight
			);
		}

		private function _cropImage($optimalWidth, $optimalHeight, $newWidth, $newHeight, $fix)
		{
			// find center
			$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
			$cropStartY = ($optimalHeight / 2) - ($newHeight /2);
			
			switch ($fix)
			{
				case 'top':
					$cropStartY = 0;
					break;
				case 'left':
					$cropStartX = 0;
					break;
				case 'bottom':
					$cropStartY = $optimalHeight - $newHeight;
					break;
				case 'right':
					$cropStartX = $optimalWidth - $newWidth;
					break;
			}

			$crop = $this->_imageResized;
			
			$this->_imageResized = imagecreatetruecolor($newWidth , $newHeight);
			imagealphablending($this->_imageResized, false);
			imagesavealpha($this->_imageResized, true);
			
			imagecopyresampled($this->_imageResized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
		}
		
		public function resizeImage($newWidth, $newHeight, $option = 'auto', $fix = '')
		{
			$optionArray = $this->_getDimensions($newWidth, $newHeight, $option);

			$optimalWidth  = $optionArray['optimalWidth'];
			$optimalHeight = $optionArray['optimalHeight'];
			
			$this->_imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
			imagealphablending($this->_imageResized, false);
			imagesavealpha($this->_imageResized, true);
			
			imagecopyresampled($this->_imageResized, $this->_image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->_width, $this->_height);
			
			if ($option == 'crop') $this->_cropImage($optimalWidth, $optimalHeight, $newWidth, $newHeight, $fix);
		}
		
		public function resizeImageByValues($newWidth, $newHeight, $x, $y, $w, $h, $cw = 0, $ch = 0)
		{
			$this->_imageResized = imagecreatetruecolor($newWidth, $newHeight);
			imagealphablending($this->_imageResized, false);
			imagesavealpha($this->_imageResized, true);
			
			$widthScale = ($cw) ? $this->_width / $cw : 1;
			$heighScale = ($ch) ? $this->_height / $ch : 1;
			
			imagecopyresampled($this->_imageResized, $this->_image, 0, 0, $x * $widthScale, $y * $heighScale, $newWidth, $newHeight, $w * $widthScale, $h * $heighScale);
		}

		public function saveImage($savePath, $imageQuality = 100)
		{
			// Get extension
    		$extension = strrchr($savePath, '.');
   			$extension = strtolower($extension);

			switch($extension)
			{
				case '.jpg':
				case '.jpeg':
					if (imagetypes() & IMG_JPG) imagejpeg($this->_imageResized, $savePath, $imageQuality);
				break;
				case '.gif':
					if (imagetypes() & IMG_GIF) imagegif($this->_imageResized, $savePath);
				break;
				case '.png':
					// Scale quality from 0-100 to 0-9
					$scaleQuality = round(($imageQuality / 100) * 9);

					// 0 is best, not 9
					$invertScaleQuality = 9 - $scaleQuality;

					if (imagetypes() & IMG_PNG) imagepng($this->_imageResized, $savePath, $invertScaleQuality);
					break;
				default:
					// *** No extension - No save.
					break;
			}

			imagedestroy($this->_imageResized);
		}
	}
?>