<?php
require_once("../../jamp/class/system.class.php");
$system = new ClsSystem(true);
$ds = $system->newObj("ds", "ds");
$ds->setProperty("conn", "db_lmp");
$ds->ds->dsConnect();

$ds = $system->newObj("ds", "ds");
$ds->setProperty("conn", "db_lmp_master");
$ds->ds->dsConnect();
?>