App.define('Abstract.TableController', {

    _isAbstract_: true,

    table: '',
    model: '',
    form: '',

    $container :'body',
    $createBtn: '.create-btn',
    $updateBtn: '.update-btn',
    $deleteBtn: '.delete-btn',
    $refreshBtn:'.refresh-btn',

    dataStore: {},
    dataTotal: 0,
    selectedRow: null,

    rasterTable: function(data, total){

        var model, days = [];

        this.table.removeAllRows();
        this.dataTotal = 0;

        if(total > 0){

            this.table.hideEmptyMessage();

            for(var i in data){
                this.addOrUpdateTableRow(data[i]);
            }
        }
        else{
            this.table.showEmptyMessage();
        }

        return total;
    },

    modelToDisplay:function(model){
        return model;
    },

    addOrUpdateTableRow: function(model, autoSelect){

        autoSelect = autoSelect ? true : false;
        var row = null;

        if(this.dataStore.hasOwnProperty(model.id)){

            row = this.table.queryRow('li[data-id='+model.id+']');

            if(row){
                this.table.updateRow(row, this.modelToDisplay(model));
            }
            else{
                row = this.table.addRow(this.modelToDisplay(model));
                this.dataTotal++;
            }

            this.dataStore[model.id] = model;
        }
        else{

            row = this.table.addRow(this.modelToDisplay(model));
            this.dataTotal++;
            this.dataStore[model.id] = model;
            this.table.hideEmptyMessage();
            this.onAddRow();
        }

        if(autoSelect){
            this.table.selectRow(row);
            this.table.scrollToRow(row);
        }
    },

    removeTableRow: function(model){

        if(this.dataStore.hasOwnProperty(model.id)){
            var row = this.table.queryRow('li[data-id='+model.id+']');
            this.table.removeRow(row);
            this.dataTotal--;
            delete this.dataStore[model.id];
        }
    },

    updateTable: function(){

        var me = this;

        me.model.read(function(success, data, total, response){
            if(success){
                me.rasterTable(data, total);
            }
            else{
                if(response.hasOwnProperty('responseJSON')){
                    console.error(response.responseJSON.errors);
                }
                else{
                    console.error('Erro na letitura dos dados', arguments);
                }
            }
        });
    },

    onSelectRow: function(row){
        this.selectedRow = row;
        this.$updateBtn.prop("disabled", false);
        this.$deleteBtn.prop("disabled", false);
    },

    onDesselectRow: function(row){
        this.selectedRow = null;
        this.$updateBtn.prop("disabled", true);
        this.$deleteBtn.prop("disabled", true);
    },

    onAddRow: function(){},

    getFormValues: function(validate){

        var me = this,
            formIsValid = true,
            data = {};

        validate = typeof(validate) !== 'undefined' && validate ? validate : false;

        me.form.eachInput(function(input){

            var name = input.name,
                value;

            //pega o valor para cada tipo de input
            switch (input.type.toLowerCase()){
                case 'checkbox':
                    value = input.checked;
                break;
                default:
                    value = $(input).val();
            }

            if(validate){

                var isValid = true;

                if(me.model.validator.hasOwnProperty(name)){
                    isValid = me.model.validator[name](value);
                }

                if(isValid === true){
                    me.form.setInputFeedback(input, 'success');
                }

                else{
                    me.form.setInputFeedback(input, 'error', isValid);
                    formIsValid = false;
                }

            }

            if(me.model.schema.hasOwnProperty(name)){
                try{
                    data[name] = me.model.schema[name](value);
                }catch (e){
                    formIsValid = false;
                    console.warn('Alerta o campo "'+name+'": '+e);
                }
            }
        });

        return {
            isValid: formIsValid,
            data: data
        };
    },

    save: function(data){
        var me = this,
            formValues = me.getFormValues(true),
            model = null, operation;

        if(!formValues.isValid){
            console.error('Valores inválidos.');
            return;
        }

        if(isNaN(formValues.data.id) || formValues.data.id <= 0){
            operation = 'create';
            model = me.model.getInstance(formValues.data);
        }

        else if(me.dataStore.hasOwnProperty(formValues.data.id)){
            operation = 'update';
            model = me.dataStore[formValues.data.id];
            model.apply(formValues.data);
        }

        else{
            console.error('Model inválida');
            return;
        }

        me.form.enableSave(false);
        me.form.enableClose(false);

        me.model[operation](function(success, model, response){

            if(success){
                me.addOrUpdateTableRow(model, true);
                me.form.close();
            }

            else{
                console.log(response);
                alert('Um erro ocorreu no servidor.');
                me.form.enableSave(true);
                me.form.enableClose(true);
            }

        }, model);
    },

    create: function(){
        this.form.resetForm();
        this.form.show();
    },

    update: function(){

        if(!this.selectedRow || typeof(this.selectedRow.id) !== 'number'){
            console.log('Nenhuma linha selecionada');
            return;
        }

        if(!this.dataStore.hasOwnProperty(this.selectedRow.id)){
            console.log('Registro inválido');
            return;
        }

        this.form.resetForm();
        this.form.show();
        return this.dataStore[this.selectedRow.id];
    },

    delete: function(model, message, title){

        var me = this;

        if(!me._appRoot_.get('Util.Classes').isInstance('ModelBase', model))
            throw 'Model inválida.';

        if(typeof (message) === 'undefined')
            message = 'Deseja realmente excluir o registro?';

        if(typeof (title) === 'undefined')
            title = 'Excluir o registro';


        me._appRoot_.get('View.ConfirmationModal').show({
            title: title,
            body: message,
            yes: function(){

                me.table.mask.show();
                me.model.delete(function(success, model, response){

                    if(success){
                         me.removeTableRow(model);
                         me.table.mask.hide();
                    }

                    else{
                        /**
                         * TODO: Exibir erro
                         */
                        console.log(response);
                    }


                }, model);
            }
        });

    },

    ready: function(){

        var me = this;

        me.$container = $(me.$container);
        me.$createBtn = me.$container.find(me.$createBtn);
        me.$updateBtn = me.$container.find(me.$updateBtn);
        me.$deleteBtn = me.$container.find(me.$deleteBtn);
        me.$refreshBtn = me.$container.find(me.$refreshBtn);
        me.table = me._appRoot_.get(me.table);
        me.model = me._appRoot_.get(me.model);
        me.form = me._appRoot_.get(me.form);

        me.updateTable();

        me.$updateBtn.prop("disabled", true);
        me.$deleteBtn.prop("disabled", true);

        me.table.$tableDomObj.on('select-row', function(e, selected){
            me.onSelectRow(selected);
        }).on('desselect-row', function(e, desselected){
            me.onDesselectRow(desselected);
        });

        me.$createBtn.click(function(){me.create();});
        me.$updateBtn.click(function(){me.update();});
        me.$refreshBtn.click(function(){me.updateTable();});

        me.table.$tableDomObj.on('dblclick-row', function(e){
            me.update();
        });

        me.$deleteBtn.click(function(){

            if(!me.selectedRow || typeof(me.selectedRow.id) !== 'number'){
                console.log('Nenhuma linha selecionada');
                return;
            }

            if(!me.dataStore.hasOwnProperty(me.selectedRow.id)){
                console.log('Registro inválido');
                return;
            }

            var model = me.dataStore[me.selectedRow.id];
            me.delete(model);
        });

        me.form.addListener('submit', function(){me.save();});
    }
});
