App.define('Abstract.TableView', {

    _isAbstract_: true,

    $container: '',
    $tableDomObj: '',
    rowCls: '',
    rowTpl: '',

    renderer: function (field, data){
        return data.toString();
    },

    getRowTpl: function (){
        return $.parseHTML(this.rowTpl)[0];
    },

    selectRow: function (row){
        var me = this,
            $selected = me.$tableDomObj.find('.selected');

        $selected.each(function(index, tr){me.desselectRow(tr);});

        $(row).addClass('selected');
        me._appRoot_.Base.fireEvent(
            'select-row',
            me.$tableDomObj[0],
            {
                row: row,
                id: $(row).data('id')
            }
        );
    },

    desselectRow: function(row){
        $(row).removeClass('selected');
        this._appRoot_.Base.fireEvent(
            'desselect-row',
            this.$tableDomObj[0],
            {
                row: row,
                id: $(row).data('id')
            }
        );
    },

    dblClickRow: function(row){
        this._appRoot_.Base.fireEvent(
            'dblclick-row',
            this.$tableDomObj[0],
            {
                row: row,
                id: $(row).data('id')
            }
        );
    },

    scrollToRow: function(row){

        if(!(row instanceof HTMLElement) || !$(row).is(":visible")){
            return;
        }

        $(window).scrollTop($(row).offset().top);
    },

    addRow: function (data){

        var me = this,
            row = me.getRowTpl();

        $(row).addClass(this.rowCls);
        me.updateRow(row, data);
        me.addListener(row);
        me.$tableDomObj.append(row);

        return row;
    },

    updateRow: function(row, data){

        if(!(row instanceof HTMLElement)){
            console.error('Objeto inválido.');
            return;
        }

        var $row = $(row);
        if(!$row.hasClass(this.rowCls)) return;

        for(var i in data){

            var $col = $row.find('.'+i);
            if($col.length <= 0) continue;

            var html = this.renderer(i, data[i]);
            $col.html(html);
        }

        $row.attr('data-id', data['id']);
    },

    removeRow: function(row){

        if(!(row instanceof HTMLElement)){
            console.error('Objeto inválido.');
            return;
        }

        this.desselectRow(row);
        var $row = $(row);

        if($row.hasClass(this.rowCls))
            $(row).remove();
    },

    removeAllRows: function(){
        var me = this;
        me.$tableDomObj.find('.'+me.rowCls).each(function(index, row){
            me.removeRow(row);
        });
    },

    addListener: function(row){
        var me = this;
        $(row).click(function(e){
            me.selectRow(row, e);
        }).dblclick(function (){
            me.dblClickRow(row);
        });
    },

    queryRow: function(selector){
        return this.$tableDomObj.find(selector)[0];
    },

    showEmptyMessage: function (){
        this.$container.find('.empty-message').show();
    },

    hideEmptyMessage: function (){
        this.$container.find('.empty-message').hide();
    },

    init: function(){
        this.$container = $(this.$container);
        this.$tableDomObj = $(this.$tableDomObj);
        this.showEmptyMessage();
    }
});
