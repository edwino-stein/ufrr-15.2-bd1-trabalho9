App.define('Abstract.ModalView',{

    _isAbstract_: true,

    bootstrapCfg: {
        backdrop: 'static',
        show: false
    },

    renderTo: 'body',
    $domObj: '',

    baseTpl:'<div class="modal fade">\n\
                <div class="modal-dialog">\n\
                    <div class="modal-content">\n\
                        <div class="modal-header"><h4 class="modal-title"></h4></div>\n\
                        <div class="modal-body"></div>\n\
                        <div class="modal-footer"></div>\n\
                    </div>\n\
                </div>\n\
            </div>',

    getTitle: function(){
        return this.$domObj.find('.modal-title');
    },

    setTitle: function(html){
        this.$domObj.find('.modal-title').html(html);
        return this;
    },

    getBody: function(){
        return this.$domObj.find('.modal-body');
    },

    setBody: function(html){
        this.$domObj.find('.modal-body').html(html);
        return this;
    },

    getFooter: function(){
        return this.$domObj.find('.modal-footer');
    },

    setFooter: function(html){
        this.$domObj.find('.modal-footer').html(html);
        return this;
    },

    onBeforeShow: function(e){},
    onShow: function(e){},
    onClose: function(e){},

    show: function(){
        if(this.isOpen()) return;
        this.$domObj.modal('show');
        return this;
    },

    close: function(){
        if(!this.isOpen()) return;
        this.$domObj.modal('hide');
        return this;
    },

    isOpen: function(){
        return this.$domObj.hasClass('in');
    },

    init: function(){
        var me = this;

        me.$domObj = $($.parseHTML(me.baseTpl)[0]).attr('id', me.$domObj);
        $(me.renderTo).append(me.$domObj);
        me.$domObj.modal(me.bootstrapCfg);
        me.$domObj.on('show.bs.modal', function(e){me.onBeforeShow(e);})
                  .on('shown.bs.modal', function(e){me.onShow(e);})
                  .on('hidden.bs.modal', function(e){me.onClose(e);});
    }

});
