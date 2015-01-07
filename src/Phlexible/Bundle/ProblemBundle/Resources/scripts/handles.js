Ext.require('Phlexible.Handles');
Ext.require('Phlexible.gui.menuhandle.handle.XtypeHandle');
Ext.require('Phlexible.problems.ProblemsGrid');

Phlexible.Handles.add('problems', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.problems.Strings.problems,
        iconCls: 'p-problem-component-icon',
        component: 'problems-problemspanel'
    });
});
