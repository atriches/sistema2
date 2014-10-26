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
$vmsqlcomplemento = "";
if ($_SESSION["tipo"] == 'ENTREVISTADOR') {

    $vmsqlcomplemento = " and a.status <> 'NAO LOCALIZADO' ";
}

//if ($_SESSION["tipo"] == 'PSICOLOGO') {

  //  $vmsqlcomplemento = " and a.idusuario =  '".$_SESSION["idusuario"]."' " ;
//}

$query = " SELECT a.idhistorico, b.nome, a.comentario, a.idpaciente, DATE_FORMAT(a.datahora,'%d/%m/%Y %H:%i:%s') as datahora, 
    a.tentativa,a.status,a.idhistorico  , a.idusuario ,
    DATE_FORMAT(a.melhor_horario_contato,'%d/%m/%Y %H:%i:%s') as melhor_horario_contato, responsavel_contato,mudou ,ressonancia  
    FROM `historico` a inner join usuario b on a.idusuario =  b.idusuario 
    where a.idpaciente = " . $_REQUEST['idpaciente'] . $vmsqlcomplemento;
// echo $query;

$result = $mysql -> consulta($query);
$nbrows = mysql_num_rows($result);

$rows = array('data' => array());
while ($dados = mysql_fetch_assoc($result)) {

    foreach ($dados as $key => $dado) {
        $vmretorna[$key] = utf8_encode(trim($dado));
    }
    $rows['data'][] = $vmretorna;
    //die(var_dump($dados));
}

echo json_encode($rows);
?>