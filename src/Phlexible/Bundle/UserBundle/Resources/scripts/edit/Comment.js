Ext.provide('Phlexible.users.edit.Comment');

Phlexible.users.edit.Comment = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.comment,
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

    initComponent: function() {
        this.items = [{
            xtype: 'textarea',
            name: 'comment',
            hideLabel: true,
            anchor: '100%',
            height: 300
        }];

        Phlexible.users.edit.Comment.superclass.initComponent.call(this);
    },

    loadUser: function(user) {
        this.getForm().loadRecord(user);
    },

    isValid: function() {
        return true;
    },

    getData: function() {
        return {
            comment: this.getForm().getValues().comment
        };
    }
});

Ext.reg('user_edit_comment', Phlexible.users.edit.Comment);

Phlexible.PluginRegistry.prepend('userEditPanels', {
    xtype: 'user_edit_comment'
});
