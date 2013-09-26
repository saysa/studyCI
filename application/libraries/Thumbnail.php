<?php

require("imagine/Image/ImagineInterface.php");
require("imagine/Image/ManipulatorInterface.php");
require("imagine/Image/ImageInterface.php");
require("imagine/Image/BoxInterface.php");
require("imagine/Image/Box.php");
require("imagine/Gd/Imagine.php");
require("imagine/Gd/Image.php");
require("imagine/Image/PointInterface.php");
require("imagine/Image/Point.php");
require("imagine/Image/Palette/PaletteInterface.php");
require("imagine/Image/Palette/ColorParser.php");
require("imagine/Image/Palette/RGB.php");

class Thumbnail {
	
	protected $_filename;
	
	public function __construct($options = array())
	{
		if (isset($options["file"]))
		{
			$file = $options["file"];
			$path = dirname(BASEPATH)."/uploads";
			
			$width = 64;
			$height = 64;
			
			$name = $file->name;
			$filename = pathinfo($name, PATHINFO_FILENAME);
			$extension = pathinfo($name, PATHINFO_EXTENSION);
			
			if ($filename && $extension)
			{
				$thumbnail = "{$filename}-{$width}x{$height}.{$extension}";
				
				if (!file_exists("{$path}/{$thumbnail}"))
				{
					$imagine = new Imagine\Gd\Imagine();
					$size = new Imagine\Image\Box($width, $height);
					$mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
					
					$imagine
						->open("{$path}/{$name}")
						->thumbnail($size, $mode)
						->save("{$path}/{$thumbnail}");
				}
				
				$this->_filename = $thumbnail;
			}
		}
	}
	
	public function getFilename()
	{
		return $this->_filename;
	}
}