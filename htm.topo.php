<?php
/***** CALCULO TOTAL DO CARRINHO - INICIO *****/

$total	= 0;

if ($carrinho->getQtdItens() > 0) {
	
	foreach ($carrinho->getProdsInCart() as $ProTamCor) {
		// $preco = getFieldDBI('CONVERT(NUMERIC(10,2),(B.PRECO_TAB_17) - ((B.PRECO_TAB_17)*(F.DESCONTO_ITEM/100))) AS TAB_17', 'produtos a
					// JOIN wimg_produtos_precos b on a.produto=b.produto 
					// JOIN W_ESTOQUE_DISPONIVEL c on a.produto=c.produto
					// LEFT JOIN produtos_foto d on a.produto=d.produto
					// INNER JOIN FORMA_PGTO F ON F.CONDICAO_ESPECIAL = 0', "1=1
						// and c.qtde_d > 0
						// AND F.CONDICAO_PGTO = '205'
						// AND a.PRODUTO = '".$ProTamCor."'");
        
        $cnpj = $Cliente->getClienteCNPJ();
        $preco = getFieldDBI('CONVERT(NUMERIC(10,2),(B.PRECO1) - ((B.PRECO1)*(F.DESCONTO_ITEM/100))) AS TAB_17', 'produtos a
                    INNER JOIN CADASTRO_CLI_FOR AS cl ON cl.CGC_CPF = \''.$cnpj.'\'
                    INNER JOIN IMG_TAB_PRECO_UF AS pr ON pr.uf = cl.uf AND PR.GRIFFE = \'LUDI\'
                    INNER JOIN PRODUTOS_PRECOS AS b ON b.CODIGO_TAB_PRECO = pr.CODIGO_TAB_PRECO and b.produto = a.produto
                    INNER JOIN FORMA_PGTO F ON F.CONDICAO_ESPECIAL = 0 AND F.CONDICAO_PGTO = \'205\'
                    JOIN (SELECT PRODUTO, QTDE_D, D1 FROM W_ESTOQUE_DISPONIVEL WHERE FILIAL=\'IMAGINARIUM SAO JOSE\') c on c.produto=a.produto
                    ', "1=1
                        and c.qtde_d > 0
                        AND a.PRODUTO = '".$ProTamCor."'");
        
		$total += $carrinho->getQtdItem($ProTamCor) * $preco;
		
		//if ($_GET['teste']=='t')
		//	echo $ProTamCor." - " . $preco ."<br>";
	}
}
/***** CALCULO TOTAL DO CARRINHO - FIM *****/
?>
<div class="logo"><a href="<?=($Cliente->getSession('LOGADO') ? '/loja/' : '/pt_br/')?>index.php" title="LUDI"><img src="../shared/img/logo.gif" width="141" height="141" class="LUDI" /></a><h1>LUDI</h1></div>
<div id="navega-topo">
    <div id="bem-vindo">
    	<?='<strong>Seja bem vindo(a)'.($Cliente->getSession('LOGADO') ? ' <strong>'.cortaNome(getField('cliRaz', 'nws_cliente', 'cliCod = '.$Cliente->getSession('COD'))).'</strong>,<a href="loja.logoff.php">Sair</a>' : ',<a href="autentica.php">fa�a seu login</a></strong>');?> 
    </div>
    <span id="_selo_mm" style="z-index:1000;display:block;position:fixed;width:182px;height:174px;bottom:10px;right:10px;">
		<span style='position:absolute;top:0;right:0;display:block;padding:10px;background:white;color:black;cursor:pointer;' onclick="document.getElementById('_selo_mm').style.display='none'">x</span>
		<a href="http://www.imaginarium.com.br/namedida" title="Conhe&ccedil;a o projeto MultiMarcas" target="_blank"><img src="/selo_mm.png" width="182" height="174" alt="http://www.imaginarium.com.br/namedida"></a>
	</span>

    <div id="menu-topo">
        <ul>
            <li class="first"><a href="<?=($Cliente->getSession('LOGADO') ? '/loja/' : '/pt_br/')?>index.php">HOME</a> </li>
            <li><a href="loja.cadastro.php"><?=($Cliente->getSession('LOGADO') ? 'MEUS DADOS' : 'CADASTRE-SE')?></a> </li>
            <li><a href="contato.php">CONTATO</a> </li>
            <?=($Cliente->getSession('LOGADO') ? '<li>&nbsp;<a href="../loja/politica_comercial.pdf" target="_blank">POL. COMERCIAL</a> </li><li class="last destaque">&nbsp;<a href="entrega.pdf" target="_blank">ENTREGA</a>&nbsp;</li><li><a href="complementares.pdf" alt="Informa&ccedil;&otilde;es Complementares" target="_blank"">INF. COMPLEM.</a></li>' : '')?>
            
        </ul>
    </div>
    <div id="busca-topo">
        <form id="FormPesquisa" name="FormPesquisa" method="get" action="../loja/produtos.php?">
            <table border="0" cellspacing="4" cellpadding="0">
                <tr>
                    <td>	
                    	<select name="cid" id="cid">
                    		<option value="0" style="color:#333">Todos os departamentos</option>
                    		<?=getOptionCategorias();?>
                    	</select>
                    </td>
                    <td><input name="s_text" type="text" id="s_text" size="10" value="<?=cg('s_text');?>" /></td>
                    <td><input type="submit" name="Pesquisa" id="btpesquisa" value="Pesquisa" class="bt-pesquisa" /></td>
                </tr>
            </table>
        </form>
    </div>
    <div id="carrinho-compras">
        <div class="itens-sacola">
        <a href="loja.carrinho.php" style="font-weight:normal"><strong><?=$carrinho->getQtdItens()?></strong> Item(ns) na sacola<br />
        <strong>R$ <?=number_br($total,2)?></strong></a>
        </div>
    </div>
</div>