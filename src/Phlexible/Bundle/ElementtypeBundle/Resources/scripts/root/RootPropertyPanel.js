Phlexible.elementtypes.RootPropertyPanel = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.properties,
    iconCls: 'p-elementtype-tab_properties-icon',
    autoScroll: true,
    border: false,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 120,

    initComponent: function() {
        this.items = [{
            xtype: 'textfield',
            fieldLabel: this.strings.name,
            name: 'title',
            width: 200,
            allowBlank: false
        },{
            fieldLabel: this.strings.uniqueid,
            name: 'unique_id',
            width: 200,
            allowBlank: false
        },{
            xtype: 'trigger',
            fieldLabel: this.strings.icon,
            name: 'icon',
            width: 200,
            onTriggerClick: function() {
                var www = new Phlexible.gui.util.ImageSelectWindow({
                    storeUrl: Phlexible.Router.generate('elementtypes_data_images'),
                    value: this.getComponent(2).getValue(),
                    listeners: {
                        imageSelect: function(image) {
                            //Phlexible.console.log(image);

                            this.getComponent(2).setValue(image);
                        },
                        scope: this
                    }
                });
                www.show();
            }.createDelegate(this)
        },{
            xtype: 'twincombobox',
            fieldLabel: this.strings.default_tab,
            emptyText: this.strings.no_default_tab,
            hiddenName: 'default_tab',
            width: 200,
            listWidth: 200,
            store: new Ext.data.SimpleStore({
                fields: ['id', 'title'],
                data: [
                    ['0', 'List'],
                    ['1', 'Data'],
                    ['4', 'Links'],
                    ['5', 'History']
                ]
            }),
            displayField: 'title',
            valueField: 'id',
            editable: false,
            mode: 'local',
            typeAhead: false,
            triggerAction: 'all',
            selectOnFocus: true,
            allowEmpty: true
        },{
            xtype: 'twincombobox',
            fieldLabel: this.strings.default_content_tab,
            emptyText: this.strings.no_default_content_tab,
            hiddenName: 'default_content_tab',
            width: 200,
            listWidth: 200,
            store: new Ext.data.SimpleStore({
                fields: ['id', 'title'],
                data: [
                    ['', 'No Tab defined']
                ]
            }),
            displayField: 'title',
            valueField: 'id',
            editable: false,
            mode: 'local',
            typeAhead: false,
            triggerAction: 'all',
            selectOnFocus: true,
            allowEmpty: true
        },{
            xtype: 'twincombobox',
            fieldLabel: this.strings.metaset,
            emptyText: this.strings.no_metaset,
            hiddenName: 'metaset',
            width: 200,
            listWidth: 200,
            store: new Ext.data.SimpleStore({
                fields: ['id', 'title'],
                data: []
            }),
            displayField: 'title',
            valueField: 'id',
            editable: false,
            mode: 'local',
            typeAhead: false,
            triggerAction: 'all',
            selectOnFocus: true,
            allowEmpty: true
        },{
            xtype: 'textarea',
            fieldLabel: this.strings.comment,
            name: 'comment',
            width: 200
        },{
            hidden: true
        },{
            xtype: 'checkbox',
            fieldLabel: '',
            labelSeparator: '',
            boxLabel: this.strings.hide_children,
            name: 'hide_children'
        }];

        Phlexible.elementtypes.RootPropertyPanel.superclass.initComponent.call(this);
    },

    loadValues: function(properties, node) {
        var store = this.getComponent(4).store;
        store.removeAll();
        for(var i=0; i<node.childNodes.length; i++) {
            store.add(new Ext.data.Record({id: i + '', title: node.childNodes[i].text}));
        }

        var metaStore = this.getComponent(5).store;
        metaStore.removeAll();
        metaStore.loadData(properties.metasets);

        if (properties.root.default_content_tab !== null) {
            properties.root.default_content_tab += '';
        }

        this.getForm().setValues(properties.root);
    },

    loadRoot: function(properties, node) {
        this.loadValues(properties, node);
    },

    getSaveValues: function() {
        return this.getForm().getValues();
    },

    isValid: function() {
        if(this.getForm().isValid()) {
            //this.header.child('span').removeClass('error');
            this.setIconClass('p-elementtype-tab_properties-icon');

            return true;
        } else {
            //this.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    }
});

Ext.reg('elementtypes-root-properties', Phlexible.elementtypes.RootPropertyPanel);