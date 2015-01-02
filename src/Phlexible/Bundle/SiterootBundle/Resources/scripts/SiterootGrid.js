Ext.namespace('Phlexible.siteroots');

Phlexible.siteroots.SiterootGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.siteroots.Strings.siteroot,
    strings: Phlexible.siteroots.Strings,
    autoExpandColumn: 'title',

    initComponent: function () {

        this.addEvents(
            /**
             * @event siterootChange
             * Fires after the active Siteroot has been changed
             * @param {Number} siteroot_id The ID of the selected ElementType.
             * @param {String} siteriit_title The Title of the selected ElementType.
             */
            'siterootChange',
            /**
             * @event siterootDataChange
             * Fires after a siteroot is added or title has been changed
             */
            'siterootDataChange'
        );

        this.contextMenu = new Ext.menu.Menu({
            items: [
                {
                    text: this.strings.remove,
                    iconCls: 'p-siteroot-siteroot_delete-icon',
                    handler: function (item) {
                        var r = item.parentMenu.record;

                        Ext.MessageBox.confirm(
                            this.strings.remove,
                            this.strings.sure,
                            this.onDeleteSiteroot.createDelegate(this, [r], true)
                        );
                    },
                    scope: this
                }
            ]
        });


        this.tbar = [
            {
                text: this.strings.add_siteroot,
                iconCls: 'p-siteroot-siteroot_add-icon',
                handler: this.onAddSiteroot,
                scope: this
            }
        ];

        this.store = new Ext.data.JsonStore({
            autoLoad: true,
            root: 'siteroots',
            id: 'id',
            totalProperty: 'count',
            fields: Phlexible.siteroots.model.Siteroot,
            url: Phlexible.Router.generate('siteroots_siteroot_list'),
            sortInfo: {
                field: 'title',
                direction: 'ASC'
            },
            listeners: {
                load: {
                    fn: this.onLoadStore,
                    scope: this
                }
            }
        });

        this.columns = [
            {
                id: 'id',
                header: this.strings.id,
                hidden: true,
                dataIndex: 'id'
            },
            {
                id: 'title',
                header: this.strings.siteroots,
                dataIndex: 'title',
                sortable: true
            }
        ];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true,
            listeners: {
                'rowselect': {
                    fn: this.onSelectSiteroot,
                    scope: this
                }
            }
        });

        this.addListener({
            rowcontextmenu: {
                fn: this.onRowContextMenu,
                scope: this
            },
            siterootDataChange: {
                fn: this.onSiterootDataChange,
                scope: this
            }
        });

        Phlexible.siteroots.SiterootGrid.superclass.initComponent.call(this);
    },

    /**
     * Store the current record and shoe context menu
     *
     * @param {Object} grid
     * @param {Number} rowIndex
     * @param {Object} event
     */
    onRowContextMenu: function (grid, rowIndex, event) {
        event.stopEvent();

        var r = grid.store.getAt(rowIndex);
        this.contextMenu.record = r;

        var coords = event.getXY();
        this.contextMenu.showAt([coords[0], coords[1]]);

        var sm = grid.getSelectionModel();
        if (!sm.hasSelection() || (sm.getSelected().id != r.id)) {
            sm.selectRow(rowIndex);
        }
    },

    /**
     * If the siteroot store is loaded and no siteroot
     * is selected then select the first siteroot initially.
     *
     * @param {Object} store
     */
    onLoadStore: function (store) {

        var sm = this.getSelectionModel();

        if ((store.getCount() > 0)) {
            if (!this.selected) {
                sm.selectFirstRow();
            } else {
                var i = store.find('id', this.selected);
                this.selected = null;
                sm.selectRow(i);
            }
        }
    },

    /**
     * If the siteroot selection changes fire the siterootChange event.
     *
     * @param {Object} selModel
     * @param {Number} rowIndex
     * @param {Object} record
     */
    onSelectSiteroot: function (selModel, rowIndex, record) {
        this.fireEvent('siterootChange', record.get('id'), record.get('title'));
    },

    /**
     * Action if site
     */
    onAddSiteroot: function () {
        Ext.MessageBox.prompt(this.strings.new_siteroot, this.strings.siteroot_title, function (btn, text) {
            if (btn !== 'ok') {
                return;
            }

            Ext.Ajax.request({
                url: Phlexible.Router.generate('siteroots_siteroot_create'),
                params: {
                    title: text
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.fireEvent('siterootDataChange');
                    }
                    else {
                        Ext.Msg.alert(this.strings.failure, data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} btn
     * @param {String} text
     * @param {Object} r
     */
    onDeleteSiteroot: function (btn, text, x, r) {

        if (btn == 'yes') {
            Ext.Ajax.request({
                url: Phlexible.Router.generate('siteroots_siteroot_delete'),
                params: {
                    id: r.id
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        this.fireEvent('siterootDataChange');
                        Phlexible.Frame.menu.load();
                    }
                    else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }
    },

    /**
     * After the siteroot data changed.
     *  - new siteroot added
     *  - title of siteroot changed
     */
    onSiterootDataChange: function () {
        this.store.reload();
    }

});

Ext.reg('siteroots-siteroots', Phlexible.siteroots.SiterootGrid);