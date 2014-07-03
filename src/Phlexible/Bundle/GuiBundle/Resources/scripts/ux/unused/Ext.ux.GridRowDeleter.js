/*jsl:ignoreall*/
Ext.ux.GridRowDeleter = function (config) {
    Ext.apply(this, config);
    if (!this.id) {
        this.id = Ext.id(null, 'x-grid3-row-deleter');
    }
    this.header = '<div class="x-grid3-row-deleter"> </div>';
    this.width = 25;
    this.renderer = this.renderer.createDelegate(this);
}
Ext.ux.GridRowDeleter.prototype = {
    init: function (grid) {
        this.grid = grid;
        this.grid.on('render', function () {
            this.grid.on('cellclick', this.onClick, this);
        }, this);
    },

    getColumnIndex: function () {
        return this.grid.getColumnModel().getIndexById(this.id);
    },

    onClick: function (grid, rowIndex, columnIndex) {
        if (columnIndex == this.getColumnIndex()) {
            grid.getStore().remove(grid.getStore().getAt(rowIndex));
        }
    },

    renderer: function (value, meta) {
        meta.css = 'x-grid3-row-deleter';
        return '<div ext:qtip="Delete Row"> </div>';
    }
}