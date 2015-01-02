Ext.ns('Phlexible.messages.view');

Phlexible.messages.view.FilterPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.messages.Strings.filter,
    strings: Phlexible.messages.Strings,
    bodyStyle: 'padding: 5px;',
    cls: 'p-messages-filter-panel',
    iconCls: 'p-message-filter-icon',
    autoScroll: true,

    initComponent: function () {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.items = [
            {
                xtype: 'panel',
                title: this.strings.text,
                layout: 'form',
                frame: true,
                collapsible: true,
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: this.strings.subject,
                        anchor: '-25',
                        name: 'subject',
                        labelAlign: 'top',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: function (field, event) {
                                if (event.getKey() == event.ENTER) {
                                    this.task.cancel();
                                    this.updateFilter();
                                    return;
                                }

                                this.task.delay(500);
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: this.strings.text,
                        anchor: '-25',
                        name: 'text',
                        labelAlign: 'top',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: function (field, event) {
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
                ]
            },
            {
                xtype: 'panel',
                title: this.strings.priority,
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                html: '<div class="loading-indicator">' + this.strings.loading + '...</div>'
            },
            {
                xtype: 'panel',
                title: this.strings.type,
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                html: '<div class="loading-indicator">' + this.strings.loading + '...</div>'
            },
            {
                xtype: 'panel',
                title: this.strings.channel,
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                html: '<div class="loading-indicator">' + this.strings.loading + '...</div>'
            },
            {
                xtype: 'panel',
                title: this.strings.role,
                layout: 'form',
                frame: true,
                collapsible: true,
                defaults: {
                    hideLabel: true
                },
                html: '<div class="loading-indicator">' + this.strings.loading + '...</div>'
            },
            {
                xtype: 'panel',
                title: this.strings.date,
                layout: 'form',
                frame: true,
                collapsible: true,
                labelWidth: 55,
                items: [
                    {
                        xtype: 'datefield',
                        name: 'date_after',
                        fieldLabel: this.strings.after,
                        editable: false,
                        format: 'Y-m-d',
                        listeners: {
                            select: this.updateFilter,
                            scope: this
                        }
                    },
                    {
                        xtype: 'datefield',
                        name: 'date_before',
                        fieldLabel: this.strings.before,
                        editable: false,
                        format: 'Y-m-d',
                        listeners: {
                            select: this.updateFilter,
                            scope: this
                        }
                    }
                ]
            }
        ];

        this.tbar = ['->', {
            text: this.strings.reset,
            iconCls: 'p-message-reset-icon',
            disabled: true,
            handler: this.resetFilter,
            scope: this
        }];

        Ext.Ajax.request({
            url: Phlexible.Router.generate('messages_messages_facets'),
            success: function (response) {
                var facets = Ext.decode(response.responseText);

                this.loadFacets(facets);
            },
            scope: this
        });

        Phlexible.messages.view.FilterPanel.superclass.initComponent.call(this);
    },

    updateFacets: function (facets) {
        if (facets.priorities && facets.priorities.length && Ext.isArray(facets.priorities)) {
            this.getComponent(1).items.each(function (item) {
                var found = false;
                Ext.each(facets.priorities, function (priority) {
                    if (item.name === 'priority_' + priority) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }

        if (facets.types && facets.types.length && Ext.isArray(facets.types)) {
            this.getComponent(2).items.each(function (item) {
                var found = false;
                Ext.each(facets.types, function (type) {
                    if (item.name === 'type_' + type) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }

        if (facets.channels && facets.channels.length && Ext.isArray(facets.channels)) {
            this.getComponent(3).items.each(function (item) {
                var found = false;
                Ext.each(facets.channels, function (channel) {
                    if (item.name === 'channel_' + channel) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }

        if (facets.roles && facets.roles.length && Ext.isArray(facets.roles)) {
            this.getComponent(4).items.each(function (item) {
                var found = false;
                Ext.each(facets.roles, function (role) {
                    if (item.name === 'role_' + role) {
                        found = true;
                        return false;
                    }
                }, this);
                if (found) {
                    item.enable();
                } else {
                    item.disable();
                }
            }, this);
        }
    },

    loadFacets: function (facets) {
        if (facets.priorities && facets.priorities.length && Ext.isArray(facets.priorities)) {
            this.getComponent(1).body.update('');
            Ext.each(facets.priorities, function (item) {
                this.getComponent(1).add({
                    xtype: 'checkbox',
                    name: 'priority_' + item.id,
                    boxLabel: Phlexible.inlineIcon('p-message-priority_' + item.title + '-icon') + ' ' + item.title,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            this.getComponent(1).items.each(function (item) {
                this.form.add(item);
            }, this);
        } else {
            this.getComponent(1).hide();
        }

        if (facets.types && facets.types.length && Ext.isArray(facets.types)) {
            this.getComponent(2).body.update('');
            Ext.each(facets.types, function (item) {
                this.getComponent(2).add({
                    xtype: 'checkbox',
                    name: 'type_' + item.id,
                    boxLabel: Phlexible.inlineIcon('p-message-type_' + item.title + '-icon') + ' ' + item.title,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            this.getComponent(2).items.each(function (item) {
                this.form.add(item);
            }, this);
        } else {
            this.getComponent(2).hide();
        }

        if (facets.channels && facets.channels.length && Ext.isArray(facets.channels)) {
            this.getComponent(3).body.update('');
            Ext.each(facets.channels, function (item) {
                this.getComponent(3).add({
                    xtype: 'checkbox',
                    name: 'channel_' + item.id,
                    boxLabel: item.title,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
        } else {
            this.getComponent(3).hide();
        }

        if (facets.roles && facets.roles.length && Ext.isArray(facets.roles)) {
            this.getComponent(4).body.update('');
            Ext.each(facets.roles, function (item) {
                this.getComponent(4).add({
                    xtype: 'checkbox',
                    name: 'role_' + item.id,
                    boxLabel: item.title,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
        } else {
            this.getComponent(4).hide();
        }

        this.doLayout();
    },

    resetFilter: function (btn) {
        this.getComponent(1).items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.getComponent(2).items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.getComponent(3).items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.getComponent(4).items.each(function (item) {
            item.enable();
            item.setValue(false);
        });
        this.updateFilter();
        btn.disable();
    },

    updateFilter: function () {
        this.getTopToolbar().items.items[1].enable();
        var values = this.form.getValues();
        this.fireEvent('updateFilter', values);
    }
});

Ext.reg('messages-view-filterpanel', Phlexible.messages.view.FilterPanel);