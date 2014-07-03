Phlexible.mediatemplates.BasePreviewPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatemplates.Strings.preview,
    strings: Phlexible.mediatemplates.Strings,
    cls: 'p-mediatemplates-preview-panel',
    border: true,
    bodyStyle: 'padding: 5px',
    autoScroll: true,
    disabled: true,

    initComponent: function () {
        this.items = [
            {
                border: false,
                html: this.strings.no_preview_available,
                bodyStyle: 'padding-bottom: 10px'
            },
            {
                xtype: 'fieldset',
                title: this.strings.debug,
                collapsible: true,
                autoHeight: true,
                hidden: true,
                items: [
                    {
                        border: false,
                        html: '&nbsp;'
                    }
                ]
            },
            {
                border: false,
                html: '&nbsp;',
                bodyStyle: 'padding: 10px;'
            }
        ];

        Phlexible.mediatemplates.BasePreviewPanel.superclass.initComponent.call(this);
    },

    clear: function () {
        this.getComponent(0).body.update('&nbsp;');
        this.getComponent(1).getComponent(0).body.update('&nbsp;');
        this.getComponent(2).body.update('&nbsp;');
    },

    getCreateUrl: function () {
        throw new Error('getCreateUrl() has to be implemented in Classes extending Phlexible.mediatemplates.BasePreviewPanel.');
    },

    createPreview: function (params, debug) {
        Ext.Ajax.request({
            url: this.getCreateUrl(),
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.updatePreview(data.data, debug);
                }
                else {
                    this.clear();
                    this.updateFailure(data.msg);
                }
            },
            scope: this
        });
    },

    updateFailure: function (msg) {
        this.getComponent(0).body.update(msg);
    },

    getResult: Ext.emptyFn,
    getPreviewDomHelperConfig: Ext.emptyFn,

    updatePreview: function (data, debug) {
        this.getComponent(0).body.update(this.getResult(data));

        if (debug) {
            this.getComponent(1).show();
            this.getComponent(1).getComponent(0).body.update('<pre>' + data.debug + '</pre>');
        }
        else {
            this.getComponent(1).hide();
        }

        Ext.DomHelper.overwrite(this.getComponent(2).body, this.getPreviewDomHelperConfig(data));

        this.enable();
    }
});
