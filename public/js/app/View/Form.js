App.define('View.Form',{

    $domObj: '#visitas-form',

    ready: function(){
        this.callSuper();
        this.setTitle('Registrar Visita');
        var template = $('#form-template');
        this.setBody(template.find('.template-container').clone().removeClass('template-container'));
        template.remove();
    },

    init: function(){
        this.callSuper();
    }

}, 'Abstract.ModalFormView');
