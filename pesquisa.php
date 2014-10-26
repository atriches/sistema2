<?php
if (!	include "./core/config.inc.php")
	die('erro: include config');
if (!	include "./core/functions.php")
	die('erro: include function');
if (!	include "./core/dbconnect.class.php")
	die('erro: include db');
session_start();
$array_campos_n_consulta = array('endereco_responsaveis', 'cep_responsaveis', 'nome_usuario', 'mudou');
$vmcomplemento = " ";
if ($_SESSION["tipo"] == 'ADM_PSICOLOGO') {
	$vmcomplemento = " and paciente.etapa = 'PSICOLOGO' ";
} else {
	$vmcomplemento = " and paciente.etapa <> 'PSICOLOGO' ";
}
$vmSql = " select paciente.`idpaciente`, paciente.`idusuario`, `idligador`, `identrevistador`, `idpsicologo`, `nome_crianca`, `sexo_crianca`, 
`data_nascimento_crianca`, `idade_crianca`, `cel_crianca`, `email`, `endereco`, `complemento`, `bairro`, `cidade_crianca`, 
`estado_crianca`, `escola_crianca`, `responsavel_direto_crianca`, `tipo_responsavel1`, `tipo_contato_resp1`, `tel_residencial_resp1`, 
`end_resp1`, `intencao_mudanca_resp1`, `novo_endereco_resp1`, `nome_resp1`, `tel_comercial_resp1`, `tel_celular_resp1`, `novo_tel_resp1`, 
`ocupacao_resp1`, `cep_resp1`, `estado_civil_resp1`, `notas1`, `outro_residencial_resp1`, `outro_comercial_resp1`, `outro_celular_resp1`, 
`tipo_responsavel2`, `tel_residencial_resp2`, `end_resp2`, `intencao_mudanca_resp2`, `novo_endereco_resp2`, `nome_resp2`, `estado_civil_resp2`,
 `conhecido_resp2`, `tipo_contato_resp2`, `tel_comercial_resp2`, `tel_celular_resp2`, `novo_tel_resp2`, `ocupacao_resp2`, `cep_resp2`, 
 `contato1_nome`, `contato2_nome`, `contato3_nome`, `contato4_nome`, `contato5_nome`, `contato1_telefone`, `contato2_telefone`, 
 `contato3_telefone`, `contato4_telefone`, `contato5_telefone`, `contato1_parentesco`, `contato2_parentesco`, `contato3_parentesco`,
  `contato4_parentesco`, `contato5_parentesco`, `anotacoes_entrevistador`, paciente.`status`, `etapa`, `qtd_tentativa_ligacoes`, 
  `qtd_tentativa_entrevista`, `qtd_tentativa_psi`, `qtd_recusa_ligacoes`, `qtd_recusa_entrevista`, `qtd_recusa_psi`, 
  `qtd_pergutas_resp30`, `qtd_pergutas_resp4`, `parecer_pscologico`, `exame_realizado`, `checagem_telefonica`, 
  `data_ultimo_contato`, `qtd_nao_atendeu`, `idquestionario` , nome
  
  from paciente  left join usuario on paciente.idusuario = usuario.idusuario left join historico on paciente.idpaciente = historico.idpaciente where 1=1 " . $vmcomplemento;
//para atribuição em massa
$vmSqlPesquisa = "";
//select paciente.idpaciente from paciente  left join usuario on paciente.idusuario = usuario.idusuario left join historico on paciente.idpaciente = historico.idpaciente where 1=1 ";
$vmArrayLinha = explode("|", $_REQUEST['dados']);
foreach ($vmArrayLinha as $key => $Consulta) {
	$arrayValores = explode(";", $Consulta);
	//die(var_dump($arrayValores));
	if ($arrayValores[0] != "") {
		$vmSql .= trataArray($arrayValores[0], $arrayValores[1], $arrayValores[2], $arrayValores[3], $array_campos_n_consulta);
		$vmSqlPesquisa .= trataArray($arrayValores[0], $arrayValores[1], $arrayValores[2], $arrayValores[3], $array_campos_n_consulta);
	}
}
$vmSql .= " group by paciente.`idpaciente`, paciente.`idusuario`, `idligador`, `identrevistador`, `idpsicologo`, `nome_crianca`, `sexo_crianca`, 
`data_nascimento_crianca`, `idade_crianca`, `cel_crianca`, `email`, `endereco`, `complemento`, `bairro`, `cidade_crianca`, 
`estado_crianca`, `escola_crianca`, `responsavel_direto_crianca`, `tipo_responsavel1`, `tipo_contato_resp1`, `tel_residencial_resp1`, 
`end_resp1`, `intencao_mudanca_resp1`, `novo_endereco_resp1`, `nome_resp1`, `tel_comercial_resp1`, `tel_celular_resp1`, `novo_tel_resp1`, 
`ocupacao_resp1`, `cep_resp1`, `estado_civil_resp1`, `notas1`, `outro_residencial_resp1`, `outro_comercial_resp1`, `outro_celular_resp1`, 
`tipo_responsavel2`, `tel_residencial_resp2`, `end_resp2`, `intencao_mudanca_resp2`, `novo_endereco_resp2`, `nome_resp2`, `estado_civil_resp2`,
 `conhecido_resp2`, `tipo_contato_resp2`, `tel_comercial_resp2`, `tel_celular_resp2`, `novo_tel_resp2`, `ocupacao_resp2`, `cep_resp2`, 
 `contato1_nome`, `contato2_nome`, `contato3_nome`, `contato4_nome`, `contato5_nome`, `contato1_telefone`, `contato2_telefone`, 
 `contato3_telefone`, `contato4_telefone`, `contato5_telefone`, `contato1_parentesco`, `contato2_parentesco`, `contato3_parentesco`,
  `contato4_parentesco`, `contato5_parentesco`, `anotacoes_entrevistador`, paciente.`status`, `etapa`, `qtd_tentativa_ligacoes`, 
  `qtd_tentativa_entrevista`, `qtd_tentativa_psi`, `qtd_recusa_ligacoes`, `qtd_recusa_entrevista`, `qtd_recusa_psi`, 
  `qtd_pergutas_resp30`, `qtd_pergutas_resp4`, `parecer_pscologico`, `exame_realizado`, `checagem_telefonica`, 
  `data_ultimo_contato`, `qtd_nao_atendeu`, `idquestionario`, nome ";
//$vmSqlPesquisa.=" group by paciente.idpaciente ";
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
			$vmsqllocal .= $operadorFinal . "  usuario.nome like '%" . $valor . "%' ";
		} elseif ($condição == "nao_contem") {
			$vmsqllocal .= $operadorFinal . "  usuario.nome not like '%" . $valor . "%' ";
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
		} elseif ($campo == 'data_ultimo_contato') {
			if ($valor != "") {
				if ($condição == 'igual' or $condição == 'contem') {
					$aux = " LIKE ";
				} elseif ($condição == 'maior') {
					$aux = " > ";
				} elseif ($condição == 'menor') {
					$aux = " < ";
				}
				if ($aux != " LIKE ") {
					list($d, $m, $a) = explode("/", $valor);
					$vmsqllocal .= $operadorFinal . "  paciente.data_ultimo_contato " . $aux . "'" . $a . "-" . $m . "-" . $d . "' ";
				} else {
					list($d, $m, $a) = explode("/", $valor);
					$vmsqllocal .= $operadorFinal . "  paciente.data_ultimo_contato " . $aux . "'%" . $a . "-" . $m . "-" . $d . "%' ";
				}
			}
		} else {
			switch ($condição) {
				case 'igual' :					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  = '" . $valor . "' ";
					break;
				case 'contem' :					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  like  '%" . $valor . "%' ";
					break;
				case 'maior' :					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  > '" . $valor . "' ";
					break;
				case 'menor' :					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  < '" . $valor . "' ";
					break;
				case 'diferente' :					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  <> '" . $valor . "' ";
					break;
				case 'nao_contem' :					$vmsqllocal .= $operadorFinal . " paciente." . $campo . "  not like  '%" . $valor . "%' ";
					break;
				default :					break;			}
		}
	}
	return $vmsqllocal;
}//die($vmSql);
$mysql = new dbconnect();
$result = $mysql -> consulta($vmSql);
$nbrows = mysql_num_rows($result);
if ($nbrows > 0) {
	$arr = Array();
	$ct = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$ar = array_keys($row);
		for ($i = 0; $i < count($ar); $i++) {
			$arr['dados'][$ct][$ar[$i]] = utf8_encode(trim($row[$ar[$i]]));
		}
		$ct++;
	}
	$arr['sql'] = $vmSqlPesquisa;
} else {
	$arr['dados'] = 0;
	$arr['sql'] = "";
}
echo utf8_encode(json_encode($arr));?>