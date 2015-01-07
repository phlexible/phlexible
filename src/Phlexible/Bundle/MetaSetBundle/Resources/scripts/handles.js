Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.metasets.MainPanel');

Phlexible.Handles.add('metasets', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.metasets.Strings.metasets,
        iconCls: 'p-metaset-component-icon',
        component: 'metasets-main'
    });
});
