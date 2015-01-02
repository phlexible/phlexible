Phlexible.Handles.add('problems', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        text: Phlexible.problems.Strings.problems,
        iconCls: 'p-problem-component-icon',
        component: 'problems-problemspanel'
    });
});
