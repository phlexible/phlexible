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
        this.task1 = new Ext.util.DelayedTask(this.updateDefaultPreview, this);
        this.task2 = new Ext.util.DelayedTask(this.updateHomePreview, this);

        this.items = [
            {
                xtype: 'form',
                border: false,
                xlabelAlign: 'top',
                items: []
            },
            {
                xtype: 'editorgrid',
                title: this.strings.custom_titles,
                style: 'padding-top: 5px;',
                store: new Ext.data.JsonStore({
                    fields: ['name', 'pattern', 'example'],
                    data: [{name: 'bla', pattern: 'bla', example:'bla'}]
                }),
                columns: [{
                    header: this.strings.name,
                    dataIndex: 'name',
                    width: 50,
                    editor: new Ext.form.TextField()
                },{
                    header: this.strings.pattern,
                    dataIndex: 'pattern',
                    width: 300,
                    editor: new Ext.form.TextField()
                },{
                    header: this.strings.example,
                    dataIndex: 'example',
                    width: 300
                }],
                listeners: {
                    afteredit: function(e) {
                        if (e.column === 1 && e.value !== e.originalValue) {
                            this.updatePreview(e.record);
                        }
                    },
                    scope: this
                }
            },
            {
                xtype: 'panel',
                title: this.strings.legend,
                bodyStyle: 'padding: 5px;',
                style: 'padding-top: 5px;',
                items: [
                    {
                        xtype: 'dataview',
                        store: new Ext.data.JsonStore({
                            url: Phlexible.Router.generate('siteroots_customtitle_placeholders'),
                            root: 'placeholders',
                            fields: ['placeholder', 'title'],
                            autoLoad: true
                        }),
                        tpl: Phlexible.siteroots.CustomTitleTpl,
                        autoHeight: true,
                        singleSelect: true,
                        overClass: 'xxx',
                        itemSelector: 'div'
                    }
                ]
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

        this.getComponent(1).getStore().removeAll();
        this.getComponent(1).getStore().loadData(data.patterns);
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
        var patterns = {};
        this.getComponent(1).getStore().each(function(r) {
            patterns[r.get('name')] = r.get('pattern');
        });

        return {
            titles: this.getComponent(0).getForm().getValues(),
            patterns: patterns
        };
    },


    updatePreview: function (record) {
        var pattern = record.get('pattern');
        if (!pattern) {
            record.set('example');
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroots_customtitle_example'),
            params: {
                siteroot_id: this.siterootId,
                pattern: pattern
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    record.set('example', data.msg);
                }
            },
            scope: this
        });
    }
});

Ext.reg('siteroots-titles', Phlexible.siteroots.TitleForm);