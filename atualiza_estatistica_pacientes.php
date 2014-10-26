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
    
    //die(var_dump($_REQUEST));
    
   $mysql = new dbconnect();
    
    $vmcampo =    $_REQUEST['campo'];
     
    $query = "UPDATE paciente SET $vmcampo = $vmcampo +1 WHERE idpaciente = " . $_REQUEST['idpaciente'];
   
    
    $result = $mysql->consulta($query);
  
  die($query);
    
 /** 
  * Estatísticas Ligador:
Tentativa de Contato - Soma + 1 toda vez que um comentário é salvo e/ou marcado "NÃO ATENDEU"
Não atendeu - Soma + 1 toda vez que um comentário é marcado "NÃO ATENDEU"
Recusa - Soma +1 toda vez que é marcado RECUSA.
Questionário Recusa Completo - só pode ser igual ao zero ou um. Soma +1 quanto responder ao questionário recusa completo.
Questionário Recusa Inicial - só pode ser igual ao zero ou um. Soma +1 quanto responder ao questionário recusa Inicial.

Estatísticas Entrevistador: 
Tentativa de Contato - Soma + 1 toda vez que um comentário é salvo e/ou marcado "NÃO ATENDEU"
Não atendeu - Soma + 1 toda vez que um comentário é marcado "NÃO ATENDEU"
Tentativa de entrevista - Soma + 1 toda vez que um comentário é salvo com status "Visita Realizada" e o status 
  * "Entrevista realizada" não for marcado (*Criar Campo no Histórico de Contato)
Recusa - Soma +1 toda vez que é marcado RECUSA.

Não precisa haver Estatísticas para o Psicólogo. Vamos fornecer somente o Histórico do contato para que se possa acrescentar 
  * alguma informação e o status de "Entrevista Realizada".
  */
  
?>