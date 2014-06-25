Phlexible.metasets.Fields = function(config) {
    this.initFields();
    this.initEditors();
    this.initSelectEditorCallbacks();
    this.initBeforeEditCallbacks();
    this.initAfterEditCallbacks();

    Phlexible.metasets.Fields.superclass.constructor.call(this, config);
};
Ext.extend(Phlexible.metasets.Fields, Ext.util.Observable, {
    getFields: function() {
        return this.fields;
    },

    getEditors: function() {
        return this.editors;
    },

    getSelectEditorCallbacks: function() {
        return this.selectEditorCallbacks;
    },

    getBeforeEditCallbacks: function() {
        return this.beforeEditCallbacks;
    },

    getAfterEditCallbacks: function() {
        return this.afterEditCallbacks;
    },

    initFields: function() {
        this.fields = [
            ['textfield', 'Textfield'],
            ['textarea', 'Textarea'],
            ['date', 'Date'],
            ['boolean', 'Boolean'],
            ['select', 'Select'],
            ['suggest', 'Suggest']
        ];
    },

    initEditors: function() {
        this.editors = {
            textfield: new Ext.form.TextField(),
            textarea: new Ext.form.TextArea(),
            date: new Ext.form.DateField({
                format: 'd.m.Y'
            }),
            'boolean': new Ext.form.ComboBox({
                store: new Ext.data.SimpleStore({
                    fields: ['value'],
                    data: [['true'], ['false']]
                }),
                displayField: 'value',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                typeAhead: false
            }),
            select: new Ext.form.ComboBox({
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value']
                }),
                valueField: 'key',
                displayField: 'value',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                typeAhead: false
            })
        };
    },

    initSelectEditorCallbacks: function() {
        this.selectEditorCallbacks = {
            select: function(editor, record) {
                editor.field.store.loadData(record.data.options);
            }
        };
    },

    initBeforeEditCallbacks: function() {
        this.beforeEditCallbacks = {
            suggest: function(grid, field, record) {
                if (grid.master !== undefined) {
                    var isSynchronized = (1 == record.get('synchronized'));

                    // skip editing english values if language is synchronized
                    if (!grid.master && isSynchronized) {
                        return false;
                    }
                }

                var w = new Phlexible.metasets.MetaSuggestWindow({
                    record: record,
                    valueField: field,
                    metaLanguage: grid.language,
                    listeners: {
                        store: function() {
                            grid.validateMeta();
                        }
                    }
                });

                w.show();

                return false;
            }
        };
    },

    initAfterEditCallbacks: function() {
        this.afterEditCallbacks = {};
    }
});
