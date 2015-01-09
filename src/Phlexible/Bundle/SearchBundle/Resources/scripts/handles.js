Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.Handle');
Ext.require('Phlexible.gui.menuhandle.handle.SeparatorHandle');
Ext.require('Phlexible.search.field.SearchBox');

Phlexible.Handles.add('searchbox', function() {
    return new Phlexible.gui.menuhandle.handle.Handle({
        createConfig: function() {
            return new Phlexible.search.field.SearchBox({
                width: 150
            });
        }
    });
});

Phlexible.Handles.add('searchboxseparator', function() {
    return new Phlexible.gui.menuhandle.handle.SeparatorHandle();
});
