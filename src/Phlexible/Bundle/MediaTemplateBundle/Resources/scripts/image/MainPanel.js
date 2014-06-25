Phlexible.mediatemplates.image.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatemplates.Strings.image_template,
    strings: Phlexible.mediatemplates.Strings,
    layout: 'border',

    initComponent: function() {
        this.items = [{
            xtype: 'mediatemplates-imageformpanel',
            region: 'west',
            width: 320,
            header: false,
            listeners: {
                paramsload: {
                    fn: function() {

                    },
                    scope: this
                },
                paramssave: {
                    fn: function() {
                        this.fireEvent('paramssave');
                    },
                    scope: this
                },
                preview: {
                    fn: function(params, debug) {
                        this.getComponent(1).createPreview(params, debug);
                    },
                    scope: this
                }
            }
        },{
            xtype: 'mediatemplates-imagepreviewpanel',
            region: 'center',
            header: false
        }];

        Phlexible.mediatemplates.image.MainPanel.superclass.initComponent.call(this);
    },

    loadParameters: function(template_key) {
        this.setTitle(String.format(this.strings.image_template_title, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});