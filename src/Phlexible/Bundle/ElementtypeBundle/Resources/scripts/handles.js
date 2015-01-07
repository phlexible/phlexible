Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.elementtypes.MainPanel');

Phlexible.Handles.add('elementtypes', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.elementtypes.Strings.elementtypes,
        iconCls: 'p-elementtype-component-icon',
        component: 'elementtypes-main'
    });
});
