Ext.provide('Phlexible.accesscontrol.RightsGrid');

Ext.require('Phlexible.accesscontrol.model.AccessControlEntry');
Ext.require('Ext.ux.IconCombo');
Ext.require('Ext.ux.grid.RowActions');

Phlexible.accesscontrol.RightsGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    title: Phlexible.accesscontrol.Strings.access,
    strings: Phlexible.accesscontrol.Strings,
    border: false,
    autoExpandColumn: 1,
    loadMask: true,
    stripeRows: true,
    viewConfig: {
        emptyText: Phlexible.accesscontrol.Strings.no_subjects
    },

    contentClass: null,
    languageEnabled: false,
    deletedSubjects: [],

    getDefaultUrls: function () {
        return {
            users: Phlexible.Router.generate('accesscontrol_users'),
            groups: Phlexible.Router.generate('accesscontrol_groups'),
            rights: Phlexible.Router.generate('accesscontrol_rights'),
            save: Phlexible.Router.generate('accesscontrol_save')
        };
    },

    initComponent: function () {
        if (this.strings) {
            this.strings = Ext.apply(Phlexible.accesscontrol.Strings, this.strings);
        } else {
            this.strings = Phlexible.accesscontrol.Strings;
        }

        Ext.applyIf(this.urls, this.getDefaultUrls());

        if (!this.urls.users || !this.urls.groups || !this.urls.subjects || !this.urls.rights || !this.urls.add || !this.urls.save) {
            throw 'RightsGrid URL config incomplete.';
        }

        if (!this.contentClass) {
            throw 'RightsGrid contentClass config incomplete.';
        }

        Ext.Ajax.request({
            url: this.urls.rights,
            params: {
                contentClass: this.contentClass
            },
            success: function (response) {
                var fields = [
                    {
                        header: this.strings.id,
                        dataIndex: 'objectId',
                        width: 100,
                        hidden: true,
                        sortable: true
                    },
                    {
                        header: this.strings.subject,
                        dataIndex: 'label',
                        width: 100,
                        sortable: true,
                        renderer: function (v, md, r) {
                            return Phlexible.inlineIcon('p-accesscontrol-' + r.data.type + '-icon') + ' ' + v;
                        }
                    }
                ];

                if (this.languageEnabled) {
                    var languageData = this.getLanguageData();

                    fields.push({
                        header: this.strings.language,
                        dataIndex: 'language',
                        width: 100,
                        sortable: true,
                        renderer: function (v, md, r) {
                            var suffix = '';
                            if (r.data['new']) {
                                suffix += ' ' + Phlexible.inlineIcon('p-accesscontrol-new-icon');
                            }

                            if (!v || v == '_all_') {
                                return Phlexible.inlineIcon('p-accesscontrol-all-icon') + ' ' + this.strings.all + suffix;
                            }

                            Ext.each(Phlexible.Config.get('set.language.frontend'), function (item) {
                                if (item[0] === v) {
                                    v = Phlexible.inlineIcon(item[2]) + ' ' + item[1] + suffix;
                                    return false;
                                }
                            }, this);

                            return v;
                        }.createDelegate(this),
                        editor: new Ext.ux.IconCombo({
                            store: new Ext.data.SimpleStore({
                                fields: ['key', 'title', 'iconCls'],
                                data: languageData
                            }),
                            valueField: 'key',
                            displayField: 'title',
                            iconClsField: 'iconCls',
                            editable: false,
                            emptyText: '_select',
                            selectOnFocus: true,
                            mode: 'local',
                            typeAhead: true,
                            triggerAction: 'all',
                            listeners: {
                                beforeselect: function (combo, comboRecord, index) {
                                    var cancel = false;
                                    var subjectRecord = this.selModel.getSelected();
                                    this.store.each(function (record) {
                                        if (subjectRecord.id !== record.id &&
                                            subjectRecord.data.objectType == record.data.objectType &&
                                            subjectRecord.data.objectId == record.data.objectId) {
                                            if ((index < 1 && record.data.language != '_all_') ||
                                                (index >= 1 && (
                                                    record.data.language == '_all_' ||
                                                        record.data.language == comboRecord.data.key
                                                    )
                                                    )) {
                                                cancel = true;
                                                return false;
                                            }
                                        }
                                    });
                                    if (cancel) return false;
                                },
                                scope: this
                            }
                        })
                    });
                }

                var data = Ext.decode(response.responseText);
                var plugins = [];

                Ext.each(data, function(permission) {
                    var name = permission.name;
                    var iconCls = permission.iconCls || 'p-accesscontrol-right-icon';

                    fields.push({
                        header: Phlexible.inlineIcon(iconCls, {
                            qtip: name
                        }),
                        dataIndex: 'rights',
                        arrayIndex: name,
                        width: 40,
                        renderer: function (v, md, r, rowIndex, colIndex, store, key) {
                            var s = '';
                            v[key]['status'] = parseInt(v[key]['status'], 10);
                            switch (v[key]['status']) {
                                case 0:
                                    s = Phlexible.inlineIcon('p-accesscontrol-stopped-icon');
                                    break;

                                case 1:
                                    s = Phlexible.inlineIcon('p-accesscontrol-single_right-icon');
                                    break;

                                case 2:
                                    s = Phlexible.inlineIcon('p-accesscontrol-checked-icon');
                                    break;

                                case 3:
                                    s = Phlexible.inlineIcon('p-accesscontrol-checked_inherit-icon');
                                    break;

                                case 4:
                                    s = Phlexible.inlineIcon('p-accesscontrol-unchecked_inherit-icon');
                                    break;

                                default:
                                    s = Phlexible.inlineIcon('p-accesscontrol-unchecked-icon');
                                    break;
                            }
                            return s;
                        }.createDelegate(this, [permission.name], true)
                    });
                });

                fields.push(this.actions);

                var cm = new Ext.grid.ColumnModel(fields);

                this.reconfigure(this.store, cm);
            },
            scope: this
        });

        /*
         this.expander = new Ext.grid.RowExpander({
         dataIndex: 'rights',
         tpl: new Ext.XTemplate(
         '{[for(var xyz in rights) {}]}',
         '<tpl for="rights">',
         '<p>x {#} {.}</p>',
         '{[Phlexible.console.log(values)]}',
         '</tpl>'
         )
         });
         */

        this.actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            autoWidth: false,
            width: 70,
            actions: [
                {
                    showIndex: 'setHere',
                    iconCls: 'p-accesscontrol-delete-icon',
                    tooltip: this.strings['delete'],
                    callback: this.deleteAction
                },
                {
                    showIndex: 'inherited',
                    iconCls: 'p-accesscontrol-link-icon',
                    tooltip: this.strings.link,
                    callback: this.linkAction
                },
                {
                    showIndex: 'restore',
                    iconCls: 'p-accesscontrol-restore-icon',
                    tooltip: this.strings.restore,
                    callback: this.restoreAction
                }
            ]
        });

        this.store = new Ext.data.JsonStore({
            url: this.urls.subjects,
            root: 'subjects',
            fields: Phlexible.accesscontrol.model.AccessControlEntry,
            baseParams: {
                contentClass: this.contentClass,
                contentId: null
            }
        });

        this.columns = [
            //this.expander,
            {
                header: this.strings.id,
                dataIndex: 'objectId',
                width: 30,
                hidden: true,
                sortable: true
            },
            {
                header: this.strings.subject,
                dataIndex: 'label',
                sortable: true,
                renderer: function (v, md, r) {
                    return Phlexible.inlineIcon('p-accesscontrol-' + r.data.type + '-icon') + ' ' + v;
                }
            }/*,{
             header: this.strings.language,
             dataIndex: 'language',
             width: 50,
             sortable: true,
             renderer: function(v) {
             return Phlexible.inlineIcon('p-gui-de-icon');
             }
             }*/
        ];

        this.selModel = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.plugins = [
            //this.expander,
            this.actions
        ];

        this.tbar = [
            {
                text: this.strings.save,
                iconCls: 'p-accesscontrol-save-icon',
                handler: this.onSave,
                scope: this
            },
            '-',
            {
                xtype: 'combo',
                listWidth: 300,
                store: new Ext.data.JsonStore({
                    url: this.urls.users,
                    root: 'data',
                    id: 'objectId',
                    totalProperty: 'total',
                    fields: [
                        'type',
                        'objectType',
                        'objectId',
                        'label'
                    ],
                    autoLoad: false
                }),
                pageSize: 20,
                displayField: 'label',
                valueField: 'objectId',
                emptyText: this.strings.users,
                selectOnFocus: true,
                mode: 'remote',
                typeAhead: true,
                triggerAction: 'all'
            },
            {
                text: this.strings.add,
                iconCls: 'p-accesscontrol-add-icon',
                handler: function () {
                    var usersCombo = this.getTopToolbar().items.items[2];

                    this.onAdd(usersCombo);
                },
                scope: this
            },
            '-',
            {
                xtype: 'combo',
                listWidth: 300,
                store: new Ext.data.JsonStore({
                    url: this.urls.groups,
                    root: 'data',
                    id: 'objectId',
                    fields: [
                        'type',
                        'objectType',
                        'objectId',
                        'label'
                    ],
                    autoLoad: false
                }),
                displayField: 'label',
                valueField: 'objectId',
                emptyText: this.strings.groups,
                selectOnFocus: true,
                mode: 'remote',
                typeAhead: true,
                triggerAction: 'all'
            },
            {
                text: this.strings.add,
                iconCls: 'p-accesscontrol-add-icon',
                handler: function () {
                    var groupsCombo = this.getTopToolbar().items.items[5];

                    this.onAdd(groupsCombo);
                },
                scope: this
            },
            '-',
            {
                text: this.strings.reload,
                iconCls: 'p-accesscontrol-reload-icon',
                handler: function () {
                    this.store.reload();
                },
                scope: this
            }
        ];

        this.bbar = [
            {
                xtype: 'tbtext',
                text: this.strings.legend
            },
            '-',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-unchecked-icon') + ' ' + this.strings.not_set
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-checked-icon') + ' ' + this.strings.set_here
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-checked_inherit-icon') + ' ' + this.strings.set_above
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-unchecked_inherit-icon') + ' ' + this.strings.stopped_above
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-stopped-icon') + ' ' + this.strings.stopped_here
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-single_right-icon') + ' ' + this.strings.stopped_below
            }
        ];

        this.on({
            beforeedit: function (e) {
                if (!e.record.data['new']) {
                    return false;
                }
            },
            cellclick: function (grid, rowIndex, colIndex) {
                if (colIndex < (this.languageEnabled ? 3 : 2) || colIndex == grid.getColumnModel().getColumnCount() - 1) return;

                var record = grid.getStore().getAt(rowIndex);
                var cm = grid.getColumnModel();
                var col = cm.getColumnById(cm.getColumnId(colIndex));
                var right = col.arrayIndex;
                var rights = record.data.rights;
                var original = record.data.original;
                var above = record.data.above;
                var status = parseInt(rights[right]['status'], 10);
                var originalStatus = parseInt(original[right]['status'], 10);
                var aboveStatus = parseInt(above[right]['status'], 10);

                //Phlexible.console.log('right: ' + right);
                //Phlexible.console.log('status: ' + status);
                //Phlexible.console.log('originalStatus: ' + originalStatus);
                //Phlexible.console.log('aboveStatus: ' + aboveStatus);

                switch (status) {
                    case 2:
                    case 3:
                        status = 1;
                        break;

                    case 1:
                        if (aboveStatus === 2 || aboveStatus === 3) {
                            status = 0;
                        }
                        else if (aboveStatus === 4) {
                            status = 4;
                        }
                        else if (originalStatus === 4) {
                            status = 4;
                        }
                        else {
                            status = -1;
                        }
                        break;

                    default:
                        if (aboveStatus === 2 || aboveStatus === 3) {
                            status = aboveStatus;
                        }
                        else if (originalStatus === 2 || originalStatus === 3) {
                            status = originalStatus;
                        }
                        else {
                            status = 2;
                        }
                        break;
                }

                rights[right]['status'] = status;

                record.beginEdit();
                //r.set('rights', rights);
                var dummy = record.data.label;
                record.set('label', 'xxx');
                record.set('label', dummy);
                record.set('restore', 1);
                record.set('inherited', !record.get('setHere'));
                record.endEdit();
            },
            scope: this
        });

        Phlexible.accesscontrol.RightsGrid.superclass.initComponent.call(this);
    },

    doLoad: function (contentType, contentId) {
        if (this.contentType !== contentType || this.contentId !== contentId) {
            this.contentType = contentType;
            this.contentId = contentId;

            this.store.removeAll();

            //this.selModel.clearSelections();

            this.store.baseParams = {
                contentClass: this.contentClass,
                contentId: contentId
            };
            this.store.load();
        }

        this.enable();
    },

    getLanguageData: function () {
        return [];
    },

    deleteAction: function (grid, record, action, row, col) {
        grid.store.remove(record);

        grid.deletedSubjects.push({
            contentType: grid.contentType,
            contentId: grid.contentId,
            objectType: record.data.objectType,
            objectId: record.data.objectId,
            language: record.data.language
        });
    },

    linkAction: function (grid, record, action, row, col) {
        var empty = true;
        for (var i in record.data.above) {
            if (record.data.above[i].status != -1) {
                empty = false;
                break;
            }
        }

        if (empty) {
            grid.store.remove(record);
        }
        else {
            var above = record.get('above');
            var rights = record.get('rights');

            rights = Phlexible.clone(above);

            record.beginEdit();
            record.set('rights', false);
            record.set('rights', rights);
            record.set('restore', 1);
            record.set('inherited', 0);
            var dummy = record.data.label;
            record.set('label', 'xxx');
            record.set('label', dummy);
            record.endEdit();
            //record.commit();
        }

        /*
         grid.deletedSubjects.push({
         content_type: grid.content_type,
         content_id: grid.content_id,
         object_type: record.data.object_type,
         object_id: record.data.object_id,
         language: record.data.language
         });
         */
    },

    restoreAction: function (grid, record, action, row, col) {
        var original = record.get('original');
        var rights = record.get('rights');

        rights = Phlexible.clone(original);

        record.beginEdit();
        record.set('rights', false);
        record.set('rights', rights);
        record.set('restore', 0);
        record.set('inherited', !record.get('setHere'));
        var dummy = record.data.label;
        record.set('label', 'xxx');
        record.set('label', dummy);
        record.endEdit();
        record.commit();
    },

    onAdd: function (combo) {
        var key = combo.getValue();
        var r = combo.getStore().getById(key);
        combo.clearValue();
        if (!r) {
            return;
        }
        var language = '_all_';

        Ext.Ajax.request({
            url: this.urls.add,
            params: {
                contentClass: this.contentClass,
                contentId: this.contentId,
                objectType: r.data.objectType,
                objectId: r.data.objectId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                var newRecord = new Ext.data.Record({
                    type: data.type,
                    objectType: data.objectType,
                    objectId: data.objectId,
                    label: data.label,
                    rights: data.rights,
                    original: data.original,
                    above: data.above,
                    language: language,
                    inherited: 0,
                    setHere: 1,
                    restore: 0,
                    'new': 1
                });

                this.store.insert(0, newRecord);
            },
            scope: this
        });
    },

    onSave: function () {
        var mr = this.store.getModifiedRecords();

        var modified = [];
        var deleted = this.deletedSubjects;

        for (var i = 0; i < mr.length; i++) {
            var skip = false;
            for (var j = 0; j < deleted.length; j++) {
                if (deleted[j].objectType == mr[i].data.objectType &&
                    deleted[j].objectId == mr[i].data.objectId) {
                    skip = true;
                    break;
                }
            }

            if (skip) {
                continue;
            }

            modified.push({
                objectType: mr[i].data.objectType,
                objectId: mr[i].data.objectId,
                contentType: this.contentType,
                contentId: this.contentId,
                language: mr[i].data.language,
                rights: mr[i].data.rights
            });
        }

        Phlexible.console.log(modified);
        Phlexible.console.log(deleted);

        Ext.Ajax.request({
            url: this.urls.save,
            params: {
                contentClass: this.contentClass,
                modified: Ext.encode(modified),
                deleted: Ext.encode(deleted)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.store.reload();

                    this.store.commitChanges();
                    this.deletedSubjects = [];
                }
                else {
                    Ext.MessageBox.alert('Warning', data.msg);
                }
            },
            scope: this
        });
    }
});

Ext.reg('accesscontrol-rightsgrid', Phlexible.accesscontrol.RightsGrid);
