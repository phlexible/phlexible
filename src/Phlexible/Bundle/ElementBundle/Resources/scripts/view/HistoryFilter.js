Ext.provide('Phlexible.elements.HistoryFilter');

Phlexible.elements.HistoryFilter = Ext.extend(Ext.form.FormPanel, {
    title: '_Filter',//Phlexible.elements.Strings.filter,
    strings: Phlexible.elements.Strings,
    cls: 'p-elements-filter-panel',
    iconCls: 'p-element-filter-icon',
    labelWidth: 60,
    bodyStyle: 'padding: 5px;',
    defaultType: 'textfield',
    autoScroll: true,
    labelAlign: 'top',

    initComponent: function () {
        this.addEvents(
            'applySearch',
            'resetSearch'
        );

        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.items = [
            {
                xtype: 'panel',
                title: '_Search',//this.strings.search,
                header: false,
                layout: 'form',
                frame: true,
                collapsible: true,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: this.strings.action,
                        anchor: '100%',
                        name: 'filter_action',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        }
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: this.strings.tid,
                        anchor: '100%',
                        name: 'filter_tree_id',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        }
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: this.strings.teaser_id,
                        anchor: '100%',
                        name: 'filter_teaser_id',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        }
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: this.strings.eid,
                        anchor: '100%',
                        name: 'filter_eid',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        }
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: this.strings.comment,
                        anchor: '100%',
                        name: 'filter_comment',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        }
                    }
                ]
            }
        ];

        this.tbar = [
            '->',
            {
                text: this.strings.reset,
                iconCls: 'p-users-reset-icon',
                handler: this.resetFilter,
                scope: this,
                disabled: true
            }];

        Phlexible.elements.HistoryFilter.superclass.initComponent.call(this);
    },

    resetFilter: function (btn) {
        this.form.reset();
        this.updateFilter();
        btn.disable();
    },

    updateFilter: function () {
        this.getTopToolbar().items.items[1].enable();
        this.fireEvent('applySearch', this.form.getValues() || {});
    }
});

Ext.reg('elements-historyfilter', Phlexible.elements.HistoryFilter);
