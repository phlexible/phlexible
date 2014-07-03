Phlexible.elements.accordion.AllowedChildren = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.allowed_child_elements,
    cls: 'p-elements-children-accordion',
    iconCls: 'p-element-add-icon',
    border: false,
    autoHeight: true,
    autoScroll: true,
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.elements.Strings.no_allowed_child_elements,
        forceFit: true
    },

    initComponent: function () {
        this.store = new Ext.data.SimpleStore({
            fields: ['id', 'title', 'icon'],
            id: 0,
            sortInfo: {field: 'title', direction: 'ASC'}
        });

        this.columns = [
            {
                header: this.strings.title,
                dataIndex: 'title',
                renderer: function (value, meta, r) {
                    return '<img src="' + r.get('icon') + '" width="18" height="18" style="vertical-align: middle;" />' + value;
                }
            }
        ];

        Phlexible.elements.accordion.AllowedChildren.superclass.initComponent.call(this);
    },

    load: function (data) {
        if (data.properties.et_type == 'part' || !data.children.length) {
            this.hide();
            return;
        }

        this.eid = data.properties.eid;

        this.setTitle(this.strings.allowed_child_elements + ' [' + data.children.length + ']');
        this.store.loadData(data.children);

        this.show();
    }
});

Ext.reg('elements-allowedchildrenaccordion', Phlexible.elements.accordion.AllowedChildren);
