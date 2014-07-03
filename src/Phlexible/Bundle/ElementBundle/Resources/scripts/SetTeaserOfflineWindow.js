Phlexible.elements.SetTeaserOfflineWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.elements.Strings.set_teaser_offline,
    width: 530,
    minWidth: 530,
    maxWidth: 530,
    height: 440,
    minHeight: 440,
    maxHeight: 440,
    iconCls: 'p-element-set_offline-icon',

    textHeader: Phlexible.elements.Strings.set_teaser_offline_header,
    textDescription: Phlexible.elements.Strings.set_teaser_offline_description,
    textOk: Phlexible.elements.Strings.set_offline,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-publish',
    iconClsOk: 'p-element-save-icon',

    labelAlign: 'top',
    noFocus: false,
    //submitUrl: Phlexible.Router.generate('teasers_layout_publish'),

    comment_required: false,

    getFormItems: function () {
        return [
            {
                /*            xtype: 'checkbox',
                 name: 'include_sub_elements',
                 boxLabel: 'With all sub elements'
                 },{
                 xtype: 'checkbox',
                 name: 'include_teasers',
                 boxLabel: 'With all teasers'
                 },{
                 xtype: 'checkbox',
                 name: 'only_offline',
                 boxLabel: 'Only publish offline nodes'
                 },{
                 xtype: 'checkbox',
                 name: 'only_modified',
                 boxLabel: 'Only publish modified nodes'
                 },{*/
                xtype: 'textarea',
                name: 'comment',
                fieldLabel: Phlexible.elements.Strings.comment,
                allowBlank: !this.comment_required,
                anchor: '-80'
            }
        ];
    }
});
