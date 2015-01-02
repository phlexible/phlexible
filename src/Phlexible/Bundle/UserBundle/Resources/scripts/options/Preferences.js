Ext.ns('Phlexible.users.options');

Phlexible.users.options.Preferences = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.preferences,
    bodyStyle: 'padding: 15px',
    border: false,
    labelWidth: 150,
    defaultType: 'textfield',
    defaults: {
        msgTarget: 'under'
    },
    labelAlign: 'top',

    initComponent: function () {
        this.items = [
            {
                xtype: 'iconcombo',
                fieldLabel: this.strings.interface_language,
                hiddenName: 'interfaceLanguage',
                anchor: '100%',
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'name', 'iconCls'],
                    data: Phlexible.Config.get('set.language.backend', 'en')
                }),
                editable: false,
                displayField: 'name',
                valueField: 'id',
                iconClsField: 'iconCls',
                mode: 'local',
                triggerAction: 'all',
                selectOnFocus: true,
                value: Phlexible.Config.get('user.property.interfaceLanguage', 'en')
            }/*,{
             xtype: 'iconcombo',
             fieldLabel: this.strings.content_language,
             editable: false,
             hiddenName: 'frontendLanguage',
             anchor: '100%',
             store: new Ext.data.SimpleStore({
             fields: ['id', 'name', 'iconCls'],
             data : Phlexible.Config.get('set.language.frontend')
             }),
             displayField: 'name',
             valueField: 'id',
             iconClsField: 'iconCls',
             mode: 'local',
             triggerAction: 'all',
             selectOnFocus: true,
             value: Phlexible.Config.get('user.property.frontendLanguage', 'en)
             }*/
        ];

        this.buttons = [
            {
                text: this.strings.save,
                handler: function () {
                    this.form.submit({
                        url: Phlexible.Router.generate('users_options_savepreferences'),
                        success: function (form, result) {
                            if (result.success) {
                                var values = form.getValues();
                                //Phlexible.Config.set('user.property.frontendLanguage', values.frontendLanguage);
                                Phlexible.Config.set('user.property.interfaceLanguage', values.interfaceLanguage);
                                Phlexible.Config.set('user.property.dateFormat', values.dateFormat);

                                this.fireEvent('save');
                            } else {
                                Ext.Msg.alert('Failure', result.msg);
                            }
                        },
                        scope: this
                    });
                },
                scope: this
            },
            {
                text: this.strings.cancel,
                handler: function () {
                    this.fireEvent('cancel');
                },
                scope: this
            }
        ];

        Phlexible.users.options.Preferences.superclass.initComponent.call(this);
    }
});

Ext.reg('usersoptionspreferences', Phlexible.users.options.Preferences);

Phlexible.PluginRegistry.prepend('userOptionCards', {
    xtype: 'usersoptionspreferences',
    title: Phlexible.users.Strings.preferences,
    description: Phlexible.users.Strings.preferences_description,
    iconCls: 'p-user-preferences-icon'
});