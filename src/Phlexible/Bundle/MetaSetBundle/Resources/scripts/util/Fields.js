Ext.provide('Phlexible.metasets.util.Fields');

Phlexible.metasets.util.Fields = function (config) {
    this.initFields();
    this.initEditors();
    this.initSelectEditorCallbacks();
    this.initBeforeEditCallbacks();
    this.initAfterEditCallbacks();

    Phlexible.metasets.util.Fields.superclass.constructor.call(this, config);
};
Ext.extend(Phlexible.metasets.util.Fields, Ext.util.Observable, {
    getFields: function () {
        return this.fields;
    },

    getEditors: function () {
        return this.editors;
    },

    getSelectEditorCallbacks: function () {
        return this.selectEditorCallbacks;
    },

    getBeforeEditCallbacks: function () {
        return this.beforeEditCallbacks;
    },

    getAfterEditCallbacks: function () {
        return this.afterEditCallbacks;
    },

    initFields: function () {
        this.fields = [
            ['textfield', 'Textfield'],
            ['textarea', 'Textarea'],
            ['date', 'Date'],
            ['boolean', 'Boolean'],
            ['select', 'Select']
        ];
    },

    initEditors: function () {
        this.editors = {
            textfield: new Ext.form.TextField(),
            textarea: new Ext.form.TextArea(),
            date: new Ext.form.DateField({
                format: 'd.m.Y'
            }),
            'boolean': new Ext.form.ComboBox({
                store: new Ext.data.SimpleStore({
                    fields: ['value'],
                    data: [
                        ['true'],
                        ['false']
                    ]
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

    initSelectEditorCallbacks: function () {
        this.selectEditorCallbacks = {
            select: function (editor, record) {
                var options = Phlexible.clone(record.data.options);
                if (!record.data.required) {
                    options.unshift(['', '(' + Phlexible.elements.Strings.empty + ')']);
                }
                editor.field.store.loadData(options);
            }
        };
    },

    initBeforeEditCallbacks: function () {
        this.beforeEditCallbacks = {};
    },

    initAfterEditCallbacks: function () {
        this.afterEditCallbacks = {};
    }
});
