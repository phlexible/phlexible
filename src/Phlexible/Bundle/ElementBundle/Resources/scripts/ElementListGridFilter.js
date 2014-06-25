Phlexible.elements.ElementsListGridFilter = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.elements.Strings,
    bodyStyle: 'background-color: #DFE8F6; padding: 5px;',
    animCollapse: false,

    mode: 'node',

    initComponent: function() {
        this.populateItems();

        this.height = 40 + this.items[0].items[0].items.length * 20;

        this.on('expand', function(c) {
            this.doLayout();
        }, this);

        Phlexible.elements.ElementsListGridFilter.superclass.initComponent.call(this);
    },

    populateItems: function() {
        this.items = [{
            layout: 'column',
            border: false,
            items: [{
                width: 280,
                layout: 'form',
                border: false,
                items: [{
                    xtype: 'lovcombo',
                    fieldLabel: this.strings.status,
                    emptyText: this.strings.not_filtered,
                    hiddenName: 'status',
                    maxHeight: 300,
                    width: 143,
                    listWidth: 160,
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'title'],
                        id: 'id',
                        data: [
                            ['online', this.strings.status_online],
                            ['async', this.strings.status_async],
                            ['offline', this.strings.status_offline]
                        ]
                    }),
                    displayField: 'title',
                    valueField: 'id',
                    editable: false,
                    triggerAction: 'all',
                    mode: 'local'
                },{
                    xtype: 'twincombobox',
                    fieldLabel: this.strings.navigation,
                    emptyText: this.strings.not_filtered,
                    hiddenName: 'navigation',
                    maxHeight: 300,
                    width: 143,
                    listWidth: 160,
                    hidden: this.mode != 'node',
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'title'],
                        id: 'id',
                        data: [
                            ['in_navigation', this.strings.in_navigation],
                            ['not_in_navigation', this.strings.not_in_navigation]
                        ]
                    }),
                    displayField: 'title',
                    valueField: 'id',
                    editable: false,
                    triggerAction: 'all',
                    mode: 'local'
                },{
                    xtype: 'twincombobox',
                    fieldLabel: this.strings.restricted,
                    emptyText: this.strings.not_filtered,
                    hiddenName: 'restricted',
                    maxHeight: 300,
                    width: 143,
                    listWidth: 160,
                    hidden: this.mode != 'node',
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'title'],
                        id: 'id',
                        data: [
                            ['is_restricted', this.strings.restricted],
                            ['is_not_restricted', this.strings.is_not_restricted]
                        ]
                    }),
                    displayField: 'title',
                    valueField: 'id',
                    editable: false,
                    triggerAction: 'all',
                    mode: 'local'
                }]
            },{
                width: 280,
                layout: 'form',
                border: false,
                items: [{
                    xtype: 'twincombobox',
                    fieldLabel: this.strings.date,
                    emptyText: this.strings.not_filtered,
                    hiddenName: 'date',
                    maxHeight: 300,
                    width: 143,
                    listWidth: 160,
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'title'],
                        id: 'id',
                        data: [
                            ['create', this.strings.create_date],
                            ['publish', this.strings.publish_date],
                            ['custom', this.strings.custom_date]
                        ]
                    }),
                    displayField: 'title',
                    valueField: 'id',
                    editable: false,
                    triggerAction: 'all',
                    mode: 'local',
                    listeners: {
                        clear: {
                            fn: function() {
                                this.getComponent(0).getComponent(1).getComponent(1).disable();
                                this.getComponent(0).getComponent(1).getComponent(2).disable();
                            },
                            scope: this
                        },
                        select: {
                            fn: function() {
                                this.getComponent(0).getComponent(1).getComponent(1).enable();
                                this.getComponent(0).getComponent(1).getComponent(2).enable();
                            },
                            scope: this
                        }
                    }
                },{
                    xtype: 'datefield',
                    fieldLabel: this.strings.from,
                    width: 143,
                    name: 'date_from',
                    format: 'Y-m-d',
                    disabled: true
                },{
                    xtype: 'datefield',
                    fieldLabel: this.strings.to,
                    width: 143,
                    name: 'date_to',
                    format: 'Y-m-d',
                    disabled: true
                }]
            },{
                width: 280,
                layout: 'form',
                border: false,
                items: [{
                    xtype: 'textfield',
                    name: 'search',
                    width: 160,
                    fieldLabel: this.strings.content,
                    hidden: this.mode != 'node'
                }]
            }]
        }/*,{
            xtype: 'button',
            text: this.strings.filter,
            handler: function() {
                var values = this.form.getValues();
                this.fireEvent('filter', values);
            },
            scope: this
        },{
            xtype: 'button',
            text: this.strings.reset,
            handler: function() {
                this.form.reset();
                var values = this.form.getValues();
                this.fireEvent('filter', values);
            },
            scope: this
        }*/];

        this.buttons = [{
            xtype: 'button',
            text: this.strings.filter,
            handler: function() {
                var values = this.form.getValues();
                this.fireEvent('filter', values);
            },
            scope: this
        },{
            xtype: 'button',
            text: this.strings.reset,
            handler: function() {
                this.form.reset();
                var values = this.form.getValues();
                this.fireEvent('filter', values);
            },
            scope: this
        }];
    }
});

Ext.reg('elements-listgridfilter', Phlexible.elements.ElementsListGridFilter);
