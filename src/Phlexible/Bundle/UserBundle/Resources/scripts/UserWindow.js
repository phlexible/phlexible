Phlexible.users.UserWindow = Ext.extend(Ext.Window,{
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

    initComponent: function() {
        this.addEvents(
            'save'
        );

        this.items = [{
            xtype: 'uxtabpanel',
            tabPosition:'left',
            tabStripWidth:150,
            activeTab: 0,
            border: true,
            deferredRender: false,
            items: [
                this.detailsPanel = new Ext.form.FormPanel({
                    title: this.strings.personal_details,
                    iconCls: 'p-user-user_detail-icon',
                    border: false,
                    bodyStyle:'padding:5px',
                    labelWidth: 130,
                    labelAlign: 'top',
                    defaultType: 'textfield',
                    defaults:{
                        msgTarget: 'under'
                    },

                    items:[{
                        name: 'username',
                        fieldLabel: this.strings.username,
                        anchor: '100%',
                        allowBlank: false
                    },{
                        name: 'firstname',
                        fieldLabel: this.strings.firstname,
                        anchor: '100%',
                        allowBlank: false
                    },{
                        name: 'lastname',
                        fieldLabel: this.strings.lastname,
                        anchor: '100%',
                        allowBlank: false
                    },{
                        name: 'email',
                        fieldLabel: this.strings.email,
                        anchor: '100%',
                        allowBlank: false,
                        vtype: 'email'
                    }]
            }),{
                xtype: 'form',
                title: this.strings.password,
                iconCls: 'p-user-user_password-icon',
                border: false,
                bodyStyle:'padding:5px' ,
                hideMode: 'offsets',
                labelWidth: 130,
                labelAlign: 'top',
                defaultType: 'textfield',
                defaults: {
                    msgTarget: 'under'
                },
                items:[{
                    xtype: 'passwordfield',
                    name: 'password',
                    fieldLabel: this.strings.password,
                    inputType: "password",
                    minLength: Phlexible.Config.get('system.password_min_length'),
                    width: 200,
                    showStrengthMeter: true,
                    showCapsWarning: true,
                    listeners: {
                        change: function() {
							if(this.mode == 'add') return;
							this.getComponent(0).getComponent(1).getComponent(2).enable();
						},
						scope: this
                    }
                },{
                    name: 'password_repeat',
                    fieldLabel: this.strings.password_repeat,
                    inputType: "password",
                    minLength: Phlexible.Config.get('system.password_min_length'),
                    width: 200,
                    invalidText: this.strings.passwords_dont_match,
                    validator: function() {
                        return this.getComponent(0).getComponent(1).getComponent(0).getValue() === this.getComponent(0).getComponent(1).getComponent(1).getValue();
                    }.createDelegate(this),
                    listeners: {
                        change: function() {
							if(this.mode == 'add') return;
							this.getComponent(0).getComponent(1).getComponent(2).enable();
						},
						scope: this
                    }
                },{
                    xtype: 'checkbox',
                    name: 'password_notify',
                    hideLabel: true,
                    boxLabel: this.strings.notify_user,
                    disabled: true,
                    hidden: this.mode == 'add' ? true : false
                },{
                    xtype: 'panel',
                    border: false,
                    bodyStyle: 'padding-top: 20px; padding-bottom: 10px;',
                    html: "<hr>" + this.strings.generate_password_text
                },{
                    xtype: 'textfield',
                    emptyText: this.strings.generated_password,
                    hideLabel: true,
                    readOnly: true,
                    width: 200,
                    append: [{
                        xtype: 'button',
                        text: this.strings.generate,
                        iconCls: 'p-user-user_password-icon',
                        handler: function(btn) {
                            btn.setIconClass('x-tbar-loading');
                            btn.disable();
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('users_password'),
                                success: function(response) {
                                    var data = Ext.decode(response.responseText);

                                    var p = this.getComponent(0).getComponent(1);
                                    if (data.success) {
                                        p.getComponent(0).setValue(data.password);
                                        p.getComponent(1).setValue(data.password);
                                        p.getComponent(2).enable();
                                        p.getComponent(4).setValue(data.password);
                                    }

                                    btn.setIconClass('p-user-user_password-icon');
                                    btn.enable();
                                },
                                scope: this
                            });
                        },
                        scope: this
                    }]
                }]
            },{
                xtype: 'form',
                title: this.strings.comment,
                iconCls: 'p-user-user_comment-icon',
                border: false,
                bodyStyle:'padding:5px' ,
                hideMode: 'offsets',
                labelWidth: 130,
                labelAlign: 'top',
                defaultType: 'textfield',
                defaults: {
                    msgTarget: 'under'
                },
                items:[{
                    xtype: 'textarea',
                    name: 'password',
                    hideLabel: true,
                    anchor: '100%',
                    height: 300
                }]
            },{
                title: this.strings.options,
                iconCls: 'p-user-user_options-icon',
                border: false,
                xtype: 'form',
                bodyStyle:'padding:5px' ,
                labelWidth: 130,
                labelAlign: 'top',
                defaultType: 'textfield',
                items:[{
                    xtype: 'iconcombo',
                    fieldLabel: this.strings.interface_language,
                    hiddenName: 'interfaceLanguage',
                    anchor: '100%',
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'name', 'iconCls'],
                        data : Phlexible.Config.get('set.language.backend')
                    }),
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'iconCls',
                    mode: 'local',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    editable: false
                },{
                    xtype: 'combo',
                    fieldLabel: this.strings.theme,
                    hiddenName: 'theme',
                    anchor: '100%',
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'name'],
                        data : Phlexible.Config.get('set.themes')
                    }),
                    displayField: 'name',
                    valueField: 'id',
                    mode: 'local',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    editable: false
                }]

            },{
                title: this.strings.account,
                iconCls: 'p-user-user_account-icon',
                border: false,
                xtype: 'form',
                bodyStyle:'padding:5px' ,
                labelWidth: 130,
                labelAlign: 'top',
                items:[{
                    xtype: 'checkbox',
                    boxLabel: this.strings.cant_change_password,
                    hideLabel: true,
                    name: 'noPasswordChange'
                },{
                    xtype: 'checkbox',
                    boxLabel: this.strings.password_doesnt_expire,
                    hideLabel: true,
                    name: 'noPasswordExpire'
                },{
                    xtype: 'checkbox',
                    boxLabel: this.strings.change_password_next_login,
                    hideLabel: true,
                    name: 'forcePasswordChange'
                },{
                    xtype: 'datefield',
                    fieldLabel: this.strings.account_expires_on,
                    name: 'expires',
                    format: 'Y-m-d',
                    helpText: this.strings.expire_help
                }]
            },{
                xtype: 'grid',
                title: this.strings.roles,
                iconCls: 'p-user-role-icon',
                border: false,
                stripeRows: true,
                store: new Ext.data.JsonStore({
                    autoLoad: true,
                    fields: Phlexible.users.model.UserRole,
                    url: Phlexible.Router.generate('security_acl_roles'),
					listeners: {
						load: function(store, records) {
							Ext.each(records, function(record) {
								if (this.record.get('roles').indexOf(record.get('id')) !== -1) {
									record.set('member', 1);
								}
							}, this);
						},
						scope: this
					}
                }),
                columns: [{
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
                viewCofig:{
                    forceFit:true
                }
            },{
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
						load: function(store, records) {
							Ext.each(records, function(record) {
								if (this.record.get('groups').indexOf(record.get('gid')) !== -1) {
									record.set('member', 1);
								}
							}, this);
						},
						scope: this
					}
                }),
                columns: [{
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
                viewCofig:{
                    forceFit:true
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
                handler: function() {
                    this.getComponent(0).setActiveTab(4);
                },
                scope: this
            }]
        });

        this.buttons = [{
			text: this.strings.cancel,
			handler: this.close,
			scope: this
        },{
			text: this.strings.save,
			iconCls: 'p-user-save-icon',
			handler: this.save,
			scope: this
        }];

        if (this.mode == 'add') {
            this.buttons.push({
                text: this.strings.save_and_notify,
                handler: this.saveAndNotify,
                scope: this
            });
        }

        Phlexible.users.UserWindow.superclass.initComponent.call(this);
    },

    show: function(record) {
        this.record = record;
        this.uid = record.get('uid');

        if (record.get('username')) {
            this.setTitle(this.strings.user + ' "' + record.get('username') + '"');
        } else {
            this.setTitle(this.strings.new_user);
        }

        Phlexible.users.UserWindow.superclass.show.call(this);

        this.detailsPanel.getForm().loadRecord(record);

        var commentForm = this.getComponent(0).getComponent(2);
        commentForm.getComponent(0).setValue(record.get('comment'));

		var properties = {expires: record.get('expireDate')};
		Ext.apply(properties, record.get('properties'));

        var optionsForm = this.getComponent(0).getComponent(3);
        optionsForm.getForm().setValues(properties);

        var accountForm = this.getComponent(0).getComponent(4);
        accountForm.getForm().setValues(properties);

		//var rolesGrid = this.getComponent(0).getComponent(5);
        //rolesgrid.getStore().proxy.conn.url = Phlexible.Router.generate('users_user_roles', {userId: this.uid});

		//var groupsGrid = this.getComponent(0).getComponent(6);
		//groupsGrid.getStore().proxy.conn.url = Phlexible.Router.generate('users_user_groups', {userId: this.uid});

        if (record.data.expireDate) {
            var now = new Date();
            if (record.data.expireDate.format('U') < now.format('U')) {
                this.getTopToolbar().show();
            }
        }
    },

	saveAndNotify: function() {
		return this.save(true);
	},

    save: function(notify) {
		notify = notify ? 1 : 0;

        var detailForm = this.detailsPanel.getForm(),
        	passwordForm = this.getComponent(0).getComponent(1).getForm(),
        	commentForm = this.getComponent(0).getComponent(2),
        	optionsForm = this.getComponent(0).getComponent(3).getForm(),
        	accountForm = this.getComponent(0).getComponent(4).getForm(),
        	rolesGrid = this.getComponent(0).getComponent(5),
        	groupsGrid = this.getComponent(0).getComponent(6);

        if (!detailForm.isValid() || !passwordForm.isValid() || !optionsForm.isValid() || !accountForm.isValid()) {
			return;
		}

		var records, i,
			roles = [],
			groups = [];

		records = rolesGrid.store.getRange();
		for(i=0; i<records.length; i++) {
			if (records[i].get('member')) {
				roles.push(records[i].get('id'));
			}
		}

		records = groupsGrid.store.getRange();
		for(i=0; i<records.length; i++) {
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
		if (account.noPasswordExpire) {
			properties["property_noPasswordExpire"] = 1;
		}
		if (account.forcePasswordChange) {
			properties["property_forcePasswordChange"] = 1;
		}

		var params = {
			notify: notify,
			expires: account.expires,
			comment: commentForm.getComponent(0).getValue(),
			roles: roles.join(','),
			groups: groups.join(',')
		};

		Ext.apply(params, detailForm.getValues());
		Ext.apply(params, passwordForm.getValues());
		Ext.apply(params, properties);

		if ((!params.password && params.password_repeat)
				|| (params.password && !params.password_repeat)
				|| (params.password != params.password_repeat)) {
			return;
		}

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

    onSaveSuccess: function(response) {
        var data = Ext.decode(response.responseText);

        if(data.success) {
            this.uid = data.uid;
            Phlexible.success(data.msg);
            this.fireEvent('save', this.uid);
            this.close();
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }

    }
});
