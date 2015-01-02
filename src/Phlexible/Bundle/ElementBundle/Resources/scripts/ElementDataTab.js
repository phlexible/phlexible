Ext.ns('Phlexible.elements');

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
    autoScroll: true,
    hideMode: 'offsets',
    layout: 'form'
});

Ext.reg('elements-elementdatatab', Phlexible.elements.ElementDataTab);