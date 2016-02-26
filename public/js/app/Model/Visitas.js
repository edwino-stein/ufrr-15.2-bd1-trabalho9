App.define('Model.Visitas', {

    createUrl: 'visitas.php?controller=visitas&action=create',
    readUrl: 'visitas.php?controller=visitas&action=read',

    schema:{
        id: Number,
        nome: String,
        localizacao: String,
        mensagem: String,
        data: function(input){

            if(input !== null){
                var timeStamp = Date.parse(input.date.replace(' ', 'T')+'Z');
                return new Date(timeStamp);
            }

            return null;
        },
    },

    validator:{
        nome: function(input){
            if(input.length <= 0) return 'O nome deve ser informado.';
            if(input.length > 250) return 'O nome deve conter no máximo 250 caracteres.';
            return true;
        },

        localizacao: function(input){
            if(input.length <= 0) return 'A localização deve ser informada.';
            if(input.length > 250) return 'A localização deve conter no máximo 45 caracteres.';
            return true;
        }
    }

}, 'Abstract.Model');
