Phlexible.fields.Registry.addFactory('tab', function (parentConfig, item, valueStructure, element) {
    var config = {
        xtype: 'panel',
        title: item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
        layout: 'form',
        cls: 'p-elements-data-tab',
        autoHeight: true,
        autoWidth: true,
        hideMode: 'offsets',
        header: false,
        frame: false,
        border: false,
        collapsible: true,
        collapsed: item.configuration.default_collapsed == 'on',
        titleCollapse: true,
        animCollapse: false,

        isMaster: element.master,
        isDiff: !!element.data.diff,
        isSortable: (item.configuration.sortable ? true : false),
        //groupId: groupId,
        element: element,

        listeners: {
            activate: function (c) {
                c.currentETID = c.element.data.properties.et_id;
                c.currentActive = c.items.indexOf(this.layout.activeItem);
            }
        }
    };

    if (item.children) {
        config.items = Phlexible.elements.ElementDataTabHelper.loadItems(item.children, valueStructure, config, element);
    }

    return config;
});

Phlexible.fields.FieldTypes.addField('tab', {
    titles: {
        de: 'Reiter',
        en: 'Tab'
    },
    iconCls: 'p-elementtype-container_tab-icon',
    allowedIn: [
        'root',
        'referenceroot'
    ],
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 0,
            suffix: 0,
            help: 1
        }
    }
});
