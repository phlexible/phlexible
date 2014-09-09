Phlexible.elementfinder.ElementFinderConfigWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elementfinder.Strings,
    title: Phlexible.elementfinder.Strings.finder,
    iconCls: 'p-elementfinder-finder-icon',
    width: 500,
    height: 500,
    layout: 'fit',
    modal: true,
    resizable: false,

    initComponent: function () {
        this.items = [{
            xtype: 'elementfinder-finder-config-panel',
            siterootId: this.siterootId,
            elementtypeIds: this.elementtypeIds,
            inNavigation: this.inNavigation,
            maxDepth: this.maxDepth,
            filter: this.filter
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
