Phlexible.fields.Registry.addFactory('link', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    Ext.apply(config, {
        xtype: 'linkfield',
        hiddenName: config.name,

        value: item.displayContent,
        hiddenValue: item.rawContent ? item.rawContent : (item.default_content ? item.default_content : ''),
        width: (parseInt(item.configuration.width, 10) || 200),

        allowed: {
            tid: item.configuration.link_allow_internal == "on",
            intrasiteroot: item.configuration.link_allow_intra == "on",
            url: item.configuration.link_allow_external == "on",
            mailto: item.configuration.link_allow_email == "on"
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
            sync: 1,
            width: 1,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        /*
         values: {
         default_text: 0,
         default_number: 0,
         default_textarea: 0,
         default_date: 0,
         default_time: 0,
         default_select: 0,
         default_link: 1,
         default_checkbox: 0,
         default_table: 0,
         source: 0,
         source_values: 0,
         source_function: 0,
         source_datasource: 0,
         text: 0
         },
         */
        validation: {
            required: 1,
            text: 0,
            numeric: 0,
            content: 0
        }
    }
});
