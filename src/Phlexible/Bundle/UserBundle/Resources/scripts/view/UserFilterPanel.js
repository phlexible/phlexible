Ext.provide('Phlexible.users.UserFilterPanel');

Phlexible.users.UserFilterPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.users.Strings.filter,
    strings: Phlexible.users.Strings,
    cls: 'p-users-filter-panel',
    iconCls: 'p-user-filter-icon',
    labelWidth: 60,
    bodyStyle: 'padding: 5px;',
    defaultType: 'textfield',
    autoScroll: true,

    initComponent: function () {
        this.addEvents(
            'applySearch',
            'resetSearch'
        );

        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.initMyItems();
        this.initMyTbar();
        this.loadMyFilterPanels();
        this.initMyListeners();

        Phlexible.users.UserFilterPanel.superclass.initComponent.call(this);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'panel',
                title: this.strings.search,
                layout: 'form',
                frame: true,
                collapsible: true,
                items: [
                    {
                        xtype: 'textfield',
                        hideLabel: true,
                        anchor: '100%',
                        name: 'key',
                        enableKeyEvents: true,
                        value: this.params.query,
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
                title: this.strings.status,
                iconCls: 'p-user-user_account-icon',
                layout: 'form',
                frame: true,
                collapsible: true,
                items: [{
                    xtype: 'radio',
                    name: 'status',
                    inputValue: 'all',
                    hideLabel: true,
                    boxLabel: this.strings.all,
                    checked: true,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                }, {
                    xtype: 'radio',
                    name: 'status',
                    inputValue: 'enabled',
                    hideLabel: true,
                    boxLabel: this.strings.enabled,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                }, {
                    xtype: 'radio',
                    inputValue: 'disabled',
                    name: 'status',
                    hideLabel: true,
                    boxLabel: this.strings.disabled,
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                }]
            }
        ];
    },

    initMyTbar: function() {
        this.tbar = [
            '->',
            {
                text: this.strings.reset,
                iconCls: 'p-user-reset-icon',
                handler: this.resetFilter,
                scope: this,
                disabled: true
            }];
    },

    loadMyFilterPanels: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('users_users_filtervalues'),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.initMyFilterPanels(data);

                Ext.each(this.filterPanels, function(p) {
                    var r = this.add(p);

                    r.items.each(function (item) {
                        this.form.add(item);
                    }, this);
                }, this);

                delete this.filterPanels;

                this.doLayout();
            },
            scope: this
        });
    },

    initMyFilterPanels: function(data) {
        var panels = [];

        if (data.roles && Ext.isArray(data.roles) && data.roles.length) {
            var roles = [];
            Ext.each(data.roles, function (role) {
                roles.push({
                    xtype: 'checkbox',
                    hideLabel: true,
                    boxLabel: role['title'],
                    name: 'role_' + role['id'],
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            panels.push({
                xtype: 'panel',
                title: this.strings.roles,
                iconCls: 'p-user-role-icon',
                layout: 'form',
                frame: true,
                collapsible: true,
                items: roles
            });
        }

        if (data.groups && Ext.isArray(data.groups) && data.groups.length) {
            var groups = [];
            Ext.each(data.groups, function (group) {
                groups.push({
                    xtype: 'checkbox',
                    hideLabel: true,
                    boxLabel: group['title'],
                    name: 'group_' + group['id'],
                    listeners: {
                        check: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
            panels.push({
                xtype: 'panel',
                title: this.strings.groups,
                iconCls: 'p-user-group-icon',
                layout: 'form',
                frame: true,
                collapsible: true,
                items: groups
            });
        }

        this.filterPanels = panels;
    },

    initMyListeners: function() {
        this.on({
            render: function () {
                this.task.delay(100);//updateFilter();

                var keyMap = this.getKeyMap();
                keyMap.addBinding({
                    key: Ext.EventObject.ENTER,
                    fn: this.updateFilter,
                    scope: this
                });
            },
            scope: this
        });
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

Ext.reg('users-users-filterpanel', Phlexible.users.UserFilterPanel);
