<?
function erro($msg="Erro não identificado."){
	die('({"success": false,"msg":"'.str_replace(array("\r\n", "\n", "\r"),'',$msg).'"})');		
};
function erroHtml($msg="Erro não identificado."){
	die('<div class="erro">Erro: '.$msg.'</div>');		
};
function retornaErro($msg="Erro não identificado."){
	die('<div class="erro">Erro: '.$msg.'</div>');		
};
//http://stackoverflow.com/questions/2934563/how-to-decode-unicode-escape-sequences-like-u00ed-to-proper-utf-8-encoded-char
/* Para resolver problema de json passado por ajax, e nao sendo importado corretamente para mysql porq */
/* uma codificacao como "T\u00edtulo"  vira "Tu00edtulo" dentro do banco, ficando sem traducao posterior */
function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function unicode_to_utf8($t) {
	$t = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $t);
	return $t; 
}
function mes($m) {
	
	switch ((int)$m) {
		
		case 1: return "Jan";
		case 2: return "Fev";
		case 3: return "Mar";
		case 4: return "Abr";
		case 5: return "Mai";
		case 6: return "Jun";
		case 7: return "Jul";
		case 8: return "Ago";
		case 9: return "Set";
		case 10: return "Out";
		case 11: return "Nov";
		case 12: return "Dez";
		
	}
	
}
?>