Phlexible.fields.Registry.addFactory('group', function (parentConfig, item, valueStructure, element, repeatableId) {
    var minRepeat = parseInt(item.configuration.repeat_min, 10) || 0;
    var maxRepeat = parseInt(item.configuration.repeat_max, 10) || 0;
    var isRepeatable = minRepeat != maxRepeat || maxRepeat > 1;
    var isOptional = minRepeat == 0 && maxRepeat;
    var defaultRepeat = parseInt(item.configuration.repeat_default, 10) || 0;
    if (element.version > 1 && minRepeat === 0) {
        defaultRepeat = 0;
    }

    element.prototypes.incCount(item.dsId, parentConfig.id);

    var groupId = null;
    var name = null;
    if (isOptional || isRepeatable) {
        groupId = 'group-' + item.dsId + '-';
        if (item.id) {
            groupId += 'id-' + item.id;
        }
        else {
            groupId += Ext.id(null, 'new-');
        }
        name = groupId;
        if (repeatableId) {
            name += '__' + repeatableId;
        }
        repeatableId = groupId;
    }

    var config = {
        xtype: 'group',
        title: item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
        cls: (item.configuration.group_single_line ? 'p-form-group-singleline' : 'p-form-group-multiline') + ' ' + (item.configuration.group_show_border ? 'p-fields-group-border' : 'p-fields-group-noborder'),

        id: Ext.id(),
        workingTitle: item.name,
        name: name,
        dsId: item.dsId,
        repeatableId: repeatableId,
        helpText: item.labels.context_help[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || '',
        isMaster: element.master,
        isDiff: !!element.data.diff,
        isOptional: isOptional,
        isRepeatable: isRepeatable,
        minRepeat: minRepeat,
        maxRepeat: maxRepeat,
        defaultRepeat: defaultRepeat,
        singleLine: (item.configuration.group_single_line ? true : false),
        singleLineLabel: (item.configuration.group_single_line && !item.configuration.group_show_border ? item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] : ''),
        showBorder: (item.configuration.group_show_border ? true : false),
        isSortable: (item.configuration.sortable ? true : false),
        element: element
    };

    if (item.children) {
        config.items = Phlexible.elements.ElementDataTabHelper.loadItems(item.children, valueStructure, config, element, groupId ? groupId : repeatableId, true);

        var allowedItems = {}, has = false;
        Ext.each (item.children, function(child) {
            if (child.type === "group") {
                var minRepeat = parseInt(child.configuration.repeat_min, 10) || 0;
                var maxRepeat = parseInt(child.configuration.repeat_max, 10) || 0;
                var isRepeatable = minRepeat != maxRepeat || maxRepeat > 1;
                var isOptional = minRepeat == 0 && maxRepeat;
                if (isRepeatable || isOptional) {
                    allowedItems[child.dsId] = {
                        max: maxRepeat,
                        title: child.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')]
                    };
                    has = true;
                }
            }
        });
        if (has) {
            config.allowedItems = allowedItems;
        }
    }

    return config;
});

Phlexible.fields.FieldTypes.addField('group', {
    titles: {
        de: 'Gruppe',
        en: 'Group'
    },
    iconCls: 'p-elementtype-container_group-icon',
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 0,
            suffix: 0,
            help: 1
        },
        configuration: {
            sync: 0,
            width: 0,
            height: 0,
            readonly: 0,
            hide_label: 0,
            sortable: 1
        }
    }
});
