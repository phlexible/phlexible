Ext.provide('Phlexible.elements.SetTreeNodeOfflineWindow');

Ext.require('Phlexible.gui.util.Dialog');

Phlexible.elements.SetTreeNodeOfflineWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.elements.Strings.set_treenode_offline,
    width: 530,
    minWidth: 530,
    maxWidth: 530,
    height: 460,
    minHeight: 460,
    maxHeight: 460,
    iconCls: 'p-element-set_offline-icon',

    textHeader: Phlexible.elements.Strings.set_treenode_offline_header,
    textDescription: Phlexible.elements.Strings.set_treenode_offline_description,
    textOk: Phlexible.elements.Strings.set_offline,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-publish',
    iconClsOk: 'p-element-publish-icon',

    labelAlign: 'top',
    noFocus: false,
    //submitUrl: Phlexible.Router.generate('tree_publish'),

    comment_required: false,

    getFormItems: function () {
        return [
            {
                xtype: 'checkbox',
                name: 'include_sub_elements',
                hideLabel: true,
                boxLabel: Phlexible.elements.Strings.set_treenode_offline_recursive
                /*            },{
                 xtype: 'checkbox',
                 name: 'include_teasers',
                 hideLabel: true,
                 boxLabel: 'include teasers'
                 },{
                 xtype: 'checkbox',
                 name: 'only_offline',
                 hideLabel: true,
                 boxLabel: 'only publish offline elements'
                 },{
                 xtype: 'checkbox',
                 name: 'only_modified',
                 hideLabel: true,
                 boxLabel: 'only publish modified elements'
                 */            },
            {
                xtype: 'textarea',
                name: 'comment',
                fieldLabel: Phlexible.elements.Strings.comment,
                allowBlank: !this.comment_required,
                anchor: '-80'
            }
        ];
    }
});
