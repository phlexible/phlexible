Ext.provide('Phlexible.users.UserWindow');

Ext.require('Phlexible.users.model.UserRole');
Ext.require('Phlexible.users.model.UserGroup');
Ext.require('Ext.grid.CheckColumn');
Ext.require('Ext.ux.TabPanel');

Phlexible.users.UserWindow = Ext.extend(Ext.Window, {
    title: Phlexible.users.Strings.user,
    strings: Phlexible.users.Strings,
    plain: true,
    iconCls: 'p-user-user-icon',
    width: 530,
    minWidth: 530,
    height: 400,
    minHeight: 400,
    layout: 'fit',
    border: false,
    modal: true,

    initComponent: function () {
        this.addEvents(
            'save'
        );

        this.items = [{
            xtype: 'uxtabpanel',
            tabPosition: 'left',
            tabStripWidth: 150,
            activeTab: 0,
            border: true,
            deferredRender: false,
            items: [{
                xtype: 'form',
                title: this.strings.personal_details,
                iconCls: 'p-user-user_detail-icon',
                border: false,
                bodyStyle: 'padding:5px',
                labelWidth: 130,
                labelAlign: 'top',
                defaultType: 'textfield',
                defaults: {
                    msgTarget: 'under'
                },

                items: [
                    {
                        name: 'username',
                        fieldLabel: this.strings.username,
                        anchor: '100%',
                        allowBlank: false
                    },
                    {
                        name: 'firstname',
                        fieldLabel: this.strings.firstname,
                        anchor: '100%',
                        allowBlank: false
                    },
                    {
                        name: 'lastname',
                        fieldLabel: this.strings.lastname,
                        anchor: '100%',
                        allowBlank: false
                    },
                    {
                        name: 'email',
                        fieldLabel: this.strings.email,
                        anchor: '100%',
                        allowBlank: false,
                        vtype: 'email'
                    }
                ]
            },{
                xtype: 'form',
                title: this.strings.password,
                iconCls: 'p-user-user_password-icon',
                border: false,
                bodyStyle: 'padding:5px',
                hideMode: 'offsets',
                defaultType: 'textfield',
                defaults: {
                    msgTarget: 'under'
                },
                items: [{
                    xtype: 'checkbox',
                    boxLabel: this.strings.add_optin,
                    hideLabel: true,
                    checked: true,
                    name: 'optin',
                    border: false,
                    disabled: this.mode !== 'add',
                    hidden: this.mode !== 'add',
                    listeners: {
                        check: function(c, checked) {
                            this.getPasswordFormPanel().getComponent(2).setDisabled(checked);
                        },
                        scope: this
                    }
                },{
                    xtype: 'checkbox',
                    boxLabel: this.strings.edit_optin,
                    hideLabel: true,
                    checked: false,
                    name: 'optin',
                    border: false,
                    disabled: this.mode === 'add',
                    hidden: this.mode === 'add',
                    listeners: {
                        check: function(c, checked) {
                            this.getPasswordFormPanel().getComponent(2).setDisabled(checked);
                        },
                        scope: this
                    }
                },{
                    xtype: 'fieldset',
                    text: this.strings.password,
                    title: this.strings.password,
                    autoHeight: true,
                    disabled: this.mode === 'add',
                    items: [{
                        xtype: 'passwordfield',
                        name: 'password',
                        hideLabel: true,
                        inputType: "password",
                        minLength: Phlexible.Config.get('system.password_min_length'),
                        width: 200,
                        showStrengthMeter: true,
                        showCapsWarning: true
                    },{
                        xtype: 'panel',
                        border: false,
                        bodyStyle: 'padding-bottom: 10px;',
                        html: this.strings.generate_password_text
                    },{
                        xtype: 'textfield',
                        emptyText: this.strings.generated_password,
                        hideLabel: true,
                        readOnly: true,
                        width: 200,
                        append: [
                            {
                                xtype: 'button',
                                text: this.strings.generate,
                                iconCls: 'p-user-user_password-icon',
                                handler: function (btn) {
                                    btn.setIconClass('x-tbar-loading');
                                    btn.disable();
                                    Ext.Ajax.request({
                                        url: Phlexible.Router.generate('users_password'),
                                        success: function (response) {
                                            var data = Ext.decode(response.responseText);

                                            var panel = this.getPasswordFormPanel();
                                            if (data.success) {
                                                panel.getComponent(2).getComponent(0).setValue(data.password);
                                                panel.getComponent(2).getComponent(2).setValue(data.password);
                                            }

                                            btn.setIconClass('p-user-user_password-icon');
                                            btn.enable();
                                        },
                                        scope: this
                                    });
                                },
                                scope: this
                            }
                        ]
                    }]
                }]
            }, {
                xtype: 'form',
                title: this.strings.comment,
                iconCls: 'p-user-user_comment-icon',
                border: false,
                bodyStyle: 'padding:5px',
                hideMode: 'offsets',
                labelWidth: 130,
                labelAlign: 'top',
                defaultType: 'textfield',
                defaults: {
                    msgTarget: 'under'
                },
                items: [
                    {
                        xtype: 'textarea',
                        name: 'comment',
                        hideLabel: true,
                        anchor: '100%',
                        height: 300
                    }
                ]
            }, {
                title: this.strings.options,
                iconCls: 'p-user-user_options-icon',
                border: false,
                xtype: 'form',
                bodyStyle: 'padding:5px',
                labelWidth: 130,
                labelAlign: 'top',
                defaultType: 'textfield',
                items: [
                    {
                        xtype: 'iconcombo',
                        fieldLabel: this.strings.interface_language,
                        hiddenName: 'interfaceLanguage',
                        anchor: '100%',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'name', 'iconCls'],
                            data: Phlexible.Config.get('set.language.backend')
                        }),
                        valueField: 'id',
                        displayField: 'name',
                        iconClsField: 'iconCls',
                        mode: 'local',
                        triggerAction: 'all',
                        selectOnFocus: true,
                        editable: false
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: this.strings.theme,
                        hiddenName: 'theme',
                        anchor: '100%',
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'name'],
                            data: Phlexible.Config.get('set.themes')
                        }),
                        displayField: 'name',
                        valueField: 'id',
                        mode: 'local',
                        triggerAction: 'all',
                        selectOnFocus: true,
                        editable: false
                    }
                ]

            }, {
                title: this.strings.account,
                iconCls: 'p-user-user_account-icon',
                border: false,
                xtype: 'form',
                bodyStyle: 'padding:5px',
                labelWidth: 130,
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'checkbox',
                        boxLabel: this.strings.enabled,
                        hideLabel: true,
                        name: 'enabled'
                    },
                    {
                        xtype: 'checkbox',
                        boxLabel: this.strings.cant_change_password,
                        hideLabel: true,
                        name: 'noPasswordChange'
                    },
                    {
                        xtype: 'datefield',
                        fieldLabel: this.strings.account_expires_on,
                        name: 'expiresAt',
                        format: 'Y-m-d',
                        helpText: this.strings.expire_help
                    },
                    {
                        xtype: 'datefield',
                        fieldLabel: this.strings.credentials_expire_on,
                        name: 'credentialsExpireAt',
                        format: 'Y-m-d',
                        helpText: this.strings.expire_help
                    }
                ]
            }, {
                xtype: 'grid',
                title: this.strings.roles,
                iconCls: 'p-user-role-icon',
                border: false,
                stripeRows: true,
                store: new Ext.data.JsonStore({
                    autoLoad: true,
                    fields: Phlexible.users.model.UserRole,
                    url: Phlexible.Router.generate('users_roles_list'),
                    listeners: {
                        load: function (store, records) {
                            Ext.each(records, function (record) {
                                if (this.record.get('roles').indexOf(record.get('id')) !== -1) {
                                    record.set('member', 1);
                                }
                            }, this);
                            store.commitChanges();
                        },
                        scope: this
                    }
                }),
                columns: [
                    {
                        header: this.strings.role,
                        sortable: true,
                        dataIndex: 'name',
                        width: 300
                    },
                    this.cc1 = new Ext.grid.CheckColumn({
                        header: this.strings.member,
                        dataIndex: 'member',
                        width: 50
                    })
                ],
                plugins: [this.cc1],
                viewCofig: {
                    forceFit: true
                }
            }, {
                xtype: 'grid',
                title: this.strings.groups,
                iconCls: 'p-user-group-icon',
                border: false,
                stripeRows: true,
                store: new Ext.data.JsonStore({
                    autoLoad: true,
                    fields: Phlexible.users.model.UserGroup,
                    url: Phlexible.Router.generate('users_groups_list'),
                    listeners: {
                        load: function (store, records) {
                            Ext.each(records, function (record) {
                                if (this.record.get('groups').indexOf(record.get('gid')) !== -1) {
                                    record.set('member', 1);
                                }
                            }, this);
                            store.commitChanges();
                        },
                        scope: this
                    }
                }),
                columns: [
                    {
                        header: this.strings.group,
                        sortable: true,
                        dataIndex: 'name',
                        width: 300
                    },
                    this.cc1 = new Ext.grid.CheckColumn({
                        header: this.strings.member,
                        dataIndex: 'member',
                        width: 50
                    })
                ],
                plugins: [this.cc1],
                viewCofig: {
                    forceFit: true
                }
            }]
        }];

        this.tbar = new Ext.Toolbar({
            hidden: true,
            cls: 'p-users-expired',
            items: [
                '->',
                {
                    iconCls: 'p-user-user_account-icon',
                    text: this.strings.account_is_expired,
                    handler: function () {
                        this.getComponent(0).setActiveTab(4);
                    },
                    scope: this
                },
                {
                    iconCls: 'p-user-user_account-icon',
                    text: this.strings.credentials_are_expired,
                    handler: function () {
                        this.getComponent(0).setActiveTab(4);
                    },
                    scope: this
                },
                {
                    iconCls: 'p-user-user_account-icon',
                    text: this.strings.account_is_disabled,
                    handler: function () {
                        this.getComponent(0).setActiveTab(4);
                    },
                    scope: this
                }]
        });

        this.buttons = [
            {
                text: this.strings.cancel,
                handler: this.close,
                scope: this
            },
            {
                text: this.strings.save,
                iconCls: 'p-user-save-icon',
                handler: this.save,
                scope: this
            }
        ];

        Phlexible.users.UserWindow.superclass.initComponent.call(this);
    },

    getDetailsForm: function() {
        return this.getComponent(0).getComponent(0).getForm();
    },

    getPasswordFormPanel: function() {
        return this.getComponent(0).getComponent(1);
    },

    getPasswordForm: function() {
        return this.getPasswordFormPanel().getForm();
    },

    getCommentForm: function() {
        return this.getComponent(0).getComponent(2).getForm();
    },

    getOptionsForm: function() {
        return this.getComponent(0).getComponent(3).getForm();
    },

    getAccountForm: function() {
        return this.getComponent(0).getComponent(4).getForm();
    },

    getRolesGrid: function() {
        return this.getComponent(0).getComponent(5);
    },

    getGroupsGrid: function() {
        return this.getComponent(0).getComponent(6);
    },

    show: function (record) {
        this.record = record;
        this.uid = record.get('uid');

        if (record.get('username')) {
            this.setTitle(this.strings.user + ' "' + record.get('username') + '"');
        } else {
            this.setTitle(this.strings.new_user);
        }

        Phlexible.users.UserWindow.superclass.show.call(this);

        this.getDetailsForm().loadRecord(record);
        this.getCommentForm().setValues({comment: record.get('comment')});

        var properties = {
            enabled: record.get('enabled'),
            expiresAt: record.get('expiresAt'),
            credentialsExpireAt: record.get('credentialsExpireAt')
        };
        Ext.apply(properties, record.get('properties'));

        this.getOptionsForm().setValues(properties);
        this.getAccountForm().setValues(properties);

        //var rolesGrid = this.getComponent(0).getComponent(5);
        //rolesgrid.getStore().proxy.conn.url = Phlexible.Router.generate('users_user_roles', {userId: this.uid});

        //var groupsGrid = this.getComponent(0).getComponent(6);
        //groupsGrid.getStore().proxy.conn.url = Phlexible.Router.generate('users_user_groups', {userId: this.uid});

        if (record.data.expired || record.data.credentialsExpired || !record.data.enabled) {
            this.getTopToolbar().show();
            if (record.data.expired) {
                this.getTopToolbar().items.items[1].show();
            } else {
                this.getTopToolbar().items.items[1].hide();
            }
            if (record.data.credentialsExpired) {
                this.getTopToolbar().items.items[2].show();
            } else {
                this.getTopToolbar().items.items[2].hide();
            }
            if (!record.data.enabled) {
                this.getTopToolbar().items.items[3].show();
            } else {
                this.getTopToolbar().items.items[3].hide();
            }
        }
    },

    save: function () {
        var detailForm = this.getDetailsForm(),
            passwordForm = this.getPasswordForm(),
            commentForm = this.getCommentForm(),
            optionsForm = this.getOptionsForm(),
            accountForm = this.getAccountForm(),
            rolesGrid = this.getRolesGrid(),
            groupsGrid = this.getGroupsGrid();

        if (!detailForm.isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the detail tab for details.');
            return;
        }

        if (!passwordForm.isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the password tab for details.');
            return;
        }

        if (!optionsForm.isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the options tab for details.');
            return;
        }

        if (!accountForm.isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the account tab for details.');
            return;
        }

        var records, i,
            roles = [],
            groups = [];

        records = rolesGrid.store.getRange();
        for (i = 0; i < records.length; i++) {
            if (records[i].get('member')) {
                roles.push(records[i].get('id'));
            }
        }

        records = groupsGrid.store.getRange();
        for (i = 0; i < records.length; i++) {
            if (records[i].get('member')) {
                groups.push(records[i].get('gid'));
            }
        }

        var options = optionsForm.getValues(),
            account = accountForm.getValues(),
            properties = {};

        if (options.theme) {
            properties["property_theme"] = options.theme;
        }
        if (options.interfaceLanguage) {
            properties["property_interfaceLanguage"] = options.interfaceLanguage;
        }
        if (account.noPasswordChange) {
            properties["property_noPasswordChange"] = 1;
        }

        var params = {
            enabled: account.enabled ? 1 : 0,
            expiresAt: account.expiresAt,
            credentialsExpireAt: account.credentialsExpireAt,
            comment: commentForm.getValues().comment,
            roles: roles.join(','),
            groups: groups.join(',')
        };

        Ext.apply(params, detailForm.getValues());
        Ext.apply(params, passwordForm.getValues());
        Ext.apply(params, properties);

        var url, method;
        if (this.uid) {
            url = Phlexible.Router.generate('users_users_update', {userId: this.uid});
            method = 'PUT';
        } else {
            url = Phlexible.Router.generate('users_users_create');
            method = 'POST';
        }

        Ext.Ajax.request({
            url: url,
            method: method,
            params: params,
            success: this.onSaveSuccess,
            scope: this
        });
    },

    onSaveSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.uid = data.uid;
            Phlexible.success(data.msg);
            this.fireEvent('save', this.uid);
            this.close();
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }

    }
});
