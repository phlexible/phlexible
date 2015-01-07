Ext.provide('Phlexible.elements.NewElementInstanceWindow');

Ext.require('Phlexible.gui.util.Dialog');

Phlexible.elements.NewElementInstanceWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.elements.Strings.new_alias,
    width: 530,
    minWidth: 530,
    maxWidth: 530,
    height: 240,
    minHeight: 240,
    maxHeight: 240,
    iconCls: 'p-element-alias_add-icon',

    textHeader: Phlexible.elements.Strings.new_alias_header,
    textDescription: Phlexible.elements.Strings.new_alias_description,
    textOk: Phlexible.elements.Strings.save,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-newelementinstance',
    iconClsOk: 'p-element-save-icon',

    noFocus: false,

    getSubmitUrl: function () {
        return Phlexible.Router.generate('tree_create_instance');
    },

    labelWidth: 100,

    getFormItems: function () {
        return [
            new Phlexible.elements.EidSelector({
                fieldLabel: Phlexible.elements.Strings.source_element,
                allSiteroots: true,
                language: this.language,
                labelSeparator: '',
                element: {
                    siteroot_id: this.submitParams.siteroot_id
                },
                anchor: '-70',
                listWidth: 233,
                treeWidth: 233,
                menuConfig: {
                    width: 250
                },
                listeners: {
                    render: function (c) {
                        c.hiddenField = c.el.insertSibling({
                            tag: 'input',
                            type: 'hidden',
                            name: 'for_tree_id'
                        }, 'before', true);
                    },
                    change: function (c) {
                        c.hiddenField.value = c.getValue();
                    }
                }
            }),
            this.posCombo = new Ext.form.ComboBox({
                fieldLabel: Phlexible.elements.Strings.position,
                hiddenName: 'prev_id',
                hidden: this.sort_mode != 'free',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('tree_childelements'),
                    baseParams: this.submitParams,
                    root: 'elements',
                    fields: ['id', 'title', 'icon'],
                    autoLoad: true,
                    listeners: {
                        load: {
                            fn: function () {
                                this.posCombo.setValue('0');
                            },
                            scope: this
                        }
                    }
                }),
                value: '0',
                displayField: 'title',
                valueField: 'id',
                listClass: 'x-combo-list-big',
                tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                mode: 'remote',
                editable: false,
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                allowBlank: false
            })
        ];
    }
});
