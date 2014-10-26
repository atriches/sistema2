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
//die(var_dump($_REQUEST));
$mysql = new dbconnect();

$query = " update paciente set idquestionario  = '" . $_REQUEST['idquestionario'] . "' where idpaciente = " . $_REQUEST['idpaciente'];

$result = $mysql -> consulta($query);

echo $query;
?>