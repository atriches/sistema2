
/**
 * @class i Objeto global da intranet
 * @singleton
 */
i = function() {
	//variaveis privadas	
	var version = 1;
	var config = {}
	var logCt;
	var initFuncs = [];
	//var usuario = UsuarioConta;
	// var usuarioId = UsuarioId;
	
	var dateInfo;
	
	/*
	 * Pega usuario interno, por segurança.
	 * Quando global eh carregada, pega usuario de var global.
	 * Depois disso, sempre usar i.getUser() para pegar sempre o usuario interno gravado aqui
	 */
	var getUser = function() {
		return usuario;
	};
	
	var getUserId = function() {
		return usuarioId;
	};
	/* Cria janela de log e escreve msg
	 * @params
	 * fix (boolean): true para log não sumir (hide) depois.
	 */
	var log = function (msg,show,fix) {
		if (typeof logCt == 'undefined') {
			logCt = Ext.core.DomHelper.insertFirst(document.body, {
				id: 'i_LogWnd'
			}, true);
			var b = Ext.core.DomHelper.append(logCt, '<div class="header"><div id="log-cls-btn"></div></div><div id="log-body"></div>', true);
			var btn = new Ext.Button({
				renderTo: 'log-cls-btn',
				text: 'X',
				padding: '0 3 0 3',
				handler: function(){
					logCt.slideOut('b')
				}
			})
			//logCt.alignTo(document, 'bl-bl', [10, 0]);
		}
		if (msg) {
			var m = Ext.core.DomHelper.append('log-body', '<div class="msg">' + msg + '</div>', true);
				if (show) {
				logCt.alignTo(document, 'bl-bl', [10, 0]);
				if (!fix) {
					if (!logCt.inAnim) {
						logCt.inAnim = true;
						logCt.slideIn('b').ghost("b", {
							delay: 3000,
							callback: function(){
								logCt.inAnim = false;
							},
							remove: false
						});
					}
				}
				else 
					logCt.slideIn('b')
			}
		}
		return {
			show: function(){
				logCt.show()
			},
			hide: function(){
				logCt.slideOut('b')
			},
			clear: function(){
				Ext.fly('log-body').update()
			}
		}
	};
	
	function reais(v) {
		return Ext.util.Format.currency(v);
	}
	
	function mes(v) {
		
		switch (v) {
		
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
	/*
	 * Se nao existir console (FF ou Chrome), usar o do site
	 */
	if (!window.console) {
		console = {
			log: log
		}
	}
	
	/* @funcao: armazena acoes para serem executadas apenas quando todo o site estiver carregado (onReady)
	 * @param: (func) funcao para ser executada depois
	 * @args: Array com argumentos da função. Ex:
	 * 
	 * var teste = function(msg1, msg2) {
	 * 		alert('A: ' + msg1 + ' B: ' + msg2);
	 * }
	 * 
	 * i.postExec(teste,['teste 1','teste 2'])
	 * 
	 */
	function saveInitFunctions(func,args) {		
		var p = {
			func: func,
			args: args
		}
		initFuncs.push(p);
	}
	function execInitFunction(f){
		f.func.apply(this,f.args);
	}
	//passa por cada acao guardada e executa
	function loopInitFunction() {
		if (initFuncs.length > 0) {
			for (var v = 0; v < initFuncs.length; v++) {				
				if (typeof initFuncs[v].func == 'function') {
					execInitFunction(initFuncs[v])
					//initFuncs[v].func.apply(this,initFuncs[v].args);
				}
			}
		}
	}
	/* @funcao: tradutor de URL
	 * config.URLparams : guarda "REQUESTS" da URL
	 * config.URLpath : guarda caminho para modulos e telas
	 */
	function readURI() {
		var u = Ext.getHead().dom.ownerDocument.URL
		if (u.indexOf('?') > 0) {
			if (u.indexOf('#') > 0) config.URLparams = Ext.urlDecode(u.slice(u.indexOf('?')+1,u.indexOf('#')))
			else config.URLparams = Ext.urlDecode(u.slice(u.indexOf('?')+1))
		}
		if (u.indexOf('#') > 0) {
			var p = u.slice(u.indexOf('#')+1).split("/")
			var path=[]
			Ext.each(p,function(a){
				if (a!="") path.push(a)
			})
			config.URLpath = path
		}
	}
	readURI()
	
	
	/**
	 * Janela padrao com barra de carregamento de dados
	 * 
	 * i.wait('mensagem') - cria janela
	 * i.wait().hide() - esconde a janela
	 */
	function waitWindow(msg) {
		if (msg) {
			Ext.MessageBox.show({
				title: 'Carregando',
				msg: msg,
				progressText: 'Aguarde...',
				width:300,
				y: 50, //nao posicionando. Bug?
				closable: false,
				wait:true,
				waitConfig: {interval:200}           
			});
			// Ext.MessageBox.setPagePosition(this.x,50,1000) //janela indo para a posicao correta (com animacao)
		}
		return {
			hide: function() {
				Ext.MessageBox.hide()
			}
		}
	}
	/**
	 * Janela padrao de mensagem
	 */
	function msgWindow(msg) {
		Ext.MessageBox.show({
           title: 'Atenção',
           msg: msg,
           buttons: Ext.MessageBox.OK,
           icon: Ext.MessageBox.INFO
       });
	}
	/**
	 * Alterador de data
	 */
	function _format_date(unix_timestamp,data_atual) {
	  var difference_in_seconds = (data_atual/1000) - (unix_timestamp/1000),
	      current_date = new Date(unix_timestamp), minutes, hours, days,
	      months = new Array(
	        'Janeiro','Fevereiro','Março','Abril','Maio',
	        'Junho','Julho','Agosto','Setembro','Outubro',
	        'Novembro','Dezembro');
	  
	  if(difference_in_seconds < 60) {
	    return " há " + difference_in_seconds + " segundo" + _plural(difference_in_seconds) + "";
	  } else if (difference_in_seconds < 60*60) {
	    minutes = Math.floor(difference_in_seconds/60);
	    return " há " +minutes + " minuto" + _plural(minutes) + "";
	  } else if (difference_in_seconds < 60*60*24) {
	    hours = Math.floor(difference_in_seconds/60/60);
	    return " há " +hours + " hora" + _plural(hours) + "";
	  } else if (difference_in_seconds > 60*60*24){
	  	  	if(current_date.getYear() !== new Date().getYear())
	  	  		return Ext.Date.format(current_date, 'd') + " " + months[current_date.getMonth()].substr(0,3) + " " + _fourdigits(current_date.getYear()); 
	  	
	  	days = Math.floor(difference_in_seconds/60/60/24);
	  	return" há " + days + " dia" + _plural(days) + "";
	    // if(current_date.getYear() !== new Date().getYear()) 
	      // return Ext.Date.format(current_date, 'd') + " " + months[current_date.getMonth()].substr(0,3) + " " + _fourdigits(current_date.getYear());
// 	    
	    // return Ext.Date.format(current_date, 'd') + " " + months[current_date.getMonth()].substr(0,3);
	  }
	  
	  return difference_in_seconds;
	  
	  function _fourdigits(number)	{
	        return (number < 1000) ? number + 1900 : number;}
	
	  function _plural(number) {
	    if(parseInt(number) === 1) {
	      return "";
	    }
	    return "s";
	  }
	}
	/**
	 * Passa por cada span com classe dateinfo e faz alteracao de data
	 */
	function updateDateInfo() {
		
		Ext.Ajax.request({
		    url: 'app/store/tarefas/acompanhamento/data/datetime.php',
		    method: 'GET',
		    success: function(response){
		        var text = response.responseText;
								
				var data_atual = text;
				//passa por cada span que possui classe dateinfo para atualizacao
				Ext.query(".dateinfo").forEach(function(o) {
					var c = Ext.get(o);
					var atr = c.dom.attributes;
					
					//pega atributos do dom e procura pelo atributo idtask, que guarda o id dessa tarefa
					for (x=0; x<atr.length; x++) {
						if (atr[x].name=='dateinfo') var data_str = parseInt(atr[x].value);
					}
					
					Ext.fly(o).update(_format_date(data_str,data_atual) + ' (' + Ext.Date.format(new Date(data_str), 'd/m/Y H:i:s') + 'h)')
					
					// console.log(data_str + ' : ' + _format_date(Math.round(data.getTime()/1000)) + ' (' + Ext.Date.format(data, 'd/m/Y h:i:s') + 'h)')
					
				})
				
		    },
		    failure: function(response, opts) {
		        Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
		    }
		});
		
	}
	/**
	 * Aplica alteracao de datas em todo o site, a cada X segundos
	 */
	function autoUpdateDateInfo(t) {
		
		//aplicando interval
		
		//update de datas a cada 60s
		if (t==true){
			dateInfo = window.clearInterval(dateInfo);
			dateInfo = window.setInterval("i.updateDateInfo()",60000)
		}
		else dateInfo = window.clearInterval(dateInfo);
		
	}
	/**
	 * Tornando publico as informacoes apenas necessarias
	 */	
	return {
		version: version,
		getUser: getUser,
		getUserId:getUserId,
		mes: mes,
		main: {},
		ux: {},
		log: log,
		reais: reais,
		postExec: saveInitFunctions,
		loopInitFunction: loopInitFunction,
		wait: waitWindow,
		msg: msgWindow,
		updateDateInfo: updateDateInfo,
		autoUpdateDateInfo: autoUpdateDateInfo
		
	}
	//eo publicacao
}();
