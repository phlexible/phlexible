Phlexible.elements.PublishSlaveWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.publish_slave_header,
    iconCls: 'p-element-publish-icon',
    width: 600,
    height: 400,
    border: true,
    modal: true,
    bodyStyle: 'padding: 10px; background: #DFE8F6;',

    data: [],

    initComponent: function () {
        var checkColumn = Ext.grid.CheckboxColumn({
            xheader: this.strings.publish,
            dataIndex: 'action',
            width: 50
        });

        this.items = [
            {
                height: 150,
                border: false,
                html: this.strings.publish_slave_text
            },
            {
                xtype: 'grid',
                height: 160,
                border: true,
                autoExpandColumn: 2,
                store: new Ext.data.SimpleStore({
                    fields: ['id', 'language', 'version', 'status', 'action'],
                    data: this.data
                }),
                plugins: [checkColumn],
                columns: [
                    {
                        header: this.strings.id,
                        dataIndex: 'id',
                        width: 100,
                        hidden: true
                    },
                    {
                        header: this.strings.language,
                        dataIndex: 'language',
                        width: 120,
                        renderer: function (v) {
                            return Phlexible.inlineIcon('p-flags-' + v + '-icon') + ' ' + Phlexible.languages.Strings[v];
                        }
                    },
                    {
                        header: this.strings.status,
                        dataIndex: 'status',
                        width: 200,
                        renderer: function (v) {
                            if (v == Phlexible.elements.STATUS_OFFLINE) {
                                return Phlexible.elements.Strings.not_published;
                            }
                            else if (v == Phlexible.elements.STATUS_ONLINE) {
                                return '<span style="color: green;">' + Phlexible.elements.Strings.published + '</span>';
                            }
                            else if (v == Phlexible.elements.STATUS_ASYNC) {
                                return '<span style="color: red;">' + Phlexible.elements.Strings.published_async + '</span>';
                            }
                            return v;
                        }
                    },
                    checkColumn
                ]
            }
        ];

        this.buttons = [
            {
                text: this.strings.publish_cancel,
                handler: function () {
                    this.close();
                },
                scope: this
            },
            {
                text: this.strings.publish,
                iconCls: 'p-element-publish-icon',
                handler: function () {
                    var targets = [];
                    this.getComponent(1).getStore().each(function (r) {
                        if (r.get('action')) {
                            targets.push({
                                tid: r.data.id,
                                language: r.data.language,
                                version: r.data.version
                            });
                        }
                    }, this);

                    this.buttons[0].setText(this.strings.close);
                    this.buttons[1].disable();
                    this.buttons[1].hide();

                    Ext.each(targets, function (target) {
                        Ext.Ajax.request({
                            url: Phlexible.Router.generate('elements_publish_publish'),
                            params: target,
                            success: function (response) {
                                var result = Ext.decode(response.responseText);

                                if (result.success) {
                                    var store = this.getComponent(1).getStore();
                                    var index = store.find('language', target.language);
                                    var r = store.getAt(index);
                                    if (!r) {
                                        Phlexible.failure('record not found');
                                        return;
                                    }
                                    r.set('status', this.strings.published);
                                    r.set('action', 0);
                                }
                                else {
                                    Phlexible.failure(result.msg);
                                }
                            },
                            scope: this
                        });
                    }, this);
                },
                scope: this
            }
        ];

        Phlexible.elements.PublishSlaveWindow.superclass.initComponent.call(this);
    }
});
