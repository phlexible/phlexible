Phlexible.mediatemplates.video.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatemplates.Strings.video_template,
    strings: Phlexible.mediatemplates.Strings,
    layout: 'border',

    initComponent: function() {
        this.items = [{
            xtype: 'mediatemplates-videoformpanel',
            region: 'west',
            width: 320,
            header: false,
            listeners: {
                paramsload: function() {
                },
                paramssave: function() {
                    this.fireEvent('paramssave');
                },
                preview: function(params, debug) {
                    this.getComponent(1).createPreview(params, debug);
                },
                scope: this
            }
        },{
            xtype: 'mediatemplates-videopreviewpanel',
            region: 'center',
            header: false
        }];

        Phlexible.mediatemplates.video.MainPanel.superclass.initComponent.call(this);
    },

    loadParameters: function(template_key) {
        this.setTitle(String.format(this.strings.video_template_title, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});