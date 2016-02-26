App.define('Controller.Visitas',{

   $createBtn: '.new-guestbook',

    table: 'View.Table',
    model: 'Model.Visitas',
    form: 'View.Form',
    $container : '#main-panel',

    onAddRow: function(){
        this.$container.find('.total').html(this.dataTotal);
    },

    create: function(){
        this.form.setTitle('Registrar Visita');
        this.callSuper();
    },

    ready: function(){
        this.callSuper();
    },

    init: function(){
        this.callSuper();
    }

}, 'Abstract.TableController');
