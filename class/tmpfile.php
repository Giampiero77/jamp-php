<?php
/**
* Page to read files
* @author	Alyx Association <info@alyx.it>
* @version	Factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

if (isset($_GET['filename'])) 
{
	require_once("system.class.php");
	$system = new ClsSystem(true);	
	$filename = $system->dir_real_jamp."/".$system->dir_tmp.str_replace("..", "", preg_replace('/[^a-zA-Z0-9\-_\.]/','', $_GET['filename']));
	if (!file_exists($filename)) die("File not found.");
    $file_extension = strtolower(substr(strrchr($filename,"."),1));
    switch ($file_extension) 
	{
        case "html":
        case "htm": $ctype="application/html"; break;
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpe": case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        default: $ctype="application/force-download";
    }
    if ($ctype!="application/html") header("Content-Type: $ctype");
    @readfile("$filename") or die("File not found.");
    @unlink("$filename");
}
?>