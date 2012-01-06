<?php	
/**
* Function saving files
* @author	Alyx Association <info@alyx.it>
* @version	1.0.1 stable
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

require_once("../class/system.class.php");
$system = new ClsSystem(true);

function before_exception_error($exception)
{
	$error = str_replace("'", "\'", $exception->param["message"]);
	sendResponse($error, "");
	return false;
}

function sendResponse($error, $newfilename)
{
	$type = $_REQUEST['classname'];
	print "<script type=\"text/javascript\" language=\"JavaScript1.5\">\n";
	if(!empty($error)) print "window.parent.$type.AfterPost(false, '".$error."', '$newfilename');\n";
	else print "window.parent.$type.AfterPost(true, '', '$newfilename');\n";
	print "</script>\n";
}

$error = $newfilename = "";

if(NOUPLOAD == false)
{
	foreach ($_FILES as $file) 
	{
		$dirname = $system->dir_real_web.$_REQUEST['directory'];
		$filename = $dirname.$file['name'];
		$filetype = $file['type'];
		if (!empty($_REQUEST['forcename']))
		{			
		  $path = pathinfo($filename);
		  $path['extension'] = (!isset($path['extension'])) ? "" : ".".strtolower($path['extension']);
		  if ($path['extension']==".jpeg") $path['extension'] = ".jpg";
		  else if ($path['extension']==".tiff") $path['extension'] = ".tif";
		  $forcename = str_replace('$$NAME$$', basename($filename, $path['extension']), $_REQUEST['forcename']);
		  $forcename = str_replace('$$TIMESTAMP$$', time(), $forcename);
		  $forcename = str_replace('$$DATEFILE$$', date("d_m_Y"), $forcename);
		  $filename = $path["dirname"]."/".$forcename.$path['extension'];
		  $newfilename = $forcename.$path['extension'];
		} 
		if (substr($file['name'], 0, 1) == ".") $error = LANG::translate("STORE001"); 
		switch($filetype)
		{
			case "application/x-php":
			case "application/javascript":
			case "application/x-perl":
				$error = LANG::translate("STORE001");
		}

		if ($file['size']==0) $error = LANG::translate("STORE002");
// 		else if (!empty($_POST['MAX_FILE_SIZE']) && $file['size'] > $_POST['MAX_FILE_SIZE']) $error = "Dimensione del file troppo grande";
		else 	
		{
			if (empty($error) && isset($_REQUEST['extension']) && !empty($_REQUEST['extension'])) 
			{
				$ctype = strtolower($file['type']);
				$authext = explode("|", strtolower($_REQUEST['extension']));
				$type = explode("/", strtolower($file['type']));
				$tipology = $type[0];
				$type = $type[1];
				if ($_REQUEST['extension']!=$tipology && !array_search($type, $authext)) $error = LANG::translate("STORE003", $filename);
			}
		}
		if (empty($error) && (!is_uploaded_file($file['tmp_name'])))
		{
			$error = LANG::translate("STORE006", $filename);
		}
		if (isset($_REQUEST["createdir"]) && ($_REQUEST["createdir"]=="true") && (file_exists($dirname) == false)) mkdir($dirname);

		if (isset($_REQUEST["rewrite"]))
		{
			if($_REQUEST["rewrite"] == "false") if (file_exists($filename) == true) $error = LANG::translate("STORE004", $filename);
			if($_REQUEST["rewrite"] == "rename")
			{
				while(file_exists($filename) == true)
				{
					$path = pathinfo($filename);
					$filename = $path["dirname"]."/".LANG::translate("STORE005", $filename).$path["basename"];
					$newfilename = "Copia_di_".$path["basename"];
				}
			}
		}
		if (empty($error))
		{
			if (!isset($_POST["dimension"]) || !is_array(getimagesize($file['tmp_name'])))
			{
				if (!copy($file['tmp_name'],$filename)) $error = LANG::translate("STORE006", $filename);
			}
			else
			{
				require_once("../class/image.class.php");
				$image = new ClsImage();
				$sfilename = $filename;
				foreach ($_POST["dimension"] as $i => $dimension)
				{
					 $rif_filename = $file['tmp_name'];
					 $filename = $sfilename;
					 $pictures = explode("|", strtolower($dimension));
					 foreach ($pictures as $y => $picture)
					 {
						  $typedim = "resize";
						  $dim = explode("x", $picture);
						  if (count($dim)==1) 
						  {
								$typedim = "cut";
								$dim = explode("-", $picture);
								$position = (!empty($dim[2])) ? $dim[2] : 0; 
						  }
						  $width = $dim[0];
						  $height = $dim[1]; 
						  if ($i>0)
						  {
								$pos = strrpos($filename, ".");
								if ($pos === false) $filename."_".$i;
								else $filename = substr($filename, 0, $pos)."_".strtolower($picture).substr($filename, $pos);
						  }
						  if ($typedim == "resize") $image->resize($rif_filename, $filename, $width, $height);
						  else if ($typedim == "cut") $image->cutImage($rif_filename, $filename, $width, $height, $position);  
						  if ($i>0 && $y>0) unlink($rif_filename);
						  $rif_filename = $filename;
					}
				}
			}
		}
	}
}
else $error = LANG::translate("STORE007");
sendResponse($error, $newfilename);
?>