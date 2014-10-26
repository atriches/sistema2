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

$mysql = new dbconnect();

$vmcomplemento = " ";

if ($_SESSION["tipo"] == 'ADM_PSICOLOGO') {
	$vmcomplemento = " and pa.etapa = 'PSICOLOGO' ";
} else {
	$vmcomplemento = " and pa.etapa <> 'PSICOLOGO' ";
}

$array_campos_n_consulta = array('endereco_responsaveis', 'cep_responsaveis', 'nome_usuario', 'mudou');

$vmSql = " SELECT h.idpaciente,
				   h.sql AS dados_historio,
					DATE_FORMAT( h.data,  '%d/%c/%Y %H:%i:%s' ) AS data_historico
	
	FROM historico_sql h 
	
	INNER JOIN paciente pa  ON h.idpaciente = pa.idpaciente where 1=1 " . $vmcomplemento;

function trataArray($operador, $campo, $condição, $valor, $array_checa) {

	$vmsqllocal = "";
	$operadorFinal = "";

	if ($operador == 'E' or $operador == "Pesquisar Pacientes onde:") {
		$operadorFinal = " AND ";
	} else {
		$operadorFinal = " OR ";
	}

	if ($campo == "endereco_responsaveis") {
		$vmsqllocal .= $operadorFinal . "  ( pa.end_resp1 like '%" . $valor . "%'  or pa.end_resp2 like '%" . $valor . "%') ";
	}

	if ($campo == 'cep_responsaveis') {
		$vmsqllocal .= $operadorFinal . " ( pa.cep_resp1 like '%" . $valor . "%'  or pa.cep_resp2 like '%" . $valor . "%') ";
	}

	if ($campo == 'nome_usuario') {
		if ($condição == "contem") {
			$vmsqllocal .= $operadorFinal . "   pa.idusuario in (select idusuario from usuario where  nome like '%" . $valor . "%' ) ";
		} elseif ($condição == "nao_contem") {
			$vmsqllocal .= $operadorFinal . "  pa.idusuario not in (select idusuario from usuario where  nome like '%" . $valor . "%' ) ";
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
					$vmsqllocal .= $operadorFinal . "  h.data " . $aux . "'" . $a . "-" . $m . "-" . $d . "' ";
				}
			}
		} else {
			switch ($condição) {
				case 'igual' :
					$vmsqllocal .= $operadorFinal . " pa." . $campo . "  = '" . $valor . "' ";
					break;
				case 'contem' :
					$vmsqllocal .= $operadorFinal . " pa." . $campo . "  like  '%" . $valor . "%' ";
					break;
				case 'maior' :
					$vmsqllocal .= $operadorFinal . " pa." . $campo . "  > '" . $valor . "' ";
					break;
				case 'menor' :
					$vmsqllocal .= $operadorFinal . " pa." . $campo . "  < '" . $valor . "' ";
					break;
				case 'diferente' :
					$vmsqllocal .= $operadorFinal . " pa." . $campo . "  <> '" . $valor . "' ";
					break;
				case 'nao_contem' :
					$vmsqllocal .= $operadorFinal . " pa." . $campo . "  not like  '%" . $valor . "%' ";
					break;
				default :
					break;
			}
		}
	}
	return $vmsqllocal;
}

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

//die($vmSql);
$result = $mysql -> consulta($vmSql);
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

} else {
	die("Nenhum item encontrado.");
}

$result = $mysql -> consulta($vmSql);
$nbrows = mysql_num_rows($result);


$table = '<table border ="1">
			<tr>
				<td>CONTADOR</td>
				<td>IDHISTORICO</td>
				<td>LOG HISTORICO</td>
				<td>DATA LOG HISTORICO</td>
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
?>