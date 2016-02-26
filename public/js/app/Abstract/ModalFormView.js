App.define('Abstract.ModalFormView',{

    _isAbstract_: true,
    $domObj: '',

    eachInput: function(handle){
        var $inputs = this.getBody().find('form input, form textarea');
        $inputs.each(function(index, input){
            handle(input);
        });
    },

    getInput: function(name){
        return this.getBody().find('form input[name='+name+']');
    },

    setInputFeedback: function(input, type, message){

        var $group = $(input).parent('.form-group');
        $group.removeClass('has-success has-warning has-error');

        switch(type){
            case 'success':
                $group.addClass('has-success');
            break;

            case 'warning':
                $group.addClass('has-warning');
            break;

            case 'error':
                $group.addClass('has-error');
            break;

            case 'none':
            default:
                return;
        }

        var $feedback = $group.find('.feedback');

        if($feedback.length <= 0){
            $feedback = $($.parseHTML('<p class="feedback help-block" ></p>')[0]);
            $group.append($feedback);
        }

        if(!message){
            $feedback.html('');
        }
        else{
            $feedback.html(message.toString());
        }
    },

    resetForm: function(){
        var me = this;

        me.eachInput(function(input){

            switch(input.type){

                case 'checkbox':
                    if(input.checked) $(input).click();
                break;

                case 'number':
                    $(input).val(input.min);
                break;

                case 'text':
                case 'hidden':
                default :
                   $(input).val('');
            }

            me.setInputFeedback(input, 'none');
        });
    },

    enableSave: function(enable){
        enable = typeof(enable) !== 'undefined' && enable ? true : false;
        this.$domObj.find('.submit-action').prop("disabled", !enable);
    },

    enableClose: function(enable){
        enable = typeof(enable) !== 'undefined' && enable ? true : false;
        this.$domObj.find('.cancel-action').prop("disabled", !enable);
    },

    onSubmit: function(){
        this._appRoot_.Base.fireEvent('submit', this.$domObj, [this]);
    },

    onBeforeShow: function(){
        this._appRoot_.Base.fireEvent('beforeshow', this.$domObj, [this]);
        this.enableSave(false);
        this.enableClose(false);
    },

    onShow: function(){
        this._appRoot_.Base.fireEvent('show', this.$domObj, [this]);
        this.enableSave(true);
        this.enableClose(true);
    },

    onClose: function(){
        this._appRoot_.Base.fireEvent('close', this.$domObj, [this]);
    },

    addListener: function(eventName, handle){

        switch (eventName){
            case 'beforeshow':
                eventName = 'show.bs.modal';
            break;
            case 'show':
                eventName = 'shown.bs.modal';
            break;
            case 'close':
                eventName = 'hidden.bs.modal';
            break;
        }

        this.$domObj.on(eventName, handle);
    },

    ready: function(){
        var me = this;
        me.callSuper();

        me.setFooter([

            $($.parseHTML('<button type="button" class="btn btn-primary submit-action">Enviar</button>'))
            .click(function(e){me.onSubmit(e);}),

            $($.parseHTML('<button type="button" class="btn btn-default cancel-action" data-dismiss="modal">Fechar</button>'))

        ]);
    },

    init: function(){
        this.callSuper();
    }

}, 'Abstract.ModalView');
