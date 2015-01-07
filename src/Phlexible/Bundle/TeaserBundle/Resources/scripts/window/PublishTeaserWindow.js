Ext.provide('Phlexible.teasers.PublishTeaserWindow');

Ext.require('Phlexible.gui.util.Dialog');

Phlexible.teasers.PublishTeaserWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.teasers.Strings.publish_teaser,
    width: 530,
    minWidth: 530,
    maxWidth: 530,
    height: 440,
    minHeight: 440,
    maxHeight: 440,
    iconCls: 'p-element-publish-icon',

    textHeader: Phlexible.teasers.Strings.publish_teaser_header,
    textDescription: Phlexible.teasers.Strings.publish_teaser_description,
    textOk: Phlexible.teasers.Strings.publish,
    textCancel: Phlexible.teasers.Strings.cancel,

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
