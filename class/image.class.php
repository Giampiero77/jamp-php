<?php
/**
* Class IMAGE
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/
class ClsImage
{
	/**
	* Construct
	*/
	public function __construct()
	{
	}

	 public function Mime($filename)
	 {
		  $result = false;
		  if (is_file($filename) === true)
		  {
				if (function_exists('finfo_open') === true)
				{
					 $finfo = finfo_open(FILEINFO_MIME_TYPE);
					 if (is_resource($finfo) === true) $result = finfo_file($finfo, $filename);
					 finfo_close($finfo);
				}
				else if (function_exists('mime_content_type') === true)
					 $result = preg_replace('~^(.+);.*$~', '$1', mime_content_type($filename));
				else if (function_exists('exif_imagetype') === true)
					 $result = image_type_to_mime_type(exif_imagetype($filename));
		  }
		  return $result;
	 }

	/**
	* resize the value in the original formatting
	* @param string $old_image path of image
	* @param string $new_image path of new image
	* @param string $width new image width
	* @param string $height new image height
	* @return boolean Function result
	*/   
	public function resize($old_image, $new_image, $width, $height)
	{
		$width = intVal($width);
		$height = intVal($height);
		if ($width == 0 && $height == 0)
		{
			 if (!copy($old_image,$new_image)) return false;
			 return true;
		}
		$type = getimagesize($old_image);  
		list($old_width, $old_height) = $type;
		if ($height == 0) $height = floor($width/round($old_width/$old_height,3));
		else if ($width == 0) $width = floor($height/round($old_height/$old_width,3));

		$image = NULL;
		switch ($type["mime"]) 
		{
			case "image/jpeg":
				if (imagetypes() && IMG_JPG) $image = imagecreatefromjpeg($old_image);
			break;
			case "image/png":
				if (imagetypes() && IMG_PNG) $image = imagecreatefrompng($old_image);
			break;
			case "image/gif":
				if (imagetypes() && IMG_GIF) $image = imagecreatefromgif($old_image);
			break;
		}
		if ($image == NULL) return false;
		$new_res = imagecreatetruecolor($width, $height);
		if (imagecopyresampled($new_res, $image, 0, 0, 0, 0, $width, $height, $old_width, $old_height) == FALSE) return false;
		$new_est = strtolower(substr($new_image, -3));
		switch ($new_est) 
		{
			case "jpg":
				imagejpeg($new_res, $new_image);
			break;
			case "png":
				imagepng($new_res, $new_image);
			break;
			case "gif":
				imagegif($new_res, $new_image);
			break;
		}
		imagedestroy($image);
		imagedestroy($new_res);
		return true;
	}

    /**
    * resize the value in the original formatting
    * @param string $old_image path of image
    * @param string $new_image path of new image
    * @param string $width new image width
    * @param string $height new image height
    * @return boolean Function result
    */   

    public function cutImage($old_image, $new_image, $width, $height, $position = 0) 
    {
		$width = intVal($width);
		$height = intVal($height);
		if ($width == 0 && $height == 0)
		{
			 if (!copy($old_image,$new_image)) return false;
			 return true;
		}
	    $type = getimagesize($old_image); 
	    $new_est = substr($new_image, -3);
	    $image = NULL;
	    switch ($type["mime"]) 
	    {
		    case "image/jpeg":
			    if (imagetypes() && IMG_JPG) $image = imagecreatefromjpeg($old_image);
		    break;
		    case "image/png":
			    if (imagetypes() && IMG_PNG) $image = imagecreatefrompng($old_image);
		    break;
		    case "image/gif":
			    if (imagetypes() && IMG_GIF) $image = imagecreatefromgif($old_image);
		    break;
	    }
	    if ($image == NULL) return false;
	    list($old_width, $old_height) = getimagesize($old_image);
		  if ($height == 0) 		$height = $old_height;
		  else if ($width == 0) $width = $old_width;

	    $new_res = imagecreatetruecolor($width, $height);
// 		 imagecopy($dst_im,$src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
		  $left = $top = 0; // left top
		  $right = ($old_width-$width);
		  $bottom = ($old_height-$height);
		  $center = $right/2;
		  $middle = $bottom/2;
		  switch ($position)
		  {
				case "ct": // center top 
					 $left = ($center>0) ? $center : 0;
				  break;
				case "rt": // right top 
					 $left = ($right>0) ? ($right) : 0;
				  break;
				case "lm": // left middle
					 $top = ($middle>0) ? ($middle) : 0;
				  break;
				case "cm": // center middle 	  
					 $left = ($center>0) ? $center : 0;
					 $top = ($middle>0) ? ($middle) : 0;
				  break;
				case "rm": // right middle 
					 $left = ($right>0) ? ($right) : 0;
					 $top = ($middle>0) ? ($middle) : 0;
				  break;
				case "lb": // left bottom
					 $top  = ($bottom>0) ? $bottom : 0;
				  break;
				case "cb": // center bottom 
					 $left = ($center>0) ? $center : 0;
					 $top  = ($bottom>0) ? $bottom : 0;
				  break;
				case "rb": // right bottom 
					 $left = ($right>0) ? ($right) : 0;
					 $top  = ($bottom>0) ? $bottom : 0;
				  break;
		  }

	    if (imagecopy($new_res, $image, 0, 0, $left, $top, $width, $height) == false) return false;
	    switch ($new_est) 
	    {
		    case "jpg":
			    imagejpeg($new_res, $new_image);
		    break;
		    case "png":
			    imagepng($new_res, $new_image);
		    break;
		    case "gif":
			    imagegif($new_res, $new_image);
		    break;
	    }
	    imagedestroy($image);
	    imagedestroy($new_res);
	    return true;
    }
}
?>