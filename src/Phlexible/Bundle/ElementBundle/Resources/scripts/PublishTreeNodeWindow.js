Phlexible.elements.PublishTreeNodeWindow = Ext.extend(Ext.Window, {
    title: Phlexible.elements.Strings.publish_element_advanced,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-publish-icon',
    cls: 'p-elements-publish',
    width: 980,
    minWidth: 980,
    height: 520,
    minHeight: 520,
    layout: 'border',
    border: false,
    modal: true,
    constrainHeader: true,
    maximizable: true,

    comment_required: false,

    initComponent: function () {
        this.task = new Ext.util.DelayedTask(this.doPreview, this);

        var langCheckColumn = Ext.grid.CheckboxColumn({
            dataIndex: 'action',
            width: 50
        });

        var previewCheckColumn = Ext.grid.CheckboxColumn({
            dataIndex: 'action',
            width: 50
        });

        var languages = Phlexible.clone(Phlexible.Config.get('set.language.frontend'));
        Ext.each(languages, function (item) {
            item[3] = this.language === item[0];
        }, this);

        this.items = [
            {
                layout: 'accordion',
                region: 'west',
                width: 300,
                border: false,
                items: [
                    {
                        xtype: 'form',
                        title: this.strings.filter,
                        cls: 'p-elements-publish-filter',
                        bodyStyle: 'padding: 8px;',
                        //labelAlign: 'top',
                        border: true,
                        items: [
                            {
                                xtype: 'panel',
                                title: this.strings.full_elements,
                                layout: 'form',
                                frame: true,
                                collapsible: true,
                                defaults: {
                                    hideLabel: true
                                },
                                items: [
                                    {
                                        xtype: 'checkbox',
                                        name: 'include_elements',
                                        boxLabel: this.strings.include_full_elements,
                                        checked: true,
                                        listeners: {
                                            check: function (cb, checked) {
                                                if (checked) {
                                                    this.getComponent(0).getComponent(0).getComponent(0).getComponent(1).enable();
                                                }
                                                else {
                                                    this.getComponent(0).getComponent(0).getComponent(0).getComponent(1).disable();
                                                }

                                                this.delayTask();
                                            },
                                            scope: this
                                        }
                                    },
                                    {
                                        xtype: 'checkbox',
                                        name: 'include_element_instances',
                                        boxLabel: this.strings.include_full_element_instances,
                                        listeners: {
                                            check: this.delayTask,
                                            scope: this
                                        }
                                    }
                                ]
                            },
                            {
                                xtype: 'panel',
                                title: this.strings.part_elements,
                                layout: 'form',
                                frame: true,
                                collapsible: true,
                                defaults: {
                                    hideLabel: true
                                },
                                items: [
                                    {
                                        xtype: 'checkbox',
                                        name: 'include_teasers',
                                        boxLabel: this.strings.include_part_elements,
                                        listeners: {
                                            check: function (cb, checked) {
                                                if (checked) {
                                                    this.getComponent(0).getComponent(0).getComponent(1).getComponent(1).enable();
                                                }
                                                else {
                                                    this.getComponent(0).getComponent(0).getComponent(1).getComponent(1).disable();
                                                }

                                                this.delayTask();
                                            },
                                            scope: this
                                        }
                                    },
                                    {
                                        xtype: 'checkbox',
                                        name: 'include_teaser_instances',
                                        boxLabel: this.strings.include_part_element_instances,
                                        disabled: true,
                                        listeners: {
                                            check: this.delayTask,
                                            scope: this
                                        }
                                    }
                                ]
                            },
                            {
                                xtype: 'panel',
                                title: this.strings.options,
                                layout: 'form',
                                frame: true,
                                collapsible: true,
                                defaults: {
                                    hideLabel: true
                                },
                                items: [
                                    {
                                        xtype: 'checkbox',
                                        name: 'recursive',
                                        boxLabel: this.strings.recursive,
                                        listeners: {
                                            check: this.delayTask,
                                            scope: this
                                        }
                                    },
                                    {
                                        xtype: 'checkbox',
                                        name: 'only_offline',
                                        boxLabel: this.strings.only_offline_elements,
                                        listeners: {
                                            check: this.delayTask,
                                            scope: this
                                        }
                                    },
                                    {
                                        xtype: 'checkbox',
                                        name: 'only_async',
                                        boxLabel: this.strings.only_async_elements,
                                        listeners: {
                                            check: this.delayTask,
                                            scope: this
                                        }
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'grid',
                        title: this.strings.languages,
                        border: true,
                        autoExpandColumn: 1,
                        plugins: [langCheckColumn],
                        store: new Ext.data.SimpleStore({
                            fields: ['code', 'title', 'iconCls', 'action'],
                            data: languages,
                            listeners: {
                                update: this.delayTask,
                                scope: this
                            }
                        }),
                        columns: [
                            langCheckColumn,
                            {
                                header: this.strings.language,
                                dataIndex: 'title',
                                renderer: function (v, md, r) {
                                    return Phlexible.inlineIcon(r.data.iconCls) + ' ' + v;
                                }
                            }]
                    },
                    {
                        xtype: 'form',
                        region: 'west',
                        title: this.strings.comment,
                        width: 300,
                        border: true,
                        bodyStyle: 'padding: 5px;',
                        //labelAlign: 'top',
                        defaults: {
                            hideLabel: true
                        },
                        items: [
                            {
                                html: this.strings.comment + ':',
                                border: false,
                                style: 'padding-top: 5px;'
                            },
                            {
                                xtype: 'textarea',
                                name: 'comment',
                                allowBlank: !this.comment_required,
                                labelAlign: 'top',
                                height: 100,
                                anchor: '-10'
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'grid',
                title: this.strings.preview,
                region: 'center',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('elements_publish_preview'),
                    //id: 'tid',
                    root: 'preview',
                    fields: ['action', 'type', 'instance', 'tid', 'teaser_id', 'eid', 'version', 'language', 'depth', 'path', 'icon', 'title'],
                    sortInfo: {
                        field: 'path',
                        direction: 'asc'
                    },
                    listeners: {
                        load: function (store) {
                            var cnt = store.getCount();
                            if (cnt === 1) {
                                cnt += ' ' + this.strings.element;
                            } else {
                                cnt += ' ' + this.strings.elements;
                            }
                            this.getComponent(1).getBottomToolbar().items.items[1].setText(cnt);
                        },
                        scope: this
                    }
                }),
                viewConfig: {
                    deferEmptyText: true,
                    emptyText: this.strings.no_elements_match_filter
                },
                autoExpandColumn: 8,
                loadMask: true,
                plugins: [previewCheckColumn],
                columns: [
                    previewCheckColumn,
                    {
                        header: this.strings.type,
                        dataIndex: 'type',
                        width: 80,
                        sortable: true,
                        renderer: function (v, md, r) {
                            v = Phlexible.elements.Strings[v];

                            if (r.data.instance) {
                                v += ' / ' + Phlexible.elements.Strings.instance;
                            }

                            return v;
                        }
                    }, {
                        header: this.strings.tid,
                        dataIndex: 'tid',
                        width: 50,
                        sortable: true
                    }, {
                        header: this.strings.teaser_id,
                        dataIndex: 'teaser_id',
                        width: 50,
                        sortable: true
                    }, {
                        header: this.strings.eid,
                        dataIndex: 'eid',
                        width: 50,
                        sortable: true,
                        hidden: true
                    }, {
                        header: this.strings.version,
                        dataIndex: 'version',
                        width: 50,
                        sortable: true
                    }, {
                        header: this.strings.language,
                        dataIndex: 'language',
                        width: 50,
                        sortable: true,
                        renderer: function (v) {
                            return Phlexible.inlineIcon('p-flags-' + v + '-icon');
                        }
                    }, {
                        header: this.strings.depth,
                        dataIndex: 'depth',
                        width: 50,
                        sortable: true,
                        hidden: true
                    }, {
                        header: this.strings.title,
                        dataIndex: 'title',
                        width: 100,
                        sortable: true,
                        renderer: function (v, md, r) {
                            var prefix = '';

                            if (r.data.depth > 0) {
                                prefix += '<span class="x-tree-lines">';
                                for (var i = 0; i < r.data.depth - 1; i++) {
                                    prefix += '<img src="' + Ext.BLANK_IMAGE_URL + '" class="x-tree-elbow-line">';
                                }
                                prefix += '<img src="' + Ext.BLANK_IMAGE_URL + '" class="x-tree-elbow">';
                                prefix += '</span>';
                            }

                            if (r.data.icon) {
                                prefix += '<img src="' + r.data.icon + '" width="18" height="18" style="vertical-align: middle;" /> ';
                            }

                            return prefix + v;
                        }
                    }],
                bbar: [
                    '->',
                    {
                        text: '0 ' + this.strings.elements,
                        handler: function () {
                            this.getComponent(1).getStore().reload();
                        },
                        scope: this
                    }],
                listeners: {
                    render: this.delayTask,
                    scope: this
                }
            }
        ];

        this.buttons = [
            {/*
             text: this.strings.preview,
             iconCls: 'p-element-preview-icon',
             handler: this.onPreview,
             scope: this
             },{*/
                text: this.strings.publish,
                iconCls: 'p-element-publish-icon',
                disabled: true,
                handler: this.doPublish,
                scope: this
            },
            {
                text: this.strings.cancel,
                handler: this.close,
                scope: this
            }
        ];

        Phlexible.elements.PublishTreeNodeWindow.superclass.initComponent.call(this);
    },

    delayTask: function () {
        this.buttons[0].disable();

        this.task.delay(800);
    },

    doPreview: function () {
        var params = this.getComponent(0).getComponent(0).getForm().getValues();
        params.tid = this.tid;
        params.language = this.language;
        params.version = this.version;

        var languages = [];
        this.getComponent(0).getComponent(1).getStore().each(function (r) {
            if (r.data.action) {
                languages.push(r.data.code);
            }
        });

        if (!languages.length) return;

        params.languages = languages.join(',');

        this.getComponent(1).getStore().load({
            params: params,
            callback: function (records) {
                if (records.length) {
                    this.buttons[0].enable();
                }
            },
            scope: this
        });
    },

    doPublish: function () {
        this.buttons[0].disable();

        if (!this.getComponent(0).getComponent(2).getForm().isValid()) {
            Ext.MessageBox.alert(this.strings.error, this.strings.check_input);
            return;
        }

        var comment = this.getComponent(0).getComponent(2).getComponent(1).getValue();

        var data = [];
        this.getComponent(1).getStore().each(function (r) {
            if (!r.data.action) return;

            data.push({
                eid: r.data.eid,
                tid: r.data.tid,
                teaser_id: r.data.teaser_id,
                version: r.data.version,
                language: r.data.language
            });
        });

        if (!data.length) {
            Ext.MessageBox.alert(this.strings.error, this.strings.check_elements);
            return;
        }

        var params = {
            tid: this.tid,
            language: this.language,
            version: this.version,
            comment: comment,
            data: Ext.encode(data)
        };

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_publish_advancedpublish'),
            params: params,
            timeout: 600000,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);
                    this.fireEvent('success');
                    this.close();
                } else {
                    Phlexible.failure(data.msg);
                }
            },
            scope: this
        });
    }
});
