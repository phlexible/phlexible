Ext.require('Phlexible.elementtypes.configuration.FieldConfiguration');
Ext.require('Phlexible.datasources.configuration.FieldConfigurationSuggest');
Ext.require('Phlexible.datasources.MetaSuggestWindow');
Ext.require('Phlexible.datasources.SuggestConfigurationWindow');

Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems =
    Phlexible.elementtypes.configuration.FieldConfiguration.prototype.initMyItems.createSequence(function() {
        this.items.push({
            xtype: 'datasources-configuration-field-configuration-suggest',
            additional: true
        });
    });


Phlexible.metasets.util.Fields.prototype.initFields =
    Phlexible.metasets.util.Fields.prototype.initFields.createSequence(function() {
        this.fields.push(['suggest', 'Suggest']);
    });


Phlexible.metasets.util.Fields.prototype.initBeforeEditCallbacks =
    Phlexible.metasets.util.Fields.prototype.initBeforeEditCallbacks.createSequence(function() {
        this.beforeEditCallbacks.suggest = function (grid, field, record) {
            if (grid.master !== undefined) {
                var isSynchronized = (1 == record.get('synchronized'));

                // skip editing english values if language is synchronized
                if (!grid.master && isSynchronized) {
                    return false;
                }
            }

            var w = new Phlexible.datasources.MetaSuggestWindow({
                record: record,
                valueField: field,
                metaLanguage: grid.language,
                listeners: {
                    store: function () {
                        grid.validateMeta();
                    }
                }
            });

            w.show();

            return false;
        };
    });


Phlexible.metasets.MainPanel.prototype.configureField =
    Phlexible.metasets.MainPanel.prototype.configureField.createSequence(function(grid, record) {
        if (record.get('type') === 'suggest') {
            var w = new Phlexible.datasources.SuggestConfigurationWindow({
                options: record.get('options'),
                listeners: {
                    select: function(options) {
                        record.set('options', options);
                    },
                    scope: this
                }
            });
            w.show();
        }
    });
