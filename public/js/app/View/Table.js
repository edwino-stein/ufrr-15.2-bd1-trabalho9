App.define('View.Table',{

    $container: '#main-panel',
    $tableDomObj: '#visitas-list',

    rowCls: 'visitas-list-row',
    rowTpl: '<li class="list-group-item">\n\
                <div class="list-group-item-heading nome">Nome</div>\n\
                <p class="list-group-item-text mensagem">Mensagem</p>\n\
                <div class="list-group-item-footer">\n\
                    Publicado em\n\
                    <span class="data">data</span> em <span class="localizacao">Local</span>\n\
                </div>\n\
            </li>',


    renderer: function (field, data){

        if(field === 'data'){

            var dia = (data.getDate() < 10 ? '0' : '') + data.getDate(),
                mes = (data.getMonth() + 1 < 10 ? '0' : '')+(data.getMonth() + 1),
                ano = data.getFullYear(),
                hora = (data.getUTCHours() < 10 ? '0' : '')+data.getUTCHours(),
                min = (data.getUTCMinutes() < 10 ? '0' : '')+data.getUTCMinutes(),
                sec = (data.getUTCSeconds() < 10 ? '0' : '')+data.getUTCSeconds();

            return dia+'/'+mes+'/'+ano+' ás '+hora+':'+min+':'+sec;
        }
        else{
            return data.toString();
        }
    },

    ready: function(){
        this.callSuper();
    },

    init: function(){
        this.callSuper();
    }

}, 'Abstract.TableView');
