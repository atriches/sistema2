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
//atualiza data do ultimo contato
$query = " update paciente  set data_ultimo_contato = ( SELECT datahora  FROM `historico` WHERE historico.idpaciente = paciente.idpaciente   and comentario <> 'Houve visualizacao do registro' order by historico.datahora desc limit 1) ";
$result = $mysql -> consulta($query);
//retorna os pacientes
$query2 = " update  `paciente` set status ='RECUSADO15' WHERE status = 'RECUSADO' and data_ultimo_contato <= DATE_SUB(CURRENT_DATE(), INTERVAL 15 DAY) ";
$result2 = $mysql -> consulta($query2);
//mais de 10 tentativas
$query4 = "SELECT historico.idpaciente
FROM historico
INNER JOIN paciente p ON historico.idpaciente = p.idpaciente
WHERE historico.status =  'NAO LOCALIZADO'
AND p.status =  'NAO LOCALIZADO'
GROUP BY historico.idpaciente
HAVING COUNT( * ) >9 ";
$result4 = $mysql -> consulta($query4);
$nbrows = mysql_num_rows($result4);
if ($nbrows > 0) {
	while ($row = mysql_fetch_array($result4, MYSQL_ASSOC)) {
		$query3 = " update paciente set status = 'NENHUM FONE FUNCIONA' where idpaciente 
    in ( '" . $row['idpaciente'] . "' )  ";
		$result3 = $mysql -> consulta($query3);
		// echo $query3;
	}
}
//echo $query.'<br>'.$query2.'<br>'.$query3;?>
