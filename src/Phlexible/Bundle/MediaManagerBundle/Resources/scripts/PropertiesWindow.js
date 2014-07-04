Phlexible.mediamanager.PropertiesTemplate = new Ext.XTemplate(
    '<div style="padding: 10px;">',
    '<div style="padding: 3px;">',
    '<img style="vertical-align: middle;" src="' + Phlexible.component('/phlexiblemediamanager/images/folderdialog.gif') + '" width="60" height="60" />',
    '{title}',
    '</div>',
    '<hr />',
    '<div style="padding: 3px;">',
    '<div style="float: left; width:90px;">{[this.strings.type]}:</div>',
    '<div style="float: left;">{[this.strings[values.type]]}</div>',
    '<div style="clear: left;"></div>',
    '</div>',
    '<div style="padding: 3px;">',
    '<div style="float: left; width:90px;">{[this.strings.path]}:</div>',
    '<div style="float: left;">{path}</div>',
    '<div style="clear: left;"></div>',
    '</div>',
    '<div style="padding: 3px;">',
    '<div style="float: left; width:90px;">{[this.strings.size]}:</div>',
    '<div style="float: left;">{[Phlexible.Format.size(values.size)]} ({size} Bytes)</div>',
    '<div style="clear: left;"></div>',
    '</div>',
    '<div style="padding: 3px;">',
    '<div style="float: left; width:90px;">{[this.strings.contents]}:</div>',
    '<div style="float: left;">{folders} {[values.folders == 1 ? this.strings.folder : this.strings.folders]}, {files} {[values.files == 1 ? this.strings.file : this.strings.files]}</div>',
    '<div style="clear: left;"></div>',
    '</div>',
    '<hr />',
    '<div style="padding: 3px;">',
    '<div style="float: left; width:90px;">{[this.strings.create_date]}:</div>',
    '<div style="float: left;">{[Phlexible.Format.date(values.create_time)]}</div>',
    '<div style="clear: left;"></div>',
    '</div>',
    '<tpl if="values.modify_date">',
    '<div style="padding: 3px;">',
    '<div style="float: left; width:90px;">{[this.strings.modify_date]}:</div>',
    '<div style="float: left;">{[Phlexible.Format.date(values.modify_time)]}</div>',
    '<div style="clear: left;"></div>',
    '</div>',
    '</tpl>',
    '</div>',
    {
        strings: Phlexible.mediamanager.Strings
    }
);
Phlexible.mediamanager.PropertiesWindow = Ext.extend(Ext.Window, {
    title: Phlexible.mediamanager.Strings.properties,
    strings: Phlexible.mediamanager.Strings,
    cls: 'p-mediamanager-properties-window',
    width: 300,
    height: 300,
    layout: 'fit',
    modal: true,
    constrainHeader: true,

    initComponent: function () {
        this.items = [
            {
                html: '_',
                bodyStyle: 'padding: 5px',
                listeners: {
                    render: {
                        fn: function (c) {
                            Phlexible.mediamanager.PropertiesTemplate.overwrite(c.el, this.data);
                        },
                        scope: this
                    }
                }
            }
        ];

        this.buttons = [
            {
                text: this.strings.close,
                handler: this.close,
                scope: this
            }
        ];

        Phlexible.mediamanager.PropertiesWindow.superclass.initComponent.call(this);
    },

    show: function () {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_properties'),
            params: {
                site_id: this.site_id,
                folder_id: this.folder_id
            },
            success: function (response) {
                this.data = Ext.decode(response.responseText);

                Phlexible.mediamanager.PropertiesWindow.superclass.show.call(this);
            },
            scope: this
        });
    }
});
