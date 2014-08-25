Phlexible.fields.Accordion = Ext.extend(Ext.Panel, {
    autoHeight: true,
    collapsible: true,
    hideMode: 'offsets',

    // private
    initComponent: function () {
        Phlexible.fields.Accordion.superclass.initComponent.call(this);

        this.on('add', function (me, comp) {
            if (this.isMaster && this.isSortable && !this.isDiff) {
                comp.on('render', function (comp) {
                    this.xsortable.enableElement(comp.id);
                }, this);
            }
        }, this);
    },

    // private
    onRender: function (ct, position) {
        if (this.isMaster && !this.isDiff) {
            this.tools = [
                {
                    id: 'right',
                    hidden: this.allowedItems ? false : true,
                    handler: function (event, tool, panel) {
                        var coords = event.getXY();

                        var menuConfig = [];

                        //                Phlexible.console.log(this.ownerCt.allowedItems);
                        for (var dsId in this.allowedItems) {
                            var item = this.allowedItems[dsId];
                            disabled = false;
                            if (this.element.prototypes.getCount(dsId, this.id) >= item.max) {
                                disabled = true;
                            }
                            menuConfig.push({
                                text: item.title[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
                                disabled: disabled,
                                handler: function (dsId) {
                                    this.expand();
                                    var pt = this.element.prototypes.getPrototype(dsId);
                                    var pos = 0;
                                    var factory = Phlexible.fields.Registry.getFactory(pt.factory);
                                    factory(this, pt, pos, this.element, null, true, true);
                                    this.doLayout();
                                }.createDelegate(this, [dsId], false)
                            });
                        }

                        var menu = new Ext.menu.Menu(menuConfig);
                        menu.showAt([coords[0], coords[1]]);
                    },
                    scope: this
                }
            ];
        }

        Phlexible.fields.Accordion.superclass.onRender.call(this, ct, position);

        // sortable handler
        if (this.isMaster && this.isSortable && !this.isDiff) {
            this.xsortable = new Ext.ux.Sortable({
                container: this.id,
                handles: true,
                autoEnable: false,
                tagName: 'fieldset',
                className: 'p-sortable-' + this.id,
                dragGroups: [
                    this.id
                ]
            });
            this.xsortable.on('startsort', function (dz, s) {
                s.htmleditors = [];
                var textareas = Ext.get(dz.dragData.ddel.id).query('textarea');
                if (textareas.length) {
                    for (var i = 0; i < textareas.length; i++) {
                        var id = textareas[i].id;
                        var tc = Ext.getCmp(textareas[i].parentNode.id);
                        if (tc && tc.ed) {
                            s.htmleditors.push(tc);
                            tc.syncValue();
                            tc.removeControl();
                        }
                    }
                }
            });
            this.xsortable.on('endsort', function (dz, s, dd, e, data) {
                var cmp = Ext.getCmp(data.ddel.id);
                var parentCmp = cmp.ownerCt;
                var oldPos = parentCmp.items.items.indexOf(cmp);
                var parentNode = data.ddel.parentNode;
                var newPos = false;
                for (var i = 0; i < parentNode.childNodes.length; i++) {
                    if (parentNode.childNodes[i] === data.ddel) {
                        newPos = i;
                    }
                }

                if (newPos !== false) {
//                    Phlexible.console.log('oldPos: ' + oldPos);
//                    Phlexible.console.log('newPos: ' + newPos);

                    parentCmp.items.remove(cmp);
                    parentCmp.items.insert(newPos, cmp);
                }

                if (s.htmleditors && s.htmleditors.length) {
                    for (var i = 0; i < s.htmleditors.length; i++) {
                        s.htmleditors[i].restoreControl();
                    }
                }
            });
        }
    }
});
Ext.reg('elementtypes-field-accordion', Phlexible.fields.Accordion);

Phlexible.fields.Registry.addFactory('accordion', function (parentConfig, item, valueStructure, element) {
//        var groupId = 'group_' + item.dsId + '_';
//        if (item.data_id) {
//            groupId += 'id-' + item.data_id;
//        } else {
//            groupId += Ext.id(null, 'new');
//        }
    var config = {
        xtype: 'elementtypes-field-accordion',
        title: item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
        layout: 'form',
        frame: false,
        border: false,
        collapsible: true,
        collapsed: item.configuration.default_collapsed == 'on',
        titleCollapse: true,
        animCollapse: false,

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
