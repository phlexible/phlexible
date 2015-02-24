Ext.provide('Phlexible.mediatype.MainPanel');

Ext.require('Phlexible.mediatype.MediaTypesGrid');
Ext.require('Phlexible.mediatype.MimetypesGrid');

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
                    mediaTypeChange: function (mimetype) {
                        var mimetypes = null;
                        if (mimetype) {
                            mimetypes = mimetype.get('mimetypes');
                        }
                        this.getComponent(1).getComponent(0).loadMimetypes(mimetypes);
                        this.updateIcons(mimetype);
                    },
                    scope: this
                }
            },
            {
                xtype: 'panel',
                layout: 'border',
                region: 'east',
                width: 400,
                border: false,
                items: [{
                    xtype: 'mediatype-mimetypesgrid',
                    region: 'north',
                    height: 300
                },{
                    xtype: 'panel',
                    region: 'center',
                    bodyStyle: 'background: white; background: linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, lightgray; background-size: 30px 30px; padding: 5px;',
                    html: ''
                }]
            }
        ];

        Phlexible.mediatype.MainPanel.superclass.initComponent.call(this);
    },

    loadParams: function () {

    },

    updateIcons: function(mediatype) {
        if (!mediatype) {
            this.getComponent(1).getComponent(1).body.update('');
            return;
        }

        var icons = [], desc = [], key = mediatype.get('key');

        if (mediatype.get('icon16')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                    '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes16/' + key + '.gif') + '" width="16" height="16" />' +
                '</td>'
            );
            desc.push('<td align="center">16x16</td>');
        }
        if (mediatype.get('icon32')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                    '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes32/' + key + '.gif') + '" width="32" height="32" />' +
                '</td>'
            );
            desc.push('<td align="center">32x32</td>');
        }
        if (mediatype.get('icon48')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                    '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes48/' + key + '.gif') + '" width="48" height="48" />' +
                '</td>'
            );
            desc.push('<td align="center">48x48</td>');
        }
        if (mediatype.get('icon256')) {
            icons.push(
                '<td align="center" valign="bottom">' +
                    '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes256/' + key + '.gif') + '" width="256" height="256" />' +
                '</td>'
            );
            desc.push('<td align="center">256x256</td>');
        }

        if (!icons.length) {
            this.getComponent(1).getComponent(1).body.update('');
            return;
        }

        this.getComponent(1).getComponent(1).body.update('<table><tr>' + icons.join('') + '</tr><tr>' + desc.join('') + '</tr></table>');
    }
});

Ext.reg('mediatype-mainpanel', Phlexible.mediatype.MainPanel);