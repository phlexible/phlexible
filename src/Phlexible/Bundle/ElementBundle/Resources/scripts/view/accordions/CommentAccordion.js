Ext.provide('Phlexible.elements.accordion.Comment');

Phlexible.elements.accordion.Comment = Ext.extend(Ext.FormPanel, {
    title: Phlexible.elements.Strings.comment,
    cls: 'p-elements-comment-accordion',
    iconCls: 'p-element-comment-icon',
    border: false,
    height: 200,
    autoHeight: true,

    key: 'comment',

    initComponent: function () {
        this.items = [
            {
                xtype: 'textarea',
                hideLabel: true,
                anchor: '100%',
                height: 200
            }
        ];

        Phlexible.elements.accordion.Comment.superclass.initComponent.call(this);
    },

    load: function (data) {
        this.getComponent(0).setValue(data.comment);
    },

    getData: function () {
        return this.getComponent(0).getValue();
    }
});

Ext.reg('elements-commentaccordion', Phlexible.elements.accordion.Comment);