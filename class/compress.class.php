<?php
/**
* Class Compress
* @author	Alyx Association <info@alyx.it>
* @version	2.1.0_factory
* @package	Class
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
* You can find documentation and sourcecode on the JAMP official website
* http://jamp.alyx.it/
*/

class clsCompress
{	

	/**
	* Construct
	*/
	public function __construct()
	{
		extension_loaded('zlib') or ClsError::showError("FILE000");
		$this->HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"];
	}

	/**
	* Create a compressed file from the string $value
	* @param string $filename Filename
	* @param string $value To compress data
	* @param string $type Compression type
	* @return Path of the compressed file
	*/
	public function Save($filename, $value, $type = 'gz')
	{
		$filezp = $filename.".".$type;
		switch ($type)
		{
			case "gz":
					$zp = gzopen($filezp, "w9") or ClsError::showError("FILE001");
					gzwrite($zp, $value);
					gzclose($zp);
				break;
			case "bz2":
					$zp = bzopen($filezp, "w") or ClsError::showError("FILE001");
					bzwrite($zp, $value);
					bzclose($zp);
				break;
			case "zip":
					$zip = new ZipArchive;
					$zp = $zip->open($filezp, ZipArchive::OVERWRITE) or ClsError::showError("FILE001");
					$zip->addFromString($filename, $value);
					$zip->close();
				break;
			default:
				ClsError::showError("FILE001");
		}
		return $filezp;
	}

	/**
	* Decompress a compressed file
	* @param string $filezp Path of the compressed file
	* @param string $type Compression type
	* @return Decompressed data
	*/
	public function Read($filezp, $type = 'gz')
	{
		switch ($type)
		{
			case "gz":
					$zp = gzopen($filezp, "r") or ClsError::showError("FILE001");
					$ctx = gzread($zp, 3);
					gzpassthru($zp);
					gzclose($zp);
				break;
			case "bz2":
					$zp = bzopen($filezp, "r") or ClsError::showError("FILE001");
					$ctx = bzread($zp, 10);
					$ctx .= bzread($zp);
					bzclose($zp);
				break;
			case "zip":
					$zip = new ZipArchive;
					$zp = $zip->open($filezp) or ClsError::showError("FILE001");
					$ctx = $zip->getFromName(str_replace(".zip", "", $filezp));
					$zip->close();
				break;
			default:
				ClsError::showError("FILE001");
		}
		return $ctx;
	}

	/**
	* Downloads a compressed file
	* @param string $filezp Path of the compressed file
	* @return File
	*/
	public function Download($filezp) 
	{
 		header('Content-Disposition: attachment; filename="'.basename($filezp).'"');
	 	$lines = file($filezp) or ClsError::showError("FILE001");
		foreach ($lines as $line) print $line;
	}

	/**
	* Compress a request HTML
	* @param string $contents To compress data
	* @return File
	*/
	public function gzPost($contents) 
	{
		$encoding = false;
		if (headers_sent()) $encoding = false;
		else if (strpos($this->HTTP_ACCEPT_ENCODING, 'x-gzip') !== false) $encoding = 'x-gzip';
		else if (strpos($this->HTTP_ACCEPT_ENCODING,'gzip') !== false) $encoding = 'gzip';
		if ($encoding)
		{
 			header('Content-Encoding: '.$encoding);
 			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$size = strlen($contents);
			$contents = gzcompress($contents, 9);
 			$contents = substr($contents, 0, $size);
		}
		return $contents;
	}
}
?>