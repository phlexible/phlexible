Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.Menu');

Phlexible.Handles.add('reports', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        text: Phlexible.cms.Strings.reports,
        iconCls: 'p-cms-reports-icon'
    });
});

Phlexible.Handles.add('statistics', function() {
    return new Phlexible.gui.menuhandle.handle.Menu({
        text: Phlexible.cms.Strings.statistics,
        iconCls: 'p-cms-statistics-icon'
    });
});
