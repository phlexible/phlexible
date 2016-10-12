Ext.provide('Phlexible.elements.tab.Security');

Ext.require('Ext.ux.form.SuperBoxSelect');

Phlexible.elements.tab.Security = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.elements.Strings.security.security,
    iconCls: 'p-element-security-icon',
    strings: Phlexible.elements.Strings.security,
    bodyStyle: 'margin: 5px',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            internalSave: this.onInternalSave,
            scope: this
        });

        this.items = [{
            xtype: 'checkbox',
            name: 'authenticationRequired',
            fieldLabel: '',
            labelSeparator: '',
            boxLabel: this.strings.authentication_required
        },{
            xtype: 'superboxselect',
            fieldLabel: this.strings.required_roles,
            width: 300,
            listWidth: 300,
            name: 'roles',
            store: new Ext.data.JsonStore({
                fields: ['role', 'name'],
                id: 'role'
            }),
            displayField: 'name',
            valueField: 'role',
            mode: 'local',
            allowAddNewData: true,
            stackItems: true,
            pinList: false,
            listeners: {
                newitem: function (bs, v) {
                    var newObj = {
                        role: v
                    };
                    bs.addNewItem(newObj);
                }
            }
        },{
            xtype: 'checkbox',
            name: 'checkAcl',
            fieldLabel: '',
            labelSeparator: '',
            boxLabel: this.strings.check_acl
        },{
            xtype: 'textfield',
            name: 'expression',
            fieldLabel: this.strings.expression,
            width: 300
        }];

        Phlexible.elements.tab.Security.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        if (element.properties.et_type !== 'full') {
            this.disable();
            //this.hide();

            this.getForm().reset();

            return;
        }

        this.enable();
        //this.show();

        this.getForm().reset();

        if (this.element.data.configuration.security && this.element.data.configuration.security.roles) {
            var store = this.getComponent(1).getStore(),
                roles = [];
            Ext.each (this.element.data.configuration.security.roles, function(role) {
                roles.push({role: role, name: role});
            }, this);
            store.loadData(roles);
        }

        this.getForm().setValues(this.element.data.configuration.security || {});
    },

    onInternalSave: function (parameters, errors) {
        if (!this.getForm().isValid()) {
            errors.push('Required fields are missing.');
            return false;
        }

        parameters.security = Ext.encode(this.getForm().getValues());
    }
});

Ext.reg('elements-tab-security', Phlexible.elements.tab.Security);
