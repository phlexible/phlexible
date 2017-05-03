Ext.require('Phlexible.fields.Registry');
Ext.require('Phlexible.fields.FieldTypes');
Ext.require('Phlexible.elementtypes.field.LinkField');
Ext.require('Phlexible.fields.FieldHelper');

Phlexible.fields.Registry.addFactory('link', function (parentConfig, item, valueStructure, element, repeatableId) {
    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    var media = {};
    if (config.value && config.value.tid && element.data.links) {
        var foundLink = null;
        Ext.each(element.data.links, function(link) {
            if (link.type === 'link-internal' && link.payload.nodeId === config.value.tid) {
                foundLink = link;
                return false;
            }
        });

        if (foundLink) {
            media = {
                name: foundLink.payload.name
            };
        }
    }

    Ext.apply(config, {
        xtype: 'linkfield',
        hiddenName: config.name,

        value: media.name || null,
        hiddenValue: config.value,
        width: (parseInt(item.configuration.width, 10) || 200),

        allowed: {
            tid: item.configuration.link_allow_internal,
            intrasiteroot: item.configuration.link_allow_intra,
            url: item.configuration.link_allow_external,
            mailto: item.configuration.link_allow_email
        },
        siteroot_id: element.siteroot_id,
        elementTypeIds: item.configuration.link_element_types || '',

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsInlineDiff: true,
        supportsUnlink: {unlinkEl: 'trigger'},
        supportsRepeatable: true
    });

    if (!config.value && config.hiddenValue && config.hiddenValue.type) {
        if (config.hiddenValue.type === 'external') {
            config.value = config.hiddenValue.url;
        } else if (config.hiddenValue.type === 'mailto') {
            config.value = config.hiddenValue.recipient;
        } else {
            config.value = config.hiddenValue.tid;
        }
    }

    delete config.name;

    return config;
});

Phlexible.fields.FieldTypes.addField('link', {
    titles: {
        de: 'Link',
        en: 'Link'
    },
    iconCls: 'p-elementtype-field_link-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    allowMap: true,
    defaultValueField: 'default_value_textfield',
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 1,
            suffix: 1,
            help: 1
        },
        configuration: {
            required: 1,
            sync: 1,
            width: 1,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        }
    }
});
