Ext.ns('Phlexible.mediamanager');

Phlexible.mediamanager.FolderDetailWindow = Ext.extend(Ext.Window, {
    title: 'Folder Details',
    iconCls: 'p-mediamanager-folder-icon',
    strings: Phlexible.mediamanager.Strings,
    width: 840,
    height: 495,
    layout: 'fit',
    cls: 'p-mediamanager-detail-window',
    modal: true,
    constrainHeader: true,
    maximizable: true,
    border: false,

    activeTabId: 'properties',

    folder_id: null,
    folder_name: null,
    folder_rights: [],

    initComponent: function () {
        this.title = this.folder_name;

        this.populateTabs();

        var activeTab = 0;
        if (this.activeTabId) {
            var len = this.tabs.length;
            for (var i = 0; i < len; i++) {
                if (this.tabs[i].tabId == this.activeTabId) {
                    activeTab = i;
                    break;
                }
            }
        }

        this.items = [{
            xtype: 'tabpanel',
            deferredRender: false,
            activeTab: activeTab,
            items: this.tabs
        }];

        Phlexible.mediamanager.FolderDetailWindow.superclass.initComponent.call(this);
    },

    populateTabs: function () {
        this.tabs = [{
            xtype: 'mediamanager-folderproperties',
            tabId: 'properties',
            listeners: {
                render: function (c) {
                    c.loadData(this.folder_id);
                },
                scope: this
            }
        },{
            xtype: 'mediamanager-foldermeta',
            border: false,
            stripeRows: true,
            listeners: {
                render: function (c) {
                    c.setRights(this.folder_rights);
                    if (!c.disabled) {
                        c.loadMeta({folder_id: this.folder_id});
                    }
                },
                scope: this
            }
        },{
            xtype: 'accesscontrol-rightsgrid',
            tabId: 'rights',
            title: this.strings.folder_rights,
            iconCls: 'p-mediamanager-folder_rights-icon',
            disabled: this.folder_rights.indexOf(Phlexible.mediamanager.Rights.FOLDER_RIGHTS) === -1,
            hidden: Phlexible.User.isGranted('ROLE_MEDIA_ACCESS_CONTROL'),
            rightType: 'internal',
            contentType: 'folder',
            strings: {
                users: this.strings.select_user,
                user: '_user',
                groups: this.strings.select_group,
                group: '_group'
            },
            urls: {
                subjects: Phlexible.Router.generate('mediamanager_rights_subjects'),
                add: Phlexible.Router.generate('mediamanager_rights_add')
            },
            listeners: {
                render: function (c) {
                    if (!c.disabled) c.doLoad('folder', this.folder_id);
                },
                scope: this
            }
        }];
    },

    loadData: function () {
        this.setTitle(this.folder_name);

        this.getComponent(0).getComponent(0).loadData(this.folder_id);
        this.getComponent(0).getComponent(0).doLoad('folder', this.folder_id);
        this.getComponent(0).getComponent(0).loadMeta({folder_id: this.folder_id});

        this.getComponent(0).getComponent(1).setRights(this.folder_rights);
    }
});
