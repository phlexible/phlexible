Ext.ns('Phlexible.elementtypes');

Phlexible.elementtypes.FieldTree = Ext.extend(Ext.tree.TreePanel, {
    strings: Phlexible.elementtypes.Strings,
    title: Phlexible.elementtypes.Strings.fields,
    rootVisible: false,
    lines: false,
    autoScroll: true,
    collapseFirst: false,
    animate: false,
    enableDD: true,
    ddGroup: 'fieldsDD',
    containerScroll: true,
    dragEnabled: true,
    enabled: false,

    initComponent: function () {

        this.loader = new Ext.tree.TreeLoader({
            dataUrl: Phlexible.Router.generate('elementtypes_fields_categoryTree')
        });

        this.root = new Ext.tree.AsyncTreeNode({
            text: 'Root',
            draggable: false,
            id: -1,
            expanded: true,

            listeners: {
                load: function (node) {
                    node.getOwnerTree().enable();
                }
            }
        });

//    this.dragZone = new Ext.tree.TreeDragZone(this);

        this.tbar = [
            {
                text: this.strings.new_field,
                iconCls: 'p-elementtype-field-add-icon',
                handler: function () {
                    var w = new Phlexible.elementtypes.NewFieldWindow({
                        listeners: {
                            save: function () {
                                this.disable();
                                this.root.reload();
                            },
                            scope: this
                        }
                    });
                    w.show();
                },
                scope: this
            },
            '-',
            {
                xtype: 'cycle',
                showText: true,
                iconCls: 'p-elementtype-field-refresh-icon',
                changeHandler: function (btn, item) {
                    this.disable();
                    switch (item.source) {
                        case 'categories':
                            this.loader.dataUrl = Phlexible.Router.generate('elementtypes_fields_categorytree');
                            break;
                        case 'types':
                            this.loader.dataUrl = Phlexible.Router.generate('elementtypes_fields_typetree');
                            break;
                        case 'groups':
                            this.loader.dataUrl = Phlexible.Router.generate('elementtypes_fields_grouptree');
                            break;
                    }
                    this.root.reload();
                },
                scope: this,
                items: [
                    {
                        text: this.strings.categories,
                        iconCls: 'p-elementtype-field-refresh-icon',
                        checked: true,
                        source: 'categories',
                        scope: this
                    },
                    {
                        text: this.strings.types,
                        iconCls: 'p-elementtype-field-refresh-icon',
                        source: 'types',
                        scope: this
                    },
                    {
                        text: this.strings.groups,
                        iconCls: 'p-elementtype-field-refresh-icon',
                        source: 'groups',
                        scope: this
                    }
                ]
            }
        ];

        this.contextMenu = new Ext.menu.Menu({
            items: [
                {
                    cls: 'x-btn-text-icon-bold',
                    text: '.'
                },
                '-',
                {
                    text: this.strings.duplicate,
                    iconCls: 'p-elementtype-duplicate-icon',
                    handler: function (item) {
                        this.onDuplicate(item.parentMenu.node);
                    },
                    scope: this
                },
                '-',
                {
                    text: this.strings.del,
                    iconCls: 'p-elementtype-delete-icon',
                    handler: function (item) {
                        Ext.MessageBox.confirm('Confirm', 'Do you really want to delete this Field?', function (btn, text, x, item) {
                            if (btn == 'yes') {
                                this.onDelete(item.parentMenu.node);
                            }
                        }.createDelegate(this, [item], true));
                    },
                    scope: this
                }
            ]
        });

        this.on('contextmenu', function (node, event) {
            event.stopEvent();
            var coords = event.getXY();

            this.contextMenu.node = node;

            this.contextMenu.items.items[0].setText(node.text);

            if (node.isLeaf()) {
                this.contextMenu.items.items[2].enable();
                this.contextMenu.items.items[4].enable();
            } else {
                this.contextMenu.items.items[2].disable();
                this.contextMenu.items.items[4].disable();
            }

            this.contextMenu.showAt([coords[0], coords[1]]);
        }, this);

        this.on('dblclick', function (node, e) {
            var w = new Phlexible.elementtypes.NewFieldWindow({
                listeners: {
                    save: function () {
                        this.disable();
                        this.root.reload();
                    },
                    scope: this
                }
            });
            w.show(node);
        }, this);

        Phlexible.elementtypes.FieldTree.superclass.initComponent.call(this);

        this.disable();
    },

    initEvents: function () {
        this.dragZone = new Phlexible.elementtypes.FieldDragZone(this, {
            ddGroup: this.ddGroup,
            scroll: this.ddScroll
        });
//        Phlexible.console.log(this.dragZone);

        Phlexible.elementtypes.FieldTree.superclass.initEvents.call(this);
    },

    onDelete: function (node) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_fields_delete', {id: node.id}),
            success: this.onDeleteSuccess,
            scope: this
        });
    },

    onDeleteSuccess: function (response) {
        this.root.reload();
    },

    onDuplicate: function (node) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_fields_duplicate', {id: node.id}),
            success: this.onDuplicateSuccess,
            scope: this
        });
    },

    onDuplicateSuccess: function (response) {
        this.root.reload();
    }
});
