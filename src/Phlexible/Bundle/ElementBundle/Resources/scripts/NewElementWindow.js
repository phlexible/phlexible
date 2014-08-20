Phlexible.elements.NewElementWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.elements.Strings.new_element,
    width: 550,
    minWidth: 550,
    height: 350,
    minHeight: 350,
    iconCls: 'p-element-add-icon',

    textHeader: Phlexible.elements.Strings.new_element_header,
    textDescription: Phlexible.elements.Strings.new_element_description,
    textOk: Phlexible.elements.Strings.save,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-newelement',
    iconClsOk: 'p-element-save-icon',

    getSubmitUrl: function () {
        return Phlexible.Router.generate('tree_create');
    },

    labelWidth: 100,

    getFormItems: function () {
        var items = [
            {
                xtype: 'combo',
                fieldLabel: Phlexible.elements.Strings.elementtype,
                hiddenName: 'element_type_id',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('tree_childelementtypes'),
                    baseParams: this.submitParams,
                    root: 'elementtypes',
                    fields: ['id', 'title', 'icon', 'type'],
                    autoLoad: true,
                    listeners: {
                        load: function (store) {
                            if (this.element_type_id) {
                                this.getComponent(0).getComponent(1).setValue(this.element_type_id);
                            }
                        },
                        scope: this
                    }
                }),
                emptyText: Phlexible.elements.Strings.select_elementtype,
                displayField: 'title',
                valueField: 'id',
                listClass: 'x-combo-list-big',
                editable: false,
                tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                allowBlank: false,
                listeners: {
                    select: function (cb, record) {
                        if (record.get('type') == Phlexible.elementtypes.TYPE_FULL) {
                            this.getComponent(0).getComponent(4).enable();
                        } else {
                            this.getComponent(0).getComponent(4).setValue(0);
                            this.getComponent(0).getComponent(4).disable();
                        }
                    },
                    scope: this
                }
            },
            {
                xtype: 'combo',
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
                        load: function () {
                            this.getComponent(0).getComponent(2).setValue('0');
                        },
                        scope: this
                    }
                }),
                value: '0',
                valueField: 'id',
                displayField: 'title',
                listClass: 'x-combo-list-big',
                editable: false,
                tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                allowBlank: false,
                helpText: Phlexible.elements.Strings.position_help
            },
            {
                xtype: 'iconcombo',
                fieldLabel: Phlexible.elements.Strings.sort_mode,
                hiddenName: 'sort',
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'title', 'icon'],
                    data: [
                        ['free', Phlexible.elements.Strings.free, 'p-element-sort_free-icon'],
                        ['title', Phlexible.elements.Strings.title, 'p-element-sort_alpha_down-icon'],
                        ['createdate', Phlexible.elements.Strings.created, 'p-element-sort_date_down-icon'],
                        ['publishdate', Phlexible.elements.Strings.published, 'p-element-sort_date_down-icon'],
                        ['customdate', Phlexible.elements.Strings.custom_date, 'p-element-sort_date_down-icon']
                    ]
                }),
                displayField: 'title',
                valueField: 'id',
                iconClsField: 'icon',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                value: 'free',
                allowBlank: false
            },
            {
                xtype: 'checkbox',
                fieldLabel: '',
                name: 'navigation',
                labelSeparator: '',
                boxLabel: Phlexible.elements.Strings.in_navigation,
                disabled: true
            },
            {
                xtype: 'checkbox',
                fieldLabel: '',
                name: 'restricted',
                labelSeparator: '',
                boxLabel: Phlexible.elements.Strings.is_restricted,
                checked: Phlexible.Config.get('elements.create.restricted')
            }
        ];

        if (Phlexible.Config.get('elements.create.use_multilanguage')) {
            if (Phlexible.Config.get('set.language.frontend').length > 1) {
                items.push({
                    xtype: 'iconcombo',
                    fieldLabel: Phlexible.elements.Strings.masterlanguage_dialog,
                    hiddenName: 'masterlanguage',
                    store: new Ext.data.SimpleStore({
                        fields: ['key', 'title', 'iconCls'],
                        data: Phlexible.Config.get('set.language.frontend')
                    }),
                    value: Phlexible.Config.get('language.frontend'),
                    valueField: 'key',
                    displayField: 'title',
                    iconClsField: 'iconCls',
                    editable: false,
                    mode: 'local',
                    typeAhead: false,
                    emptyText: '_select',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    anchor: '-70',
                    allowBlank: false
                });
            }
            else {
                items.push({
                    xtype: 'textfield',
                    inputType: 'hidden',
                    name: 'masterlanguage',
                    value: Phlexible.Config.get('set.language.frontend')[0][0]
                });
            }
        }
        else {
            items.push({
                xtype: 'textfield',
                inputType: 'hidden',
                name: 'masterlanguage',
                value: this.language
            });
        }

        return items;
    }

//        this.getComponent(0).getComponent(1).setValue(this.elementtype_id);
//        this.getComponent(0).getComponent(2).setValue('0');
});
