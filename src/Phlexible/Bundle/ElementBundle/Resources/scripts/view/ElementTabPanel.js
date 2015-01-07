Ext.provide('Phlexible.elements.ElementTabPanel');

Phlexible.elements.ElementTabPanel = Ext.extend(Ext.TabPanel, {
    title: 'Element',
    cls: 'p-elements-tabs',
    border: false,
    plain: false,
    layoutOnTabChange: true,
    deferredRender: false,
    autoHeight: false,
    hideMode: 'offsets',
    accordionCollapsed: false,

    initComponent: function () {
        this.element.on({
            afterload: this.onAfterLoadElement,
            scope: this
        });

        /*
         this.on({
         xbeforetabchange: function(tabPanel, newTab, currentTab) {
         if (newTab.xtype !== 'elements-elementdatapanel') {
         tabPanel.element.fireEvent('disableSave');
         }
         },
         xtabchange: function(tabPanel, tab) {
         if (tab.element.tid && tab.xtype === 'elements-elementdatapanel') {
         tabPanel.element.fireEvent('enableSave');
         }
         }
         });
         */

        this.populateItems();

        Phlexible.elements.ElementTabPanel.superclass.initComponent.call(this);
    },

    onAfterLoadElement: function (element) {
        // set active tab to default value
        this.items.each(function (item) {
            console.log('xxx', element.data.default_tab, item.tabKey);
            if (element.data.default_tab === item.tabKey) {
                this.setActiveTab(item);
                return false;
            }
        }, this);

        // if current tab is disabled, select list tab (because it's always active)
        if (this.activeTab.disabled) {
            this.setActiveTab(0);

            if (this.activeTab.disabled) {
                this.setActiveTab(1);
            }
        }

        //if (this.activeTab && this.activeTab.xtype === 'elements-elementdatapanel') {
        //    element.fireEvent('enableSave');
        //}
    },

    populateItems: function () {
        this.items = [
            {
                xtype: 'elements-elementlistgrid',
                tabKey: 'list',
                element: this.element,
                listeners: {
                    listLoadTeaser: function (teaser_id) {
                        this.fireEvent('listLoadTeaser', teaser_id);
                    },
                    listLoadNode: function (tid) {
                        this.fireEvent('listLoadNode', tid);
                    },
                    listReloadNode: function (tid) {
                        this.fireEvent('listReloadNode', tid);
                    },
                    scope: this
                }
            },
            {
                xtype: 'elements-elementdatapanel',
                tabKey: 'data',
                element: this.element,
                accordionCollapsed: this.accordionCollapsed
            },
            {
                xtype: 'elements-elementpreviewpanel',
                tabKey: 'preview',
                element: this.element
            },
            {
                xtype: 'elements-rightsgrid',
                tabKey: 'rights',
                iconCls: 'p-element-tab_rights-icon',
                element: this.element,
                title: Phlexible.elements.Strings.access,
                right: 'ACCESS',
                contentClass: 'Phlexible\\Bundle\\TreeBundle\\Entity\\TreeNode',
                strings: {
                    users: Phlexible.elements.Strings.users,
                    user: Phlexible.elements.Strings.user,
                    groups: Phlexible.elements.Strings.groups,
                    group: Phlexible.elements.Strings.group
                },
                urls: {
                    subjects: Phlexible.Router.generate('elements_rights_subjects'),
                    add: Phlexible.Router.generate('elements_rights_add')
                }
            },
            {
                xtype: 'elements-elementlinksgrid',
                tabKey: 'links',
                element: this.element
            },
            {
                xtype: 'elements-elementhistorygrid',
                tabKey: 'history',
                element: this.element
            },
            {
                xtype: 'elements-urlpanel',
                tabKey: 'urls',
                element: this.element
            }
        ];
    }
});

Ext.reg('elements-elementtabpanel', Phlexible.elements.ElementTabPanel);
