Phlexible.metasets.SuggestConfigurationWindow = Ext.extends(Ext.Window, {
    title: '_suggest',
    width: 400,
    height: 200,
    modal: true,

    initComponent: function() {
        this.items = [{
            xtype: 'form',
            border: false,
            items: [{
                xtype: 'combo',
                fieldLabel: '_datasource',
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'name'],
                    data: [[1, 'a'], [2, 'b'], [3, 'c']]
                }),
                mode: 'local',
                valueField: 'id',
                displayField: 'name',
                typeAhead: false,
                triggerAction: 'all',
                selectOnFocus: true,
                editable: false
            }]
        }];

        this.buttons = [{
            text: '_cancel',
            handler: function() {
                this.close();
            },
            scope: this
        },{
            text: '_store',
            handler: function() {

            },
            scope: this
        }];

        Phlexible.metasets.SuggestConfigurationWindow.superclass.initComponent.call(this);
    }
});