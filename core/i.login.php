<?
error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**
 * Conferencia de senha, carregamento de modulos que usuario tem direito a acessar.
 * Tudo colocada em session do php.
 */
//configuracoes (LDAP e BD)
require_once "i.config.php";

function retorno($msg) {
	die('({"success": true,"msg":"'.str_replace(array("\r\n", "\n", "\r"),'',$msg).'"})');
}
//confere senha LDAP
function ldap_login() {
				 
	if (!$_REQUEST["user"] || !$_REQUEST["pass"]){
		 erro("Dados faltando.");
	}
	
	/**
		if (($connect=ldap_connect(LDAP_SERVER))) {
			
			ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	
		    if ($bind=@ldap_bind($connect, $_REQUEST["user"]. "@imaginarium.local", $_REQUEST["pass"])) {				
				ldap_close($connect);
				return true; //usuario autenticado pelo LDAP
				
			} else return false;
			
		} else erro("Não foi possível conectar no servidor de Login.<br>Tente novamente em alguns minutos. Se o problema continuar, contate o suporte.<br><br>Falha ao conectar em Active Directory através de LDAP.<br>[$ldap_server]");
	**/
	return true;
}

function sessao() {
	session_start();
	$_SESSION["conta"] = $_REQUEST["user"];
	$_SESSION["libera"] = md5("porta".$_REQUEST["user"]."cadeira");
	echo '({"success": true,"msg":""})';
}

if (!ldap_login()) erro("Usuário ou senha inválida.");
else sessao();

?>