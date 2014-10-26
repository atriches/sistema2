<?php
if (!
	include "./core/config.inc.php")
	die('erro: include config');
if (!
	include "./core/functions.php")
	die('erro: include function');
if (!
	include "./core/dbconnect.class.php")
	die('erro: include db');
session_start();
$mysql = new dbconnect();

$vmcomplemento = " ";

if( $_SESSION["tipo"] == 'ADM_PSICOLOGO' or $_SESSION["tipo"] == 'PSICOLOGO'  ){
    $vmcomplemento = " and a.etapa = 'PSICOLOGO' ";
} else {
     $vmcomplemento = " and (a.etapa <> 'PSICOLOGO' or a.status = 'NAO INICIADO PSICOLOGO' or a.status = 'SITUACAO ESPECIFICA') ";
}

$vmwhere = " ";
$queryaux = " SELECT * 
				FROM paciente a
				 JOIN usuario b ON a.idusuario = b.idusuario ";

if ($_SESSION["tipo"] != 'ADMINISTRADOR' and $_SESSION["tipo"] != 'ADM_PSICOLOGO') {
	$vmwhere = " and a.idusuario = '".$_SESSION['idusuario']."' ";
}

$query = $queryaux.$vmwhere.$vmcomplemento;

//die($query);
$result = $mysql -> consulta($query);
$nbrows = mysql_num_rows($result);

if ($nbrows > 0) {

	$arr = Array();
	$ct = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$ar = array_keys($row);
		for ($i = 0; $i < count($ar); $i++) {

			$arr[$ct][$ar[$i]] = utf8_encode(trim($row[$ar[$i]]));

		}
		$ct++;

	}
	
} else
	die("Nenhum item encontrado.");

echo (json_encode($arr));
?>