<?php
error_reporting(0);
if (!
	include "./core/config.inc.php")
	die('erro: include config');
if (!
	include "./core/functions.php")
	die('erro: include function');
if (!
	include "./core/dbconnect.class.php")
	die('erro: include db');

$mysql = new dbconnect();

$vmcomentario = str_replace("'", "\'", $_REQUEST['comentario']);
$vmcomentario = str_replace('"', "\"", $vmcomentario);

if (@$_REQUEST['idhistorico'] != "" and $_REQUEST['idhistorico'] > 0) {
	$query = "delete from historico where idhistorico = " . @$_REQUEST['idhistorico'];
	echo($query . "<br>");
	$result = $mysql -> consulta($query);
}
list($data, $hora) = explode(" ", $_REQUEST['datahora']);
list($d, $m, $a) = explode("/", $data);

$datahora = $a . "-" . $m . "-" . $d . " " . $hora;
$tentativa = '0';
$mudou = '0';
$ressonancia = '0';
if (@$_REQUEST['tentativa'] == 'true') {
	$tentativa = '1';
}
if (@$_REQUEST['mudou'] == 'true') {
	$mudou = '1';
}

if (@$_REQUEST['ressonancia'] == 'true') {
	$ressonancia = '1';
}

$query = "INSERT INTO  historico (
                        
                        `idpaciente` ,
                        `idusuario` ,
                        `datahora` ,
                        `comentario` ,
                        `tentativa` ,
                        `status` ,
                        `mudou`,   
                        `ressonancia`
                        )
                        VALUES (
                         '" . @$_REQUEST['idpaciente'] . "',  '" . @$_REQUEST['idusuario'] . "',  '" . $datahora . "',  '" . $vmcomentario . "',  '" . $tentativa . "',  '" . @$_REQUEST['status'] . "', '" . $mudou . "', '" . $ressonancia . "');";

$result = $mysql -> consulta(utf8_decode($query));

$array_id = array('idhistorico' => mysql_insert_id());

if ($_REQUEST['status']) {
	$query2 = "update paciente set status = '" . @$_REQUEST['status'] . "' where idpaciente = " . @$_REQUEST['idpaciente'];
	$result2 = $mysql -> consulta($query2);

	$vmsql2 = "INSERT INTO  `historico_sql` (`sql` ,`data`,`idpaciente` )
            VALUES (\"" . $query . "\",  '" . date("Y-m-d h:i:s", mktime()) . "','" . $_REQUEST['idpaciente'] . "');";
	$result2 = $mysql -> consulta(utf8_decode($vmsql2));

	$vmsql3 = "INSERT INTO  `historico_sql` (`sql` ,`data` ,`idpaciente`)
            VALUES (\"" . $query2 . "\",  '" . date("Y-m-d h:i:s", mktime()) . "','" . $_REQUEST['idpaciente'] . "');";
	$result3 = $mysql -> consulta(utf8_decode($vmsql3));
}
echo json_encode($array_id);
?>
