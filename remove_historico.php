<?php
if (!	include "./core/config.inc.php")
	die('erro: include config');
if (!	include "./core/functions.php")
	die('erro: include function');
if (!	include "./core/dbconnect.class.php")
	die('erro: include db');
session_start();
//die(var_dump($_REQUEST));
$mysql = new dbconnect();
if ($_SESSION["tipo"] == 'ADMINISTRADOR' and $_REQUEST['idhistorico'] != "") {
	$query = " delete from historico where idhistorico = " . $_REQUEST['idhistorico'];
	$result = $mysql -> consulta($query);
	echo $query;
}?>