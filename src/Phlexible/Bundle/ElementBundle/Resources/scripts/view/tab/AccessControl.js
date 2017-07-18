Ext.provide('Phlexible.elements.tab.AccessControl');

Ext.require('Phlexible.accesscontrol.RightsGrid');

Phlexible.elements.tab.AccessControl = Ext.extend(Phlexible.accesscontrol.RightsGrid, {
    languageEnabled: false,
    element: null,
    title: Phlexible.elements.Strings.access_control,
    iconCls: 'p-element-tab_rights-icon',
    right: 'ACCESS',
    objectType: 'Phlexible\\Bundle\\TreeBundle\\Entity\\TreeNode',
    strings: {
        users: Phlexible.elements.Strings.users,
        user: Phlexible.elements.Strings.user,
        groups: Phlexible.elements.Strings.groups,
        group: Phlexible.elements.Strings.group
    },

    createIconCls: function(permission) {
        return 'p-element-' + permission.name.toLowerCase() + '-icon';
    },

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

        Phlexible.elements.tab.AccessControl.superclass.initComponent.call(this);
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
        var objectType, objectId;

        if (teaserId && teaserId !== undefined) {
            objectType = 'teaser';
            objectId = teaserId;
        }
        else {
            objectType = 'Phlexible\\Bundle\\TreeBundle\\Entity\\TreeNode';
            objectId = tid;
        }

        this.doLoad(objectType, objectId);
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

Ext.reg('elements-tab-accesscontrol', Phlexible.elements.tab.AccessControl);
