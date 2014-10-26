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

$vmcomplemento = " ";
$statusHistorico = "";

$_REQUEST['sql'] = str_replace("\\", "", $_REQUEST['sql']);

if ($_SESSION["tipo"] == 'ADM_PSICOLOGO') {
    $vmcomplemento = " and paciente.etapa = 'PSICOLOGO' ";
} else {
    $vmcomplemento = " and paciente.etapa <> 'PSICOLOGO' ";
}

if ($_REQUEST['sql'] != "") {
    $mysql = new dbconnect();
    $mysql2 = new dbconnect();
    $mysql3 = new dbconnect();

    $vmsql = " update paciente  left join usuario  on paciente.idusuario = usuario.idusuario left join historico on paciente.idpaciente = historico.idpaciente set ";

    if (@$_REQUEST['idusuario'] != "") {
        $vmsql .= " paciente.idusuario = " . $_REQUEST['idusuario'] . " ,";
    }

    if (@$_REQUEST['etapa'] != "") {
        $vmsql .= " paciente.etapa = '" . $_REQUEST['etapa'] . "' ,";
    }

    if (@$_REQUEST['exame_realizado'] != "") {
        $vmsql .= " paciente.exame_realizado = '" . $_REQUEST['exame_realizado'] . "' ,";
    }

    if (@$_REQUEST['checagem_tel'] != "") {
        $vmsql .= " paciente.checagem_telefonica = '" . $_REQUEST['checagem_tel'] . "' ,";
    }

    if (@$_REQUEST['status'] != "") {
        $vmsql .= " paciente.status = '" . $_REQUEST['status'] . "' ,";
        $statusHistorico = $_REQUEST['status'];
    }

    $vmsql = substr($vmsql, 0, strlen($vmsql) - 1);

    $vmsql .= " where 1=1   " . $vmcomplemento . $_REQUEST['sql'];


    //echo($vmsql);

    //atualização do historico

    $vmsqlHistoricoAux = "select DISTINCT  paciente.idpaciente from paciente 
     left join usuario  on paciente.idusuario = usuario.idusuario 
     left join historico on paciente.idpaciente = historico.idpaciente  where 1=1 " . $vmcomplemento . $_REQUEST['sql'];


   //consuta do historico antes do prontuario
    $result2 = $mysql2 -> consulta($vmsqlHistoricoAux);
	
	//aqui a consuta do sql anterior
	$result = $mysql -> consulta($vmsql);
	
    
    $nbrows2 = mysql_num_rows($result2);

     // echo('<br>' . $vmsqlHistoricoAux);

    //if ($nbrows2 > 0) {

    $vmsqlHistorico = "INSERT INTO `historico`  (`idpaciente`, `idusuario`, `datahora`, `comentario`, `status`) ";

    $arr = Array();

    while ($row = mysql_fetch_array($result2, MYSQL_ASSOC)) {

        if ($statusHistorico != "") {
            $vmsqlHistorico2 = "";
            $vmsqlHistorico2 .= " VALUES ('" . $row['idpaciente'] . "', '" . $_REQUEST['usuario_logado'] .  "', ' " . date("Y-m-d H:i:s") . "', 'Processamento em Massa realizado','" . $statusHistorico . "') ";
            echo $vmsqlHistorico . $vmsqlHistorico2 . "<br>";
            $result3 = $mysql3 -> consulta($vmsqlHistorico . $vmsqlHistorico2);
        } else {
            $vmsqlHistorico2 = "";
            $vmsqlHistorico2 = " select " . $row['idpaciente'] . ",'" . $_REQUEST['usuario_logado'] .  "','" . date("Y-m-d H:i:s") . "','Processamento em Massa realizado', status from paciente where idpaciente =  " . $row['idpaciente'];
            echo $vmsqlHistorico . $vmsqlHistorico2 . "<br>";
            $result3 = $mysql3 -> consulta($vmsqlHistorico . $vmsqlHistorico2);
        }

    }

    $mysql -> close();
    $mysql2 -> close();
    $mysql3 -> close();
    // } else
    //  die("Nenhum item encontrado.");

}
?>