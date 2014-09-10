Phlexible.elementfinder.ElementFinderConfigWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elementfinder.Strings,
    title: Phlexible.elementfinder.Strings.finder,
    iconCls: 'p-elementfinder-finder-icon',
    width: 500,
    height: 500,
    layout: 'fit',
    modal: true,
    resizable: false,
    border: false,

    initComponent: function () {
        this.items = [{
            xtype: 'elementfinder-finder-config-panel',
            header: false,
            siterootId: this.siterootId,
            values: this.values,
            baseValues: this.baseValues
        }];

        this.buttons = [{
            text: this.strings.cancel,
            handler: this.close,
            scope: this
        },{
            text: this.strings.store,
            handler: function() {
                var values = this.getComponent(0).getValues();

                if (!values) {
                    return;
                }

                this.fireEvent('set', this, values);
                this.close();
            },
            scope: this
        }]

        Phlexible.elementfinder.ElementFinderConfigWindow.superclass.initComponent.call(this);
    }
});
