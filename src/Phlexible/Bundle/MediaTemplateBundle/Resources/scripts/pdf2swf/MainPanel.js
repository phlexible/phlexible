Phlexible.mediatemplates.pdf2swf.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatemplates.Strings.pdf2swf_template,
    strings: Phlexible.mediatemplates.Strings,
    layout: 'border',

    initComponent: function () {
        this.items = [
            {
                xtype: 'mediatemplates-pdf2swfformpanel',
                region: 'west',
                width: 320,
                header: false,
                listeners: {
                    paramsload: function () {

                    },
                    paramssave: function () {
                        this.fireEvent('paramssave');
                    },
                    preview: function (params, debug) {
                        this.getComponent(1).createPreview(params, debug);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatemplates-pdf2swfpreviewpanel',
                region: 'center',
                header: false
            }
        ];

        Phlexible.mediatemplates.pdf2swf.MainPanel.superclass.initComponent.call(this);
    },

    loadParameters: function (template_key) {
        this.setTitle(String.format(this.strings.pdf2swf_template_title, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});