Phlexible.fields.Group = Ext.extend(Ext.Panel, {
    autoHeight: true,
    collapsible: false,
    hideMode: 'offsets',
    header: false,
    border: true,
    layout: 'form',

    // private
    initComponent: function () {
        Phlexible.fields.Group.superclass.initComponent.call(this);

        this.on({
            add: function (me, comp) {
                if (this.isMaster && this.isSortable && !this.isDiff) {
                    comp.on('render', function (comp) {
                        this.xsortable.enableElement(comp.id);
                    }, this);
                }
            },
            scope: this
        });
    },

    // private
    onRender: function (ct, position) {
        Phlexible.fields.Group.superclass.onRender.call(this, ct, position);

        this.el.addClass('p-fields-group');

        if (this.isRepeatable) {
            this.el.addClass('p-fields-group-repeatable');
        }

        if (true) {
            this.el.insertFirst({
                tag: 'div',
                'class': 'p-fields-group-header x-panel-header x-accordion-hd',
                html: this.title
            });
            var headerEl = this.el.first();
        }

        if (this.groupId) {
            this.el.insertFirst({
                tag: 'input',
                type: 'hidden',
                name: this.groupId + (this.repeatableId ? '__' + this.repeatableId : ''),
                value: 'repeatable-' + this.workingTitle + '-' + this.title
            });
        }

        // if there is helpText create a div and display the text below the field
        if (this.helpText && typeof this.helpText == 'string') {
            this.body.createChild({ tag: 'div', cls: 'x-form-helptext', html: this.helpText });
        }

        // sortable handler
        if (this.isMaster && this.isSortable && !this.isDiff) {
            this.xsortable = new Ext.ux.Sortable({
                container: this.body.id,
                handles: true,
                autoEnable: false,
                tagName: 'div',
                className: 'p-fields-group-sortable p-sortable-' + this.body.id,
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

        if (this.isMaster && this.ownerCt.isSortable && !this.isDiff) {
            this.el.addClass('p-fields-group-sortable p-sortable-' + this.ownerCt.body.id);

            if (Ext.isIE) {
                var helper1 = document.createElement('div');
                helper1.className = 'p-handle';
                helper1.id = 'handle_' + this.el.id;
                headerEl.dom.appendChild(helper1);
            }
            else {
                headerEl.insertFirst({
                    tag: 'div',
                    id: 'handle_' + this.el.id,
                    cls: 'p-handle'
                });
            }
        }

        if (this.checkboxToggle) {
            var o = typeof this.checkboxToggle == 'object' ?
                this.checkboxToggle :
            {tag: 'input', type: 'checkbox', name: this.checkboxName || this.id + '-checkbox'};
            this.checkbox = this.header.insertFirst(o);
            this.checkbox.dom.checked = !this.collapsed;
            this.checkbox.on('click', this.onCheckClick, this);
        }

        if (this.isMaster && this.isRepeatable && !this.isDiff) {
            if (Ext.isIE) {
                var helper2a = document.createElement('div');
                helper2a.className = 'x-tool x-tool-plus p-repeat-plus p-repeat-plus-' + this.ownerCt.id + '_' + this.dsId;
                var helper2b = headerEl.dom.appendChild(helper2a);
                this.buttonAdd = Ext.get(helper2b);
            }
            else {
                this.buttonAdd = headerEl.insertFirst({
                    tag: 'div',
                    cls: 'x-tool x-tool-plus p-repeat-plus p-repeat-plus-' + this.ownerCt.id + '_' + this.dsId
                });
            }
            this.buttonAdd.setVisibilityMode(Ext.Element.DISPLAY);

            if (Ext.isIE) {
                var helper3a = document.createElement('div');
                helper3a.className = 'x-tool x-tool-minus p-repeat-minus p-repeat-minus-' + this.ownerCt.id + '_' + this.dsId;
                var helper3b = headerEl.dom.appendChild(helper3a);
                this.buttonRemove = Ext.get(helper3b);
            }
            else {
                this.buttonRemove = headerEl.insertFirst({
                    tag: 'div',
                    cls: 'x-tool x-tool-minus p-repeat-minus p-repeat-minus-' + this.ownerCt.id + '_' + this.dsId
                });
            }
            this.buttonRemove.setVisibilityMode(Ext.Element.DISPLAY);

            var cnt = this.element.prototypes.getCount(this.dsId, this.ownerCt.id);
            if (this.minRepeat == cnt) {
                this.buttonRemove.hide();
            }
            if (!this.maxRepeat || this.maxRepeat >= cnt) {
                this.buttonAdd.hide();
            }

            this.buttonRemove.on('click', function () {
//                var remove = false;

                var cnt = this.element.prototypes.getCount(this.dsId, this.ownerCt.id);
                if (cnt > this.minRepeat) {
                    cnt = this.element.prototypes.decCount(this.dsId, this.ownerCt.id);

                    this.fixButtons();

                    this.el.remove();
                    this.ownerCt.remove(this);
                }
            }, this);

            this.buttonAdd.on('click', function () {
                var cnt = this.element.prototypes.getCount(this.dsId, this.ownerCt.id);
                if (!this.maxRepeat || cnt < this.maxRepeat) {
                    var pt = this.element.prototypes.getPrototype(this.dsId);
                    var pos = this.ownerCt.items.items.indexOf(this);

                    var factory = Phlexible.fields.Registry.getFactory('group');
                    var config = factory(this.ownerCt, pt, {structures: [], values: []}, this.element, this.repeatableId);
                    this.ownerCt.insert(pos + 1, config);

                    this.ownerCt.doLayout();
                }
            }, this);
        }

        if (this.isMaster && !this.isDiff && (this.isRepeatable || this.allowedItems)) {
            if (Ext.isIE) {
                var helper4a = document.createElement('div');
                helper4a.className = 'x-tool x-tool-right p-repeat-right';
                var helper4b = headerEl.dom.appendChild(helper4a);
                this.buttonMenu = Ext.get(helper4b);
            }
            else {
                this.buttonMenu = headerEl.insertFirst({
                    tag: 'div',
                    cls: 'x-tool x-tool-right p-repeat-right'
                });
            }
            this.buttonMenu.setVisibilityMode(Ext.Element.DISPLAY);

            this.buttonMenu.on('click', function (event, node) {
                var coords = event.getXY(),
                    menuConfig = {
                        items: [
                            {
                                text: Phlexible.elementtypes.Strings.add_above,
                                iconCls: 'p-elementtype-drop_over-icon',
                                hidden: true,
                                menu: []
                            },
                            {
                                text: Phlexible.elementtypes.Strings.add_below,
                                iconCls: 'p-elementtype-drop_under-icon',
                                hidden: true,
                                menu: []
                            },
                            {
                                text: Phlexible.elementtypes.Strings.add_child,
                                iconCls: 'p-elementtype-drop_add-icon',
                                hidden: true,
                                menu: []
                            }
                        ]
                    },
                    disabled = false,
                    dsId;

                if (this.allowedItems) {
                    menuConfig.items[2].hidden = false;

                    for (dsId in this.allowedItems) {
                        if (!this.allowedItems.hasOwnProperty(dsId)) {
                            continue;
                        }

                        var item = this.allowedItems[dsId];
                        disabled = false;
                        if (this.element.prototypes.getCount(dsId, this.id) >= item.max) {
                            disabled = true;
                        }
                        menuConfig.items[2].menu.push({
                            text: item.title,
                            iconCls: 'p-elementtype-container_group-icon',
                            disabled: disabled,
                            handler: function (dsId) {
                                var pt = this.element.prototypes.getPrototype(dsId);
                                var pos = 0;
                                var factory = Phlexible.fields.Registry.getFactory(pt.factory);
                                this.insert(pos, factory(this, pt, {structures: [], values: []}, this.element, this.repeatableId));

                                this.doLayout();
                            }.createDelegate(this, [dsId], false)
                        });
                    }
                }

                if (this.ownerCt.allowedItems) {
                    menuConfig.items[0].hidden = false;
                    menuConfig.items[1].hidden = false;

                    for (dsId in this.ownerCt.allowedItems) {
                        if (!this.ownerCt.allowedItems.hasOwnProperty(dsId)) {
                            continue;
                        }

                        var item = this.ownerCt.allowedItems[dsId];
                        disabled = false;
                        if (this.element.prototypes.getCount(dsId, this.ownerCt.id) >= item.max) {
                            disabled = true;
                        }
                        menuConfig.items[0].menu.push({
                            text: item.title,
                            iconCls: 'p-elementtype-container_group-icon',
                            disabled: disabled,
                            handler: function (dsId) {
                                var pt = this.element.prototypes.getPrototype(dsId);
                                var pos = this.ownerCt.items.items.indexOf(this);
                                var factory = Phlexible.fields.Registry.getFactory(pt.factory);
                                this.ownerCt.insert(pos, factory(this.ownerCt, pt, {structures: [], values: []}, this.element, this.repeatableId));
                                this.ownerCt.doLayout();
                            }.createDelegate(this, [dsId], false)
                        });
                        menuConfig.items[1].menu.push({
                            text: item.title,
                            iconCls: 'p-elementtype-container_group-icon',
                            disabled: disabled,
                            handler: function (dsId) {
                                var pt = this.element.prototypes.getPrototype(dsId);
                                var pos = this.ownerCt.items.items.indexOf(this) + 1;
                                var factory = Phlexible.fields.Registry.getFactory(pt.factory);
                                this.ownerCt.insert(pos, factory(this.ownerCt, pt, {structures: [], values: []}, this.element, this.repeatableId));
                                this.ownerCt.doLayout();
                            }.createDelegate(this, [dsId], false)
                        });
                    }
                }

                var menu = new Ext.menu.Menu(menuConfig);
                menu.showAt([coords[0], coords[1]]);
            }, this);

            this.fixButtons();
        }
    },

    fixButtons: function () {
        if (this.isMaster && !this.isDiff) {
            var cnt = this.element.prototypes.getCount(this.dsId, this.ownerCt.id),
                btns;

            //Phlexible.console.log([this.minRepeat, this.maxRepeat, cnt]);
            if (this.minRepeat && this.buttonRemove) {
                btns = this.buttonRemove.up('.p-elements-data-tab').query('.p-repeat-minus-' + this.ownerCt.id + '_' + this.dsId);
                if (cnt > this.minRepeat) {
                    for (var i = 0; i < btns.length; i++) {
                        Ext.get(btns[i]).show();
                    }
                } else {
                    for (var i = 0; i < btns.length; i++) {
                        Ext.get(btns[i]).hide();
                    }
                }
            }

            if (this.maxRepeat && this.buttonRemove) {
                btns = this.buttonRemove.up('.p-elements-data-tab').query('.p-repeat-plus-' + this.ownerCt.id + '_' + this.dsId);
                if (cnt < this.maxRepeat) {
                    for (var i = 0; i < btns.length; i++) {
                        Ext.get(btns[i]).show();
                    }
                } else {
                    for (var i = 0; i < btns.length; i++) {
                        Ext.get(btns[i]).hide();
                    }
                }
            }
        }
    }
});
Ext.reg('elementtypes-field-group', Phlexible.fields.Group);

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

    // Phlexible.console.log(item.dsId + ' ' + item.name + ' ' + this.ids[item.dsId]);

    // var test = new Ext.form.Textfield({
    //     inputType: 'hidden',
    //     name: 'group_' + item.dsId + '_' + this.ids[item.dsId]
    // });

    var groupId = null;
    if (isRepeatable || parentConfig.isSortable) {
        groupId = 'group-' + item.dsId + '-';
        if (item.id) {
            groupId += 'id-' + item.id;
        }
        else {
            groupId += Ext.id(null, 'new-');
        }
    }

    var config = {
        xtype: 'elementtypes-field-group',
        title: item.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')],
        cls: (item.configuration.group_single_line ? 'p-form-group-singleline' : 'p-form-group-multiline') + ' ' + (item.configuration.group_show_border ? 'p-fields-group-border' : 'p-fields-group-noborder'),

        workingTitle: item.name,
        dsId: item.dsId,
        groupId: groupId,
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
        config.items = Phlexible.elements.ElementDataTabHelper.loadItems(item.children, valueStructure, config, element, groupId && isRepeatable ? groupId : repeatableId, true);

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
