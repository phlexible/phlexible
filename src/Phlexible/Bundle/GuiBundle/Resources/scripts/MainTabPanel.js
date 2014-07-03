Phlexible.gui.MainTabPanel = Ext.extend(Ext.TabPanel, {
    plain: false,
    border: false,
    activeTab: 0,
    deferredRender: false,
    layoutOnTabChange: true,
    dirtyTitles: new Ext.util.MixedCollection(),
    enableTabScroll: true,

    initComponent: function () {
        this.addEvents(
            'beforeLoadPanel',
            'loadPanel'
        );

        this.addListener({
            beforeremove: function (main, panel) {
                if (panel.isDirty && panel.isDirty()) {
                    Ext.Msg.alert('Warning', "Panel dirty. Close aborted.");
                    return false;
                }
            }
        });

        Phlexible.gui.MainTabPanel.superclass.initComponent.call(this);
    },

    loadPanel: function (id, cls, params) {
        if (!params) {
            params = {};
        }

        if (this.fireEvent('beforeLoadPanel', id, cls, params) !== false) {

            var panel = Phlexible.Frame.mainPanel.getComponent(id);
            if (!panel) {
                var panel = new cls({
                    id: id,
                    closable: true,
                    params: params,
                    listeners: {
                        dirty: {
                            fn: function (panel) {
                                return;
                                this.dirtyTitles.add(panel.id, panel.title);

                                var domEl = panel.ownerCt.getTabEl(panel);
                                var el = Ext.get(domEl);

                                el.addClass('p-tab-dirty');
                                el.removeClass('x-tab-strip-closable');
                            },
                            scope: this
                        },
                        clean: {
                            fn: function (panel) {
                                return;
                                this.dirtyTitles.remove(panel.id);

                                var domEl = panel.ownerCt.getTabEl(panel);
                                var el = Ext.get(domEl);

                                el.removeClass('p-tab-dirty');
                                el.addClass('x-tab-strip-closable');
                            },
                            scope: this
                        }
                    },
                    scope: this
                });
                this.add(panel);
            } else {
                if (!panel.dirty && panel.loadParams) {
                    panel.loadParams(params);
                }
            }
            this.activate(panel);
            this.ownerCt.doLayout();

            this.fireEvent('loadPanel', id, cls, params);
        }
    }
});
