Phlexible.elementtypes.configuration.FieldContentchannel = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.content_channels,
    iconCls: 'p-contentchannel-component-icon',
    border: false,
    autoScroll: true,
    bodyStyle: 'padding:3px',
    defaultType: 'textfield',
    labelWidth: 100,
    defaults: {
//        width: 200
    },

    initComponent: function () {
        this.items = [
            {
                xtype: 'checkbox',
                fieldLabel: 'Deactivation',
                boxLabel: this.strings.allow,
                name: 'allow_deactivation'
            },
            {
                xtype: 'checkbox',
                fieldLabel: 'Unlink',
                boxLabel: this.strings.allow,
                name: 'allow_unlink'
            },
            {
                xtype: 'elementtypes-configuration-field-contentchannel-grid',
                listeners: {
                    defaultchange: function (key) {
                        this.getComponent(0).setValue(key);
                    },
                    scope: this
                }
            }
        ];

        Phlexible.elementtypes.configuration.FieldContentchannel.superclass.initComponent.call(this);
    },

    getContentchannelGrid: function () {
        return this.getComponent(2);
    },

    loadData: function (fieldData, fieldType) {
        fieldData = fieldData || {};
        fieldData = Ext.apply(fieldData, {list: []});

        this.getForm().setValues([
            {id: 'allow_deactivation', value: fieldData.allow_deactivation},
            {id: 'allow_unlink', value: fieldData.allow_unlink}
        ]);

        this.getContentchannelGrid().loadData(fieldData.list);
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();

        var list = [];

        for (var i = 0; i < this.getContentchannelGrid().getStore().getCount(); i++) {
            var r = this.getContentchannelGrid().getStore().getAt(i);
            if (r.get('available')) {
                list.push(r.get('id'));
            }
        }
        this.getContentchannelGrid().getStore().commitChanges();

        values.list = list;

        return values;
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            //this.header.child('span').removeClass('error');
            this.setIconClass('p-contentchannel-component-icon');

            return true;
        } else {
            //this.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    },

    isActive: function() {
        return !!this.active;
    },

    loadField: function (properties, node, fieldType) {
        this.active = true;
        this.ownerCt.getTabEl(this).hidden = false;
        this.loadData(properties.content_channels, fieldType);
    }
});
Ext.reg('elementtypes-configuration-field-contentchannel', Phlexible.elementtypes.configuration.FieldContentchannel);

Phlexible.elementtypes.configuration.FieldContentchannelGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.elementtypes.Strings.content_channels,
    strings: Phlexible.elementtypes.Strings,
    autoExpandColumn: 'title',
    border: true,
    autoHeight: true,
    viewConfig: {
        forceFit: true
    },

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elementtypes_data_contentchannels'),
            root: 'contentChannels',
            id: 'id',
            fields: ['id', 'title', 'available'],
            autoLoad: true
        });

        this.columns = [
            {
                id: 'title',
                header: this.strings.contentchannel,
                dataIndex: 'title',
                width: 200
            },
            this.cc1 = new Ext.grid.CheckColumn({
                header: this.strings.available,
                dataIndex: 'available',
                width: 50
            })
        ];

        this.plugins = [this.cc1];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        Phlexible.elementtypes.configuration.FieldContentchannelGrid.superclass.initComponent.call(this);
    },

    loadData: function (data) {
        this.store.each(function (r) {
            r.set('available', false);
        });
        for (var i = 0; i < data.length; i++) {
            var r = this.store.getById(data[i]);
            if (r) {
                r.set('available', true);
            }
        }

        this.store.commitChanges();
    }
});

Ext.reg('elementtypes-configuration-field-contentchannel-grid', Phlexible.elementtypes.configuration.FieldContentchannelGrid);