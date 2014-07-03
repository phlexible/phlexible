Phlexible.elements.ElementPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    title: 'Element',
    header: false,
    layout: 'border',
    disabled: true,
    cls: 'p-elements-elements-panel',

    accordionCollapsed: false,

    initComponent: function () {
        this.items = [
            /*{
             xtype: 'elements-taskbar',
             element: this.element,
             region: 'north',
             height: 26,
             hidden: true,
             listeners: {
             newStatus: {
             fn: function() {
             this.element.reload({
             lock: 0,
             unlock: this.element.eid
             });
             },
             scope: this
             }
             }
             },*/{
                xtype: 'elements-elementtabpanel',
                element: this.element,
                region: 'center',
                activeTab: 1,
                accordionCollapsed: this.accordionCollapsed,
                listeners: {
                    listLoadTeaser: {
                        fn: function (teaser_id) {
                            this.fireEvent('listLoadTeaser', teaser_id);
                        },
                        scope: this
                    },
                    listLoadNode: {
                        fn: function (tid) {
                            this.fireEvent('listLoadNode', tid);
                        },
                        scope: this
                    },
                    listReloadNode: {
                        fn: function (tid) {
                            this.fireEvent('listReloadNode', tid);
                        },
                        scope: this
                    }
                }
            }
        ];

        this.element.on('load', this.enable, this);

        Phlexible.elements.ElementPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('elements-elementpanel', Phlexible.elements.ElementPanel);
