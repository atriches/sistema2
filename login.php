<?php

session_start();
session_destroy();
?>
<html>
	<head>
		<title>Sistema de Gerenciamento de Coleta de Dados</title>

		<link rel="stylesheet" type="text/css" href="./lib/extjs420/resources/css/ext-all.css" />

		<script type="text/javascript" src="./lib/extjs420/ext-all.js" charset="UTF-8"></script>
		<script type="text/javascript" src="./i.core.js" charset="UTF-8"></script>

		<script type="text/javascript">
            Ext.require(['*']);
            var constrainedWin, constrainedWin2;

            var changingImage = Ext.create('Ext.Img', {
                src : 'analysis_imagem.jpg',
                width : '400',
                height : '200',
                style : {
                    'margin-left' : '39%' ,
                    'margin-top' : '3%',
                    'overflow': 'auto',
                     'background-color': 'white'
                }

            });

            function doLogin() {
                i.wait('Carregando Sistema....');
                Ext.Ajax.request({
                    url : 'dologin.php',
                    params : {
                        login : constrainedWin.items.items[0].items.items[0].getValue(),
                        pass : constrainedWin.items.items[0].items.items[1].getValue()
                    },
                    method : 'GET',
                    success : function(response) {
                        //    i.wait().hide();
                        var text = response.responseText;

                        data = Ext.decode(text, true);

                        if (data.erroLogin == "nada") {
                            window.location = "http://analysismg.com.br/sistema/";
                            return
                        } else {
                            i.msg(data.erroLogin);
                            return
                        }

                        if (data.length > 0) {
                            return
                        } else {

                            if (/deadlock/.test(text)) {
                                i.msg('Ocorreu um erro de Deadlock: O servidor n&atilde;o conseguiu processar a informaç&atilde;o. Tente novamente');
                            } else {
                                i.msg('Ocorreu um erro: ' + text);
                            }
                        }
                        i.wait().hide();
                        //  if (callback && typeof callback != undefined)
                        // callback(me.produtoTemplate);
                    },
                    failure : function(response, opts) {
                        i.wait().hide();
                        Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                    }
                });
            }


            Ext.onReady(function() {

                var login = Ext.define('LoginForm', {
                    extend : 'Ext.form.Panel',
                    xtype : 'login-form',

                    frame : true,
                    width : '100%',
                    height : '100%',
                    layout : 'anchor',
                    constrain : true,

                    //contentEl : 'center',

                    defaultType : 'textfield',

                    items : [{
                        allowBlank : false,
                        fieldLabel : 'Login',
                        name : 'user',
                        emptyText : 'Usuario'
                    }, {
                        allowBlank : false,
                        fieldLabel : 'Senha',
                        name : 'pass',
                        emptyText : 'Senha',
                        inputType : 'password'
                    }],

                    buttons : [{
                        text : 'Login',
                        listeners : {
                            'click' : function() {
                                if (constrainedWin.items.items[0].items.items[0].getValue() != "" || constrainedWin.items.items[0].items.items[1].getValue() != "") {
                                    doLogin();
                                } else {
                                    i.msg('Informe dados de acesso');
                                }

                            }
                        }
                    }]
                });

                constrainedWin = Ext.create('Ext.Window', {
                    title : 'Informe dados de acesso',
                    width : 300,
                    height : 120,
                    closable : false,
                    // Constraining will pull the Window leftwards so that it's within the parent Window
                    constrain : true,
                    layout : 'fit',
                    items : [login]
                });

                win2 = Ext.create('widget.window', {
                    height : '100%',
                    width : '100%',
                    title : 'Sistema de Gerenciamento de Coleta de Dados',
                    closable : false,
                    plain : true,
                     style : {
                        'background-color': 'white'
                    },
                    //layout : 'hbox',
                    items : [changingImage, constrainedWin, {
                        border : false
                    }]
                });
                
                win2.show();
                constrainedWin.show();
                win2.body.setStyle('background','white');
                //constrainedWin2.show();

            });

		</script>
	</head>
	<body>
		<!-- use class="x-hide-display" to prevent a brief flicker of the content -->
		<img style="background-color: white" />
	</body>
</html>