Ext.namespace('Phlexible.gui.grid');

Phlexible.gui.grid.TypeColumnModel = function (config) {

//    if (editorsConfig) {
//        Ext.apply(config, editorsConfig);
//    }

    if (config.editors) {
        this.setEditors(config.editors);
        delete config.editors;
    }
    if (config.field) {
        this.setEditorField(config.field);
        delete config.field;
    }
    if (config.store) {
        this.setStore(config.store);
        delete config.store;
    }
    if (config.grid) {
        config.grid.on({
            beforeedit: {
                fn: function (e) {
                    var grid = e.grid;
                    var record = e.record;

                    // call before edit callbacks
                    var type = record.data[this.editorField];
                    if (this.beforeEditCallbacks[type]) {
                        var field = e.field;

                        if (this.beforeEditCallbacks[type]
                            && false === this.beforeEditCallbacks[type](grid, field, record)) {
                            return false;
                        }
                    }
                },
                scope: this
            },
            afteredit: {
                fn: function (e) {
                    var grid = e.grid;
                    var record = e.record;

                    // call after edit callbacks
                    var type = record.data[this.editorField];
                    if (this.afterEditCallbacks[type]) {
                        var field = e.field;

                        if (this.afterEditCallbacks[type]) {
                            this.afterEditCallbacks[type](grid, field, record);
                        }
                    }

                },
                scope: this
            }
        });
    }

    Phlexible.gui.grid.TypeColumnModel.superclass.constructor.call(this, config);
};
Ext.extend(Phlexible.gui.grid.TypeColumnModel, Ext.grid.ColumnModel, {
    editors: {},
    editorField: 'type',
    store: false,
    selectEditorCallbacks: {},
    beforeEditCallbacks: {},
    afterEditCallbacks: {},

    getCellEditor: function (cellIndex, rowIndex) {
        if (this.store) {
            var r = this.store.getAt(rowIndex);
            var type = r.get(this.editorField);

            if (this.editors[type]) {
                var editor = this.editors[type];

                this.onEditorSelect(editor, r);
                this.fireEvent('editorselect', editor, r);

                return editor;
            }
        }

        return Phlexible.gui.grid.TypeColumnModel.superclass.getCellEditor.call(this, cellIndex, rowIndex);
    },

    onEditorSelect: function (editor, record) {
        var type = record.data[this.editorField];
        if (this.selectEditorCallbacks[type]) {
            this.selectEditorCallbacks[type](editor, record);
        }
    },

    /**
     * Set the different Cell Editors
     * @param {Array} editors Array of Form Fields
     */
    setEditors: function (editors) {
        this.editors = {};

        for (var i in editors) {
            if (editors[i].isFormField) {
                this.editors[i] = new Ext.grid.GridEditor(editors[i]);
            }
        }
    },

    setEditorField: function (field) {
        this.editorField = field;
    },

    setStore: function (store) {
        this.store = store;
    }
});
