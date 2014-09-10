Phlexible.elementfinder.configuration.FieldConfigurationFinder = Ext.extend(Ext.form.FieldSet, {
    strings: Phlexible.elementfinder.Strings,
    title: Phlexible.elementfinder.Strings.finder,
    iconCls: 'p-elementfinder-finder-icon',
    autoHeight: true,
    labelWidth: 139,

    initComponent: function () {
        this.items = [
            {
                xtype: 'multiselect',
                fieldLabel: this.strings.elementtype,
                name: 'element_type_ids',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementfinder_catch_elementtypes'),
                    root: 'elementtypes',
                    fields: ['id', 'title', 'icon'],
                    autoLoad: true,
                    listeners: {
                        load: function () {
                            if (this.elementtypeIds) {
                                this.getComponent(0).setValue(this.elementtypeIds);
                            }
                        },
                        scope: this
                    }
                }),
                width: 300,
                emptyText: this.strings.select_elementtype,
                valueField: 'id',
                displayField: 'title',
                tpl: '<tpl for="."><div class="ux-mselect-item' + ((Ext.isIE || Ext.isIE7) ? '" unselectable=on' : ' x-unselectable"') + '><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                //                    width:280,
                height: 165,
                allowBlank: true,
                listeners: {
                    change: function() {
                        this.getComponent(3).onTrigger1Click();
                    },
                    scope: this
                }
            },{
                xtype: 'checkbox',
                fieldLabel: this.strings.elements,
                boxLabel: this.strings.in_navigation,
                name: 'in_navigation'
            },{
                xtype: 'numberfield',
                fieldLabel: this.strings.max_depth,
                name: 'max_depth',
                width: 30,
                allowBlank: true
            },{
                xtype: 'twincombobox',
                width: 283,
                listWidth: 300,
                fieldLabel: this.strings.sort_field,
                hiddenName: 'sort_field',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementfinder_catch_sortfields'),
                    root: 'data',
                    fields: ['ds_id', 'title', 'icon']
                }),
                //tpl: '<tpl for="."><div class="x-combo-list-item {icon}">{title}</div></tpl>',
                displayField: 'title',
                valueField: 'ds_id',
                mode: 'remote',
                emptyText: this.strings.unsorted,
                listClass: 'x-combo-list-big',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true,
                listeners: {
                    beforequery: function (event) {
                        console.log(this.getComponent(0).getValue());
                        event.query = this.getComponent(0).getValue();
                    },
                    scope: this
                }
            },{
                xtype: 'combo',
                width: 283,
                listWidth: 300,
                fieldLabel: this.strings.sort_dir,
                hiddenName: 'sort_order',
                store: new Ext.data.SimpleStore({
                    fields: ['title', 'value'],
                    data: [
                        [this.strings.sort_ascending, 'ASC'],
                        [this.strings.sort_descending, 'DESC']
                    ]
                }),
                value: 'ASC',
                displayField: 'title',
                valueField: 'value',
                mode: 'local',
                listClass: 'x-combo-list-big',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true,
                allowBlank: false
            },{
                xtype: 'twincombobox',
                width: 283,
                listWidth: 300,
                fieldLabel: this.strings.filter,
                hiddenName: 'catch_filter',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elementfinder_catch_filters'),
                    root: 'filters',
                    fields: ['name', 'class']
                }),
                displayField: 'name',
                valueField: 'class',
                mode: 'remote',
                emptyText: this.strings.no_filter,
                listClass: 'x-combo-list-big',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },{
                xtype: 'textfield',
                width: 300,
                fieldLabel: this.strings.template,
                name: 'template'
            }
        ];

        Phlexible.elementfinder.configuration.FieldConfigurationFinder.superclass.initComponent.call(this);
    },

    updateVisibility: function (type) {
        var isFinder = type === 'finder';
        this.getComponent(0).setDisabled(!isFinder);
        this.getComponent(1).setDisabled(!isFinder);
        this.getComponent(2).setDisabled(!isFinder);
        this.getComponent(3).setDisabled(!isFinder);
        this.getComponent(4).setDisabled(!isFinder);
        this.getComponent(5).setDisabled(!isFinder);
        this.getComponent(6).setDisabled(!isFinder);
        this.setVisible(isFinder);
    },

    loadData: function (fieldData, fieldType) {
        this.elementtypeIds = fieldData.element_type_ids || null;
        this.getComponent(0).setValue(fieldData.element_type_ids || null);
        this.getComponent(1).setValue(fieldData.in_navigation || false);
        this.getComponent(2).setValue(fieldData.max_depth || '');
        this.getComponent(3).setValue(fieldData.sort_field || null);
        this.getComponent(4).setValue(fieldData.sort_dir || 'ASC');
        this.getComponent(5).setValue(fieldData.filter || null);
        this.getComponent(6).setValue(fieldData.template || '');

        this.isValid();
    },

    getSaveValues: function () {
        return {
            element_type_ids: this.getComponent(0).getValue(),
            in_navigation: this.getComponent(1).getValue(),
            max_depth: this.getComponent(2).getValue(),
            sort_field: this.getComponent(3).getValue(),
            sort_dir: this.getComponent(4).getValue(),
            filter: this.getComponent(5).getValue(),
            template: this.getComponent(6).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid() &&
            this.getComponent(2).isValid() &&
            this.getComponent(3).isValid() &&
            this.getComponent(4).isValid() &&
            this.getComponent(5).isValid() &&
            this.getComponent(6).isValid();
    }
});

Ext.reg('elementfinder-configuration-field-configuration-finder', Phlexible.elementfinder.configuration.FieldConfigurationFinder);