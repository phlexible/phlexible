Phlexible.siteroots.CustomTitleTpl = new Ext.XTemplate(
    '<table cellpadding="5" cellspacing="5">',
    '<tpl for=".">',
    '<tr><td>{placeholder}</td><td>&nbsp;</td><td>{title}</td></tr>',
    '</tpl>',
    '</table>'
);
Phlexible.siteroots.CustomTitleForm = Ext.extend(Ext.Panel, {
    title: Phlexible.siteroots.Strings.custom_titles,
    strings: Phlexible.siteroots.Strings,
    border: false,
    autoScroll: true,
    layout: 'border',

    initComponent: function () {
        this.task1 = new Ext.util.DelayedTask(this.updateDefaultPreview, this);
        this.task2 = new Ext.util.DelayedTask(this.updateHomePreview, this);

        this.items = [
            {
                xtype: 'form',
                region: 'center',
                border: false,
                bodyStyle: 'padding: 5px;',
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'panel',
                        title: this.strings.default_custom_title,
                        layout: 'form',
                        autoScroll: true,
                        bodyStyle: 'padding: 5px;',
                        style: 'padding-bottom: 5px;',
                        items: [
                            {
                                fieldLabel: this.strings.title,
                                name: 'head_title',
                                xtype: 'textfield',
                                anchor: '-50',
                                emptyText: this.strings.no_customized_title,
                                enableKeyEvents: true,
                                listeners: {
                                    keyup: function (field, event) {
                                        if (event.getKey() == event.ENTER) {
                                            this.task1.cancel();
                                            this.updateDefaultPreview();
                                            return;
                                        }

                                        this.task1.delay(500);
                                    },
                                    scope: this
                                }
                            },
                            {
                                fieldLabel: this.strings.example,
                                name: 'example',
                                xtype: 'textfield',
                                anchor: '-50',
                                readOnly: true,
                                append: [
                                    {
                                        xtype: 'button',
                                        iconCls: 'p-siteroot-reload-icon',
                                        handler: this.updateDefaultPreview,
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'panel',
                        title: this.strings.start_custom_title,
                        layout: 'form',
                        bodyStyle: 'padding: 5px;',
                        items: [
                            {
                                fieldLabel: this.strings.title,
                                name: 'start_head_title',
                                xtype: 'textfield',
                                anchor: '-50',
                                emptyText: this.strings.no_customized_start_title,
                                enableKeyEvents: true,
                                listeners: {
                                    keyup: function (field, event) {
                                        if (event.getKey() == event.ENTER) {
                                            this.task2.cancel();
                                            this.updateHomePreview();
                                            return;
                                        }

                                        this.task2.delay(500);
                                    },
                                    scope: this
                                }
                            },
                            {
                                fieldLabel: this.strings.example,
                                name: 'start_example',
                                xtype: 'textfield',
                                anchor: '-50',
                                readOnly: true,
                                append: [
                                    {
                                        xtype: 'button',
                                        iconCls: 'p-siteroot-reload-icon',
                                        handler: this.updateHomePreview,
                                        scope: this
                                    }
                                ]
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'panel',
                region: 'east',
                collapsible: true,
                title: this.strings.legend,
                width: 250,
                bodyStyle: 'padding: 5px;',
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

        Phlexible.siteroots.CustomTitleForm.superclass.initComponent.call(this);
    },

    updateDefaultPreview: function () {
        var title = this.getComponent(0).getComponent(0).getComponent(0).getValue();
        if (!title) {
            this.getComponent(0).getComponent(0).getComponent(1).reset();
            return;
        }

        this.getComponent(0).getComponent(0).getComponent(1).append[0].setIconClass('p-siteroot-loading-icon');

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroots_customtitle_example'),
            params: {
                siteroot_id: this.siterootId,
                head_title: title
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.getComponent(0).getComponent(0).getComponent(1).setValue(data.data.example);
                }

                this.getComponent(0).getComponent(0).getComponent(1).append[0].setIconClass('p-siteroot-reload-icon');
            },
            scope: this
        });
    },

    updateHomePreview: function () {
        var title = this.getComponent(0).getComponent(1).getComponent(0).getValue();
        if (!title) {
            this.getComponent(0).getComponent(1).getComponent(1).reset();
            return;
        }

        this.getComponent(0).getComponent(1).getComponent(1).append[0].setIconClass('p-siteroot-loading-icon');

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroots_customtitle_example'),
            params: {
                siteroot_id: this.siterootId,
                head_title: title
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.getComponent(0).getComponent(1).getComponent(1).setValue(data.data.example);
                }

                this.getComponent(0).getComponent(1).getComponent(1).append[0].setIconClass('p-siteroot-reload-icon');
            },
            scope: this
        });
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
        this.getComponent(0).getForm().setValues(data.customtitles);

        this.updateDefaultPreview();
        this.updateHomePreview();
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        var values = {
            head_title: this.getComponent(0).getComponent(0).getComponent(0).getValue(),
            start_head_title: this.getComponent(0).getComponent(1).getComponent(0).getValue()
        };

        return {
            'customtitles': values
        };
    }
});

Ext.reg('siteroots-customtitles', Phlexible.siteroots.CustomTitleForm);