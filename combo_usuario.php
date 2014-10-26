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

$vmcomplemento = " ";

if ($_SESSION["tipo"] == 'ADM_PSICOLOGO') {
    $vmcomplemento = " where usuario.tipo = 'PSICOLOGO' ";
} else {
    $vmcomplemento = " where usuario.tipo <> 'PSICOLOGO' ";
}

$mysql = new dbconnect();

$query = " SELECT * FROM  usuario ".$vmcomplemento . " order by nome ";
// $query = " SELECT * FROM  `paciente` LIMIT 0 , 30 ";

$result = $mysql -> consulta($query);
$nbrows = mysql_num_rows($result);

$rows = array('data' => array());
while ($dados = mysql_fetch_assoc($result)) {
    $rows['data'][] = $dados;
}

echo json_encode($rows);
?>