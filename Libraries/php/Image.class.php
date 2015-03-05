<?php

class Image {

	public function __construct($arg1, $arg2 = NULL) {
		$this->error = false;
		
		//$time1 = microtime(true);
		
		if(is_string($arg1)) {
			$this->path = $arg1;
			
			if(!$image = $this->_getImageRessource($this->path)) { $this->error = true; return; }

			$width = imagesx($image);
			$height = imagesy($image);

		} else if(is_resource($arg1)) {
			$image = $arg1;
			$width = imagesx($image);
			$height = imagesy($image);
			$this->type = 'png';
			
		} else if(is_object($arg1)) {
			$image = $arg1->image;
			$width = $arg1->width;
			$height = $arg1->height;
			$this->type = $arg1->type;
			
		} else if(is_numeric($arg1) && is_numeric($arg2)) {
			$width = $arg1;
			$height = $arg2;
			$image = $this->_createVoidImageRessource($width, $height);
			$this->type = 'png';
			
		} else {
			$this->error = true; return;
		}
		
		
		if(in_array($this->type, array('png', 'gif'))) {
			$this->_copyImage($image, $width, $height);
		} else {
			$this->image = $image;
		}
		
		/*$time2 = microtime(true);
		echo floor(($time2 - $time1) * 1000) . "ms\n";*/
		

		$this->width = $width;
		$this->height = $height;
	}
	
	public function base64Encode() {
		ob_start(); 
			imagepng($this->image);
			$imageData = ob_get_contents(); 
		ob_end_clean(); 
		return 'data:image/png;base64,' . base64_encode($imageData);
	}
	
	public function show() {
		header('Content-Type: image/png');
		imagepng($this->image);
		return $this;
	}
	
	public function save($path) {
		imagepng($this->image, $path);
		return $this;
	}
	
	public function resize($width, $height, $keepProportions = false) {
		$newImage = new Image($width, $height);
			
		if($keepProportions) {
			$dst_w = $this->width;
			$dst_h = $this->height;
		
			if($dst_w > $width) {
				$dst_w = $width;
				$dst_h = $width * ($this->height / $this->width);
			}
			
			if($dst_h > $height) {
				$dst_w = $height * ($dst_w / $dst_h);
				$dst_h = $height;
			}
			
			$dst_x = ($width - $dst_w ) / 2;
			$dst_y = ($height - $dst_h ) / 2;
		} else {
			$dst_x = 0;
			$dst_y = 0;
			$dst_w = $width;
			$dst_h = $height;
		}
		
		imagecopyresampled($newImage->image, $this->image, floor($dst_x), floor($dst_y), 0, 0, floor($dst_w), floor($dst_h), $this->width, $this->height);
		
		return $newImage;
	}
	
	public function merge($image, $x = 0, $y = 0, $width = NULL, $height = NULL) {
	
		if(!$width) { $width = $image->width; }
		if(!$height) { $height = $image->height; }
		
		$newImage = new Image($this);
		
		imagecopyresampled($newImage->image, $image->image, $x, $y, 0, 0, $width, $height, $image->width, $image->height);
		
		return $newImage;
	}
	
	public function cut($x, $y, $width, $height) {
		$newImage = new Image($width, $height);
		
		imagecopy($newImage->image, $this->image, 0, 0, $x, $y, $width, $height);
		
		return $newImage;
	}
	
	
	private function _createVoidImageRessource($width, $height) {
			//create new image
		$image = imagecreatetruecolor($width, $height);
		
			//add black background
		$blackColor = imagecolorallocate($image, 0, 0, 0);
		
			//add transparency
		imagealphablending($image, false);
		imagesavealpha($image, true);
		
			//create a transparent image
		$transparent = imagecreatefrompng(__DIR__ . '/../ressources/images/transparent.png');
		
			//copy transparent image tu render background transparent
		imagecopyresampled($image, $transparent, 0, 0, 0, 0, imagesx($image), imagesy($image), imagesx($transparent), imagesy($transparent));
		
		imagealphablending($image, true);
		
		return $image;
	}
	
	private function _getImageRessource($path) {
		if(preg_match('#^data:image/([a-z\-]+);base64,(.*)$#isU', $path, $matches)) {
			$data = $matches[2];
			$data = base64_decode($data);
			
			$this->type = $matches[1];
			if($this->type == 'jpeg') { $this->type = 'jpg'; }
			
			return @imagecreatefromstring($data);
			
		} else if(file_exists($path) && filesize($path) > 0) {
			list($width, $height, $type, $attr) = getimagesize($path);
			
			$mimeType = image_type_to_mime_type($type);
			
			switch($mimeType) {
				case 'image/jpeg'://'jpg','jpeg'
					$this->type = 'jpg';
					return @imagecreatefromjpeg($path);
				break;
				case 'image/png'://'png'
					$this->type = 'png';
					return @imagecreatefrompng($path);
				break;
				case 'image/gif': //'gif'
					$this->type = 'gif';
					return @imagecreatefromgif($path);
				break;
				case 'image/bmp':
				case 'image/x-ms-bmp':
				case 'image/x-windows-bmp'://'bmp'
					$this->type = 'bmp';
					if(function_exists('imagecreatefrombmp')) {
						return @imagecreatefrombmp($path);
					} else {
						return false;
					}
				break;
				default:
					return false;
			}
		} else {
			return false;
		}
	}
	
	private function _getSize() {
		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
	}

	private function _copyImage($image, $width, $height) {
		$this->image = $this->_createVoidImageRessource($width, $height);
		imagecopyresampled($this->image, $image, 0, 0, 0, 0, $width, $height, $width, $height);
	}
}

?>