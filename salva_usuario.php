<?php
error_reporting(0);
if (!	include "./core/config.inc.php")
	die('erro: include config');
if (!	include "./core/functions.php")
	die('erro: include function');
if (!	include "./core/dbconnect.class.php")
	die('erro: include db');
$mysql = new dbconnect();
$nbrows = 0;
$queryVerifica = "select * from usuario where idusuario = '" . @$_REQUEST['idusuario'] . "'";
$resultVerifica = $mysql -> consulta(utf8_decode($queryVerifica));
$nbrows = mysql_num_rows($resultVerifica);
if ($nbrows > 0) {
	$query = "UPDATE  `usuario` SET  
    `nome` =  '" . @$_REQUEST['nome'] . "',
    `login` =  '" . @$_REQUEST['login'] . "',
    `senha` =  '" . @$_REQUEST['senha'] . "',
    `tipo` =  '" . @$_REQUEST['tipo'] . "' 
    WHERE  `idusuario` = " . @$_REQUEST['idusuario'];
} else {
	$query = " INSERT INTO  `usuario` (
     
            `nome` ,
            `login` ,
            `senha` ,
            `tipo` 
           
            )
            VALUES ('" . @$_REQUEST['nome'] . "',  '" . @$_REQUEST['login'] . "',  '" . @$_REQUEST['senha'] . "',  '" . @$_REQUEST['tipo'] . "');";
}
//echo($query);
$result = $mysql -> consulta(utf8_decode($query));
$nbrows2 = mysql_num_rows($result);
$data['nome'] = @$_REQUEST['nome'];
$data['login'] = @$_REQUEST['login'];
$data['senha'] = @$_REQUEST['senha'];
$data['tipo'] = @$_REQUEST['tipo'];
echo curl_post('http://analysismg.com.br/sistema/usuario_remoto.php', $data);
/**
 * Send a POST requst using cURL
 * @param string $url to request
 * @param array $post values to send
 * @param array $options for cURL
 * @return string
 */
function curl_post($url, array $post = NULL, array $options = array()) {
	$defaults = array(CURLOPT_POST => 1, CURLOPT_HEADER => 0, CURLOPT_URL => $url, CURLOPT_FRESH_CONNECT => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_FORBID_REUSE => 1, CURLOPT_TIMEOUT => 4, CURLOPT_POSTFIELDS => http_build_query($post));
	$ch = curl_init();
	curl_setopt_array($ch, ($options + $defaults));
	if (!$result = curl_exec($ch)) {
		return (curl_error($ch));
	}
	curl_close($ch);
	return $result;
}?>
