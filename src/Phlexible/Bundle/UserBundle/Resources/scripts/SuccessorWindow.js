Phlexible.users.SuccessorWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.users.Strings.successor,
    width: 400,
    height: 220,

    textHeader: Phlexible.users.Strings.successor_header,
    textDescription: Phlexible.users.Strings.successor_description,
    textOk: Phlexible.users.Strings.save,
    textCancel: Phlexible.users.Strings.cancel,

	userId: null,

    getSubmitUrl: function() {
        return Phlexible.Router.generate('users_successor_set', {userId: this.userId});
    },

    getFormItems: function(){
        return [{
            xtype: 'combo',
            hiddenName: 'successorUserId',
            fieldLabel: Phlexible.users.Strings.successor,
            anchor: '-80',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('users_successor_list', {userId: this.userId}),
                fields: ['uid', 'name'],
                id: 'uid'
            }),
            displayField: 'name',
            valueField: 'uid',
            mode: 'remote',
            allowBlank: false,
            triggerAction: 'all',
            editable: false,
            selectOnFocus: true
        }];
    }
});
