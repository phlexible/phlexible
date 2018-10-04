Ext.provide('Phlexible.accesscontrol.RightsGrid');

Ext.require('Phlexible.accesscontrol.model.AccessControlEntry');
Ext.require('Phlexible.accesscontrol.model.SecurityIdentity');
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

    objectType: null,
    languageEnabled: false,

    getDefaultUrls: function () {
        return {
            users: Phlexible.Router.generate('accesscontrol_users'),
            groups: Phlexible.Router.generate('accesscontrol_groups'),
            permissions: Phlexible.Router.generate('accesscontrol_permissions'),
            save: Phlexible.Router.generate('accesscontrol_save')
        };
    },

    createIconCls: function(permission) {
        return 'p-accesscontrol-right-icon';
    },

    initComponent: function () {
        if (this.strings) {
            this.strings = Ext.apply(Phlexible.accesscontrol.Strings, this.strings);
        } else {
            this.strings = Phlexible.accesscontrol.Strings;
        }

        Ext.applyIf(this.urls, this.getDefaultUrls());

        if (!this.urls.users) {
            throw 'RightsGrid users URL config incomplete.';
        }

        if (!this.urls.groups) {
            throw 'RightsGrid groups URL config incomplete.';
        }

        if (!this.urls.identities) {
            throw 'RightsGrid identities URL config incomplete.';
        }

        if (!this.urls.permissions) {
            throw 'RightsGrid permissions URL config incomplete.';
        }

        if (!this.urls.save) {
            throw 'RightsGrid save URL config incomplete.';
        }

        if (!this.objectType) {
            throw 'RightsGrid objectType config incomplete.';
        }

        if (Phlexible.User.isGranted('ROLE_ACCESS_CONTROL')) {
            Ext.Ajax.request({
                url: this.urls.permissions,
                params: {
                    objectType: this.objectType
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
                            dataIndex: 'securityName',
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

                    Ext.each(data.permissions, function (permission) {
                        var bit = permission.bit,
                            name = permission.name,
                            iconCls = this.createIconCls(permission),
                            test = function (test) {
                                return test !== null && (bit & test) === bit;
                            };

                        fields.push({
                            header: Phlexible.inlineIcon(iconCls, {qtip: name}),
                            dataIndex: 'mask',
                            permission: permission,
                            width: 40,
                            renderer: function (v, md, r) {
                                if (test(r.data.mask) && test(r.data.noInheritMask)) {
                                    // set here, stopped below
                                    return Phlexible.inlineIcon('p-accesscontrol-single_right-icon');
                                } else if (test(r.data.parentMask) && test(r.data.stopMask)) {
                                    // set above, stopped here
                                    return Phlexible.inlineIcon('p-accesscontrol-stopped-icon');
                                } else if (test(r.data.parentMask) && test(r.data.noInheritMask)) {
                                    // set above, stopped below
                                    return Phlexible.inlineIcon('p-accesscontrol-checked_inherit_stopped-icon');
                                } else if (!test(r.data.mask) && test(r.data.parentMask)) {
                                    // set above
                                    return Phlexible.inlineIcon('p-accesscontrol-checked_inherit-icon');
                                } else if (test(r.data.mask)) {
                                    // set here
                                    return Phlexible.inlineIcon('p-accesscontrol-checked-icon');
                                } else if (0) {
                                    // stopped above
                                    return Phlexible.inlineIcon('p-accesscontrol-unchecked_inherit-icon');
                                } else {
                                    // -
                                    return Phlexible.inlineIcon('p-accesscontrol-unchecked-icon');
                                }
                            }
                        });
                    }, this);

                    fields.push(this.actions);

                    var cm = new Ext.grid.ColumnModel(fields);

                    this.reconfigure(this.store, cm);
                },
                scope: this
            });
        }

        this.actions = new Ext.ux.grid.RowActions({
            header: this.strings.actions,
            autoWidth: false,
            width: 70,
            actions: [
                {
                    hideIndex: "values.parentMask!==null",
                    iconCls: 'p-accesscontrol-delete-icon',
                    tooltip: this.strings['delete'],
                    callback: this.deleteAction
                },
                {
                    hideIndex: "values.parentMask===null||(values.mask===null&&values.stopMask===null&&values.noInheritMask===null)",
                    iconCls: 'p-accesscontrol-link-icon',
                    tooltip: this.strings.link,
                    callback: this.linkAction
                }
            ]
        });

        this.store = new Ext.data.JsonStore({
            url: this.urls.identities,
            root: 'identities',
            fields: Phlexible.accesscontrol.model.AccessControlEntry,
            baseParams: {
                objectType: this.objectType,
                objectId: null
            }
        });

        this.columns = [
            {
                header: this.strings.id,
                dataIndex: 'objectId',
                width: 30,
                hidden: true,
                sortable: true
            },
            {
                header: this.strings.subject,
                dataIndex: 'securityName',
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
                    id: 'securityId',
                    totalProperty: 'total',
                    fields: Phlexible.accesscontrol.model.SecurityIdentity,
                    autoLoad: false
                }),
                pageSize: 20,
                displayField: 'securityName',
                valueField: 'securityId',
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
                    id: 'securityId',
                    fields: Phlexible.accesscontrol.model.SecurityIdentity,
                    autoLoad: false
                }),
                displayField: 'securityName',
                valueField: 'securityId',
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
                text: Phlexible.inlineIcon('p-accesscontrol-checked_inherit-icon') + ' ' + this.strings.set_above
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-checked-icon') + ' ' + this.strings.set_here
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-checked_inherit_stopped-icon') + ' ' + this.strings.set_above_stopped_below
            },
            //' ',
            //{
            //    xtype: 'tbtext',
            //    text: Phlexible.inlineIcon('p-accesscontrol-unchecked_inherit-icon') + ' ' + this.strings.stopped_above
            //},
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-stopped-icon') + ' ' + this.strings.stopped_here
            },
            ' ',
            {
                xtype: 'tbtext',
                text: Phlexible.inlineIcon('p-accesscontrol-single_right-icon') + ' ' + this.strings.set_here_stopped_below
            }
        ];

        this.on({
            beforeedit: function (e) {
                if (!e.record.data['new']) {
                    return false;
                }
            },
            cellclick: function (grid, rowIndex, colIndex) {
                if (colIndex < (this.languageEnabled ? 3 : 2) || colIndex == grid.getColumnModel().getColumnCount() - 1) {
                    return;
                }

                var record = grid.getStore().getAt(rowIndex),
                    cm = grid.getColumnModel(),
                    col = cm.getColumnById(cm.getColumnId(colIndex)),
                    permission = col.permission,
                    bit = permission.bit,
                    test = function(field) {
                        var v = record.get(field); //record.isModified(field) ? record.modified[field] : record.get(field);
                        return v !== null && (bit & v) === bit;
                    },
                    set = function(field) {
                        record.set(field, record.get(field) | bit);
                        Phlexible.console.info('set', field, record.get(field));
                    },
                    unset = function(field) {
                        record.set(field, record.get(field) & ~bit);
                        Phlexible.console.info('unset', field, record.get(field));
                    };

                if (test("parentMask")) {
                    if (!test("stopMask") && !test("noInheritMask")) {
                        // set to stop here
                        set("stopMask");
                        unset("noInheritMask");
                    } else  if (test("stopMask") && !test("noInheritMask")) {
                        // set to no inherit
                        unset("stopMask");
                        set("noInheritMask");
                    } else /*if (!test("mask"))*/ {
                        // set to set here
                        unset("stopMask");
                        unset("noInheritMask");
                    }
                } else {
                    if (test("mask") && !test("noInheritMask")) {
                        // set to set here, stopped below
                        set("mask");
                        set("noInheritMask");
                    } else  if (test("mask") && test("noInheritMask")) {
                        // unset
                        unset("mask");
                        unset("noInheritMask");
                    } else /*if (!test("mask"))*/ {
                        // set to set here
                        set("mask");
                        unset("noInheritMask");
                    }
                }

                if (record.get('mask') === 0) {
                    record.set('mask', null);
                }
                if (record.get('stopMask') === 0) {
                    record.set('stopMask', null);
                }
                if (record.get('noInheritMask') === 0) {
                    record.set('noInheritMask', null);
                }

                function dec2bin(dec) {
                    if (!dec) dec = 0;
                    return (dec >>> 0).toString(2);
                }
                Phlexible.console.info(dec2bin(record.get("mask")), dec2bin(record.get("stopMask")), dec2bin(record.get("noInheritMask")));
            },
            scope: this
        });

        Phlexible.accesscontrol.RightsGrid.superclass.initComponent.call(this);
    },

    doLoad: function (objectType, objectId) {
        if (this.objectType !== objectType || this.objectId !== objectId) {
            this.objectType = objectType;
            this.objectId = objectId;

            this.store.removeAll();

            //this.selModel.clearSelections();

            this.store.baseParams = {
                objectType: this.objectType,
                objectId: objectId
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
    },

    linkAction: function (grid, record, action, row, col) {
        record.beginEdit();
        record.set('mask', null);
        record.set('stopMask', null);
        record.set('noInheritMask', null);
        record.endEdit();
        //record.commit();
    },

    onAdd: function (combo) {
        var securityIdentity = combo.getStore().getById(combo.getValue()),
            entry = new Phlexible.accesscontrol.model.AccessControlEntry({
                id: null,
                objectType: this.objectType,
                objectId: this.objectId,
                mask: null,
                stopMask: null,
                noInheritMask: null,
                parentMask: null,
                parentStopMask: null,
                parentNoInheritMask: null,
                objectLanguage: '',
                securityType: securityIdentity.get('securityType'),
                securityId: securityIdentity.get('securityId'),
                securityName: securityIdentity.get('securityName')
            });

        this.store.insert(0, entry);
    },

    onSave: function () {
        var identities = [];

        this.store.each(function(r) {
            identities.push(r.data);
        });

        Ext.Ajax.request({
            url: this.urls.save,
            params: {
                objectType: this.objectType,
                objectId: this.objectId,
                identities: Ext.encode(identities)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.store.reload();

                    this.store.commitChanges();
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
