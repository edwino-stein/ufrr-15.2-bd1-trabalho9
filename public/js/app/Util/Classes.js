App.define('Util.Classes',{

    instantiable: [],

    ModelBase: function ModelBase(schema, data, entityName){

        this.entityName = typeof(entityName) === 'string' ? entityName : 'undefined';
        var schema = schema;

        this.get = function(key){
            return schema.hasOwnProperty(key) ? this[key] : null;
        };

        this.set = function(key, value){
            if(schema.hasOwnProperty(key)){
                this[key] = schema[key](value);
            }
        };

        this.apply = function(d){
            for(var i in schema){
                this.set(i, d.hasOwnProperty(i) ? d[i] : null);
            }
        };

        this.serialize = function(){
            var data = [];
            for(var i in schema) data.push(i+'='+this[i]);
            return data.join('&');
        };

        this.apply(data);
    },

    getInstance: function(className){

        if(this.instantiable.indexOf(className) < 0)
            throw  'Classe inválida';

        var args = Array.prototype.slice.call(arguments);
        args.shift();

        return new this[className](...args);
    },

    isInstance: function(className, obj){

        if(this.instantiable.indexOf(className) < 0 || typeof (obj) !== 'object')
            return false;

        return obj instanceof this[className];
    },

    init: function(){
        var unInstantiable = ['_appRoot_', '_initted_', '_namespace_', '_parent_', '_super_', 'apply', 'hasProperty', 'init', 'ready', 'getInstance', 'instantiable'];

        for(var i in this){
            if(unInstantiable.indexOf(i) < 0)
                this.instantiable.push(i);
        }
    }
});
