Phlexible.messages.filter.CriteriaForm = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.messages.Strings.criteria,
    strings: Phlexible.messages.Strings,
    cls: 'p-messages-filter-form',
    border: true,
    layout: 'border',

    componentIndex: 1,
    groupIndex: 1,
    currentGroup: 0,
    ready: true,

    initComponent: function () {
        //The panel which contains the criteria list

        this.criteriaPanel = new Ext.Panel({
            region: 'center',
            border: false,
            autoScroll: true
        });

        // The button for the adding of new criterias to the list,will be initialised in the method setAddBtnPosition()
        //###########
        this.items = [
            {
                xtype: 'panel',
                region: 'north',
                height: 30,
                layout: 'form',
                border: false,
                autoScroll: false,
                bodyStyle: 'padding: 5px 10px;',
                items: [
                    {
                        name: 'title',
                        xtype: 'textfield',
                        width: 200,
                        fieldLabel: this.strings.title,
                        xlabelStyle: 'padding-left: 10px;',
                        allowBlank: false
                    }
                ]
            },
            this.criteriaPanel
        ];

        this.tbar = [
            {
                text: this.strings.save,
                handler: this.save,
                iconCls: 'p-message-save-icon',
                cls: 'x-btn-text-icon',
                scope: this
            },
            '-',
            {
                text: this.strings.addOrBlock,
                handler: this.addOrBlock,
                iconCls: 'p-message-add-icon',
                cls: 'x-btn-text-icon',
                scope: this
            },
            '->',
            {
                text: this.strings.refresh,
                handler: this.refreshPreview,
                iconCls: 'p-message-refresh-icon',
                cls: 'x-btn-text-icon',
                scope: this
            }
        ];

        //store which contains the criterias

        this.criteriaStore = new Ext.data.JsonStore({
            fields: Phlexible.messages.model.Criterium
        });

        Phlexible.messages.filter.CriteriaForm.superclass.initComponent.call(this);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('messages_filter_filtervalues'),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.bundleList = data.bundles;
                this.roleList = data.roles;
                this.criteriaStore.loadData(data.criteria);
            },
            scope: this
        });
    },

    /**
     * Save method to save modified data
     */
    save: function () {
        if (!this.getForm().isValid()) {
            Ext.MessageBox.alert("Error", "Invalid Form");
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('messages_filter_update', {id: this.record.id}),
            params: {
                title: this.getComponent(0).getComponent(0).getValue(),
                criteria: Ext.encode(this.serializeCriteria())
            },
            success: this.saveSuccess,
            failure: this.saveFailure,
            scope: this
        });
    },

    saveSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            Phlexible.success(data.msg);
            this.fireEvent('reload');
        } else {
            Ext.MessageBox.alert('Failure', data.msg);
        }
    },
    saveFailure: function () {
    },

    /**
     * Method to load data to the Information form and then to load the criteria list
     * @param {Object} record
     */
    loadData: function (record) {
        this.record = record;
        this.enable();
        this.getForm().loadRecord(record);

        this.setTitle(String.format(this.strings.criteria_for, record.data.title));

        // Loads the criteria list
        this.initCriteriaFields(record.data.criteria);
    },

    clear: function () {
        this.getForm().reset();
        this.removeCriteriaFields();
        this.setTitle(this.strings.criteria);
    },

    /**
     * Method to remove ALL criterias
     * Will be called before initalise a new criteria list
     */
    removeCriteriaFields: function () {
        this.criteriaPanel.removeAll();
        return;
        if (this.criteriaPanel.items) {
            while (this.criteriaPanel.items.last()) {
                this.criteriaPanel.remove(this.criteriaPanel.items.last());
            }
        }
    },

    /**
     * Method to initialise the criteria field list.
     * Will be called in the loadData method
     */
    initCriteriaFields: function (criteria) {
        this.removeCriteriaFields();
        this.componentIndex = 1;
        this.groupIndex = 1;
        this.currentGroup = 0;

        //this.addOrBlock();
        if (!criteria || !criteria.length) {
            this.addOrBlock();
            this.addCriteria('', '', '1');
        } else {
            for (var i = 0; i < criteria.length; i++) {
                if (this.currentGroup != criteria[i]['group']) {
                    this.currentGroup = criteria[i]['group'];
                    this.addOrBlock();
                }
                this.addCriteria(criteria[i]['criteria'], criteria[i]['value'], criteria[i]['group']);
                //if(data.criteria[i]['criteria'] == 'orblock')
                //  this.addOrBlock();
                //else
                //this.addCriteria(criteria[i]['criteria'], criteria[i]['value'], this.groupIndex);
            }
            //this.addCriteria('','',this.groupIndex);
        }
        this.refreshPreview();

        this.ready = true;
    },

    /**
     * Method to add a criteria to the list.
     * Will be called after clicking on the add button or during
     * the load of existing criterias (params c and v are corresponding values)
     * @param {Object} c
     * @param {Object} v
     */
    addCriteria: function (c, v, gi) {
        var pMain = new Ext.Panel({
            id: 'critPanelMain' + this.componentIndex,
            componentIndex: this.componentIndex,
            cls: 'panel-filter',
            border: true,
            bodyStyle: 'padding:5px 0px 5px 0px;',
            width: 450,
            layout: 'column',
            items: [
                {
                    width: 165,
                    border: false,
                    items: []
                },
                {
                    width: 215,
                    border: false,
                    items: [
                        {
                            border: false,
                            html: this.strings.select_criterium
                        }
                    ]
                },
                {
                    width: 35,
                    border: false,
                    items: [
                        {
                            id: 'removeCriteriaButton' + this.componentIndex,
                            border: false,
                            items: [
                                {
                                    xtype: 'tbbutton',
                                    //text: this.strings.removeSymbol,
                                    iconCls: 'p-message-delete-icon',
                                    componentIndex: (this.componentIndex + 1),
                                    handler: function (btn) {
                                        this.hideCriteria('criteria', btn.componentIndex);
                                    },
                                    scope: this
                                }
                            ],
                            cls: 'removeCriteriaButton'
                        }
                    ]
                }
            ]
        });

        var c1 = pMain.getComponent(0);
        var c2 = pMain.getComponent(1);
//        var c3 = pMain.getComponent(2);

        var f2 = this.getCritComboBox(this.componentIndex, p);

        // If v and c are strings...
        if (c && c != '[object Object]' && c && v && v != '[object Object]') {
            f2.value = c;
            c1.add(f2);
            this.addCorrespondingField(f2, v, pMain);
        }
        else {
            c1.add(f2);
        }

        if (Ext.get('group' + gi)) {
            var fs = Ext.getCmp('group' + gi);
            fs.add(pMain);
            fs.doLayout();
            this.criteriaPanel.add(fs);
        }

        this.criteriaPanel.doLayout();

        this.componentIndex++;
    },

    /**
     * Method to hide a criteria after clicking the "-" button
     * @param {Object} ci
     */
    hideCriteria: function (element, ci) {
        if (element == 'criteria') {
            ci = ci - 1;

            var critPanelMain = Ext.getCmp('critPanelMain' + ci);

            // Delete the second value to ensure that the hidden citeria will not be saved
            var c = critPanelMain.getComponent(1).getComponent(0);
            if (c && c.isFormField) {
                critPanelMain.getComponent(1).getComponent(0).setValue("");
            }

            critPanelMain.collapse();
            critPanelMain.remove();

            this.criteriaPanel.doLayout();
        }
    },
    hideGroup: function (gi) {
        var i = 1;
        while (Ext.getCmp('criteriabox' + i)) {
            if (Ext.get('value' + i) && this.getOwnerGroupId(Ext.get('value' + i)) == gi) {
                Ext.get('value' + i).dom.value = '';
            }
            i++;
        }

        if (Ext.getCmp('group' + gi)) {
            Ext.getCmp('group' + gi).hide();
        }
    },
    /**
     * Returns a ComboBox containing all criterias.
     * Will be used during the initialisation of a criteria panel
     * @param {Object} ci
     * @param {Object} p
     */
    getCritComboBox: function (ci, p) {
        return new Ext.form.ComboBox({
            hiddenName: 'criteria' + ci,
            id: 'criteriabox' + ci,
            onRender: function (ct, position) {
                this.constructor.prototype.onRender.apply(this, arguments);
                this.wrap.addClass('first-filter-field');
            },
            componentIndex: ci,
            emptyText: this.strings.criteria,
            width: 150,
            store: new Ext.data.SimpleStore({
                fields: ['key', 'value'],
                data: [
                    ['subject_like', 'Subject like'],
                    ['subject_not_like', 'Subject not like'],
                    ['body_like', 'Body like'],
                    ['body_not_like', 'Body not like'],
                    ['priority_is', 'Priority is'],
                    ['priority_in', 'Priority in'],
                    ['priority_min', 'Priority min'],
                    ['type_is', 'Type is'],
                    ['type_in', 'Type in'],
                    ['channel_is', 'Channel is'],
                    ['channel_like', 'Channel like'],
                    ['channel_in', 'Channel in'],
                    ['role_is', 'Role is'],
                    ['role_in', 'Role in'],
                    ['min_age', 'Min age'],
                    ['max_age', 'Max age'],
                    ['start_date', 'Start date'],
                    ['end_date', 'End date'],
                    ['date_is', 'Date is'],
                    ['limit', 'Limit'],
                    ['order', 'Order']
                ]
            }),
            listeners: {
                collapse: function (co) {
                    if (co.valueField) {
                        if (co.getValue() != co.tempValue) {
                            this.addCorrespondingField(co, null, co.ownerCt.ownerCt);
                        }
                        co.tempValue = co.getValue();
                    }
                },
                scope: this
            },
            mode: 'local',
            displayField: 'value',
            valueField: 'key',
            editable: false,
            allowBlank: false,
            //selectOnFocus: true,
            triggerAction: 'all'
        });
    },

    /**
     * Adds the corresponding field which will be get from getCorrespondingField,
     * and puts
     * @param {Object} comp
     * @param {Object} content
     * @param {Object} p
     */
    addCorrespondingField: function (comp, content, p) {
        if (p)
            var panel = p;
        else
            var panel = Ext.getCmp('critPanelMain' + comp.componentIndex);

        // if already existing a second field, delete the panel content
        //if (Ext.getCmp('critPanelMain' + comp.componentIndex).getComponent(1)) {
        //panel.getComponent(1).removeAll();
        //var newCb = this.getCritComboBox(comp.componentIndex, panel)
        //newCb.value = comp.value;
        //panel.getComponent(1).add(newCb);
        //}

        var nf = this.getCorrespondingField(comp.value);
        // if it is a combobox, the element needs a hiddenName


        // check if the element needs a hiddenField
        var xtype = nf.getXType();

        if (xtype == 'combo' || xtype == 'lovcombo')
            nf.hiddenName = 'value' + comp.componentIndex;
        else {
            nf.id = 'value' + comp.componentIndex;
            nf.name = 'value' + comp.componentIndex;
        }

        if (content)
            nf.value = content;

        panel.getComponent(1).removeAll();
        panel.getComponent(1).add(nf);
        panel.doLayout();
    },

    /**
     * Returns the corresponding field to the criteria
     * @param {Object} which
     */
    getCorrespondingField: function (which) {
        var field;
        switch (which) {
            case 'priority_is':
                field = new Ext.ux.IconCombo({
                    width: 200,
                    store: new Ext.data.SimpleStore({
                        data: [
                            ['3', this.strings.priority_urgent, 'p-message-priority_urgent-icon'],
                            ['2', this.strings.priority_high, 'p-message-priority_high-icon'],
                            ['1', this.strings.priority_normal, 'p-message-priority_normal-icon'],
                            ['0', this.strings.priority_low, 'p-message-priority_low-icon']
                        ],
                        autoLoad: true,
                        fields: ['id', 'name', 'icon']
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'icon',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'priority_in':
                field = new Ext.ux.form.LovCombo({
                    width: 200,
                    hiddenField: 'value1',
                    hideOnSelect: false,
                    store: new Ext.data.SimpleStore({
                        data: [
                            ['3', this.strings.priority_urgent, 'p-message-priority_urgent-icon'],
                            ['2', this.strings.priority_high, 'p-message-priority_high-icon'],
                            ['1', this.strings.priority_normal, 'p-message-priority_normal-icon'],
                            ['0', this.strings.priority_low, 'p-message-priority_low-icon']
                        ],
                        autoLoad: true,
                        fields: ['id', 'name', 'icon']
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'priority_min':
                field = new Ext.ux.IconCombo({
                    width: 200,
                    store: new Ext.data.SimpleStore({
                        data: [
                            ['3', this.strings.priority_urgent, 'p-message-priority_urgent-icon'],
                            ['2', this.strings.priority_high, 'p-message-priority_high-icon'],
                            ['1', this.strings.priority_normal, 'p-message-priority_normal-icon'],
                            ['0', this.strings.priority_low, 'p-message-priority_low-icon']
                        ],
                        autoLoad: true,
                        fields: ['id', 'name', 'icon']
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'icon',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'type_is':
                field = new Ext.ux.IconCombo({
                    width: 200,
                    store: new Ext.data.SimpleStore({
                        data: [
                            ['0', this.strings.type_info, 'p-message-type_info-icon'],
                            ['1', this.strings.type_error, 'p-message-type_error-icon']
                        ],
                        autoLoad: true,
                        fields: ['id', 'name', 'icon']
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    iconClsField: 'icon',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'type_in':
                field = new Ext.ux.form.LovCombo({
                    width: 200,
                    hideOnSelect: false,
                    store: new Ext.data.SimpleStore({
                        data: [
                            ['0', this.strings.type_info, 'p-message-type_info-icon'],
                            ['1', this.strings.type_error, 'p-message-type_error-icon']
                        ],
                        autoLoad: true,
                        fields: ['id', 'name', 'icon']
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'channel_is':
                field = new Ext.form.ComboBox({
                    width: 200,
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'name'],
                        data: this.bundleList,
                        sortInfo: {
                            field: 'name',
                            direction: 'ASC'
                        }
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'channel_in':
                field = new Ext.ux.form.LovCombo({
                    width: 200,
                    hideOnSelect: false,
                    store: store = new Ext.data.SimpleStore({
                        fields: ['id', 'name'],
                        data: this.bundleList
                    }),
                    mode: 'local',
                    allowBlank: false,
                    valueField: 'id',
                    displayField: 'name',
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true,
                    forceSelection: false
                });
                break;

            case 'role_is':
                field = new Ext.form.ComboBox({
                    width: 200,
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'name'],
                        data: this.rolesList,
                        sortInfo: {
                            field: 'name',
                            direction: 'ASC'
                        }
                    }),
                    mode: 'local',
                    valueField: 'id',
                    displayField: 'name',
                    allowBlank: false,
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true
                });
                break;

            case 'role_in':
                field = new Ext.ux.form.LovCombo({
                    width: 200,
                    hideOnSelect: false,
                    store: new Ext.data.SimpleStore({
                        fields: ['id', 'name'],
                        data: this.rolesList
                    }),
                    mode: 'local',
                    allowBlank: false,
                    valueField: 'id',
                    displayField: 'name',
                    triggerAction: 'all',
                    editable: false,
                    selectOnFocus: true,
                    forceSelection: false
                });
                break;

            case 'min_age':
            case 'max_age':
                field = new Ext.form.TextField({
                    emptyText: this.strings.number_of_days,
                    width: 200
                });
                break;

            case 'start_date':
            case 'end_date':
                return new Ext.form.DateField({
                    format: 'Y-m-d',
                    editable: false,
                    width: 200
                });
                break;

            case 'date_is':
                field = new Ext.form.DateField({
                    format: 'Y-m-d 00:00:00',
                    editable: false,
                    width: 200
                });
                break;

            case 'subject_like':
            case 'body_like':
            case 'channel_like':
            default:
                field = new Ext.form.TextField({
                    width: 200
                });
                break;
        }

        return field;
    },

    refreshPreview: function () {
        this.fireEvent('refreshPreview', this.serializeCriteria(), this.record.data.title);
    },

    serializeCriteria: function (all) {
        var criteria = [];
        Ext.each(this.getComponent(1).items.items, function (item1) {
            var group = [];
            Ext.each(item1.items.items, function (item2) {
                var f1 = item2.getComponent(0).getComponent(0);
                var f2 = item2.getComponent(1).getComponent(0);
                if (!f2.isFormField || !f2.getValue()) return;
                var row = {
                    key: f1.getValue(),
                    value: f2.getValue()
                };
                group.push(row);
            }, this);
            criteria.push(group);
        }, this);

        return criteria;
    },

    getOwnerGroupId: function (cmp) {
        var fs = cmp.findParentNode('fieldset');
        var ecmp = Ext.getCmp(fs.id);

        return ecmp.groupIndex;
    },

    addOrBlock: function () {
        var fs = new Ext.form.FieldSet({
            id: 'group' + this.groupIndex,
            cls: 'or-block',
            groupIndex: this.groupIndex,
            title: this.strings.group + ' ' + this.groupIndex,
            autoHeight: true,
            scope: this,
            listeners: {
                render: function (c, groupIndex) {
                    var button_add = c.el.insertFirst({
                        tag: 'div',
                        cls: 'x-tool x-tool-plus m-messages-plus'
                    });
                    button_add.on('click', function (groupIndex) {
                        this.addCriteria('', '', groupIndex);
                    }.createDelegate(this, [groupIndex], false), this);

                    var button_close = c.el.insertFirst({
                        tag: 'div',
                        cls: 'x-tool x-tool-close m-messages-close'
                    });
                    button_close.on('click', function (groupIndex) {
                        this.hideGroup(groupIndex);
                    }.createDelegate(this, [groupIndex], false), this);
                }.createDelegate(this, [this.groupIndex], true),
                scope: this
            }
        });

        this.criteriaPanel.add(fs);
        this.criteriaPanel.doLayout();

        fs.el.frame('cccccc', 1, {duration: 0.3});

        this.groupIndex++;

        return fs;
    }
});

Ext.reg('messages-filter-criteriaform', Phlexible.messages.filter.CriteriaForm);