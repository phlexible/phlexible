Ext.provide('Phlexible.siteroots.TitleForm');
Ext.provide('Phlexible.siteroots.CustomTitleTpl');

Phlexible.siteroots.CustomTitleTpl = new Ext.XTemplate(
    '<tpl for=".">',
    '<span style="padding-right: 15px;"><b>{placeholder}</b> {title}</span>',
    '</tpl>'
);

Phlexible.siteroots.TitleForm = Ext.extend(Ext.Panel, {
    title: Phlexible.siteroots.Strings.titles,
    strings: Phlexible.siteroots.Strings,
    border: false,
    bodyStyle: 'padding: 5px;',

    initComponent: function () {
        this.items = [
            {
                xtype: 'form',
                border: false,
                xlabelAlign: 'top',
                items: []
            }
        ];

        for (var i = 0; i < Phlexible.Config.get('set.language.frontend').length; i++) {
            this.items[0].items.push({
                fieldLabel: Phlexible.inlineIcon(Phlexible.Config.get('set.language.frontend')[i][2]) + ' ' + Phlexible.Config.get('set.language.frontend')[i][1],
                name: Phlexible.Config.get('set.language.frontend')[i][0],
                xtype: 'textfield',
                width: 500,
                allowBlank: false
            });
        }

        Phlexible.siteroots.TitleForm.superclass.initComponent.call(this);
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        // remember current siteroot id
        this.siterootId = id;

        this.getComponent(0).getForm().reset();
        this.getComponent(0).getForm().setValues(data.titles);
    },

    isValid: function () {
        var valid = this.getComponent(0).getForm().isValid();

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        return {
            titles: this.getComponent(0).getForm().getValues()
        };
    }
});

Ext.reg('siteroots-titles', Phlexible.siteroots.TitleForm);