Ext.provide('Phlexible.users.edit.Options');

Phlexible.users.edit.Options = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.options,
    iconCls: 'p-user-user_options-icon',
    border: false,
    xtype: 'form',
    bodyStyle: 'padding:5px',
    labelWidth: 130,
    labelAlign: 'top',
    defaultType: 'textfield',

    initComponent: function() {
        this.items = [{
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
        },{
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
        }];

        Phlexible.users.edit.Options.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        var properties = {
            enabled: user.get('enabled')
        };
        Ext.apply(properties, user.get('properties'));

        this.getForm().setValues(properties);
    },

    isValid: function() {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert('Validation failed', 'Check the options tab for details.');
            return false;
        }

        return true;
    },

    getData: function() {
        var options = this.getForm().getValues(),
            data = {};

        if (options.theme) {
            data["property_theme"] = options.theme;
        }
        if (options.interfaceLanguage) {
            data["property_interfaceLanguage"] = options.interfaceLanguage;
        }

        return data;
    }
});

Ext.reg('user_edit_options', Phlexible.users.edit.Options);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_options'
});
