Ext.require('Phlexible.elementtypes.configuration.FieldConfiguration');
Ext.require('Phlexible.frontendmedia.configuration.FieldConfigurationFile');

Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems =
    Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems.createSequence(function() {
        this.items.push({
            xtype: 'frontendmedia-configuration-field-configuration-file',
            additional: true
        });
    });