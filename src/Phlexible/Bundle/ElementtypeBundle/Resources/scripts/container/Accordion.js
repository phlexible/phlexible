Phlexible.fields.Registry.addFactory('accordion', function (parentConfig, item, valueStructure, element) {
    var config = {
        xtype: 'accordion',
        title: item.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
        layout: 'form',
        frame: false,
        border: false,
        collapsible: true,
        collapsed: item.configuration.default_collapsed == 'on',
        titleCollapse: true,
        animCollapse: false,

        id: Ext.id(),
        workingTitle: item.workingTitle,
        dsId: item.dsId,
        isMaster: element.master,
        isDiff: !!element.data.diff,
        isSortable: (item.configuration.sortable ? true : false),
        //groupId: groupId,
        element: element
    };

    if (item.children) {
        config.items = Phlexible.elements.ElementDataTabHelper.loadItems(item.children, valueStructure, config, element);

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
                        title: child.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')]
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

Phlexible.fields.FieldTypes.addField('accordion', {
    titles: {
        de: 'Akkordion',
        en: 'Accordeon'
    },
    iconCls: 'p-elementtype-container_accordion-icon',
    allowedIn: [
        'tab',
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
