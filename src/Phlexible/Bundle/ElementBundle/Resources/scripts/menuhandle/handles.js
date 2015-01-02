Phlexible.Handles.add('element', function() {
    return new Phlexible.gui.menuhandle.handle.XtypeHandle({
        iconCls: 'p-element-component-icon',
        component: 'elements-main',

        getIdentifier: function () {
            return this.getComponent() + '_' + this.parameters.siteroot_id;
        },

        getText: function() {
            return this.parameters.title;
        }
    });
});

Phlexible.Handles.add('elements', function() {
    return new Phlexible.gui.menuhandle.handle.BubbleMenu({
        text: Phlexible.elements.Strings.websites,
        iconCls: 'p-element-component-icon',
        component: 'elements-main'
    });
});
