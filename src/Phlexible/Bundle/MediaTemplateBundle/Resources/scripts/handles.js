Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.mediatemplates.MainPanel');

Phlexible.Handles.add('mediatemplates', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.mediatemplates.Strings.mediatemplates,
        iconCls: 'p-mediatemplate-component-icon',
        component: 'mediatemplates-main'
    });
});
