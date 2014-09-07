Ext.ux.form.Group = Ext.extend(Ext.Panel, {
    autoHeight: true,
    collapsible: false,
    hideMode: 'offsets',
    header: false,
    border: true,
    layout: 'form',

    // private
    initComponent: function () {
        Ext.ux.form.Group.superclass.initComponent.call(this);

        this.on({
            xadd: function (me, comp) {
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
        Ext.ux.form.Group.superclass.onRender.call(this, ct, position);

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

        if (this.name) {
            this.el.insertFirst({
                tag: 'input',
                type: 'hidden',
                name: this.name,
                value: 'repeatable-' + this.workingTitle + '-' + this.title
            });
        }

        // if there is helpText create a div and display the text below the field
        if (this.helpText && typeof this.helpText == 'string') {
            this.body.createChild({ tag: 'div', cls: 'x-form-helptext', html: this.helpText });
        }

        if (this.ownerCt.isMaster && this.ownerCt.isSortable && !this.ownerCt.isDiff) {
            this.ownerCt.xsortable.enableElement(this.id);
        }

        // sortable handler
        if (this.isMaster && this.isSortable && !this.isDiff) {
            this.xsortable = new Ext.ux.Sortable({
                container: this.body.id,
                handles: true,
                autoEnable: true,
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
                    var config = factory(this.ownerCt, pt, {structures: [], values: []}, this.element, this.ownerCt.repeatableId);
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
Ext.reg('group', Ext.ux.form.Group);
