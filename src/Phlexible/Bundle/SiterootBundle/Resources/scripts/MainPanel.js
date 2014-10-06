Phlexible.siteroots.MainPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.siteroots.Strings,
    title: Phlexible.siteroots.Strings.siteroots,
    iconCls: 'p-siteroot-component-icon',
    cls: 'p-siteroots-main-panel',

    layout: 'border',

    initComponent: function () {

        this.addEvents(
            /**
             * @event siterootChange
             * Fires after the active Siteroot has been changed
             * @param {Number} siteroot_id The ID of the selected ElementType.
             * @param {String} siteriit_title The Title of the selected ElementType.
             */
            'siterootChange'
        );

        this.items = [
            this.siterootGrid = new Phlexible.siteroots.SiterootGrid({
                region: 'west',
                width: 250,
                minWidth: 200,
                maxWidth: 350,
                split: true,
                listeners: {
                    'siterootChange': {
                        fn: this.onSiterootChange,
                        scope: this
                    },
                    'siterootDataChange': {
                        fn: this.onSiterootDataChange,
                        scope: this
                    }
                }
            }), {
                region: 'center',
                xtype: 'panel',
                title: 'no_siteroot_loaded',
                layout: 'accordion',
                disabled: true,
                tbar: [
                    {
                        text: this.strings.save_siteroot_data,
                        iconCls: 'p-siteroot-save-icon',
                        handler: this.onSaveData,
                        scope: this
                    }
                ],
                items: [
                    {
                        xtype: 'siteroots-urls'
                    },
                    {
                        xtype: 'siteroots-titles'
                    },
                    {
                        xtype: 'siteroots-properties'
                    },
                    {
                        xtype: 'siteroots-specialtids'
                    },
                    {
                        xtype: 'siteroots-navigations'
                    }
                ]
            }
        ];

        Phlexible.siteroots.MainPanel.superclass.initComponent.call(this);
    },

    loadParams: function () {
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     */
    onSiterootChange: function (id, title) {
        this.getComponent(0).enable();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroots_siteroot_load', {id: id}),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.siterootId = id;
                this.siterootTitle = title;
                this.getComponent(1).setTitle(title);

                this.getComponent(1).items.each(function (panel) {
                    panel.loadData(id, title, data);
                });

                this.getComponent(1).enable();

                // fire event for plugins
                this.fireEvent('siterootChange', id, title);
            },
            scope: this
        })
    },

    /**
     * After the siteroot data changed.
     *  - new siteroot added
     *  - title of siteroot changed
     */
    onSiterootDataChange: function () {
        Phlexible.Frame.loadConfig();
        Phlexible.Frame.menu.load();
    },

    /**
     * If a complete siteroot should be saved (including all plugins).
     *
     * The data is collected and submitted in only one request to the server
     * all plugins must register themselfs at the PHP observer for handle the
     * submit process.
     */
    onSaveData: function () {
        var saveData = {}, valid = true;

        this.getComponent(1).items.each(function (panel) {
            if (typeof(panel.isValid) == 'function' && !panel.isValid()) {
                valid = false;
            }
            else if (typeof(panel.getSaveData) == 'function') {
                var data = panel.getSaveData();

                if (!data) {
                    return;
                }

                // merge data
                Ext.apply(saveData, data);
            }
        }, this);

        if (!valid) {
            Ext.MessageBox.alert(this.strings.failure, this.strings.err_check_accordions_for_errors);
            return;
        }

        // save data
        Ext.Ajax.request({
            method: 'POST',
            url: Phlexible.Router.generate('siteroots_siteroot_save'),
            params: {
                id: this.siterootId,
                data: Ext.encode(saveData)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    this.siterootGrid.selected = this.siterootGrid.getSelectionModel().getSelected().id;
                    this.siterootGrid.store.reload();

//                    this.onSiterootChange(this.siterootId, this.siterootTitle);
                }
                else {
                    Ext.Msg.alert('Failure', data.msg);
                }
            },
            scope: this
        });

    }
});

Ext.reg('siteroots-main', Phlexible.siteroots.MainPanel);