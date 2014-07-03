Phlexible.elements.NewTeaserInstanceWindow = Ext.extend(Ext.Window, {
    title: Phlexible.elements.Strings.add_teaser_reference,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-teasers-teaser_reference-icon',
    cls: 'p-dialog p-elements-newteaserinstance',
    width: 500,
    height: 400,
    layout: 'fit',
    border: false,
    constrainHeader: true,
    modal: true,

    initComponent: function () {
        this.treePanel = new Phlexible.elements.ElementsTree({
            title: false,
            region: 'center',
            border: false,
            element: this.element,
            enableDD: false,
            noClickHandling: true,
            dataUrl: Phlexible.Router.generate('elements_tree_teaserreference'),
            listeners: {
                click: {
                    fn: function (node) {
                        if (node.attributes.type == 'teaser') {
                            this.buttons[0].enable();
                        }
                        else {
                            this.buttons[0].disable();
                        }
                    },
                    scope: this
                }
            }
        });

        this.items = [
            {
                layout: 'border',
                items: [
                    {
                        region: 'north',
                        height: 100,
                        border: false,
                        html: '<div class="p-header">' +
                            '<div class="p-header-image"></div>' +
                            '<div class="p-header-title">' + this.strings.add_teaser_reference_header + '</div>' +
                            '<div class="p-header-description">' + this.strings.add_teaser_reference_description + '</div>'
                    },
                    this.treePanel
                ]
            }
        ];

        this.buttons = [
            {
                text: this.strings.add_teaser_reference,
                iconCls: 'p-teasers-teaser_reference-icon',
                disabled: true,
                handler: function () {
                    var node = this.treePanel.getSelectionModel().getSelectedNode();

                    if (!node || node.attributes.type != 'teaser') {
                        return;
                    }

                    this.fireEvent('teaserSelect', node.attributes.id, node.attributes.layoutarea_id, this.element.tid);

                    this.close();
                },
                scope: this
            },
            {
                text: this.strings.cancel,
                handler: function () {
                    this.close();
                },
                scope: this
            }
        ];

        this.addListener({
            render: {
                fn: function () {
//                    this.getComponent(0).load(this.elementData);
                },
                scope: this
            }
        });

        Phlexible.elements.NewTeaserInstanceWindow.superclass.initComponent.call(this);
    }
});
