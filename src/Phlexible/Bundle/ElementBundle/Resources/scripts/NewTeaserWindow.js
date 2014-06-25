Phlexible.elements.NewTeaserWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.elements.Strings.new_teaser,
    width: 450,
    minWidth: 450,
    height: 320,
    minHeight: 320,
    iconCls: 'p-element-add-icon',

    textHeader: Phlexible.elements.Strings.new_teaser_header,
    textDescription: Phlexible.elements.Strings.new_teaser_description,
    textOk: Phlexible.elements.Strings.save,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-newteaser',
    iconClsOk: 'p-element-save-icon',

    getSubmitUrl: function() {
        return Phlexible.Router.generate('teasers_layout_createteaser');
    },

    labelWidth: 100,

    getFormItems: function(){
        var items = [{
            xtype: 'combo',
            fieldLabel: Phlexible.elements.Strings.elementtype,
            hiddenName: 'element_type_id',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('teasers_layout_childelementtypes'),
                baseParams: {
                    id: this.submitParams.layoutarea_id
                },
                root: 'elementtypes',
                fields: ['id', 'title', 'icon'],
                autoLoad: true
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
            allowBlank: false
        },{
            xtype: 'combo',
            fieldLabel: Phlexible.elements.Strings.position,
            hiddenName: 'prev_id',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('teasers_layout_childelements'),
                baseParams: this.submitParams,
                root: 'elements',
                fields: ['id', 'title', 'icon'],
                autoLoad: true,
                listeners: {
                    load: {
                        fn: function() {
                            this.getComponent(0).getComponent(2).setValue('0');
                        },
                        scope: this
                    }
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
        },{
            xtype: 'checkbox',
            name: 'inherit',
            fieldLabel: '',
            labelSeparator: '',
            boxLabel: Phlexible.elements.Strings.inherited_teaser,
            checked: true
        },{
            xtype: 'checkbox',
            name: 'no_display',
            fieldLabel: '',
            labelSeparator: '',
            boxLabel: Phlexible.elements.Strings.not_shown_teaser
        }];

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

        return items;
    }
});
