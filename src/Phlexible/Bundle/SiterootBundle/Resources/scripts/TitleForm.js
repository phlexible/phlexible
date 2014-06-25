Phlexible.siteroots.TitleForm = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.siteroots.Strings.titles,
    strings: Phlexible.siteroots.Strings,
    border: false,
    bodyStyle: 'padding: 5px;',

    initComponent: function(){
        this.items = [];

        for(var i=0; i<Phlexible.Config.get('set.language.frontend').length; i++) {
            this.items.push({
                fieldLabel: Phlexible.inlineIcon(Phlexible.Config.get('set.language.frontend')[i][2]) + ' ' + Phlexible.Config.get('set.language.frontend')[i][1],
                name: Phlexible.Config.get('set.language.frontend')[i][0],
                xtype: 'textfield',
                width: 300,
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
    loadData: function(id, title, data) {
        this.getForm().reset();
        this.getForm().setValues(data.titles);
    },

    isValid: function() {
        var valid = this.getForm().isValid();

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
    getSaveData: function() {
        var values = this.getForm().getValues();

        return {
            'titles': values
        };
    }
});

Ext.reg('siteroots-titles', Phlexible.siteroots.TitleForm);