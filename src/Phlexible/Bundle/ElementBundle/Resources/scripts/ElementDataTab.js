Phlexible.elements.ElementDataTab = Ext.extend(Ext.Panel, {
    cls: 'p-elements-data-tab',
    header: false,
    frame: false,
    border: false,
    //bodyStyle: 'margin: 5px;',
    //labelWidth: 200,
    //defaults: {width: 400},
    autoHeight: true,
    autoWidth: true,
    hideMode: 'offsets',
    layout: 'form',

    xinitComponent: function () {
        this.items = Phlexible.elements.ElementDataTabHelper.loadItems(this.structure, this.valueStructure, this, this.element);

        Phlexible.elements.ElementDataTab.superclass.initComponent.call(this);
    },

    xloadData: function (structure) {
        Phlexible.elements.ElementDataTabHelper.loadItems(structure, this, this.element);
    }
});

Ext.reg('elements-elementdatatab', Phlexible.elements.ElementDataTab);