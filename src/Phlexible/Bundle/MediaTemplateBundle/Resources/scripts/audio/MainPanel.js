Phlexible.mediatemplates.audio.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediatemplates.Strings.audio_template,
    strings: Phlexible.mediatemplates.Strings,
    layout: 'border',

    initComponent: function() {
        this.items = [{
            xtype: 'mediatemplates-audioformpanel',
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
            xtype: 'mediatemplates-audiopreviewpanel',
            region: 'center',
            header: false
        }];

        Phlexible.mediatemplates.audio.MainPanel.superclass.initComponent.call(this);
    },

    loadParameters: function(template_key) {
        this.setTitle(String.format(this.strings.audio_template_title, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});