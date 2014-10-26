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

$mysql = new dbconnect();

@$query = "SELECT * FROM  usuario  WHERE login =  '" . $_REQUEST['login'] . "'";
//echo $query;
$result = $mysql -> consulta($query);
$nbrows = mysql_num_rows($result);
$arr = Array();
if ($nbrows > 0) {

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		if ($row['senha'] == $_REQUEST['pass']) {
			session_start();
			$_SESSION['idusuario'] = $row['idusuario'];
			$_SESSION['usuario'] = $row['nome'];
			$_SESSION['tipo'] = $row['tipo'];
			$arr['erroLogin'] = 'nada';
		} else {
			$arr['erroLogin'] = 'Usuario ou senha invalidos';
		}

	}

} else {
	$arr['erroLogin'] = 'Usuario ou senha invalidos';
}

echo json_encode($arr);
?>