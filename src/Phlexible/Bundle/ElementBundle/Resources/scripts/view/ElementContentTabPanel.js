Ext.provide('Phlexible.elements.ElementContentTabPanel');

Phlexible.elements.ElementContentTabPanel = Ext.extend(Ext.TabPanel, {
    strings: Phlexible.elements.Strings,
    cls: 'p-elements-data-tabs',
    frame: false,
    plain: false,
    layoutOnTabChange: true,
    autoHeight: false,
    border: false,
    autoScroll: true,
    autoWidth: false,
    tabPosition: 'top',
    deferredRender: false,
    hideMode: 'offsets',
    activeTab: 0,

    currentETID: null,
    currentActive: null,

    initComponent: function () {
        this.on({
            render: function (c) {
                Ext.dd.ScrollManager.register(c.body);
            },
            tabchange: function () {
                this.ownerCt.doLayout();
            },
            scope: this
        });

        Phlexible.elements.ElementContentTabPanel.superclass.initComponent.call(this);
    },

//    fetchDisabled: function(c) {
//        if(!c.items) return;
//
//        c.items.each(function(i) {
//            if(i.isFormField) {
//                if (i.disabled && i.fieldLabel && i.name && new String(i.getRawValue()).length) {
//                    this.disabledValues[i.name] = i.getRawValue();
//                }
//            } else {
//                this.fetchDisabled(i);
//            }
//        }, this);
//    },

    isValid: function () {
        var valid = true;

        var chk = function (item) {
            if (item.isFormField) {
                if (!item.isValid()) {
                    valid = false;
                    return false;
                }
            } else if (item.items) {
                Ext.each(item.items.items, chk);
            }
            return true;
        };
        Ext.each(this.formPanels, chk);

        return valid;
    }
});

Ext.reg('elements-elementcontenttabpanel', Phlexible.elements.ElementContentTabPanel);
