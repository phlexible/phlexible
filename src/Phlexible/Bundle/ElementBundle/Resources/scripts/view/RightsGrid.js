Ext.provide('Phlexible.elements.RightsGrid');

Ext.require('Phlexible.accesscontrol.RightsGrid');

Phlexible.elements.RightsGrid = Ext.extend(Phlexible.accesscontrol.RightsGrid, {
    languageEnabled: false,
    element: null,

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            scope: this
        });

        this.on({
            show: function () {
                this.lazyLoad(this.element.tid, this.element.data.properties.teaser_id);
            },
            scope: this
        });

        Phlexible.elements.RightsGrid.superclass.initComponent.call(this);
    },

    getLanguageData: function () {
        var languageData = Phlexible.clone(Phlexible.Config.get('set.language.frontend'));
        languageData.unshift(['_all_', this.strings.all, 'p-contentrights-all-icon']);

        return languageData;
    },

    onLoadElement: function (element) {
        if (!this.hidden) {
            this.lazyLoad(element.tid, element.data.properties.teaser_id);
        }
    },

    lazyLoad: function (tid, teaserId) {
        var contentType, contentId;

        if (teaserId && teaserId !== undefined) {
            contentType = 'teaser';
            contentId = teaserId;
        }
        else {
            contentType = 'treenode';
            contentId = tid;
        }

        this.doLoad(contentType, contentId);
    },

    onGetLock: function (element) {
        if (this.right && element.data.rights.indexOf(this.right) === -1) {
            this.disable();
        }
        else {
            this.enable();
        }
    },

    onIsLocked: function () {
        this.disable();
    },

    onRemoveLock: function () {
        this.disable();
    }
});

Ext.reg('elements-rightsgrid', Phlexible.elements.RightsGrid);
