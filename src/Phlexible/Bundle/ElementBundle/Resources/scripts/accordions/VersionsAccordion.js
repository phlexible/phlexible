Phlexible.elements.accordion.Versions = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.versions,
    cls: 'p-elements-versions-accordion',
    iconCls: 'p-element-version-icon',
    border: false,
    autoHeight: true,
    autoScroll: true,
    viewConfig: {
        deferEmptyText: false,
        emptyText: Phlexible.elements.Strings.no_versions_found,
        forceFit: true
    },

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: [
                {name: 'version', type: 'int'},
                {name: 'format', type: 'int'},
                {name: 'create_date', type: 'date', dateFormat: 'Y-m-d H:i:s'},
                {name: 'was_published', type: 'boolean'}
            ],
            id: 'version'
        });

        this.columns = [
            {
                header: this.strings.version,
                dataIndex: 'version',
                width: 40,
                renderer: function (v, md, r) {
                    if (this.element.data.properties.online_version == r.data.version) {
                        md.attr += 'style="font-weight: bold;"';
                    }
                    if (r.data.was_published) {
                        md.attr += 'style="font-style: italic;"';
                    }
                    return v;
                }.createDelegate(this)
            },
            {
                header: this.strings.date,
                dataIndex: 'create_date',
                width: 80,
                renderer: function (v, md, r) {
                    if (this.element.data.properties.online_version == r.data.version) {
                        md.attr += 'style="font-weight: bold;"';
                    }
                    if (r.data.was_published) {
                        md.attr += 'style="font-style: italic;"';
                    }
                    return v.format('Y-m-d H:i:s');
                }.createDelegate(this)
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.on({
            rowdblclick: function (grid, index) {
                var r = this.store.getAt(index);
                this.fireEvent('loadVersion', r.get('version'));
            },
            scope: this
        });

        Phlexible.elements.accordion.Versions.superclass.initComponent.call(this);
    },

    load: function (data, element) {
        this.element = element;

        this.setTitle(this.strings.versions + ' [' + data.versions.length + ']');
        this.store.loadData(data.versions);

        this.language = data.properties.language;
        this.version = data.properties.version;

        var r = this.store.getById(this.version);
        if (r) {
            this.getSelectionModel().selectRecords([r]);
        }
    }
});

Ext.reg('elements-versionsaccordion', Phlexible.elements.accordion.Versions);
