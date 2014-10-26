<?php

if (isset($_REQUEST["usuario"])) {
    session_start();
    $_SESSION["usuario"] = $_REQUEST["usuario"];
    $_SESSION['idusuario'] = $_REQUEST['idusuario'];
    $_SESSION["tipo"] = $_REQUEST["tipo"];

}
?>
<html>
    <head>
        <title>Sistema de Gerenciamento de Coleta de Dados</title>
        <link rel="stylesheet" type="text/css" href="../lib/extjs420/resources/css/ext-all.css" />
        <script type="text/javascript" src="../lib/extjs420/ext-all.js" charset="UTF-8"></script>
        <script type="text/javascript" src="../lib/exporter/swfobject.js" charset="UTF-8"></script>
        <script type="text/javascript" src="../lib/exporter/downloadify.min.js" charset="UTF-8"></script>
        <script type="text/javascript" src="../lib/exporter/Button.js" charset="UTF-8"></script>
        <script type="text/javascript" src="../lib/exporter/Exporter.js" charset="UTF-8"></script>

        <script type="text/javascript" src="./i.core.js" charset="UTF-8"></script>
        <script type="text/javascript">
            Ext.require(['*']);
            Ext.Loader.setConfig({
                enabled : true
            });
            Ext.Loader.setPath('Ext.ux.exporter', '../lib/exporter');
            usuario =   '<?=@$_SESSION["usuario"]?>';            
            idusuario = '<?=@$_SESSION['idusuario']?>';             
            tipo =      '<?=@$_SESSION["tipo"]?>';
            console.log(tipo);
            console.log(usuario);
            trava = false;
            gridAtual = "";
            statusatual = "";
            mvidusuario = "";
            janelahistorio = "";
            janelausuario = "";
            janelaConsulta = "";
            grid = "";
            verificaedicao = "";
            sqlPesquisa = "";
            sqlPesquisaHistorico = "";
            StatusDataCombo = "";
            ArrayStore = new Array();
            id = 0;
            Ext.onReady(function() {
                Ext.QuickTips.init();
                Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
                function maskFone(obj) {
                    //console.log(obj);
                    var v = '';
                    v = obj.getValue();
                    if (v.length > 0) {
                        var max = 14;
                        v = v.substring(0, max);
                        v = v.replace(/\D/g, "");
                        v = v.replace(/(\d{0})(\d)/, "$1($2");
                        v = v.replace(/(\d{2})(\d)/, "$1)$2");
                        v = v.replace(/(\d{4})(\d{1,4})$/, "$1-$2");
                    }
                    return v;
                }
                
                function GridUsuarios() {
                    var usuario = Ext.define('Usuario', {
                        extend : 'Ext.data.Model',
                        fields : [{
                            name : 'nome',
                            type : 'string'
                        }, {
                            name : 'login',
                            type : 'string'
                        }, {
                            name : 'senha',
                            type : 'string'
                        }, {
                            name : 'tipo',
                            type : 'string'
                        }, {
                            name : 'idusuario',
                            type : 'string'
                        }]
                    });
                    storeUsuario = Ext.create('Ext.data.Store', {
                        extend : 'Ext.data.Store',
                        model : usuario,
                        proxy : {
                            type : 'ajax',
                            url : 'consulta_usuarios.php',
                            reader : {
                                type : 'json',
                                root : 'data',
                            }
                        },
                        //    data : data2,
                        sorters : [{
                            property : 'start',
                            direction : 'ASC'
                        }]
                    });
                   
                    storeUsuario.load();
                   
                    rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                        clicksToMoveEditor : 2,
                        autoCancel : false
                    });
        
                    gridUsuario = Ext.create('Ext.grid.Panel', {
                        store : storeUsuario,
                        height : '100%',
                        width : '100%',
                        columns : [{
                            header : 'Cod',
                            dataIndex : 'idusuario',
                            name : 'Cod',
                            flex : 1,
                            hidden : true
                           
                        }, {
                            header : 'Usuario',
                            dataIndex : 'nome',
                            name : 'nome',
                            flex : 2,
                            editor : {
                                xtype : 'textfield',
                                allowBlank : false,
                                readOnly : false
                            }
                            
                        }, {
                            header : 'Login',
                            dataIndex : 'login',
                            name : 'login',
                            flex : 2,
                            editor : {
                                xtype : 'textfield',
                                allowBlank : false,
                                readOnly : false
                            }
                            
                        },{
                            header : 'Senha',
                            dataIndex : 'senha',
                            name : 'senha',
                            flex : 2,
                            editor : {
                                xtype : 'textfield',
                                allowBlank : false,
                                readOnly : false
                            }
                            
                        },{
                            header : 'Tipo',
                            dataIndex : 'tipo',
                            name : 'tipo',
                            flex : 2,
                            editor : {
                                xtype : 'textfield',
                                allowBlank : false,
                                readOnly : false
                            }
                            
                        }],
                        width : '100%',
                        height : '100%',
                        tbar : [{
                            text : 'Adicionar',
                            icon : 'http://analysismg.com.br/sistema/Add2.ico',
                            scale : 'large',
                            handler : function() {
                               rowEditing.cancelEdit();
                                // Create a model instance
                                r = Ext.create('Usuario', {
                                    nome : 'Nome do usuario',
                                    login : 'Login do usuario',
                                    senha : 'Senha do usuario',
                                    tipo : 'Tipo do usuario'
                                  
                                });
                                this.up('grid').getStore().insert(0, r);
                                rowEditing.startEdit(0, 0);
                                verificaedicao = true;
                            }
                        }],
                        plugins : [rowEditing],
                        listeners : {

                            'canceledit' : function() {
                                if (verificaedicao) {
                                    rowEditing.cancelEdit();
                                    this.getStore().remove(r);
                                }
                            },
                            'celldblclick' : function() {
                                if (tipo != "ADMINISTRADOR" ) {
                                    return false
                                }
                            },
                            'edit' : function(editor, e) {
                                var meUser = this;
                                 i.wait('Executando...');
                                 Ext.Ajax.request({
                                    url : 'salva_usuario.php',
                                    params : {
                                        idusuario : meUser.getSelectionModel().selected.items[0].data.idusuario,
                                        nome: meUser.getSelectionModel().selected.items[0].data.nome, 
                                        login: meUser.getSelectionModel().selected.items[0].data.login,
                                        senha :meUser.getSelectionModel().selected.items[0].data.senha, 
                                        tipo: meUser.getSelectionModel().selected.items[0].data.tipo
                                    },
                                    method : 'GET',
                                    success : function(response) {
                                        trava = false;
                                        verificaedicao = false;
                                        
                                    },
                                    failure : function(response, opts) {
                                        
                                        Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                                    }
                                });    
                                i.wait().hide(); 

                            }
                           
                        }
                    });
                    Ext.grid.RowEditor.prototype.saveBtnText = 'Salvar';
                    Ext.grid.RowEditor.prototype.cancelBtnText = 'Cancelar';

                    return gridUsuario;
                }
                
                function GridHistorico() {
                    var historio = Ext.define('Historico', {
                        extend : 'Ext.data.Model',
                        fields : [{
                            name : 'comentario',
                            type : 'string'
                        }, {
                            name : 'datahora',
                            type : 'datetime'
                        }, {
                            name : 'nome',
                            type : 'string'
                        }, {
                            name : 'idpaciente',
                            type : 'int'
                        }, {
                            name : 'idhistorico',
                            type : 'int'
                        }, {
                            name : 'tentativa',
                            type : 'bool'
                        }, {
                            name : 'status',
                            type : 'string'
                        }, {
                            name : 'idusuario',
                            type : 'string'
                        }, {
                            name : 'mudou',
                            type : 'bool'
                        }, {
                            name : 'ressonancia',
                            type : 'bool'
                        }]
                    });
                    store = Ext.create('Ext.data.Store', {
                        extend : 'Ext.data.Store',
                        model : historio,
                        proxy : {
                            type : 'ajax',
                            url : 'consulta_historico.php',
                            reader : {
                                type : 'json',
                                root : 'data',
                            }
                        },
                        //    data : data2,
                        sorters : [{
                            property : 'start',
                            direction : 'ASC'
                        }]
                    });
                    store.load({
                        params : {
                            idpaciente : gridAtual.getSelectionModel().selected.items[0].data.idpaciente
                        }
                    });
                    rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
                        clicksToMoveEditor : 2,
                        autoCancel : false
                    });

                    grid = Ext.create('Ext.grid.Panel', {
                        store : store,
                        height : '100%',
                        width : '100%',
                        columns : [{
                            header : 'Detalhes',
                            dataIndex : 'comentario',
                            name : 'comentario',
                            flex : 6,
                            editor : {
                                xtype : 'htmleditor',
                                grow : true,
                                allowBlank : false
                            }
                        }, {
                            header : 'Data Contato',
                            dataIndex : 'datahora',

                            name : 'datahora',
                            flex : 1,
                            editor : {
                                value : Ext.Date.format(new Date(), 'd/m/Y H:i:s'),
                                xtype : 'textfield',
                                allowBlank : false,
                                readOnly : true
                            }
                        }, {
                            header : 'Usuario',
                            dataIndex : 'nome',

                            name : 'nome',
                            flex : 1,
                            editor : {
                                //value : usuario,
                                xtype : 'textfield',
                                allowBlank : false,
                                readOnly : true
                            }
                        }, {
                            xtype : 'checkcolumn',
                            header : 'N&atilde;o Atendeu',
                            name : 'tentativa',
                            dataIndex : 'tentativa',
                            flex : 1,

                            editor : {
                                xtype : 'checkbox',
                                cls : 'x-grid-checkheader-editor',
                                listeners : {
                                    'change' : function() {
                                        if (this.getValue()) {
                                            if (tipo == 'LIGADOR') {
                                                this.up('grid').columns[4].field.setValue('NAO LOCALIZADO');
                                            } else if (tipo == 'ENTREVISTADOR') {
                                                this.up('grid').columns[4].field.setValue('RESPONSAVEL NAO LOCALIZADO');
                                            }else if (tipo == 'PSICOLOGO') {
                                                this.up('grid').columns[4].field.setValue('CRIANCA NAO LOCALIZADA');
                                            }

                                            this.up('grid').columns[4].field.readOnly = true;

                                        } else {
                                            this.up('grid').columns[4].field.setValue('');
                                            this.up('grid').columns[4].field.readOnly = false;
                                        }
                                    }
                                }
                            }
                        }, {
                            header : 'Status',
                            name : 'status',
                            dataIndex : 'status',

                            flex : 2,
                            editor : montaComboStatus(tipo, '')
                        }, {
                            xtype : 'checkcolumn',
                            header : 'Mudou',
                            name : 'mudou',
                            dataIndex : 'mudou',
                            flex : 1,
                            editor : {
                                xtype : 'checkbox',
                                cls : 'x-grid-checkheader-editor',
                                listeners : {
                                    'change' : function() {
                                        //if (this.getValue()) {
                                           // this.up('grid').columns[4].field.setValue(viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue());
                                            // this.up('grid').columns[4].field.readOnly = true;

                                        //} else {
                                            //this.up('grid').columns[4].field.setValue('');
                                            // this.up('grid').columns[4].field.readOnly = false;
                                        //}
                                    }
                                }
                            }
                        },{
                            xtype : 'checkcolumn',
                            header : 'Ressonancia Realizada',
                            name : 'ressonancia',
                            dataIndex : 'ressonancia',
                            flex : 1,
                            hidden : true,
                            editor : {
                                xtype : 'checkbox',
                                cls : 'x-grid-checkheader-editor',
                                listeners : {
                                    'change' : function() {
                                      
                                    }
                                }
                            }
                        }],
                        width : '100%',
                        height : '100%',
                        //  title : 'Historico de Contatos',
                        //frame : true,
                        tbar : [{
                            text : 'Adicionar',
                            icon : 'http://analysismg.com.br/sistema/Add2.ico',
                            scale : 'large',
                            handler : function() {
                                rowEditing.cancelEdit();
                                // Create a model instance
                                r = Ext.create('Historico', {
                                    comentario : '',
                                    datahora : Ext.Date.format(new Date(), 'd/m/Y H:i:s'),
                                    nome : usuario,
                                    tentativa : false
                                });
                                this.up('grid').columns[4].field.readOnly = false;
                                this.up('grid').getStore().insert(0, r);
                                rowEditing.startEdit(0, 0);
                                verificaedicao = true;

                            }
                        }, {
                            itemId : 'removeHistorico',
                            text : 'Remover',
                            iconCls : 'remove',
                            handler : function() {

                                var sm = grid.getSelectionModel();
                                deltaHistorico(sm.selected.items[0].data);
                                rowEditing.cancelEdit();
                                store.remove(sm.getSelection());
                                if (store.getCount() > 0) {
                                    sm.select(0);
                                }

                            }
                        }],
                        plugins : [rowEditing],
                        listeners : {

                            'canceledit' : function() {
                                if (verificaedicao) {
                                    rowEditing.cancelEdit();
                                    this.getStore().remove(r);
                                }
                            },
                            'celldblclick' : function() {
                                if (tipo != "ADMINISTRADOR" && tipo != "ADM_PSICOLOGO") {
                                    return false
                                }
                            },
                            'edit' : function(editor, e) {

                                //console.log(this.getSelectionModel().selected.items[0].data);
                                if (!grid.columns[3].field.getValue() && grid.columns[4].field.getValue() == null) {

                                    Ext.MessageBox.show({
                                        title : 'AVISO',
                                        msg : 'Necessario Marcar o N&atilde;o Atendeu ou selecionar um STATUS - Esse registro N&Atilde;O sera salvo...',
                                        buttons : Ext.MessageBox.OK,
                                        icon : Ext.MessageBox.INFO
                                    });
                                    rowEditing.cancelEdit();
                                    //this.getStore().remove(r);
                                    return false;
                                } else {

                                    if (tipo != "ADMINISTRADOR"  && tipo != "ADM_PSICOLOGO" && !verificaedicao) {
                                        Ext.MessageBox.show({
                                            title : 'AVISO',
                                            msg : 'Acesso Negado! Alteracoes Serao Ignoradas',
                                            buttons : Ext.MessageBox.OK,
                                            icon : Ext.MessageBox.INFO
                                        });
                                        rowEditing.cancelEdit();
                                        this.getStore().remove(r);
                                        return;
                                    } else {
                                        me2 = this;
                                        //tipo == 'ENTREVISTADOR' &&
                                        if (this.getSelectionModel().selected.items[0].data.status == 'PESQUISA REALIZADA') {
                                            Ext.Msg.prompt('Atenção', 'Informe o Codigo do Questionario:', function(btn, text) {
                                                if (btn == 'ok' && /^[A-Za-z]{2}?[0-9]{4}$/.test(text)) {
                                                    Ext.Ajax.request({
                                                        url : 'atualiza_questionario.php',
                                                        params : {
                                                            idquestionario : text,
                                                            idpaciente : gridAtual.getSelectionModel().selected.items[0].data.idpaciente
                                                        },
                                                        method : 'GET',
                                                        success : function(response) {
                                                            salvaHistorio(me2.getSelectionModel().selected.items[0].data);
                                                            //tentativa de ligacao
                                                            atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_tentativa_ligacoes');
                                                            //nao atendeu
                                                            if (me2.getSelectionModel().selected.items[0].data.tentativa) {
                                                                atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_nao_atendeu');
                                                            }

                                                            trava = false;
                                                            verificaedicao = false;

                                                        },
                                                        failure : function(response, opts) {
                                                            i.wait().hide();
                                                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                                                        }
                                                    });
                                                } else {
                                                    //Ext.Msg.alert('Falha', 'Historico não sera salvo');
                                                    if (verificaedicao) {
                                                        rowEditing.cancelEdit();
                                                        me2.getStore().remove(r);
                                                    }
                                                    alert('O historico nao foi salvo!');
                                                }
                                            });
                                        } else {

                                            if (this.getSelectionModel().selected.items[0].data.status != "" && this.getSelectionModel().selected.items[0].data.comentario != "") {
                                                salvaHistorio(this.getSelectionModel().selected.items[0].data);
                                                //tentativa de ligacao
                                                atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_tentativa_ligacoes');
                                                //nao atendeu
                                                if (this.getSelectionModel().selected.items[0].data.tentativa) {
                                                    atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_nao_atendeu');
                                                }
                                                trava = false;
                                                verificaedicao = false;
                                            } else {
                                                Ext.MessageBox.show({
                                                    title : 'AVISO',
                                                    msg : 'Necessita de um Status e Comentario... Historico Invalido.',
                                                    buttons : Ext.MessageBox.OK,
                                                    icon : Ext.MessageBox.INFO
                                                });
                                                rowEditing.cancelEdit();
                                                this.getStore().remove(r);
                                            }

                                        }

                                    }
                                }

                            }
                        }
                    });
                    Ext.grid.RowEditor.prototype.saveBtnText = 'Salvar';
                    Ext.grid.RowEditor.prototype.cancelBtnText = 'Cancelar';

                    if (tipo != 'ADMINISTRADOR' && tipo != 'ADM_PSICOLOGO') {
                        grid.dockedItems.items[1].items.items[1].disabled = true;
                    }
                    return grid;
                }

                function deltaHistorico(data) {
                    i.wait('Excluindo Registro');
                    Ext.Ajax.request({
                        url : 'remove_historico.php',
                        params : {
                            idhistorico : data.idhistorico
                        },
                        method : 'GET',
                        success : function(response) {
                            i.wait().hide();
                            Ext.Msg.alert('Concluido', 'Historico Excluido');
                        },
                        failure : function(response, opts) {
                            i.wait().hide();
                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                        }
                    });
                }

                function salvaHistorio(data) {
                    params = {
                        idpaciente : gridAtual.getSelectionModel().selected.items[0].data.idpaciente,
                        idhistorico : data.idhistorico,
                        comentario : data.comentario,
                        datahora : data.datahora,
                        nome : data.nome,
                        status : data.status,
                        tentativa : data.tentativa,
                        idusuario : idusuario,
                        mudou : data.mudou,
                        ressonancia:data.ressonancia
                    }
                    i.wait('Salvando Historico');
                    Ext.Ajax.request({
                        url : 'salva_historico.php',
                        params : params,
                        method : 'GET',
                        success : function(response) {
                            //muda combo do paciente
                            auxstatusnovoHistorio = data.status;
                            statusatualHistorio = viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue();

                            if (statusatualHistorio != auxstatusnovoHistorio) {

                                if (auxstatusnovoHistorio == 'EM ANDAMENTO') {
                                    viewport.items.items[2].items.items[1].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'RESGATADO') {
                                    viewport.items.items[2].items.items[2].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'RECUSADO') {
                                    viewport.items.items[2].items.items[4].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'NAO LOCALIZADO') {
                                    viewport.items.items[2].items.items[3].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'NAO LOCALIZADO FINAL') {
                                    viewport.items.items[2].items.items[5].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'PESQUISA30') {
                                    viewport.items.items[2].items.items[6].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'PESQUISA4') {
                                    viewport.items.items[2].items.items[7].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovoHistorio == 'RECUSA NEGADO') {
                                    viewport.items.items[2].items.items[8].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }
                                //remove registro do grid atual
                                gridAtual.getStore().remove(gridAtual.getSelectionModel().selected.items[0]);
                                viewport.items.items[4].items.items[0].getForm().reset();
                                janelahistorio.setVisible(false);
                                trava = true;
                            }
                            i.wait().hide();
                            Ext.Msg.alert('Concluido', 'Historio Salvo');

                        },
                        failure : function(response, opts) {
                            i.wait().hide();
                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                        }
                    });
                }

                function janelaConsultaW() {

                    janelaConsulta = Ext.create('widget.window', {
                        header : {
                            titlePosition : 2,
                            titleAlign : 'center'
                        },
                        title : 'Consulta',
                        height : 600,
                        width : 1200,
                        scroll : true,
                        layout : {
                            type : 'border',
                            padding : 5
                        },
                        closable : true,

                        items : [{
                            region : 'west',
                            title : 'Campos',
                            width : 300,
                            split : true,
                            collapsible : true,
                            floatable : false,
                            items : [montaComboEtapa(), montacomboUsuarios(), montaComboSimNAO('Ressonancia Realizada', 'exame_realizado'), montaComboChecagemTelefonica(), montaComboStatus(tipo, 'Status')]
                        }, {
                            region : 'center',
                            xtype : 'tabpanel', 
                            items : [{
                                rtl : false,
                                title : 'Resultados - Paciente',
                                autoScroll : true,
                                items : [Ext.create('Ext.grid.Panel', {
                                    uses : ['Ext.ux.exporter.Exporter'],
                                    store : ArrayStore['localStoreConsulta'],
                                    columns : getColunas(),
                                    height : '100%',
                                    width : '96%',
                                    scroll : true,
                                    tbar : [{
                                        height : '35',
                                        xtype : 'exporterbutton',
                                        text : 'Export Grid Data'

                                    }],
                                    listeners : {
                                        celldblclick : function() {
                                            gridAtual = this;
                                            loadDados(this.getSelectionModel().selected.items[0].data);
                                        }
                                    }
                                })]
                            },{
                                rtl : false,
                                title : 'Resultados - Hitorico',
                                autoScroll : true,
                                items : [Ext.create('Ext.grid.Panel', {
                                    uses : ['Ext.ux.exporter.Exporter'],
                                    store : ArrayStore['localStoreConsultaHistorico'],
                                    columns : getColunasHistorico(),
                                    height : '100%',
                                    width : '96%',
                                    scroll : true,
                                    tbar : [{
                                        height : '30',
                                        xtype : 'exporterbutton',
                                        text : 'Export Grid Data'

                                    }],
                                    listeners : {
                                        celldblclick : function() {
                                          //  gridAtual = this;
                                            //loadDados(this.getSelectionModel().selected.items[0].data);
                                        }
                                    }
                                })]
                            }]
                        }, {
                            region : 'south',
                            xtype : 'panel',
                            items : [Ext.create('Ext.button.Button', {
                                name : 'atualiza_massa',
                                text : 'Atualizar em Massa',
                                handler : function() {
                                    var me = this.up('window');
                                    Ext.MessageBox.confirm('Aviso', 'Realizar procedimento em Massa?', decision);
                                    function decision(buttonId) {
                                        if (buttonId === 'yes') {
                                            i.wait('Atualizando em Massa...');
                                            Ext.Ajax.request({
                                                url : 'atualiza_em_massa.php',
                                                params : {
                                                    idusuario : me.items.items[0].items.items[1].getValue(),
                                                    etapa : me.items.items[0].items.items[0].getValue(),
                                                    exame_realizado : me.items.items[0].items.items[2].getValue(),
                                                    checagem_tel : me.items.items[0].items.items[3].getValue(),
                                                    status : me.items.items[0].items.items[4].getValue(),
                                                    sql : sqlPesquisa

                                                },
                                                method : 'POST',
                                                success : function(response) {
                                                    i.wait().hide();
                                                    Ext.Msg.alert('Concluido', 'Dados Atualizados');
                                                },
                                                failure : function(response, opts) {
                                                    i.wait().hide();
                                                    Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                                                }
                                            });
                                        }
                                        if (buttonId === 'no') {
                                            return false;
                                        }
                                        if (buttonId === 'cancel') {
                                            return false;
                                        }
                                    }

                                }
                            })]
                        }]//,
                        //closeAction : 'hide'
                    });
                    janelaConsulta.show();
                }

                function janelaHistorio() {
                    janelahistorio = Ext.create('Ext.window.Window', {
                        title : 'Historico de Contato',
                        height : 600,
                        width : 1200,
                        layout : 'fit',
                        items : GridHistorico(),
                        closeAction : 'hide'
                    });
                    janelahistorio.show();
                }
                
                function janelaUsuario() {
                    janelausuario = Ext.create('Ext.window.Window', {
                        title : 'Controle de Usuarios',
                        height : 600,
                        width : 800,
                        layout : 'fit',
                        items : GridUsuarios(),
                      closeAction : 'hide'
                    });
                    janelausuario.show();
                }

                janelaEstatistica = Ext.create('Ext.window.Window', {
                    title : 'Estatisticas',
                    height : 500,
                    width : 700,
                    defaultType : 'textfield',
                    xtype : 'container',
                    layout : 'hbox',
                    closeAction : 'hide',
                    items : [{
                        xtype : 'container',
                        flex : 1,
                        layout : 'anchor',
                        defaultType : 'textfield',
                        items : [{
                            fieldLabel : 'Tentativas de contato',
                            name : 'qtd_tentativa_ligacoes',
                            anchor : '85%'

                        }, {
                            fieldLabel : 'Tentativa de entrevista',
                            name : 'qtd_tentativa_entrevista',
                            anchor : '85%'

                        }, {
                            fieldLabel : 'Recusa de liga&#231;Ãµes',
                            name : 'qtd_recusa_ligacoes',
                            anchor : '85%'

                        }, {
                            fieldLabel : 'Recusa Entrevista',
                            name : 'qtd_recusa_entrevista   ',
                            anchor : '85%'

                        }]
                    }, {
                        xtype : 'container',
                        flex : 1,
                        layout : 'anchor',
                        defaultType : 'textfield',
                        items : [{
                            fieldLabel : 'Tentativas de Contato Psicologo',
                            name : 'qtd_tentativa_psi',
                            anchor : '85%'

                        }, {
                            fieldLabel : 'Tentativas de Questionario 30',
                            name : 'qtd_pergutas_resp30',
                            anchor : '85%'

                        }, {
                            fieldLabel : 'Tentativas de Questionario 4',
                            name : 'qtd_pergutas_resp4',
                            anchor : '85%'

                        }, {
                            fieldLabel : 'Tentativa de Ligacoes',
                            name : 'qtd_nao_atendeu',
                            anchor : '85%'

                        }]
                    }]
                });
                function getColunas() {
                    var colunas = [{
                        text : 'STATUS',
                        dataIndex : 'status',
                        hideable : false,
                        flex : 1
                    },{
                        text : 'ID',
                        dataIndex : 'idpaciente',
                        hideable : false,
                        flex : 1
                    }, {
                        text : 'NOME',
                        dataIndex : 'nome_crianca',
                        hideable : false,
                        flex : 2
                    },{
                        text : 'ENDERECO',
                        dataIndex : 'end_resp1',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    }, {
                        text : 'ESTADO',
                        dataIndex : 'estado_crianca',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'TEL. RES. RESP1',
                        dataIndex : 'tel_residencial_resp1',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'TEL. CEL. RESP1',
                        dataIndex : 'tel_celular_resp1',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'TEL. COM. RESP1',
                        dataIndex : 'tel_comercial_resp1',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'TEL. RES. RESP2',
                        dataIndex : 'tel_residencial_resp2',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'TEL. CEL. RESP2',
                        dataIndex : 'tel_celular_resp2',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'TEL. COM. RESP2',
                        dataIndex : 'tel_comercial_resp2',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'ESCOLA',
                        dataIndex : 'escola_crianca',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    }, {
                        text : 'ETAPA',
                        dataIndex : 'etapa',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    }, {
                        text : 'USUARIO ATUAL',
                        dataIndex : 'nome',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    },{
                        text : 'COD QUESTIONARIO',
                        dataIndex : 'idquestionario',
                        hideable : true,
                        flex : 1,
                        hidden : true
                    }]
                    return colunas;
                }
              
                function getColunasHistorico() {
                    var colunas = [{
                        text : 'ID',
                        dataIndex : 'idpaciente',
                        hideable : false,
                        flex : 1
                    }, {
                        text : 'NOME',
                        dataIndex : 'nome_crianca',
                        hideable : false,
                        flex : 2
                    },{
                        text : 'COMENTARIO', 
                        dataIndex : 'comentario',
                        hideable : false,
                        flex : 3,
                        hidden : false
                    }, {
                        text : 'DATA HORA',
                        dataIndex : 'datahora',
                        hideable : false,
                        flex : 1,
                        hidden : false
                    },{
                        text : 'USUARIO',
                        dataIndex : 'nome',
                        hideable : false,
                        flex : 1,
                        hidden : false
                    }, {
                        text : 'STATUS',
                        dataIndex : 'hitorico_status',
                        hideable : false,
                        flex : 1,
                        hidden : false
                    },{
                        text : 'MUDOU',
                        dataIndex : 'mudou',
                        hideable : false,
                        flex : 1,
                        hidden : false
                    },{
                        text : 'RESSONANCIA',
                        dataIndex : 'ressonancia',
                        hideable : false,
                        flex : 1,
                        hidden : false
                    },{
                        text : 'NAO ATENDEU',
                        dataIndex : 'tentativa',
                        hideable : false,
                        flex : 1,
                        hidden : false
                    }]
                    return colunas;
                }
                
                function retornaGrid(storegrid, titlegrid) {
                  //  console.log(storegrid);
                    var gridcomponet = null;
                    var gridInterno = Ext.create('Ext.grid.Panel', {
                        store : storegrid,
                        columns : getColunas(),
                        height : '100%',
                        width : '96%',
                        scroll : true,
                        listeners : {
                            celldblclick : function() {
                                if (trava) {
                                    Ext.MessageBox.show({
                                        title : 'AVISO',
                                        msg : 'Necessario Adicionar Historico,Salvar ou Cancelar Registo',
                                        buttons : Ext.MessageBox.OK,
                                        icon : Ext.MessageBox.INFO
                                    });
                                    return;
                                }
                                gridAtual = this;
                                loadDados(this.getSelectionModel().selected.items[0].data);
                            }
                        }
                    });
                    var gridcomponet = {
                        xtype : 'panel',
                        contentEl : 'west',
                        title : titlegrid,
                        autoScroll : true,
                        iconCls : 'nav', // see the HEAD section for style used
                        // html : 'Pacientes nao contactados',
                        items : gridInterno,
                        listeners : {
                            click : {
                                element : 'el', //bind to the underlying el property on the panel
                                fn : function() {
                                    // alert('asdf');
                                }
                            },
                            dblclick : {
                                element : 'body', //bind to the underlying body property on the panel
                                fn : function() {
                                    // console.log('dblclick body');
                                }
                            }
                        }
                    }
                    if(storegrid != undefined){
                        if (tipo == 'ADMINISTRADOR' && (storegrid.storeId != 'localStoreNaoIniciadoPsicologo' && storegrid.storeId != 'localStoreAtribuidoPsicologo' && storegrid.storeId != 'localStoreAvaliacaoRealizada' && storegrid.storeId != 'localStoreAvaliacaoAgendada' && storegrid.storeId != 'localStoreAvaliacaoEmAndamento' && storegrid.storeId != 'localStoreRecusaAvaliacaoLocal' && storegrid.storeId != 'localStoreRecusaAvaliacaoAgendamento' && storegrid.storeId != 'localStoreSituacaoEspecificaPsi' && storegrid.storeId != 'localStoreCriancaNLocalizada' && storegrid.storeId != 'localStoreCriancaNLocalizadaFinal')) {
                            return gridcomponet;
                        } else {
                            if (tipo == 'LIGADOR') {
                                //console.log(storegrid); 
                                if (storegrid.storeId == 'localStoreNenhumFoneFunciona' || storegrid.storeId == 'localStoreRecusa15' || storegrid.storeId == 'localStoreNLocalizado' || storegrid.storeId == 'localStoreEmAndamento' || storegrid.storeId == 'localStoreNIniciado' || storegrid.storeId == 'localStoreResgatado') {
                                    return gridcomponet;
                                } else {
                                    return null;
                                }
                            } else if (tipo == 'ENTREVISTADOR') {
                                if ( storegrid.storeId == 'localStoreNaoIdentificadoEscola' || storegrid.storeId == 'localStoreAtualizadoLocal' || storegrid.storeId == 'localStoreResponsavelNLocalizadoFinal' || storegrid.storeId == 'localStoreAgendado' || storegrid.storeId == 'localStorePesquisaRealizada' || storegrid.storeId == 'localStoreAtribuidoEntrevistador' || storegrid.storeId == 'localStoreAgendamentoAndamento' || storegrid.storeId == 'localStoreRecusaLocal' || storegrid.storeId == 'localStoreRecusaAgendamento' || storegrid.storeId == 'localStoreResponsavelNLocalizado' || storegrid.storeId == 'localStorePESQUISA4' || storegrid.storeId == 'localStorePESQUISA30' ) {
                                    return gridcomponet;
                                } else {
                                    return null;
                                }
                            } else if (tipo == 'ADM_PSICOLOGO') { 
                                if ( storegrid.storeId == 'localStoreCriancaNLocalizada' || storegrid.storeId == 'localStoreCriancaNLocalizadaFinal' || storegrid.storeId == 'localStoreSituacaoEspecificaPsi' || storegrid.storeId == 'localStoreNaoIniciadoPsicologo' || storegrid.storeId == 'localStoreAtribuidoPsicologo' || storegrid.storeId == 'localStoreAvaliacaoRealizada' || storegrid.storeId == 'localStoreAvaliacaoAgendada' || storegrid.storeId == 'localStoreAvaliacaoEmAndamento' || storegrid.storeId == 'localStoreRecusaAvaliacaoLocal' || storegrid.storeId == 'localStoreRecusaAvaliacaoAgendamento'  ) {
                                    return gridcomponet;
                                } else {
                                    return null;
                                }
                            } else if (tipo == 'PSICOLOGO' ) { 
                                if ( storegrid.storeId == 'localStoreCriancaNLocalizada'  ||  storegrid.storeId == 'localStoreAtribuidoPsicologo' || storegrid.storeId == 'localStoreAvaliacaoRealizada' || storegrid.storeId == 'localStoreAvaliacaoAgendada' || storegrid.storeId == 'localStoreAvaliacaoEmAndamento' || storegrid.storeId == 'localStoreRecusaAvaliacaoLocal' || storegrid.storeId == 'localStoreRecusaAvaliacaoAgendamento'  ) {
                                    return gridcomponet;
                                } else {
                                    return null;
                                }
                            }
                        }
                    } else {
                        return null;
                    }
                    
                }//inicio consulta inicial
  
          function atualizagrid(vmStoreId, dados) {
                    
                    var AuxColuns =getFields();
                    if(vmStoreId == 'localStoreConsultaHistorico'){
                        AuxColuns = getFieldsHistorico();
                    } 
                    
                    ArrayStore[vmStoreId] = Ext.create('Ext.data.Store', {
                        storeId : vmStoreId,
                        fields : AuxColuns,
                        data : {
                            'items' : dados
                        },
                        proxy : {
                            type : 'memory',
                            reader : {
                                type : 'json',
                                root : 'items'
                            }
                        }
                    });
                }

                function getFields() {
                    return ['idpaciente', 'idusuario', 'idligador', 'identrevistador', 'idpsicologo', 'nome_crianca', 'sexo_crianca', 'idade_crianca', 'data_nascimento_crianca', 'cel_crianca', 'email', 'endereco', 'complemento', 'bairro', 'cidade_crianca', 'estado_crianca', 'escola_crianca', 'responsavel_direto_crianca', 'notas1', 'tipo_responsavel1', 'tipo_contato_resp1', 'tel_residencial_resp1', 'end_resp1', 'intencao_mudanca_resp1', 'novo_endereco_resp1', 'nome_resp1', 'tel_comercial_resp1', 'tel_celular_resp1', 'novo_tel_resp1', 'ocupacao_resp1', 'cep_resp1', 'estado_civil_resp1', 'tipo_responsavel2', 'tipo_contato_resp2', 'tel_residencial_resp2', 'end_resp2', 'intencao_mudanca_resp2', 'novo_endereco_resp2', 'nome_resp2', 'tel_comercial_resp2', 'tel_celular_resp2', 'novo_tel_resp2', 'ocupacao_resp2', 'cep_resp2', 'estado_civil_resp2', 'contato1_nome', 'contato2_nome', 'contato3_nome', 'contato4_nome', 'contato5_nome', 'contato1_telefone', 'contato2_telefone', 'contato3_telefone', 'contato4_telefone', 'contato5_telefone', 'contato1_parentesco', 'contato2_parentesco', 'contato3_parentesco', 'contato4_parentesco', 'contato5_parentesco', 'anotacoes_entrevistador', 'status', 'etapa', 'qtd_tentativa_ligacoes', 'qtd_tentativa_entrevista', 'qtd_tentativa_psi', 'qtd_recusa_ligacoes', 'qtd_recusa_entrevista', 'qtd_recusa_psi', 'qtd_pergutas_resp30', 'qtd_pergutas_resp4', 'parecer_pscologico', 'exame_realizado', 'checagem_telefonica', 'data_ultimo_contato', 'qtd_nao_atendeu', 'nome','idquestionario'];
                }
                
                function getFieldsHistorico() {
                    return ['idpaciente', 'nome_crianca','comentario','datahora','nome','hitorico_status','mudou','ressonancia','tentativa'];
                }

                function montaObjHistorico(historico){
                     var objHistorico = null;
                     var tentativa = 'NAO';
                     var mudou = 'NAO';
                     var ressonancia = 'NAO';
                     
                     
                     if(historico.mudou == '1'){
                         mudou = 'SIM';
                     }
                     
                     if(historico.ressonancia == '1'){
                         ressonancia = 'SIM';
                     }
                     
                     if(historico.tentativa == '1'){
                         tentativa = 'SIM';
                     }
                     objHistorico = {
                            'idpaciente' : historico.idpaciente,
                            'nome_crianca':historico.nome_crianca,
                            'comentario':historico.comentario,
                            'datahora':historico.datahora,
                            'nome':historico.nome,
                            'hitorico_status':historico.hitorico_status,
                            'mudou': mudou,
                            'ressonancia':ressonancia,
                            'tentativa':tentativa
                        };
                        return objHistorico;
                }
                function montaObj(paciente) {
                    var objPaciente = null;
                    objPaciente = {
                        'idpaciente' : paciente.idpaciente,
                        'idusuario' : paciente.idusuario,
                        'idligador' : paciente.idligador,
                        'identrevistador' : paciente.identrevistador,
                        'idpsicologo' : paciente.idpsicologo,
                        'nome_crianca' : paciente.nome_crianca,
                        'sexo_crianca' : paciente.sexo_crianca,
                        'idade_crianca' : paciente.idade_crianca,
                        'data_nascimento_crianca' : paciente.data_nascimento_crianca,
                        'cel_crianca' : paciente.cel_crianca,
                        'email' : paciente.email,
                        'endereco' : paciente.endereco,
                        'complemento' : paciente.complemento,
                        'bairro' : paciente.bairro,
                        'cidade_crianca' : paciente.cidade_crianca,
                        'estado_crianca' : paciente.estado_crianca,
                        'escola_crianca' : paciente.escola_crianca,
                        'responsavel_direto_crianca' : paciente.responsavel_direto_crianca,
                        'notas1' : paciente.notas1,
                        'tipo_responsavel1' : paciente.tipo_responsavel1,
                        'tipo_contato_resp1' : paciente.tipo_contato_resp1,
                        'tel_residencial_resp1' : paciente.tel_residencial_resp1,
                        'end_resp1' : paciente.end_resp1,
                        'intencao_mudanca_resp1' : paciente.intencao_mudanca_resp1,
                        'novo_endereco_resp1' : paciente.novo_endereco_resp1,
                        'nome_resp1' : paciente.nome_resp1,
                        'tel_comercial_resp1' : paciente.tel_comercial_resp1,
                        'tel_celular_resp1' : paciente.tel_celular_resp1,
                        'novo_tel_resp1' : paciente.novo_tel_resp1,
                        'ocupacao_resp1' : paciente.ocupacao_resp1,
                        'cep_resp1' : paciente.cep_resp1,
                        'estado_civil_resp1' : paciente.estado_civil_resp1,
                        'tipo_responsavel2' : paciente.tipo_responsavel2,
                        'tipo_contato_resp2' : paciente.tipo_contato_resp2,
                        'tel_residencial_resp2' : paciente.tel_residencial_resp2,
                        'end_resp2' : paciente.end_resp2,
                        'intencao_mudanca_resp2' : paciente.intencao_mudanca_resp2,
                        'novo_endereco_resp2' : paciente.novo_endereco_resp2,
                        'nome_resp2' : paciente.nome_resp2,
                        'tel_comercial_resp2' : paciente.tel_comercial_resp2,
                        'tel_celular_resp2' : paciente.tel_celular_resp2,
                        'novo_tel_resp2' : paciente.novo_tel_resp2,
                        'ocupacao_resp2' : paciente.ocupacao_resp2,
                        'cep_resp2' : paciente.cep_resp2,
                        'estado_civil_resp2' : paciente.estado_civil_resp2,
                        'contato1_nome' : paciente.contato1_nome,
                        'contato2_nome' : paciente.contato2_nome,
                        'contato3_nome' : paciente.contato3_nome,
                        'contato4_nome' : paciente.contato4_nome,
                        'contato5_nome' : paciente.contato5_nome,
                        'contato1_telefone' : paciente.contato1_telefone,
                        'contato2_telefone' : paciente.contato2_telefone,
                        'contato3_telefone' : paciente.contato3_telefone,
                        'contato4_telefone' : paciente.contato4_telefone,
                        'contato5_telefone' : paciente.contato5_telefone,
                        'contato1_parentesco' : paciente.contato1_parentesco,
                        'contato2_parentesco' : paciente.contato2_parentesco,
                        'contato3_parentesco' : paciente.contato3_parentesco,
                        'contato4_parentesco' : paciente.contato4_parentesco,
                        'contato5_parentesco' : paciente.contato5_parentesco,
                        'anotacoes_entrevistador' : paciente.anotacoes_entrevistador,
                        'status' : paciente.status,
                        'etapa' : paciente.etapa,
                        'qtd_tentativa_ligacoes' : paciente.qtd_tentativa_ligacoes,
                        'qtd_tentativa_entrevista' : paciente.qtd_tentativa_entrevista,
                        'qtd_tentativa_psi' : paciente.qtd_tentativa_psi,
                        'qtd_recusa_ligacoes' : paciente.qtd_recusa_ligacoes,
                        'qtd_recusa_entrevista' : paciente.qtd_recusa_entrevista,
                        'qtd_recusa_psi' : paciente.qtd_recusa_psi,
                        'qtd_pergutas_resp30' : paciente.qtd_pergutas_resp30,
                        'qtd_pergutas_resp4' : paciente.qtd_pergutas_resp4,
                        'parecer_pscologico' : paciente.parecer_pscologico,
                        'exame_realizado' : paciente.exame_realizado,
                        'checagem_telefonica' : paciente.checagem_telefonica,
                        'data_ultimo_contato' : paciente.data_ultimo_contato,
                        'qtd_nao_atendeu' : paciente.qtd_nao_atendeu,
                        'nome' : paciente.nome,
                        'idquestionario' :paciente.idquestionario
                    };
                    return objPaciente;
                }

                function loadDados(data) {
                    //trava tela
                    trava = true
                    //prontuario
                    viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[0].setValue(data.idpaciente);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[1].setValue(data.nome_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[2].setValue(data.cel_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[3].setValue(data.etapa);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[0].setValue(data.cidade_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[1].setValue(data.sexo_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[2].setValue(data.escola_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[4].setValue(data.notas1);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[3].setValue(data.exame_realizado);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[0].setValue(data.estado_crianca);

                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[1].items.items[0].setValue(data.data_nascimento_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[1].items.items[1].setValue(data.idade_crianca);
                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[2].setValue(data.checagem_telefonica);

                    //grava status atual
                    statusatual = data.status;
                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].setValue(data.status);
                    //responsavel principal
                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[4].setValue(data.responsavel_direto_crianca);
                    
                    //Codigo questionario
                    viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[5].setValue(data.idquestionario);
                    
                    //dados responsavel
                    //coluna1
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[0].setValue(data.tipo_responsavel1);

                    //tipo_contato_resp1
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[2].setValue(data.tipo_contato_resp1);

                    //estado_civil_resp1
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[3].setValue(data.estado_civil_resp1);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[1].setValue(data.tel_residencial_resp1);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[2].setValue(data.end_resp1);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[3].items.items[0].setValue(data.intencao_mudanca_resp1);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[3].items.items[1].setValue(data.novo_endereco_resp1);
                    //coluna2
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[0].setValue(data.nome_resp1);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[1].setValue(data.tel_comercial_resp1);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[2].setValue(data.cep_resp1);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[3].setValue(data.novo_tel_resp1);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[3].clearInvalid();
                    //coluna3
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[0].setValue(data.ocupacao_resp1);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[1].setValue(data.tel_celular_resp1);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[1].clearInvalid();
                    //dados responsavel 2
                    //coluna1
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[0].setValue(data.tipo_responsavel2);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[1].setValue(data.tel_residencial_resp2);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[2].setValue(data.end_resp2);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[3].items.items[0].setValue(data.intencao_mudanca_resp2);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[3].items.items[1].setValue(data.novo_endereco_resp2);
                    //coluna2
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[0].setValue(data.nome_resp2);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[1].setValue(data.tel_comercial_resp2);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[2].setValue(data.cep_resp2);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[3].setValue(data.novo_tel_resp2);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[3].clearInvalid();

                    //coluna3
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[0].setValue(data.ocupacao_resp2);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[1].setValue(data.tel_celular_resp2);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[1].clearInvalid();

                    //tipo_contato_resp2
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[2].setValue(data.tipo_contato_resp2);

                    //estado_civil_resp2
                    viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[3].setValue(data.estado_civil_resp2);
                    //outros contatos
                    //contato 1
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[0].setValue(data.contato1_nome);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[1].setValue(data.contato1_telefone);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[2].setValue(data.contato1_parentesco);
                    //contato 2
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[0].setValue(data.contato2_nome);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[1].setValue(data.contato2_telefone);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[2].setValue(data.contato2_parentesco);
                    //contato 3
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[0].setValue(data.contato3_nome);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[1].setValue(data.contato3_telefone);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[2].setValue(data.contato3_parentesco);
                    //contato 4
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[0].setValue(data.contato4_nome);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[1].setValue(data.contato4_telefone);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[2].setValue(data.contato4_parentesco);
                    //contato 5
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[0].setValue(data.contato5_nome);

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[1].setValue(data.contato5_telefone);
                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[1].clearInvalid();

                    viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[2].setValue(data.contato5_parentesco);
                    //combo usuario
                    viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[4].select(gridAtual.getSelectionModel().selected.items[0].data.idusuario);

                    //janela de estatisticas 
                    if (tipo == "ADMINISTRADOR" || tipo == "ADM_PSICOLOGO" ) {
                        janelaEstatistica.items.items[1].items.items[0].setValue(data.qtd_recusa_psi);
                        janelaEstatistica.items.items[1].items.items[1].setValue(data.qtd_pergutas_resp30);
                        janelaEstatistica.items.items[1].items.items[2].setValue(data.qtd_pergutas_resp4);
                        janelaEstatistica.items.items[1].items.items[3].setValue(data.qtd_nao_atendeu);
                        janelaEstatistica.items.items[0].items.items[0].setValue(data.qtd_tentativa_ligacoes);
                        janelaEstatistica.items.items[0].items.items[1].setValue(data.qtd_tentativa_entrevista);
                        janelaEstatistica.items.items[0].items.items[2].setValue(data.qtd_recusa_ligacoes);
                        janelaEstatistica.items.items[0].items.items[3].setValue(data.qtd_recusa_entrevista);
                    }

                }//fim dos grids

                function atualizaEstatisticasPaciente(idpaciente, campo) {
                    Ext.Ajax.request({
                        url : 'atualiza_estatistica_pacientes.php',
                        params : {
                            idpaciente : idpaciente,
                            campo : campo
                        },
                        method : 'GET',
                        success : function(response) {
                        },
                        failure : function(response, opts) {
                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                        }
                    });
                }

                function RecuperaRecusa15() {
                    i.wait('Retornando Pacientes Inativos...');
                    Ext.Ajax.request({
                        url : 'retorna_paciente.php',
                        params : {
                            idusuario : idusuario
                        },
                        method : 'GET',
                        success : function(response) {
                            i.wait().hide();
                            CarregaPacientes();
                        },
                        failure : function(response, opts) {
                            i.wait().hide();
                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                        }
                    });

                }

                function CarregaPacientes() {
                    i.wait('Carregando Pacientes');
                    Ext.Ajax.request({
                        url : 'consulta_pacientes.php',
                        params : {
                            idusuario : idusuario
                        },
                        method : 'GET',
                        success : function(response) {
                            //    i.wait().hide();
                            var text = response.responseText;
                            data = Ext.decode(text, true);
                            if (data.length > 0) {
                                //var loading = Math.round(data.length/100);
                                var marca = Math.round(data.length / 5);
                                var vmloading = 0;
                                var cont = 0;
                                dadosNContactados = new Array();
                                dadosResgatados = new Array();
                                dadosNLocalizadoFinal = new Array();
                                dadosNLocalizado = new Array();
                                dadosRecusado = new Array();
                                dadosPESQUISA30 = new Array();
                                dadosPESQUISA4 = new Array();
                                dadosRECUSAFINAL = new Array();
                                dadosEmAndamento = new Array();
                                dadosRecusa15 = new Array();
                                dadosSituacaoEspecifica = new Array();
                                dadosResponsavelNLocalizado = new Array();
                                dadosResponsavelNLocalizadoFinal = new Array();
                                dadosRecusaAgendamento = new Array();
                                dadosRecusaLocal = new Array();
                                dadosAtribuidoEntrevistador = new Array();
                                dadosAgendamentoAndamento = new Array();
                                dadosPesquisaRealizada = new Array();
                                dadosAgendado = new Array();
                                dadosNaoIdentificadoFace = new Array();
                                dadosNaoIdentificadoEscola = new Array();
                                dadosNenhumFoneFunciona = new Array();
                                dadosAtualizadoLocal = new Array();
                                dadosNaoIniciadoPsicologo = new Array();
                                dadosAtribuidoPsicologo = new Array();
                                dadosAvaliacaoRealizada = new Array();
                                dadosAvaliacaoAgendada = new Array();
                                dadosAvaliacaoEmAndamento = new Array();
                                dadosCriancaNLocalizada = new Array();
                                dadosCriancaNLocalizadaFinal = new Array();
                                dadosRecusaAvaliacaoLocal = new Array();
                                dadosRecusaAvaliacaoAgendamento = new Array();
                                dadosSituacaoEspecificaPsi = new Array();
                     
                                Ext.Array.forEach(data, function(paciente) {
                                    
                                    if (paciente.status == 'NAO INICIADO PSICOLOGO') {
                                        dadosNaoIniciadoPsicologo.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'ATRIBUIDO AO PSICOLOGO') {
                                        dadosAtribuidoPsicologo.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'AVALIACAO REALIZADA') {
                                        dadosAvaliacaoRealizada.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'AVALIACAO AGENDADA') {
                                        dadosAvaliacaoAgendada.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'AVALIACAO EM ANDAMENTO') {
                                        dadosAvaliacaoEmAndamento.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'CRIANCA NAO LOCALIZADA') {
                                        dadosCriancaNLocalizada.push(montaObj(paciente));
                                    }
                                    
                                    if (paciente.status == 'CRIANCA NAO LOCALIZADA FINAL') {
                                        dadosCriancaNLocalizadaFinal.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RECUSA AVALIACAO LOCAL') {
                                        dadosRecusaAvaliacaoLocal.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RECUSA AGENDAMENTO DE AVALIACAO') {
                                        dadosRecusaAvaliacaoAgendamento.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'SITUACAO ESPECIFICA PSICOLOGO') {
                                        dadosSituacaoEspecificaPsi.push(montaObj(paciente));
                                    }
                                    
                                    if (paciente.status == 'ATUALIZADO LOCAL') {
                                        dadosAtualizadoLocal.push(montaObj(paciente));
                                    }
                                    
                                    if (paciente.status == 'NAO IDENTIFICADO FACE') {
                                        dadosNaoIdentificadoFace.push(montaObj(paciente));
                                    }

                                    if (paciente.status == 'NENHUM FONE FUNCIONA') {
                                        dadosNenhumFoneFunciona.push(montaObj(paciente));
                                    }

                                    if (paciente.status == 'NAO IDENTIFICADO ESCOLA') {
                                        dadosNaoIdentificadoEscola.push(montaObj(paciente));
                                    }

                                    
                                    if (paciente.status == 'AGENDADO') {
                                        dadosAgendado.push(montaObj(paciente));
                                    }

                                    if (paciente.status == 'PESQUISA REALIZADA') {
                                        dadosPesquisaRealizada.push(montaObj(paciente));
                                    }

                                    if (paciente.status == 'AGENDAMENTO EM ANDAMENTO') {
                                        dadosAgendamentoAndamento.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'ATRIBUIDO AO ENTREVISTADOR') {
                                        dadosAtribuidoEntrevistador.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RECUSA LOCAL') {
                                        dadosRecusaLocal.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RECUSA AGENDAMENTO') {
                                        dadosRecusaAgendamento.push(montaObj(paciente));
                                    }
                                   
                                    if (paciente.status == 'RESPONSAVEL NAO LOCALIZADO') {
                                        dadosResponsavelNLocalizado.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RESPONSAVEL NAO LOCALIZADO FINAL') {
                                        dadosResponsavelNLocalizadoFinal.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'SITUACAO ESPECIFICA') {
                                        dadosSituacaoEspecifica.push(montaObj(paciente));
                                    }

                                    if (paciente.status == 'RECUSADO15') {
                                        dadosRecusa15.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'EM ANDAMENTO') {
                                        dadosEmAndamento.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'NAO INICIADO') {
                                        dadosNContactados.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RESGATADO') {
                                        dadosResgatados.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'NAO LOCALIZADO FINAL') {
                                        dadosNLocalizadoFinal.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'NAO LOCALIZADO') {
                                        dadosNLocalizado.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RECUSADO') {
                                        dadosRecusado.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'PESQUISA30') {
                                        dadosPESQUISA30.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'PESQUISA4') {
                                        dadosPESQUISA4.push(montaObj(paciente));
                                    }
                                    if (paciente.status == 'RECUSA NEGADO') {
                                        dadosRECUSAFINAL.push(montaObj(paciente));
                                    }
                                });

                                atualizagrid('localStoreRecusa15',dadosRecusa15);
                                atualizagrid('localStoreNIniciado',dadosNContactados);
                                atualizagrid('localStoreResgatado',dadosResgatados);
                                atualizagrid('localStoreNLocalizadoFinal',dadosNLocalizadoFinal);
                                atualizagrid('localStoreNLocalizado',dadosNLocalizado);
                                atualizagrid('localStoreRecusado',dadosRecusado);
                                atualizagrid('localStoreRECUSAFINAL',dadosRECUSAFINAL);
                                atualizagrid('localStoreEmAndamento',dadosEmAndamento);
                                atualizagrid('localStoreSituacaoEspecifica',dadosSituacaoEspecifica);
                                atualizagrid('localStoreResponsavelNLocalizado',dadosResponsavelNLocalizado);
                                atualizagrid('localStoreResponsavelNLocalizadoFinal',dadosResponsavelNLocalizadoFinal);
                                atualizagrid('localStoreRecusaAgendamento',dadosRecusaAgendamento);
                                atualizagrid('localStoreRecusaLocal',dadosRecusaLocal);
                                atualizagrid('localStoreAtribuidoEntrevistador',dadosAtribuidoEntrevistador);
                                atualizagrid('localStorePESQUISA30',dadosPESQUISA30);
                                atualizagrid('localStorePESQUISA4',dadosPESQUISA4);
                                atualizagrid('localStoreAgendamentoAndamento',dadosAgendamentoAndamento);
                                atualizagrid('localStorePesquisaRealizada',dadosPesquisaRealizada);
                                atualizagrid('localStoreAgendado',dadosAgendado);
                                atualizagrid('localStoreNaoIdentificadoFace',dadosNaoIdentificadoFace);
                                atualizagrid('localStoreNenhumFoneFunciona',dadosNenhumFoneFunciona);
                                atualizagrid('localStoreNaoIdentificadoEscola',dadosNaoIdentificadoEscola);
                                atualizagrid('localStoreAtualizadoLocal',dadosAtualizadoLocal);
                                atualizagrid('localStoreNaoIniciadoPsicologo',dadosNaoIniciadoPsicologo);
                                atualizagrid('localStoreAtribuidoPsicologo',dadosAtribuidoPsicologo);
                                atualizagrid('localStoreAvaliacaoRealizada',dadosAvaliacaoRealizada);
                                atualizagrid('localStoreAvaliacaoAgendada',dadosAvaliacaoAgendada);
                                atualizagrid('localStoreAvaliacaoEmAndamento',dadosAvaliacaoEmAndamento);
                                atualizagrid('localStoreCriancaNLocalizada',dadosCriancaNLocalizada);
                                atualizagrid('localStoreRecusaAvaliacaoAgendamento',dadosRecusaAvaliacaoAgendamento);
                                atualizagrid('localStoreCriancaNLocalizadaFinal',dadosCriancaNLocalizadaFinal);
                                atualizagrid('localStoreRecusaAvaliacaoLocal',dadosRecusaAvaliacaoLocal);
                                atualizagrid('localStoreSituacaoEspecificaPsi',dadosSituacaoEspecificaPsi);
                                
                                
                                loadPage(getTab(data[1]));
                            } else {
                                if (/deadlock/.test(text)) {
                                    i.msg('Ocorreu um erro de Deadlock: O servidor n&atilde;o conseguiu processar a informa&#231;&atilde;o. Tente novamente');
                                } else {
                                    i.msg('Ocorreu um erro: ' + text);
                                }
                            }
                            //i.wait().hide();
                            Ext.MessageBox.hide();
                            //  if (callback && typeof callback != undefined)
                            // callback(me.produtoTemplate);
                        },
                        failure : function(response, opts) {
                            i.wait().hide();
                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                        }
                    });
                }

                function getTab(usuario) {
                    var tab2 = Ext.widget({
                        title : 'Prontuario',
                        xtype : 'form',
                        id : 'innerTabsForm',
                        collapsible : true,
                        bodyPadding : 5,
                        width : '100%',
                        fieldDefaults : {
                            labelAlign : 'top',
                            msgTarget : 'side'
                        },
                        defaults : {
                            anchor : '100%'
                        },
                        items : [{
                            xtype : 'container',
                            layout : 'hbox',
                            items : [{
                                xtype : 'container',
                                flex : 1,
                                border : false,
                                layout : 'anchor',
                                defaultType : 'textfield',
                                items : [{
                                    fieldLabel : 'Identificador',
                                    name : 'idpaciente',
                                    disabled : true,
                                    anchor : '85%',
                                    value : ''
                                }, {
                                    fieldLabel : 'Nome da Crian&#231;a',
                                    name : 'nome_crianca',
                                    anchor : '85%'
                                }, {
                                    fieldLabel : 'Telefone Celular da Crian&#231;a',
                                    xtype : 'textfield',
                                    name : 'cel_crianca',
                                    id : 'cel_crianca',
                                    anchor : '85%',

                                    validator : function() {

                                        this.setValue(maskFone(this));
                                        //  this.unsetActiveError();
                                        this.clearInvalid();
                                    }
                                }, montaComboEtapa(), montacomboUsuarios()]
                            }, {
                                xtype : 'container',
                                flex : 1,
                                layout : 'anchor',
                                defaultType : 'textfield',
                                items : [{
                                    fieldLabel : 'Cidade',
                                    // fieldLabel : 'Inten&#231;&#245;es de mudan&#231;a em 2011',
                                    name : 'cidade_crianca',
                                    anchor : '85%'
                                }, montaComboSexo(), {
                                    fieldLabel : 'Escola',
                                    name : 'escola_crianca',
                                    anchor : '85%'
                                }, montaComboSimNAO('Ressonancia Realizada', 'exame_realizado'), {
                                    xtype : 'textarea',
                                    fieldLabel : 'Notas Pr&eacute;vias',
                                    anchor : '85%',
                                    name : 'notas1'
                                }]
                            }, {
                                xtype : 'container',
                                flex : 1,
                                layout : 'anchor',
                                defaultType : 'textfield',
                                items : [{
                                    fieldLabel : 'Estado',
                                    name : 'estado_crianca',
                                    anchor : '85%'
                                }, {
                                    xtype : 'container',
                                    //   flex : 2,
                                    layout : 'column',
                                    defaultType : 'textfield',
                                    anchor : '85%',
                                    items : [{
                                        xtype : 'datefield',
                                        name : 'data_nascimento_crianca',
                                        fieldLabel : 'Data Nascimento',
                                        columnWidth : 0.60,
                                        format : 'd/m/Y',
                                    }, {
                                        fieldLabel : 'Idade',
                                        name : 'idade_crianca',
                                        columnWidth : 0.40
                                    }]
                                }, montaComboChecagemTelefonica(), montaComboStatus(tipo, 'Status'), montaComboRespCrianca('responsavel_direto_crianca','Responsavel Principal'),{
                                    fieldLabel : 'Codigo Questionario',
                                    anchor : '85%',
                                    name : 'idquestionario',
                                     disabled : (tipo != 'ADMINISTRADOR') 
                                     
                                }]
                            }]
                        }, {
                            xtype : 'tabpanel',
                            plain : true,
                            activeTab : 0,
                            height : '100%',
                            defaults : {
                                bodyPadding : 10
                            },
                            items : [{
                                title : 'Dados Responsavel',
                                defaults : {
                                    width : '100%'
                                },
                                defaultType : 'textfield',
                                xtype : 'container',
                                layout : 'hbox',
                                items : [{
                                    xtype : 'container',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [montaComboRespCrianca('tipo_responsavel1','Responsavel Principal'), {
                                        fieldLabel : 'Telefone Residencial',
                                        name : 'tel_residencial_resp1',
                                        id : 'tel_residencial_resp1',
                                        anchor : '85%',
                                        xtype : 'textfield',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Endere&#231;o',
                                        name : 'end_resp1',
                                        anchor : '100%'
                                    }, {
                                        xtype : 'container',
                                        flex : 2,
                                        layout : 'column',
                                        defaultType : 'textfield',
                                        items : [montaComboSimNAO('Pretende Mudar?', 'intencao_mudanca_resp1'), {
                                            fieldLabel : 'Novo Endere&#231;o',
                                            name : 'novo_endereco_resp1',
                                            anchor : '70%',
                                            columnWidth : 0.60
                                        }]
                                    }]
                                }, {
                                    xtype : 'container',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Nome',
                                        name : 'nome_resp1',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone Comercial',
                                        name : 'tel_comercial_resp1',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                        }
                                    }, {
                                        fieldLabel : 'CEP',
                                        name : 'cep_resp1',
                                        anchor : '85%',
                                    }, {
                                        fieldLabel : 'Novo Telefone',
                                        name : 'novo_tel_resp1',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }]
                                }, {
                                    xtype : 'container',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [montaComboOcupacao('1'), {
                                        fieldLabel : 'Telefone Celular',
                                        name : 'tel_celular_resp1',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Tipo Contato Resp1',
                                        name : 'tipo_contato_resp1',
                                        anchor : '85%',
                                    }, montaComboEstadoCivil('estado_civil_resp1')]
                                }]
                            }, {
                                title : 'Dados Responsavel 2',
                                defaults : {
                                    width : '100%'
                                },
                                defaultType : 'textfield',
                                xtype : 'container',
                                layout : 'hbox',
                                items : [{
                                    xtype : 'container',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [montaComboRespCrianca('tipo_responsavel2','Responsavel'), {
                                        fieldLabel : 'Telefone Residencial',
                                        name : 'tel_residencial_resp2',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Endere&#231;o',
                                        name : 'end_resp2',
                                        anchor : '100%'
                                    }, {
                                        xtype : 'container',
                                        flex : 1,
                                        layout : 'column',
                                        defaultType : 'textfield',
                                        items : [montaComboSimNAO('Pretende Mudar?', 'intencao_mudanca_resp2'), {
                                            fieldLabel : 'Novo Endere&#231;o',
                                            name : 'novo_endereco_resp2',
                                            anchor : '90%',
                                            columnWidth : 0.60
                                        }]
                                    }]
                                }, {
                                    xtype : 'container',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Nome',
                                        name : 'nome_resp2',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone Comercial',
                                        name : 'tel_comercial_resp2',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'CEP',
                                        name : 'cep_resp2',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Novo Telefone',
                                        name : 'novo_tel_resp2',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }]
                                }, {
                                    xtype : 'container',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [montaComboOcupacao('2'), {
                                        fieldLabel : 'Telefone Celular',
                                        name : 'tel_celular_resp2',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Tipo Contato Resp2',
                                        name : 'tipo_contato_resp2',
                                        anchor : '85%',
                                    }, montaComboEstadoCivil('estado_civil_resp2')]
                                }]
                            }, {
                                xtype : 'tabpanel',
                                title : 'Outros Contatos',
                                defaults : {
                                    width : '100%'
                                },
                                defaultType : 'textfield',
                                layout : 'hbox',
                                items : [{
                                    xtype : 'container',
                                    title : 'Contato 1',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Primeiro Nome', //pai ou mae
                                        name : 'contato1_nome',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone',
                                        name : 'contato1_telefone',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Parentesco',
                                        name : 'contato1_parentesco',
                                        anchor : '85%'
                                    }]
                                }, {
                                    xtype : 'container',
                                    title : 'Contato 2',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Primeiro Nome', //pai ou mae
                                        name : 'contato2_nome',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone',
                                        name : 'contato2_telefone',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Parentesco',
                                        name : 'contato2_parentesco',
                                        anchor : '85%'
                                    }]
                                }, {
                                    xtype : 'container',
                                    title : 'Contato 3',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Primeiro Nome', //pai ou mae
                                        name : 'contato3_nome',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone',
                                        name : 'contato3_telefone',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Parentesco',
                                        name : 'contato3_parentesco',
                                        anchor : '85%'
                                    }]
                                }, {
                                    xtype : 'container',
                                    title : 'Contato 4',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Primeiro Nome', //pai ou mae
                                        name : 'contato4_nome',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone',
                                        name : 'contato4_telefone',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Parentesco',
                                        name : 'contato4_parentesco',
                                        anchor : '85%'
                                    }]
                                }, {
                                    xtype : 'container',
                                    title : 'Contato 5',
                                    flex : 1,
                                    layout : 'anchor',
                                    defaultType : 'textfield',
                                    items : [{
                                        fieldLabel : 'Primeiro Nome', //pai ou mae
                                        name : 'contato5_nome',
                                        anchor : '85%'
                                    }, {
                                        fieldLabel : 'Telefone',
                                        name : 'contato5_telefone',
                                        anchor : '85%',
                                        validator : function() {
                                            this.setValue(maskFone(this));
                                            this.clearInvalid();
                                        }
                                    }, {
                                        fieldLabel : 'Parentesco',
                                        name : 'contato5_parentesco',
                                        anchor : '85%'
                                    }]
                                }]
                            }]
                        }, {
                            xtype : 'panel',
                            height : '200',
                            width : '100%',
                            html : '&nbsp;',
                            margin : '50 0 50 0',
                            border : false
                        }],

                        buttons : [{
                            text : 'Historico de Contato',
                            scale : 'large',
                            handler : function() {
                                if (viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[0].getValue() == "") {
                                    Ext.Msg.alert('Aviso', 'Necessario selecionar um Paciente.');
                                } else {
                                    Ext.MessageBox.confirm('Aviso', 'Deseja salvar o paciente antes?', decision);
                                    function decision(buttonId) {
                                        if (buttonId === 'yes') {
                                            salva(false);
                                            janelaHistorio();
                                        }
                                        if (buttonId === 'no') {
                                            janelaHistorio();
                                        }
                                        if (buttonId === 'cancel') {
                                            return;
                                        }
                                    }

                                }
                            }
                        }, getBtnEstatisticas(), {
                            text : 'Cancelar',
                            scale : 'large',
                            handler : function() {
                                var me = this;
                                Ext.MessageBox.confirm('Aviso', 'Deseja cancelar?', decision);
                                function decision(buttonId) {
                                    if (buttonId === 'yes') {
                                        me.up('form').getForm().reset();
                                        trava = false;
                                    }
                                    if (buttonId === 'no') {
                                        return;
                                    }
                                    if (buttonId === 'cancel') {
                                        return;
                                    }
                                }
                            }
                        }, {
                            text : 'Salvar',
                            scale : 'large',
                            handler : function() {
                                if (viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[0].getValue() == "") {
                                    Ext.Msg.alert('Aviso', "Nenhum dado salvo");
                                } else {
                                    salva(true);
                                }
                            }
                        },getBtnCadastroUsuario()]
                    });
                    return tab2
                }

                function getBtnEstatisticas() {
                    vmbtnestatisticas = null; 
                    if (tipo == 'ADMINISTRADOR' || tipo == 'ADM_PSICOLOGO') {
                        vmbtnestatisticas = Ext.create('Ext.Button', {
                            text : 'Estatisticas',
                            scale : 'large',
                            handler : function() {
                                janelaEstatistica.show();
                            }
                        });
                    }
                    return vmbtnestatisticas;
                }


                function getBtnCadastroUsuario() {
                    vmbtnCadastroUsuario = null; 
                    if (tipo == 'ADMINISTRADOR' || tipo == 'ADM_PSICOLOGO') {
                        vmbtnCadastroUsuario = Ext.create('Ext.Button', {
                            text : 'Cadastro de Usuarios',
                            scale : 'large',
                            handler : function() {
                                janelaUsuario();
                            }
                        });
                    }
                    return vmbtnCadastroUsuario;
                }
        
                function salva(vmSalvaHistorio) {
                    //   console.log(salvaHistorio);
                    var erro = false;
                    var campoerrado = "";
                    var telres1 = viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[1];
                    var telcom1 = viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[1];
                    var telcelular1 = viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[1];
                    var telres2 = viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[1];
                    var telcom2 = viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[1];
                    var telcelular2 = viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[1];

                    if (telres1.getValue() != "" && telres1.getValue().length < 13) {
                        campoerrado = "Responsavel Principal - Telefone Residencial - Esta Incompleto";
                        erro = true;
                        telres1.focus();
                    }

                    if (telcom1.getValue() != "" && telcom1.getValue().length < 13) {
                        campoerrado = "Responsavel Principal - Telefone Comercial - Esta Incompleto";
                        erro = true;
                        telcom1.focus();
                    }

                    if (telcelular1.getValue() != "" && telcelular1.getValue().length < 13) {
                        campoerrado = "Responsavel Principal - Telefone Celular - Esta Incompleto";
                        erro = true;
                        telcelular1.focus();
                    }

                    if (telres2.getValue() != "" && telres2.getValue().length < 13) {
                        campoerrado = "Responsavel 2 - Telefone Residencial - Esta Incompleto";
                        erro = true;
                        telres2.focus();
                    }

                    if (telcom2.getValue() != "" && telcom2.getValue().length < 13) {
                        campoerrado = "Responsavel 2 - Telefone Comercial - Esta Incompleto";
                        erro = true;
                        telcom2.focus();
                    }

                    if (telcelular2.getValue() != "" && telcelular2.getValue().length < 13) {
                        campoerrado = "Responsavel 2 - Telefone Celular - Esta Incompleto";
                        erro = true;
                        telcelular2.focus();
                    }

                    if (erro) {
                        Ext.MessageBox.show({
                            title : 'ERRO',
                            msg : campoerrado,
                            buttons : Ext.MessageBox.OK,
                            icon : Ext.MessageBox.INFO
                        });
                        return false
                    }

                    if (tipo == 'ADMINISTRADOR' || tipo == 'ADM_PSICOLOGO' ) {
                        mvidusuario = viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[4].getValue();
                    } else {
                        mvidusuario = idusuario;
                    }
                    params = {
                        idpaciente : viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[0].getValue(),
                        nome_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[1].getValue(),
                        cel_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[2].getValue(),
                        etapa : viewport.items.items[4].items.items[0].items.items[0].items.items[0].items.items[3].getValue(),
                        cidade_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[0].getValue(),
                        sexo_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[1].getValue(),
                        escola_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[2].getValue(),
                        notas1 : viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[4].getValue(),
                        exame_realizado : viewport.items.items[4].items.items[0].items.items[0].items.items[1].items.items[3].getValue(),
                        estado_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[0].getValue(),
                        data_nascimento_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[1].items.items[0].getSubmitValue(),
                        idade_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[1].items.items[1].getValue(),
                        checagem_telefonica : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[2].getValue(),
                        status : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue(),
                        responsavel_direto_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[4].getValue(),
                        idquestionario:  viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[5].getValue(),

                        tipo_responsavel1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[0].getValue(),

                        tipo_contato_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[2].getValue(),
                        estado_civil_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[3].getValue(),
                        tipo_contato_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[2].getValue(),
                        estado_civil_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[3].getValue(),

                        tel_residencial_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[1].getValue(),
                        end_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[2].getValue(),
                        intencao_mudanca_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[3].items.items[0].getValue(),
                        novo_endereco_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[0].items.items[3].items.items[1].getValue(),
                        nome_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[0].getValue(),
                        tel_comercial_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[1].getValue(),
                        cep_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[2].getValue(),
                        novo_tel_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[1].items.items[3].getValue(),
                        //coluna3
                        ocupacao_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[0].getValue(),
                        tel_celular_resp1 : viewport.items.items[4].items.items[0].items.items[1].items.items[0].items.items[2].items.items[1].getValue(),
                        tipo_responsavel2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[0].getValue(),
                        tel_residencial_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[1].getValue(),
                        end_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[2].getValue(),
                        intencao_mudanca_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[3].items.items[0].getValue(),
                        novo_endereco_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[0].items.items[3].items.items[1].getValue(),
                        //coluna2
                        nome_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[0].getValue(),
                        tel_comercial_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[1].getValue(),
                        tel_celular_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[1].getValue(),
                        cep_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[1].items.items[2].getValue(),
                        //coluna3
                        ocupacao_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[0].getValue(),
                        novo_tel_resp2 : viewport.items.items[4].items.items[0].items.items[1].items.items[1].items.items[2].items.items[1].getValue(),
                        //outros contatos
                        //contato 1
                        contato1_nome : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[0].getValue(),
                        contato1_telefone : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[1].getValue(),
                        contato1_parentesco : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[0].items.items[2].getValue(),
                        //contato 2
                        contato2_nome : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[0].getValue(),
                        contato2_telefone : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[1].getValue(),
                        contato2_parentesco : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[1].items.items[2].getValue(),
                        //contato 3
                        contato3_nome : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[0].getValue(),
                        contato3_telefone : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[1].getValue(),
                        contato3_parentesco : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[2].items.items[2].getValue(),
                        //contato 4
                        contato4_nome : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[0].getValue(),
                        contato4_telefone : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[1].getValue(),
                        contato4_parentesco : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[3].items.items[2].getValue(),
                        //contato 5
                        contato5_nome : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[0].getValue(),
                        contato5_telefone : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[1].getValue(),
                        contato5_parentesco : viewport.items.items[4].items.items[0].items.items[1].items.items[2].items.items[4].items.items[2].getValue(),
                        //combo usuario
                        idusuario : mvidusuario,
                        idusuario2 : idusuario,
                        responsavel_direto_crianca : viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[4].getValue()
                    }
                    i.wait('Salvando Paciente');
                    Ext.Ajax.request({
                        url : 'salva_dados.php',
                        params : params,
                        method : 'GET',
                        success : function(response) {
                            //salva o registo atual  (backup)
                            dataLocal = gridAtual.getSelectionModel().selected.items[0];
                            // gridAtual.getSelectionModel().selected.items[0].data = params;
                            //   gridAtual.getSelectionModel().selected.items[0].data.idusuario = mvidusuario + 1;
                            //gatilho de recusa
                            if (viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue() == 'RECUSADO') {
                                atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_recusa_ligacoes');
                            }
                            //gatilho recusa primeiro questionario
                            if (viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue() == 'PESQUISA4') {
                                atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_pergutas_resp30');
                            }
                            //gatilho recusa totalmente questionario
                            if (viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue() == 'RECUSA NEGADO') {
                                atualizaEstatisticasPaciente(gridAtual.getSelectionModel().selected.items[0].data.idpaciente, 'qtd_pergutas_resp4');
                            }

                            var auxstatusnovo = viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].getValue();

                            if (vmSalvaHistorio) {

                                salvaHistorio({
                                    idpaciente : dataLocal.data.idpaciente,
                                    comentario : 'Houve visualizacao do registro',
                                    datahora : Ext.Date.format(new Date(), 'd/m/Y H:i:s'),
                                    tentativa : '1',
                                    idusuario : idusuario,
                                    status : auxstatusnovo
                                });
                            }

                            if (statusatual != auxstatusnovo) {

                                if (auxstatusnovo == 'EM ANDAMENTO') {
                                    viewport.items.items[2].items.items[1].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'RESGATADO') {
                                    viewport.items.items[2].items.items[2].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'RECUSADO') {
                                    viewport.items.items[2].items.items[4].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'NAO LOCALIZADO') {
                                    viewport.items.items[2].items.items[3].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'NAO LOCALIZADO FINAL') {
                                    viewport.items.items[2].items.items[5].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'PESQUISA30') {
                                    viewport.items.items[2].items.items[6].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'PESQUISA4') {
                                    viewport.items.items[2].items.items[7].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }

                                if (auxstatusnovo == 'RECUSA NEGADO') {
                                    viewport.items.items[2].items.items[8].items.items[0].items.items[0].getStore().add(gridAtual.getSelectionModel().selected.items[0])
                                }
                                //remove registro do grid atual
                                gridAtual.getStore().remove(gridAtual.getSelectionModel().selected.items[0]);

                            }

                            i.wait().hide();

                            Ext.Msg.alert('Concluido', 'Dados Salvos Com Sucesso.');
                        },
                        failure : function(response, opts) {
                            i.wait().hide();
                            Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                        }
                    });
                }

                function montaComboEstadoCivil(campo) {
                    var estadocivil = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : campo,
                            "nome" : "SOLTEIRA(O)"
                        }, {
                            "campo" : campo,
                            "nome" : "CASADA(O) OU MORANDO JUNTO COM O PAI(MAE) BIOLOGICA(O) DA CRIANCA"
                        }, {
                            "campo" : campo,
                            "nome" : "CASADA(O) OU MORANDO JUNTO COM OUTRA(O) COMPANHEIRA(O)"
                        }, {
                            "campo" : campo,
                            "nome" : "SEPARADA(O)/DIVORCIADA(O)"
                        }, {
                            "campo" : campo,
                            "nome" : "VIUVO(A)"
                        }, {
                            "campo" : campo,
                            "nome" : "NR/NS"
                        }]
                    });

                    comboEstadoCivil = Ext.create('Ext.form.ComboBox', {
                        store : estadocivil,
                        fieldLabel : 'Estado Civil',
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '220',
                        anchor : '100%',
                    });
                    return comboEstadoCivil;
                }

                function montaComboEtapa(etapa) {
                    var etapa = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : "ETAPA",
                            "nome" : "NAO INFORMADO"
                        }, {
                            "campo" : "ETAPA",
                            "nome" : "LIGACAO"
                        }, {
                            "campo" : "ETAPA",
                            "nome" : "ENTREVISTA"
                        }, {
                            "campo" : "ETAPA",
                            "nome" : "PSICOLOGO"
                        }]
                    });
                    comboEtapa = Ext.create('Ext.form.ComboBox', {
                        store : etapa,
                        fieldLabel : 'Etapa',
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '120'
                    });
                    comboEtapa.setVisible(false);
                    if (tipo == 'ADMINISTRADOR') {
                        comboEtapa.setVisible(true);
                    }
                    return comboEtapa;
                }

                function montaComboSimNAO(Titulo, campo) {
                    var Exame = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : campo,
                            "nome" : "NAO INFORMADO"
                        }, {
                            "campo" : campo,
                            "nome" : "SIM"
                        }, {
                            "campo" : campo,
                            "nome" : "NAO"
                        }]
                    });
                    var comboExame = Ext.create('Ext.form.ComboBox', {
                        store : Exame,
                        fieldLabel : Titulo,
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '120'
                    });
                    comboExame.setVisible(true);
                    if (tipo != 'ADMINISTRADOR' && tipo != 'ADM_PSICOLOGO'  && campo == 'exame_realizado') {
                        comboExame.setVisible(false);
                    }
                    return comboExame;
                }

                function montaComboChecagemTelefonica(checagem) {
                    var Checagem = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : "checagem_telefonica",
                            "nome" : "NAO INFORMADO"
                        }, {
                            "campo" : "checagem_telefonica",
                            "nome" : "SIM"
                        }, {
                            "campo" : "checagem_telefonica",
                            "nome" : "NAO"
                        }]
                    });
                    comboChecagem = Ext.create('Ext.form.ComboBox', {
                        store : Checagem,
                        fieldLabel : 'Checagem Tel.',
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '120'
                    });
                    comboChecagem.setVisible(false);
                    if (tipo == 'ADMINISTRADOR') {
                        comboChecagem.setVisible(true);
                    }
                    return comboChecagem;
                }

                function montaComboRespCrianca(campo,Label) {
                    var respCrianca = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : campo,
                            "nome" : "MAE"
                        }, {
                            "campo" : campo,
                            "nome" : "PAI"
                        }]
                    });
                    comborespCrianca = Ext.create('Ext.form.ComboBox', {
                        store : respCrianca,
                        fieldLabel : Label,
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '120'
                    });
                    return comborespCrianca;
                }

                function montaComboSexo(sexo) {
                    var sexo = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : "sexo_crianca",
                            "nome" : "FEMININO"
                        }, {
                            "campo" : "sexo_crianca",
                            "nome" : "MASCULINO"
                        }]
                    });
                    return combosexo = Ext.create('Ext.form.ComboBox', {
                        store : sexo,
                        fieldLabel : 'Sexo',
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '120'
                    });
                }

                function montacomboUsuarios() {
                    var usuarios = Ext.create('Ext.data.Store', {
                        extend : 'Ext.data.Store',
                        fields : ['idusuario', 'nome'],
                        proxy : {
                            type : 'ajax',
                            url : 'combo_usuario.php',
                            reader : {
                                type : 'json',
                                root : 'data'
                            }
                        },
                        autoLoad : true
                        //,data : [dados]
                    });
                    comboUsuario = Ext.create('Ext.form.ComboBox', {
                        store : usuarios,
                        fieldLabel : 'Usuario',
                        displayField : 'nome',
                        valueField : 'idusuario',
                        width : '120',
                        name : 'idusuario'
                    });
                    comboUsuario.setVisible(false);
                    if (tipo == 'ADMINISTRADOR' || tipo == 'ADM_PSICOLOGO' ) {
                        comboUsuario.setVisible(true);
                    }
                    return comboUsuario;
                }

  
                function montaComboStatus(vmtipo, VmStatus) {
                    var vmdata;
                    if (vmtipo == 'LIGADOR') {
                        vmdata = [{
                            "campo" : "status",
                            "nome" : "NAO LOCALIZADO"
                        }, {
                            "campo" : "status",
                            "nome" : "RECUSADO"
                        }, {
                            "campo" : "status",
                            "nome" : "RESGATADO"
                        }, {
                            "campo" : "status",
                            "nome" : "EM ANDAMENTO"
                        },{
                            "campo" : "status",
                            "nome" : "NENHUM FONE FUNCIONA"
                        },{
                            "campo" : "status",
                            "nome" : "PESQUISA30"
                        }, {
                            "campo" : "status",
                            "nome" : "PESQUISA4" 
                        },{
                            "campo" : "status",
                            "nome" : "RECUSADO15" 
                        }]
                    } else if (vmtipo == 'PSICOLOGO' ){
                        
                        vmdata = [{
                                    "campo" : "status",
                                    "nome" : "AVALIACAO REALIZADA"
                                }, {
                                    "campo" : "status",
                                    "nome" : "AVALIACAO AGENDADA"
                                }, {
                                    "campo" : "status",
                                    "nome" : "AVALIACAO EM ANDAMENTO"
                                }, {
                                    "campo" : "status",
                                    "nome" : "CRIANCA NAO LOCALIZADA"
                                },{
                                    "campo" : "status",
                                    "nome" : "RECUSA AVALIACAO LOCAL"
                                },{
                                    "campo" : "status",
                                    "nome" : "RECUSA AGENDAMENTO DE AVALIACAO"
                                }]
                        
                    } else if ( vmtipo == 'ADM_PSICOLOGO'){
                        
                        vmdata = [{
                                    "campo" : "status",
                                    "nome" : "AVALIACAO REALIZADA"
                                },{
                                    "campo" : "status",
                                    "nome" : "AVALIACAO AGENDADA"
                                },{
                                    "campo" : "status",
                                    "nome" : "AVALIACAO EM ANDAMENTO"
                                },{
                                    "campo" : "status",
                                    "nome" : "ATRIBUIDO AO PSICOLOGO"
                                },{
                                    "campo" : "status",
                                    "nome" : "CRIANCA NAO LOCALIZADA"
                                },{
                                    "campo" : "status",
                                    "nome" : "CRIANCA NAO LOCALIZADA FINAL"
                                },{
                                    "campo" : "status",
                                    "nome" : "NAO INICIADO PSICOLOGO"
                                },{
                                    "campo" : "status",
                                    "nome" : "RECUSA AVALIACAO LOCAL"
                                },{
                                    "campo" : "status",
                                    "nome" : "RECUSA AGENDAMENTO DE AVALIACAO"
                                },{
                                    "campo" : "status",
                                    "nome" : "SITUACAO ESPECIFICA PSICOLOGO"
                                }]
                        
                    }else if (vmtipo == 'ENTREVISTADOR') {
                        vmdata = [{
                            "campo" : "status",
                            "nome" : "AGENDAMENTO EM ANDAMENTO"
                        }, {
                            "campo" : "status",
                            "nome" : "AGENDADO"
                        }, {
                            "campo" : "status",
                            "nome" : "ATRIBUIDO AO ENTREVISTADOR"
                        },{
                            "campo" : "status",
                            "nome" : "ATUALIZADO LOCAL"
                        },{
                            "campo" : "status",
                            "nome" : "PESQUISA REALIZADA"
                        } , {
                            "campo" : "status",
                            "nome" : "PESQUISA4"
                        }, {
                            "campo" : "status",
                            "nome" : "PESQUISA30"
                        }, {
                            "campo" : "status",
                            "nome" : "RECUSA LOCAL"
                        }, {
                            "campo" : "status",
                            "nome" : "RECUSA AGENDAMENTO"
                        }, {
                            "campo" : "status",
                            "nome" : "RESPONSAVEL NAO LOCALIZADO"
                        },{
                            "campo" : "status",
                            "nome" : "RESPONSAVEL NAO LOCALIZADO FINAL"
                        }]
                    } else if (vmtipo == 'ADMINISTRADOR') {
                        vmdata = [{
                            "campo" : "status",
                            "nome" : "AGENDAMENTO EM ANDAMENTO"
                        }, {
                            "campo" : "status",
                            "nome" : "AGENDADO"
                        }, {
                            "campo" : "status",
                            "nome" : "ATRIBUIDO AO ENTREVISTADOR"
                        },{
                            "campo" : "status",
                            "nome" : "ATUALIZADO LOCAL"
                        },{
                            "campo" : "status",
                            "nome" : "EM ANDAMENTO"
                        }, {
                            "campo" : "status",
                            "nome" : "NAO INICIADO"
                        },{
                            "campo" : "status",
                            "nome" : "NAO LOCALIZADO"
                        },{
                            "campo" : "status",
                            "nome" : "NAO IDENTIFICADO ESCOLA" 
                        },{
                            "campo" : "status",
                            "nome" : "NENHUM FONE FUNCIONA" 
                        },{
                            "campo" : "status",
                            "nome" : "NAO IDENTIFICADO FACE"
                        },{
                            "campo" : "status",
                            "nome" : "NAO LOCALIZADO FINAL" 
                        },{
                            "campo" : "status",
                            "nome" : "QUESTIONARIO AVALIADO"
                        }, {
                            "campo" : "status",
                            "nome" : "PESQUISA30"
                        }, {
                            "campo" : "status",
                            "nome" : "PESQUISA4"
                        },{
                            "campo" : "status",
                            "nome" : "PESQUISA REALIZADA"
                        },{
                            "campo" : "status",
                            "nome" : "QUESTIONARIO DIGITADO"
                        },{
                            "campo" : "status",
                            "nome" : "RECUSA NEGADO"
                        },{
                            "campo" : "status",
                            "nome" : "RESGATADO"
                        },{
                            "campo" : "status",
                            "nome" : "RECUSADO"
                        },{
                            "campo" : "status",
                            "nome" : "RECUSA LOCAL"
                        },{
                            "campo" : "status",
                            "nome" : "RECUSA AGENDAMENTO" 
                        },{
                            "campo" : "status",
                            "nome" : "RECUSADO15" 
                        },{
                            "campo" : "status",
                            "nome" : "RESPONSAVEL NAO LOCALIZADO FINAL"
                        },{
                            "campo" : "status",
                            "nome" : "RESPONSAVEL NAO LOCALIZADO"
                        },{
                            "campo" : "status",
                            "nome" : "SITUACAO ESPECIFICA"
                        },]
                    } else {
                        vmdata = [{
                            "campo" : "status",
                            "nome" : "ERRO"
                        }]
                    }
                    StatusDataCombo = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : vmdata
                    });
                    comboStatus = null;
                    return comboStatus = Ext.create('Ext.form.ComboBox', {
                        store : StatusDataCombo,
                        fieldLabel : VmStatus,
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '350',
                        autoSelect : true,
                        editable:false
                    });
                }

                function montaComboOcupacao(tipo) {
                    var campo = "ocupacao_resp" + tipo;
                    var Ocupacao = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : campo,
                            "nome" : "APOSENTADO(A)"
                        }, {
                            "campo" : campo,
                            "nome" : "AUTONOMO"
                        }, {
                            "campo" : campo,
                            "nome" : "AUXILIO DOENCA/AFASTADO"
                        }, {
                            "campo" : campo,
                            "nome" : "DESEMPREGADO"
                        }, {
                            "campo" : campo,
                            "nome" : "DO LAR"
                        }, {
                            "campo" : campo,
                            "nome" : "EMPREGO FIXO"
                        }, {
                            "campo" : campo,
                            "nome" : "EMPREGO TEMPORARIO"
                        }, {
                            "campo" : campo,
                            "nome" : "ESTUDANTE"
                        }, {
                            "campo" : campo,
                            "nome" : "PRESO EM REGIME FECHADO"
                        }, {
                            "campo" : campo,
                            "nome" : "PRESO EM REGIME ABERTO"
                        }, {
                            "campo" : campo,
                            "nome" : "NR/NS"
                        }]
                    });
                    return comboOcupacao = Ext.create('Ext.form.ComboBox', {
                        store : Ocupacao,
                        fieldLabel : 'Ocupa&#231;&atilde;o',
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'nome',
                        width : '220'
                    });
                }

                function comboComparacao(idinterno) {
                    var comparacao = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : "igual",
                            "nome" : "IGUAL"
                        }, {
                            "campo" : "maior",
                            "nome" : "MAIOR"
                        }, {
                            "campo" : "menor",
                            "nome" : "MENOR"
                        }, {
                            "campo" : "diferente",
                            "nome" : "DIFERENTE"
                        }, {
                            "campo" : "contem",
                            "nome" : "CONTEM"
                        }, {
                            "campo" : "nao_contem",
                            "nome" : "NAO CONTEM"
                        }]
                    });
                    var comboConsultaComparacao = Ext.create('Ext.form.ComboBox', {
                        store : comparacao,
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'campo',
                        width : '100',
                        name : 'comparacao' + idinterno
                    });
                    return comboConsultaComparacao;
                }

                function comboEOU(idinterno) {
                    var EOU = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : "E",
                            "nome" : "E"
                        }, {
                            "campo" : "OU",
                            "nome" : "OU"
                        }]
                    });
                    var comboEOU = Ext.create('Ext.form.ComboBox', {
                        store : EOU,
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'campo',
                        width : '20',
                        name : 'EOU' + idinterno
                    });
                    return comboEOU;
                }
                
                function comboCampoConsulta(idinterno) {
                    var states = Ext.create('Ext.data.Store', {
                        fields : ['campo', 'nome'],
                        data : [{
                            "campo" : "idpaciente",
                            "nome" : "Identificador"
                        }, {
                            "campo" : "nome_crianca",
                            "nome" : "Nome da Crianca"
                        }, {
                            "campo" : "endereco_responsaveis",
                            "nome" : "Endereco Responsaveis"
                        }, {
                            "campo" : "cep_responsaveis",
                            "nome" : "Cep Responsaveis"
                        }, {
                            "campo" : "nome_resp1",
                            "nome" : "Nome da Mae"
                        }, {
                            "campo" : "nome_resp2",
                            "nome" : "Nome do Pai"
                        }, {
                            "campo" : "responsavel_direto_crianca",
                            "nome" : "Responsavel Principal"
                        }, {
                            "campo" : "nome_usuario",
                            "nome" : "Usuario (Contem)"
                        }, {
                            "campo" : "etapa",
                            "nome" : "Etapa"
                        }, {
                            "campo" : "mudou",
                            "nome" : "Mudou(SIM OU NAO)"
                        }, {
                            "campo" : "exame_realizado",
                            "nome" : "Ressonancia Realizada(SIM OU NAO)"
                        }, {
                            "campo" : "status",
                            "nome" : "Status"
                        }, {
                            "campo" : "notas1",
                            "nome" : "Notas Previas"
                        }, {
                            "campo" : "cidade_crianca",
                            "nome" : "Cidade da Crianca"
                        }, {
                            "campo" : "estado_crianca",
                            "nome" : "Estado da Crianca"
                        },{
                            "campo" : "escola_crianca",
                            "nome" : "Escola da Crianca"
                        }, {
                            "campo" : "data_contato",
                            "nome" : "Data do Contato"
                        }]
                    });
                    var comboConsultaFilds = Ext.create('Ext.form.ComboBox', {
                        store : states,
                        queryMode : 'local',
                        displayField : 'nome',
                        valueField : 'campo',
                        width : '150',
                        nome : 'consulta_valor' + idinterno
                    });
                    return comboConsultaFilds;
                }

                function getTextoConsulta(idinterno) {
                    return Ext.create('Ext.form.field.Text', {
                        name : 'valor_consulta' + idinterno
                    });
                }

                function getBotaoFiltro(idinterno) {
                    return Ext.create('Ext.button.Button', {
                        name : 'add' + idinterno,
                        text : 'Adicionar Filtro',
                        handler : function() {
                            id = id + 1;
                            var container = Ext.create('Ext.container.Container', {
                                flex : 1,
                                layout : {
                                    type : 'table',
                                    columns : 6
                                },
                                items : getLinhaConsulta(id, 'E Pesquisar Pacientes onde:', false)
                            });
                            viewport.items.items[0].add(container);
                        }
                    });
                }

                function getLinhaConsulta(idinterno, texto_inicial, tem_botao) {
                    var aux = null;
                    var aux2 = null;
                    var aux3 = null;
                    var aux4 = null;
                    if (tem_botao) {
                        
                        texto_inicial_final =   {
                            xtype : 'label',
                            text : texto_inicial,
                            value : texto_inicial,
                            getValue:function(){
                                return this.value;
                            }
                        }
                        aux = getBotaoFiltro(idinterno);
                        aux2 = Ext.create('Ext.button.Button', {
                            name : 'remove_filtro',
                            text : 'Remover Filtro',
                            handler : function() {
                                if (id > 0) {
                                    viewport.items.items[0].remove(id);
                                    id = id - 1
                                }
                            }
                        });
                        aux3 = Ext.create('Ext.button.Button', {
                            name : 'consulta',
                            text : 'Consultar',
                            handler : function() {

                                texto_consulta = "";
                                dadosConsulta = new Array();
                                dadosConsultaHistorico = new Array();
                                for (var a = 0; a <= id; a++) {
                                    texto_consulta = texto_consulta + viewport.items.items[0].items.items[a].items.items[0].getValue() + ';'+ viewport.items.items[0].items.items[a].items.items[1].getValue() + ';' + viewport.items.items[0].items.items[a].items.items[2].getValue() + ';' + viewport.items.items[0].items.items[a].items.items[4].getValue() + '|';

                                }

                                i.wait('Consultando Pacientes...');
                                Ext.Ajax.request({
                                    url : 'pesquisa.php',
                                    params : {
                                        dados : texto_consulta
                                    },
                                    method : 'POST',
                                    success : function(response) {
                                        var text = response.responseText;
                                        dataPesquisa = Ext.decode(text, true);
                                        if (dataPesquisa.dados.length > 0) {
                                            Ext.Array.forEach(dataPesquisa.dados, function(paciente2) {
                                                dadosConsulta.push(montaObj(paciente2));
                                            });
                                        }
                
                                         atualizagrid('localStoreConsulta',dadosConsulta);
                                         i.wait().hide();
                                         i.wait('Consultando Historico...');
                                          sqlPesquisa = dataPesquisa.sql;      
                                         Ext.Ajax.request({
                                            url : 'pesquisa_historico.php',
                                            params : {
                                                dados : texto_consulta
                                            },
                                            method : 'POST',
                                            success : function(response) {
                                                var text = response.responseText;
                                                dataPesquisaHistorico = Ext.decode(text, true);//
                                                if (dataPesquisaHistorico.dados.length > 0) {
                                                    Ext.Array.forEach(dataPesquisaHistorico.dados, function(historico) {
                                                        dadosConsultaHistorico.push(montaObjHistorico(historico));//
                                                    });
                                                }
                                                  
                                                atualizagrid('localStoreConsultaHistorico',dadosConsultaHistorico);        
                                                sqlPesquisaHistorico = dataPesquisaHistorico.sql;
                                                janelaConsultaW();
                                                i.wait().hide();
        
                                            },
                                            failure : function(response, opts) {
                                                i.wait().hide();
                                                Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                                            }
                                        });       
                                    },
                                    failure : function(response, opts) {
                                        i.wait().hide();
                                        Ext.Msg.alert('Falha', 'Servidor retornou um erro: ' + response.status);
                                    }
                                });
                            },
                            style : {
                                'margin-left' : '50%'
                            }
                        });

                        aux4 = Ext.create('Ext.button.Button', {
                            name : 'Logout',
                            text : 'Logout',
                            style : {
                                'margin-left' : '1400%',
                                'overflow' : 'auto'
                            },
                            handler : function() {
                                parent.window.location = "http://analysismg.com.br/sistema/login.php";
                            }
                        });
                    }else {
                        texto_inicial_final = comboEOU(idinterno);
                    }
                    return [texto_inicial_final, comboCampoConsulta(idinterno), comboComparacao(idinterno), {
                        xtype : 'label',
                        text : ' valor '
                    }, getTextoConsulta(idinterno), aux, {
                        xtype : 'label',
                        text : ' '
                    }, aux2, {
                        xtype : 'label',
                        text : ' '
                    }, aux3, aux4]
                }

                function loadPage(formulario) {
                    var viewNortItens = null;
                    if (tipo == 'ADMINISTRADOR' || tipo == 'ADM_PSICOLOGO' ) {
                        viewNortItens = [{
                            xtype : 'container',
                            flex : 1,
                            layout : {
                                type : 'table',
                                columns : 11
                            },
                            items : getLinhaConsulta(id, 'Pesquisar Pacientes onde:', true)
                        }]
                    } else {

                        var aux5 = Ext.create('Ext.button.Button', {
                            name : 'Logout',
                            text : 'Logout',

                            handler : function() {
                                parent.window.location = "http://analysismg.com.br/sistema/login.php";
                            }
                        });

                        viewNortItens = [{
                            xtype : 'container',
                            flex : 1,
                            layout : {
                                type : 'table',
                                columns : 11
                            },
                            items : aux5
                        }]

                    }
                    
                    viewport = Ext.create('Ext.Viewport', {
                        id : 'border-example',
                        layout : 'border',
                        items : [{
                            region : 'north',
                            //   contentEl : 'south',
                            split : true,
                            height : 60,
                            minSize : 10,
                            maxSize : 300,
                            collapsible : false,
                            collapsed : false,
                            //   title : 'Administra&ccedil;&atilde;o',
                            title : 'ANALYSIS - Sistema de Gerenciamento de Coleta de Dados',
                            margins : '0 0 0 0',
                            items : viewNortItens
                        }, {//4
                            region : 'west',
                            stateId : 'navigation-panel',
                            id : 'west-panel', // see Ext.getCmp() below
                            title : 'Pacientes',
                            split : true,
                            width : 460,
                            minWidth : 20,
                            maxWidth : 600,
                            collapsible : false,
                            animCollapse : false,
                            margins : '0 0 0 5',
                            layout : 'accordion',
                            autoScroll : true,
    
                            items : [
                            retornaGrid(ArrayStore['localStoreNIniciado'], 'N&Atilde;O INICIADO (' + dadosNContactados.length + ')'), 
                            retornaGrid(ArrayStore['localStoreEmAndamento'], 'EM ANDAMENTO (' + dadosEmAndamento.length + ')'), 
                            retornaGrid(ArrayStore['localStoreResgatado'], 'RESGATE CONFIRMADO (' + dadosResgatados.length + ')'), 
                            retornaGrid(ArrayStore['localStoreNLocalizado'], 'N&Atilde;O LOCALIZADO(' + dadosNLocalizado.length + ')'), 
                            retornaGrid(ArrayStore['localStoreRecusado'], 'RECUSADO (' + dadosRecusado.length + ')'),
                            retornaGrid(ArrayStore['localStoreNaoIdentificadoFace'], 'N&Atilde;O IDENTIFICADO NO FACE (' + dadosNaoIdentificadoFace.length + ')'),
                            retornaGrid(ArrayStore['localStoreNenhumFoneFunciona'], 'NENHUM TELEFONE FUNCIONA (' + dadosNenhumFoneFunciona.length + ')'),
                            retornaGrid(ArrayStore['localStoreNaoIdentificadoEscola'], 'N&Atilde;O IDENTIFICADO ESCOLA (' + dadosNaoIdentificadoEscola.length + ')'), 
                            retornaGrid(ArrayStore['localStoreNLocalizadoFinal'], 'N&Atilde;O LOCALIZADO FINAL(' + dadosNLocalizadoFinal.length + ')'), 
                            retornaGrid(ArrayStore['localStorePESQUISA30'], 'PESQUISA 30 (' + dadosPESQUISA30.length + ')'), 
                            retornaGrid(ArrayStore['localStorePESQUISA4'] , 'PESQUISA 4 (' + dadosPESQUISA4.length + ')'), 
                            retornaGrid(ArrayStore['localStoreRECUSAFINAL'], 'RECUSA NEGADO (' + dadosRECUSAFINAL.length + ')'), 
                            retornaGrid(ArrayStore['localStoreRecusa15'], 'RECUSADO A 15 DIAS (' + dadosRecusa15.length + ')'), 
                            retornaGrid(ArrayStore['localStoreAtribuidoEntrevistador'], 'ATRIBUIDO AO ENTREVISTADOR (' + dadosAtribuidoEntrevistador.length + ')'), 
                            retornaGrid(ArrayStore['localStoreAgendamentoAndamento'], 'AGENDAMENTO EM ANDAMENTO (' + dadosAgendamentoAndamento.length + ')'), 
                            retornaGrid(ArrayStore['localStoreAgendado'], 'AGENDADO (' + dadosAgendado.length + ')'), 
                            retornaGrid(ArrayStore['localStorePesquisaRealizada'], 'PESQUISA REALIZADA (' + dadosPesquisaRealizada.length + ')'), 
                            retornaGrid(ArrayStore['localStoreSituacaoEspecifica'], 'SITUACAO ESPECIFICA (' + dadosSituacaoEspecifica.length + ')'), 
                            retornaGrid(ArrayStore['localStoreResponsavelNLocalizado'], 'RESPONSAVEL NAO LOCALIZADO (' + dadosResponsavelNLocalizado.length + ')'), 
                            retornaGrid(ArrayStore['localStoreResponsavelNLocalizadoFinal'], 'RESPONSAVEL NAO LOCALIZADO FINAL(' + dadosResponsavelNLocalizadoFinal.length + ')'),
                            retornaGrid(ArrayStore['localStoreRecusaAgendamento'], 'RECUSOU AGENDAMENTO (' + dadosRecusaAgendamento.length + ')'), 
                            retornaGrid(ArrayStore['localStoreRecusaLocal'], 'RECUSA LOCAL (' + dadosRecusaLocal.length + ')'),
                            retornaGrid(ArrayStore['localStoreAtualizadoLocal'], 'ATUALIZADO LOCAL (' + dadosAtualizadoLocal.length + ')'),
                            retornaGrid(ArrayStore['localStoreNaoIniciadoPsicologo'],'NAO INICIADO PSICOLOGO (' + dadosNaoIniciadoPsicologo.length + ')'),
                             retornaGrid(ArrayStore['localStoreAtribuidoPsicologo'],'ATRIBUIDO AO PSICOLOGO (' + dadosAtribuidoPsicologo.length + ')'),
                             retornaGrid(ArrayStore['localStoreAvaliacaoRealizada'],'AVALICAO REALIZADA (' + dadosAvaliacaoRealizada.length + ')'),
                             retornaGrid(ArrayStore['localStoreAvaliacaoAgendada'],'AVALIACAO AGENDADA (' + dadosAvaliacaoAgendada.length + ')'),
                             retornaGrid(ArrayStore['localStoreAvaliacaoEmAndamento'],'AVALIACAO EM ANDAMENTO (' + dadosAvaliacaoEmAndamento.length + ')'),
                             retornaGrid(ArrayStore['localStoreCriancaNLocalizada'],'CRIANCA NAO LOCALIZADA (' + dadosCriancaNLocalizada.length + ')'),
                             retornaGrid(ArrayStore['localStoreRecusaAvaliacaoAgendamento'],'RECUSA AGENDAMENTO DE AVALIACAO (' + dadosRecusaAvaliacaoAgendamento.length + ')'),
                             retornaGrid(ArrayStore['localStoreCriancaNLocalizadaFinal'],'CRIANCA NAO LOCALIZADA FINAL (' + dadosCriancaNLocalizadaFinal.length + ')'),
                             retornaGrid(ArrayStore['localStoreRecusaAvaliacaoLocal'],'RECUSA AVALIACAO LOCAL (' + dadosRecusaAvaliacaoLocal.length + ')'),
                             retornaGrid(ArrayStore['localStoreSituacaoEspecificaPsi'],'SITUACAO ESPECIFICA PSICOLOGO (' + dadosSituacaoEspecificaPsi.length + ')')
                            ]
                        },
 
                        Ext.create('Ext.tab.Panel', {
                            id : 'cadastro_central',
                            region : 'center', // a center region is ALWAYS required for border layout
                            deferredRender : false,
                            activeTab : 0, // first tab initially active
                            items : [formulario]
                        })]
                    });

                    if (tipo != 'ADMINISTRADOR' && tipo != 'ADM_PSICOLOGO') {
                        viewport.items.items[4].items.items[0].items.items[0].items.items[2].items.items[3].setVisible(false);
                    }
                    Ext.get("hideit").on('click', function() {
                        var w = Ext.getCmp('west-panel');
                        w.collapsed ? w.expand() : w.collapse();
                    });
                }
                RecuperaRecusa15();
            });
        </script>
    </head>
    <body>
        <!-- use class="x-hide-display" to prevent a brief flicker of the content -->
        <div id="west" class="x-hide-display" style="overflow: auto">
            <p>
                <!--n&atilde; &cedil; &ccedil;&atilde;-->
            </p>
        </div>
        <div id="center2" class="x-hide-display">
            <a id="hideit" href="#">Toggle the west region</a>
            colocar div
        </div>
        <div id="center1" class="x-hide-display">
            colocar div
        </div>
        <div id="props-panel" class="x-hide-display" style="width:200px;height:200px;overflow:hidden;"></div>
        <div id="south" class="x-hide-display">
            <p>
                south - generally for informational stuff, also could be for status bar
            </p>
        </div>
    </body>
</html>
