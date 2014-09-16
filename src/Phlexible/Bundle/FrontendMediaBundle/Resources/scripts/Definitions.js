Ext.namespace('Phlexible.frontendmedia.configuration');


Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems =
    Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems.createSequence(function() {
        this.items.push({
            xtype: 'frontendmedia-configuration-field-configuration-file',
            additional: true
        });
    });