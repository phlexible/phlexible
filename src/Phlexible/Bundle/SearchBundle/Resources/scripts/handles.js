Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.ComponentHandle');
Ext.require('Phlexible.gui.menuhandle.handle.SeparatorHandle');
Ext.require('Phlexible.search.field.SearchBox');

Phlexible.Handles.add('searchbox', function() {
    return new Phlexible.gui.menuhandle.handle.ComponentHandle({
        componentXtype: 'searchbox'
    });
});

Phlexible.Handles.add('searchboxseparator', function() {
    return new Phlexible.gui.menuhandle.handle.SeparatorHandle();
});
