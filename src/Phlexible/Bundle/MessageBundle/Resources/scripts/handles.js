Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.messages.MainPanel');

Phlexible.Handles.add('messages', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.messages.Strings.messages,
        iconCls: 'p-message-component-icon',
        component: 'messages-mainpanel'
    });
});
