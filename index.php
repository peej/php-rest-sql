<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);

require_once('phprestsql.php');
require_once('urlparser.php');

$PHPRestSQL = new PHPRestSQL();
$PHPRestSQL->exec();

/*
echo '<pre>';
var_dump($PHPRestSQL->output);
//*/

?>