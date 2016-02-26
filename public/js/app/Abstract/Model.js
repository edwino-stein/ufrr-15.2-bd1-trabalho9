App.define('Abstract.Model', {

    _isAbstract_: true,

    createUrl: '',
    readUrl: '',
    updateUrl: '',
    deleteUrl: '',
    readParams: {},
    schema:{},
    validator:{},

    read: function(callback, params){

        if(!(callback instanceof Function)){
            callback = function(){};
        }

        if(typeof(params) === 'undefined') params = this.readParams;

        var me = this,
            ajax = this._appRoot_.get('Util.Ajax');

        ajax.request({
            url: me.readUrl,
            method: 'POST',
            data: params,
            success: function(data, response){

                var dataStore = {},
                    counter = 0,
                    model;

                for(var i in data){
                    model = me.getInstance(data[i]);
                    dataStore[model.id] = model;
                    counter++;
                }

                callback(true, dataStore, counter, response);
            },
            fail: function(response, type, message){
                callback(false, {}, 0, response);
            }
        });
    },

    create: function(callback, model){

        if(!this._appRoot_.get('Util.Classes').isInstance('ModelBase', model) || model.entityName !== this._namespace_){
            throw 'Model inválida.';
        }

        if(!(callback instanceof Function)){
            callback = function(){};
        }

        var me = this,
            ajax = this._appRoot_.get('Util.Ajax');

        ajax.request({
            url: me.createUrl,
            method: 'POST',
            data: model.serialize(),
            success: function(data, response){
                model.apply(data);
                callback(true, model, response);
            },
            fail: function(response, type, message){
                callback(false, {}, 0, response);
            }
        });
    },

    update: function(callback, model){

        if(!this._appRoot_.get('Util.Classes').isInstance('ModelBase', model) || model.entityName !== this._namespace_){
            throw 'Model inválida.';
        }

        if(!(callback instanceof Function)){
            callback = function(){};
        }

        var me = this,
            ajax = this._appRoot_.get('Util.Ajax');

        ajax.request({
            url: me.updateUrl,
            method: 'POST',
            data: model.serialize(),
            success: function(data, response){
                model.apply(data);
                callback(true, model, response);
            },
            fail: function(response, type, message){
                callback(false, {}, 0, response);
            }
        });
    },

    delete: function(callback, model){

        if(!this._appRoot_.get('Util.Classes').isInstance('ModelBase', model) || model.entityName !== this._namespace_){
            throw 'Model inválida.';
        }

        if(!(callback instanceof Function)){
            callback = function(){};
        }

        var me = this,
            ajax = this._appRoot_.get('Util.Ajax');

        ajax.request({
            url: me.deleteUrl,
            method: 'POST',
            data: model.serialize(),
            success: function(data, response){
                model.apply(data);
                callback(true, model, response);
            },
            fail: function(response, type, message){
                callback(false, {}, 0, response);
            }
        });
    },

    getInstance: function(data){
        data = typeof(data) === 'object' ? data : {};
        return this._appRoot_.get('Util.Classes').getInstance('ModelBase', this.schema, data, this._namespace_);
    }
});
