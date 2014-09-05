Ext.ux.form.Accordion = Ext.extend(Ext.Panel, {
    autoHeight: true,
    collapsible: true,
    hideMode: 'offsets',

    // private
    initComponent: function () {
        Ext.ux.form.Accordion.superclass.initComponent.call(this);

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
                    hidden: !this.allowedItems,
                    handler: function (event, tool, panel) {
                        var coords = event.getXY();
                        var menuConfig = [];

                        for (var dsId in this.allowedItems) {
                            if (!this.allowedItems.hasOwnProperty(dsId)) {
                                continue;
                            }
                            var item = this.allowedItems[dsId];
                            disabled = false;
                            if (this.element.prototypes.getCount(dsId, this.id) >= item.max) {
                                disabled = true;
                            }
                            menuConfig.push({
                                text: item.title,
                                iconCls: 'p-elementtype-container_group-icon',
                                disabled: disabled,
                                handler: function (dsId) {
                                    this.expand();
                                    var pt = this.element.prototypes.getPrototype(dsId);
                                    var pos = 0;
                                    var factory = Phlexible.fields.Registry.getFactory(pt.factory);
                                    this.insert(pos, factory(this, pt, {structures: [], values: []}, this.element, this.repeatableId));
                                    this.doLayout();
                                }.createDelegate(this, [dsId], false)
                            });
                        }

                        if (menuConfig.length) {
                            var menu = new Ext.menu.Menu(menuConfig);
                            menu.showAt([coords[0], coords[1]]);
                        }
                    },
                    scope: this
                }
            ];
        }

        Ext.ux.form.Accordion.superclass.onRender.call(this, ct, position);

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
Ext.reg('accordion', Ext.ux.form.Accordion);
