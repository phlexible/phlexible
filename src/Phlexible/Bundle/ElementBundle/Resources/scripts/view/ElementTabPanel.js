Ext.provide('Phlexible.elements.ElementTabPanel');

Ext.require('Phlexible.elements.tab.Data');
Ext.require('Phlexible.elements.tab.List');
Ext.require('Phlexible.elements.tab.Preview');
Ext.require('Phlexible.elements.tab.Links');
Ext.require('Phlexible.elements.tab.History');
Ext.require('Phlexible.elements.tab.AccessControl');
Ext.require('Phlexible.elements.tab.Routing');
Ext.require('Phlexible.elements.tab.Security');
Ext.require('Phlexible.elements.tab.Cache');

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
            //Phlexible.console.debug('xxx', element.data.default_tab, item.tabKey);
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
                xtype: 'elements-tab-list',
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
                xtype: 'elements-tab-data',
                tabKey: 'data',
                element: this.element,
                accordionCollapsed: this.accordionCollapsed
            },
            {
                xtype: 'elements-tab-preview',
                tabKey: 'preview',
                element: this.element
            },
            {
                xtype: 'elements-tab-routing',
                tabKey: 'routing',
                element: this.element
            },
            {
                xtype: 'elements-tab-security',
                tabKey: 'security',
                element: this.element
            },
            {
                xtype: 'elements-tab-cache',
                tabKey: 'cache',
                element: this.element
            },
            {
                xtype: 'elements-tab-accesscontrol',
                tabKey: 'rights',
                element: this.element
            },
            {
                xtype: 'elements-tab-links',
                tabKey: 'links',
                element: this.element
            },
            {
                xtype: 'elements-tab-history',
                tabKey: 'history',
                element: this.element
            }
        ];
    }
});

Ext.reg('elements-elementtabpanel', Phlexible.elements.ElementTabPanel);
