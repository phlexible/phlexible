Ext.namespace('Phlexible.mediatype');

Phlexible.mediatype.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatype.Strings.media_types,
    iconCls: 'p-mediatype-component-icon',
    closable: true,
    layout: 'border',

    initComponent: function () {
        this.items = [
            {
                xtype: 'mediatype-mediatypesgrid',
                region: 'center',
                listeners: {
                    mediaTypeChange: function (r) {
                        var mimetypes;
                        if (r) {
                            mimetypes = r.get('mimetypes');
                        } else {
                            mimetypes = null;
                        }
                        this.getComponent(1).loadMimetypes(mimetypes);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatype-mimetypesgrid',
                region: 'east',
                width: 400
            }
        ];

        Phlexible.mediatype.MainPanel.superclass.initComponent.call(this);
    },

    loadParams: function () {

    }
});

Ext.reg('mediatype-mainpanel', Phlexible.mediatype.MainPanel);