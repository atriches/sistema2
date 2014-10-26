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

function calculaIntervalo($data1, $data2 = '') {

	// se data2 for omitida, o calculo sera feito ate a data atual

	$data2 = $data2 == '' ? date("d/m/Y", mktime()) : $data2;

	// separa as datas em dia,mes e ano

	list($dia1, $mes1, $ano1) = explode("/", $data1);

	list($dia2, $mes2, $ano2) = explode("/", $data2);

	// so lembrando que o padrao eh MM/DD/AAAA

	$timestamp1 = mktime(0, 0, 0, $mes1, $dia1, $ano1);

	$timestamp2 = mktime(0, 0, 0, $mes2, $dia2, $ano2);

	// calcula a diferenca em timestamp

	$diferenca = ($timestamp1 > $timestamp2) ? ($timestamp1 - $timestamp2) : ($timestamp2 - $timestamp1);

	// retorna o calculo em anos, meses e dias

	// return (date("Y",$diferenca)-1970)." anos,".(date("m",$diferenca)-1)." meses e ".(date("d",$diferenca)-1)." dias";

	return date("Y", $diferenca) - 1970;

}

$mysql = new dbconnect();

if (@$_REQUEST['data_nascimento_crianca'] == "") {

	$vmIdade = 'NAO INFORMADO';

	$vmdatanascimento = 'NAO INFORMADO';

} else {

	$vmidade = calculaIntervalo(@$_REQUEST['data_nascimento_crianca']);

	list($d, $m, $a) = explode("/", @$_REQUEST['data_nascimento_crianca']);

	// $d = $d+1;

	$vmdatanascimento = $a . "-" . $m . "-" . $d;

}

$vmiduaurio = "  `idusuario` =  '" . $_REQUEST['idusuario'] . "', ";

if ($_REQUEST['idusuario'] == "") {

	$vmiduaurio = "";

}

//10.638, 11.903

$query = "UPDATE  paciente

           SET $vmiduaurio 

               `nome_crianca` =  '" . $_REQUEST['nome_crianca'] . "',

               `data_nascimento_crianca` =  '" . $vmdatanascimento . "',

               `idade_crianca` =  '" . $vmidade . "',

               `cel_crianca` =  '" . $_REQUEST['cel_crianca'] . "',

               `etapa` =  '" . $_REQUEST['etapa'] . "',

               `cidade_crianca` =  '" . $_REQUEST['cidade_crianca'] . "',

               `sexo_crianca` =  '" . $_REQUEST['sexo_crianca'] . "',

               `escola_crianca` =  '" . $_REQUEST['escola_crianca'] . "',

               `responsavel_direto_crianca` = '" . $_REQUEST['responsavel_direto_crianca'] . "',

               `notas1` =  '" . ($_REQUEST['notas1']) . "',

               `exame_realizado` =  '" . $_REQUEST['exame_realizado'] . "',

               `estado_crianca` =  '" . $_REQUEST['estado_crianca'] . "',

               `checagem_telefonica` =  '" . $_REQUEST['checagem_telefonica'] . "',

               `status` =  '" . $_REQUEST['status'] . "',

               `tipo_responsavel1` =  '" . $_REQUEST['tipo_responsavel1'] . "',

               `tel_residencial_resp1` =  '" . $_REQUEST['tel_residencial_resp1'] . "',

               `end_resp1` =  '" . $_REQUEST['end_resp1'] . "',

               `intencao_mudanca_resp1` =  '" . $_REQUEST['intencao_mudanca_resp1'] . "',

               `novo_endereco_resp1` =  '" . $_REQUEST['novo_endereco_resp1'] . "',

               `nome_resp1` =  '" . $_REQUEST['nome_resp1'] . "',

               `tel_comercial_resp1` =  '" . $_REQUEST['tel_comercial_resp1'] . "',

               `tel_celular_resp1` =  '" . $_REQUEST['tel_celular_resp1'] . "',

               `novo_tel_resp1` =  '" . $_REQUEST['novo_tel_resp1'] . "',

               `ocupacao_resp1` =  '" . $_REQUEST['ocupacao_resp1'] . "',

               `cep_resp1` =  '" . $_REQUEST['cep_resp1'] . "',

               `estado_civil_resp1` =  '" . $_REQUEST['estado_civil_resp1'] . "',

               `tipo_responsavel2` =  '" . $_REQUEST['tipo_responsavel2'] . "',

               `tel_residencial_resp2` =  '" . $_REQUEST['tel_residencial_resp2'] . "',

               `intencao_mudanca_resp2` =  '" . $_REQUEST['intencao_mudanca_resp2'] . "',

               `novo_endereco_resp2` =  '" . $_REQUEST['novo_endereco_resp2'] . "',

               `nome_resp2` =  '" . $_REQUEST['nome_resp2'] . "',

               `tel_comercial_resp2` =  '" . $_REQUEST['tel_comercial_resp2'] . "',

               `tel_celular_resp2` =  '" . $_REQUEST['tel_celular_resp2'] . "',

               `novo_tel_resp2` =  '" . $_REQUEST['novo_tel_resp2'] . "',

               `ocupacao_resp2` =  '" . $_REQUEST['ocupacao_resp2'] . "',

               `cep_resp2` =  '" . $_REQUEST['cep_resp2'] . "',

               `estado_civil_resp2` =  '" . $_REQUEST['estado_civil_resp2'] . "',

               `contato1_nome` =  '" . $_REQUEST['contato1_nome'] . "',

               `contato1_telefone` =  '" . $_REQUEST['contato1_telefone'] . "',

               `contato1_parentesco` =  '" . $_REQUEST['contato1_parentesco'] . "',

               `contato2_nome` =  '" . $_REQUEST['contato2_nome'] . "',

               `contato2_telefone` =  '" . $_REQUEST['contato2_telefone'] . "',

               `contato2_parentesco` =  '" . $_REQUEST['contato2_parentesco'] . "',

               `contato3_nome` =  '" . $_REQUEST['contato3_nome'] . "',

               `contato3_telefone` =  '" . $_REQUEST['contato3_telefone'] . "',

               `contato3_parentesco` =  '" . $_REQUEST['contato3_parentesco'] . "',

               `contato4_nome` =  '" . $_REQUEST['contato4_nome'] . "',

               `contato4_telefone` =  '" . $_REQUEST['contato4_telefone'] . "',

               `contato4_parentesco` =  '" . $_REQUEST['contato4_parentesco'] . "',

               `contato5_telefone` =  '" . $_REQUEST['contato5_telefone'] . "',

               `contato5_parentesco` =  '" . $_REQUEST['contato5_parentesco'] . "',

               `idquestionario` =  '" . $_REQUEST['idquestionario'] . "'

               

                WHERE  `paciente`.`idpaciente` = " . $_REQUEST['idpaciente'];

$result = $mysql -> consulta(utf8_decode($query));

$nbrows = mysql_num_rows($result);

$vmsql2 = "INSERT INTO  `paciente_sql` (`sql` ,`data` ,`idpaciente`)

            VALUES (\"" . $query . "\",  '" . date("Y-m-d h:i:s", mktime()) . "','" . $_REQUEST['idpaciente'] . "');";

$result2 = $mysql -> consulta(utf8_decode($vmsql2));

echo '<pre>';

die($query);

//echo json_encode($arr);
?>

