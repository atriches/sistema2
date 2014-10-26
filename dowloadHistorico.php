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

include ("simple_html_dom.php");

session_start();

$vmcomplemento = " ";

if ($_SESSION["tipo"] == 'ADM_PSICOLOGO') {
	$vmcomplemento = " and paciente.etapa = 'PSICOLOGO' ";
} else {
	$vmcomplemento = " and paciente.etapa <> 'PSICOLOGO' ";
}

$array_campos_n_consulta = array('endereco_responsaveis', 'cep_responsaveis', 'nome_usuario', 'mudou');

$vmSql = " select  paciente.`idpaciente`,`nome_crianca`, historico.comentario, 
DATE_FORMAT( historico.datahora , '%d/%c/%Y %H:%i:%s' ) as datahora,
 usuario.nome,historico.status as hitorico_status,
historico.mudou, historico.ressonancia, historico.tentativa
  from paciente  
  left join historico on paciente.idpaciente = historico.idpaciente 
  left join usuario on historico.idusuario = usuario.idusuario 
  where 1=1 " . $vmcomplemento;

//para atribuição em massa

$vmSqlPesquisa = "";

$vmArrayLinha = explode("|", $_REQUEST['dados']);

foreach ($vmArrayLinha as $key => $Consulta) {

	$arrayValores = explode(";", $Consulta);
	//die(var_dump($arrayValores));

	if ($arrayValores[0] != "") {
		$vmSql .= trataArray($arrayValores[0], $arrayValores[1], $arrayValores[2], $arrayValores[3], $array_campos_n_consulta);
		$vmSqlPesquisa .= trataArray($arrayValores[0], $arrayValores[1], $arrayValores[2], $arrayValores[3], $array_campos_n_consulta);
	}
}

$vmSql .= " group by paciente.`idpaciente`,`nome_crianca`, historico.comentario, 
historico.datahora, usuario.nome,historico.status,historico.mudou, historico.ressonancia, 
historico.tentativa ";

//echo $vmSql;

function trataArray($operador, $campo, $condição, $valor, $array_checa) {

	$vmsqllocal = "";
	$operadorFinal = "";

	if ($operador == 'E' or $operador == "Pesquisar Pacientes onde:") {
		$operadorFinal = " AND ";
	} else {
		$operadorFinal = " OR ";
	}

	if ($campo == "endereco_responsaveis") {
		$vmsqllocal .= $operadorFinal . "  ( paciente.end_resp1 like '%" . $valor . "%'  or paciente.end_resp2 like '%" . $valor . "%') ";
	}

	if ($campo == 'cep_responsaveis') {
		$vmsqllocal .= $operadorFinal . " ( paciente.cep_resp1 like '%" . $valor . "%'  or paciente.cep_resp2 like '%" . $valor . "%') ";
	}

	if ($campo == 'nome_usuario') {
		if ($condição == "contem") {
			$vmsqllocal .= $operadorFinal . "   paciente.idusuario in (select idusuario from usuario where  nome like '%" . $valor . "%' ) ";
		} elseif ($condição == "nao_contem") {
			$vmsqllocal .= $operadorFinal . "  paciente.idusuario not in (select idusuario from usuario where  nome like '%" . $valor . "%' ) ";
		}
	}

	if ($campo == 'mudou') {
		if ($valor == 'SIM') {
			$vmsqllocal .= $operadorFinal . " historico.mudou = 1 ";
		} else {
			$vmsqllocal .= $operadorFinal . "  historico.mudou = 0 ";
		}
	}

	if (!in_array($campo, $array_checa)) {

		if ($campo == 'data_contato') {
			if ($valor != "") {
				if ($condição == 'igual') {
					$aux = " = ";
				} elseif ($condição == 'maior') {
					$aux = " > ";
				} elseif ($condição == 'menor') {
					$aux = " < ";
				}
				if ($aux != "") {
					list($d, $m, $a) = explode("/", $valor);
					$vmsqllocal .= $operadorFinal . "  historico.datahora " . $aux . "'" . $a . "-" . $m . "-" . $d . "' ";
				}
			}
		} else {
			switch ($condição) {
				case 'igual' :
					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  = '" . $valor . "' ";
					break;
				case 'contem' :
					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  like  '%" . $valor . "%' ";
					break;
				case 'maior' :
					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  > '" . $valor . "' ";
					break;
				case 'menor' :
					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  < '" . $valor . "' ";
					break;
				case 'diferente' :
					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  <> '" . $valor . "' ";
					break;
				case 'nao_contem' :
					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  not like  '%" . $valor . "%' ";
					break;
				default :
					break;
			}
		}
	}
	return $vmsqllocal;
}

$mysql = new dbconnect();
$result = $mysql -> consulta($vmSql);
$nbrows = mysql_num_rows($result);

$table = '<table border ="1">
			<tr>
				<td>CONTADOR</td>
				<td>ID</td>
				<td>NOME</td>
				<td>COMENTARIO</td>
				<td>DATAHORA</td>
				<td>NOME USUARIO</td>
				<td>STATUS</td>
				<td>MUDOU</td>
				<td>RESSONANCIA</td>
				<td>TENTATIVA</td>
			</tr>';

if ($nbrows > 0) {

	$arr = Array();
	$ct = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$table .= '<tr>';
		$table .= '<td>' . $ct . '</td>';
		$ar = array_keys($row);
		for ($i = 0; $i < count($ar); $i++) {
			$arr['dados'][$ct][$ar[$i]] = utf8_encode(trim($row[$ar[$i]]));

			$table .= '<td>' . (trim($row[$ar[$i]])) . '</td>';
		}
		$ct++;
		$table .= '</tr>';
	}
	$arr['sql'] = $vmSqlPesquisa;
} else {
	$arr['dados'] = 0;
	$arr['sql'] = "";
}

$table .= '</table>';

//$file = 'Hisorico-' . date("Y-M-D") . "-" . time() . '.xls';
//ob_start();
echo $table;
//$content = ob_get_contents();

//ob_end_clean();
//header("Expires: 0");
//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//header("Cache-Control: no-store, no-cache, must-revalidate");
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");
//header("Content-type: application/vnd.ms-excel;charset:UTF-8");
//header('Content-length: ' . strlen($content));
//header('Content-disposition: attachment; filename=' . basename($file));
//echo $content;
//exit ;

/*
 $html = str_get_html($table);

 header('Content-type: application/ms-excel');
 header('Content-Disposition: attachment; filename=historico.csv');

 $fp = fopen("php://output", "w");

 foreach ($html->find('tr') as $element) {
 $td = array();
 foreach ($element->find('td') as $row) {
 $td[] = $row -> plaintext;
 }
 fputcsv($fp, $td);
 }

 fclose($fp);

 //echo(json_encode($arr));
 */
?>