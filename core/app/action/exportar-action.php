<?php

use Ifsnop\Mysqldump\Mysqldump;

include_once("core/controller/Mysqldump.php");
$fecha = date("Ymd");

$dumpSettings = array(
    'compress' => 'None',
    'no-data' => false,
    'add-drop-table' => true,
    'single-transaction' => true,
    'lock-tables' => true,
    'add-locks' => true,
    'extended-insert' => true,
    'disable-foreign-keys-check' => true,
    'skip-triggers' => false,
    'add-drop-trigger' => true,
    'databases' => true,
    'add-drop-database' => true,
    'hex-blob' => true
);

$dump = new Mysqldump('mysql:host=localhost;dbname=imacasa', 'root', 'mysql',$dumpSettings);
$dump->start("backup{$fecha}.sql");

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'."backup{$fecha}.sql".'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize("backup{$fecha}.sql"));
readfile("backup{$fecha}.sql");
exit;
?>