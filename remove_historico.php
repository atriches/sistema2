<?php
if (!
	die('erro: include config');
if (!
	die('erro: include function');
if (!
	die('erro: include db');
session_start();
//die(var_dump($_REQUEST));
$mysql = new dbconnect();
if ($_SESSION["tipo"] == 'ADMINISTRADOR' and $_REQUEST['idhistorico'] != "") {
	$query = " delete from historico where idhistorico = " . $_REQUEST['idhistorico'];
	$result = $mysql -> consulta($query);
	echo $query;
}