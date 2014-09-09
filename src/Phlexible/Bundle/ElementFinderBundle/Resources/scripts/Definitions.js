Ext.ns('Phlexible.elementfinder.configuration');

Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems =
    Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems.createSequence(function() {
        this.items.push({
            xtype: 'elementfinder-configuration-field-configuration-finder',
            additional: true
        });
    });