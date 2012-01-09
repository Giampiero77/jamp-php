<?php
/**
* Source File PHP
* @name JdbAdmin
* @author Alyx-Software Innovation <info@alyx.it>
* @version 1.4
* @copyright	Alyx Association 2008-2010
* @license GNU Public License
*/

require_once("./../../class/system.class.php");
$system = new ClsSystem(true);
$version = "1.5";

if (!isset($_SESSION["jdbadmin"]['engine']) || !isset($_SESSION["jdbadmin"]['server']) || 
	!isset($_SESSION["jdbadmin"]['port']) || !isset($_SESSION["jdbadmin"]['user']) || 
	!isset($_SESSION["jdbadmin"]['pwd'])) header("location: index.php");

if (isset($_SESSION["jdbadmin"]['lang'])) LANG::$language = $_SESSION["jdbadmin"]['lang'];

require("lang/".LANG::$language."/message.php");
$conn = $_SESSION["jdbadmin"]['engine']."|".$_SESSION["jdbadmin"]['server']."|".$_SESSION["jdbadmin"]['user']."|".$_SESSION["jdbadmin"]['pwd']."|".$_SESSION["jdbadmin"]['port'];
