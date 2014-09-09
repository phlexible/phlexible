Phlexible.elementfinder.CatchDataPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.teasers.Strings,
    title: Phlexible.teasers.Strings['catch'],
    iconCls: 'p-element-tab_data-icon',
    cls: 'p-elements-catch-panel',
    autoScroll: true,
    //autoWidth: false,
    bodyStyle: 'padding-top: 5px',
    //hideMode: 'offsets',

    reloadLinkFieldStore: false,
    reSetComboCounter: 0,

    isLinkInitialized: false,
    isElementTypeInitialized: false,

    disabled: true,

    initComponent: function () {
        this.addEvents(
            'save'
        );

        this.items = [
            {
                xtype: 'form',
                labelWidth: 150,
                border: false,
                items: [{
                    xtype: 'fieldset',
                    title: this.strings.catch,
                    autoHeight: true,
                    anchor: '-15',
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: Phlexible.elements.Strings.title,
                        name: 'title',
                        width: 300
                    }]
                },{
                    xtype: 'fieldset',
                    title: Phlexible.elements.Strings.elements,
                    autoHeight: true,
                    anchor: '-15',
                    items: [{
                        xtype: 'hidden',
                        name: 'for_tree_id_hidden'
                    },
                        new Phlexible.elements.EidSelector({
                            name: 'for_tree_id',
                            fieldLabel: Phlexible.elements.Strings.tid,
                            labelSeparator: '',
                            element: {
                                siteroot_id: this.siteroot_id
                            },
                            recursive: true,
                            width: 300,
                            listWidth: 300,
                            treeWidth: 292,
                            listeners: {
                                change: function (field) {
                                    field.getForm().setValues({
                                        for_tree_id_hidden: field.getValue()
                                    });
                                },
                                load: function () {
                                    if (this.catchConfig) {
                                        this.getForm().setValues({'for_tree_id': this.catchConfig.for_tree_id});
                                        this.isLinkInitialized = true;
                                        this.enableAfterInitialisation();
                                    }
                                },
                                scope: this
                            }
                        }),
                        {
                            xtype: 'multiselect',
                            fieldLabel: Phlexible.elements.Strings.elementtype,
                            name: 'catch_element_type_id',
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_elementtypes', {id: this.siteroot_id}),
                                root: 'elementtypes',
                                fields: ['id', 'title', 'icon'],
                                autoLoad: true,
                                listeners: {
                                    load: function () {
                                        if (this.catchConfig) {
                                            this.getForm().setValues({'catch_element_type_id': this.catchConfig.catch_element_type_id});
                                            this.isElementTypeInitialized = true;
                                            this.enableAfterInitialisation();
                                        }
                                    }.createDelegate(this)
                                }
                            }),
                            width: 300,
                            emptyText: Phlexible.elements.Strings.select_elementtype,
                            valueField: 'id',
                            displayField: 'title',
                            tpl: '<tpl for="."><div class="ux-mselect-item' + ((Ext.isIE || Ext.isIE7) ? '" unselectable=on' : ' x-unselectable"') + '><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                            //                    width:280,
                            height: 165,
                            allowBlank: false,
                            listeners: {
                                click: function (field, event) {
                                    var sortField = this.getForm().findField('catch_sort_field');
                                    sortField.clearValue();
                                },
                                scope: this
                            }
                        },
                        {
                            xtype: 'checkbox',
                            fieldLabel: Phlexible.elements.Strings.elements,
                            boxLabel: Phlexible.elements.Strings.in_navigation,
                            name: 'catch_in_navigation'
                        },
                        {
                            hidden: true
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: this.strings.catch_max_depth,
                            name: 'catch_max_depth',
                            width: 30,
                            allowBlank: true
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: this.strings.catch_max_elements,
                            name: 'catch_max_elements',
                            width: 30,
                            allowBlank: true
                        },
                        {
                            xtype: 'checkbox',
                            fieldLabel: this.strings.catch_rotation,
                            boxLabel: Phlexible.elements.Strings.activate,
                            name: 'catch_rotation',
                            listeners: {
                                check: function (field, checked) {
                                    var form = this.getForm();
                                    form.findField('catch_pool_size').setDisabled(!checked);
                                },
                                scope: this
                            }
                        },
                        {
                            xtype: 'numberfield',
                            fieldLabel: this.strings.catch_pool_size,
                            name: 'catch_pool_size',
                            width: 30,
                            allowBlank: true,
                            disabled: true
                        }]
                },
                    {
                        xtype: 'fieldset',
                        title: Phlexible.elements.Strings.sort_mode,
                        autoHeight: true,
                        anchor: '-15',
                        items: [{
                            xtype: 'combo',
                            width: 300,
                            listWidth: 300,
                            fieldLabel: Phlexible.elements.Strings.sort_field,
                            hiddenName: 'catch_sort_field',
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_sortfields'),
                                root: 'data',
                                fields: ['ds_id', 'title', 'icon'],
                                listeners: {
                                    beforeload: function () {
                                        ++this.reSetComboCounter;
                                        //Phlexible.console.log('+catch_sort_field');
                                    },
                                    load: function () {
                                        this.reSetComboValue('catch_sort_field');
                                    },
                                    scope: this
                                }
                            }),
                            //tpl: '<tpl for="."><div class="x-combo-list-item {icon}">{title}</div></tpl>',
                            displayField: 'title',
                            valueField: 'ds_id',
                            mode: 'remote',
                            emptyText: Phlexible.elements.Strings.unsorted,
                            listClass: 'x-combo-list-big',
                            editable: false,
                            triggerAction: 'all',
                            selectOnFocus: true,
                            listeners: {
                                beforequery: function (event) {
                                    var elementTypesField = this.getForm().findField('catch_element_type_id');
                                    event.query = elementTypesField.getValue();
                                },
                                scope: this
                            }
                        },
                            {
                                xtype: 'combo',
                                width: 300,
                                listWidth: 300,
                                fieldLabel: Phlexible.elements.Strings.sort_order,
                                hiddenName: 'catch_sort_order',
                                store: new Ext.data.SimpleStore({
                                    fields: ['title', 'value'],
                                    data: [
                                        [Phlexible.elements.Strings.ascending, 'ASC'],
                                        [Phlexible.elements.Strings.descending, 'DESC']
                                    ]
                                }),
                                value: 'ASC',
                                displayField: 'title',
                                valueField: 'value',
                                mode: 'local',
                                listClass: 'x-combo-list-big',
                                editable: false,
                                triggerAction: 'all',
                                selectOnFocus: true,
                                allowBlank: false
                            }]
                    },
                    {
                        xtype: 'fieldset',
                        title: Phlexible.elements.Strings.filter,
                        autoHeight: true,
                        anchor: '-15',
                        items: [{
                            xtype: 'combo',
                            width: 300,
                            listWidth: 300,
                            fieldLabel: Phlexible.elements.Strings.filter,
                            hiddenName: 'catch_filter',
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_filters'),
                                root: 'filters',
                                fields: ['name', 'class'],
                                listeners: {
                                    beforeload: function () {
                                        ++this.reSetComboCounter;
                                        //Phlexible.console.log('+catch_filter');
                                    },
                                    load: function () {
                                        this.reSetComboValue('catch_filter');
                                    },
                                    scope: this
                                }
                            }),
                            displayField: 'name',
                            valueField: 'class',
                            mode: 'remote',
                            emptyText: Phlexible.teasers.Strings.no_filter,
                            listClass: 'x-combo-list-big',
                            editable: false,
                            triggerAction: 'all',
                            selectOnFocus: true
                        },
                            {
                                xtype: 'combo',
                                width: 300,
                                listWidth: 300,
                                fieldLabel: Phlexible.elements.Strings.meta_filter,
                                hiddenName: 'catch_meta_key_1',
                                store: new Ext.data.JsonStore({
                                    url: Phlexible.Router.generate('elementfinder_catch_metakey'),
                                    root: 'metakeys',
                                    fields: ['key', 'value'],
                                    listeners: {
                                        beforeload: function () {
                                            ++this.reSetComboCounter;
                                            //Phlexible.console.log('+catch_meta_key_1');
                                        },
                                        load: function () {
                                            this.reSetComboValue('catch_meta_key_1');
                                        },
                                        scope: this
                                    }
                                }),
                                listeners: {
                                    select: function (combo, record, index) {
                                        var sortField = this.getForm().findField('catch_meta_keywords_1');
                                        sortField.store.load();
                                    },
                                    scope: this
                                },
                                displayField: 'value',
                                valueField: 'key',
                                mode: 'remote',
                                emptyText: Phlexible.teasers.Strings.no_filter,
                                listClass: 'x-combo-list-big',
                                editable: false,
                                triggerAction: 'all',
                                selectOnFocus: true
                            },
                            {
                                xtype: 'multiselect',
                                fieldLabel: Phlexible.elements.Strings.meta_keywords,
                                name: 'catch_meta_keywords_1',
                                store: new Ext.data.JsonStore({
                                    url: Phlexible.Router.generate('elementfinder_catch_metakeywords', {id: this.siteroot_id}),
                                    root: 'meta_keywords',
                                    fields: ['keyword'],
                                    listeners: {
                                        beforeload: function (store, options) {
                                            ++this.reSetComboCounter;
                                            //Phlexible.console.log('+catch_meta_keywords_1');

                                            options['params'] = {
                                                language: 'de',
                                                key: ''
                                            };

                                            if (this.getForm()) {
                                                var keyField = this.getForm().findField('catch_meta_key_1');
                                                if (keyField && keyField.isFormField) {
                                                    options['params']['key'] = keyField.getValue();
                                                }
                                            }
                                        },
                                        load: function () {
                                            var onLoad = this.getForm().findField('catch_meta_keywords_1').setOnLoad;
                                            if (onLoad) {
                                                this.getForm().findField('catch_meta_keywords_1').setValue(onLoad);
                                                this.getForm().findField('catch_meta_keywords_1').setOnLoad = false;
                                            }

                                            this.reSetComboValue('catch_template');
                                        },
                                        scope: this
                                    }
                                }),
                                width: 300,
                                emptyText: Phlexible.elements.Strings.select_elementtype,
                                valueField: 'keyword',
                                displayField: 'keyword',
                                tpl: '<tpl for="."><div class="ux-mselect-item' + ((Ext.isIE || Ext.isIE7) ? '" unselectable=on' : ' x-unselectable"') + '>{keyword}</div></tpl>',
                                //                width:280,
                                height: 165,
                                allowBlank: true
                            }]
                    },
                    {
                        xtype: 'fieldset',
                        title: Phlexible.elements.Strings.paging,
                        autoHeight: true,
                        anchor: '-15',
                        items: [{
                            xtype: 'checkbox',
                            fieldLabel: Phlexible.elements.Strings.paging,
                            boxLabel: Phlexible.elements.Strings.activate,
                            name: 'catch_paginator',
                            listeners: {
                                check: function (field, checked) {
                                    var form = this.getForm();
                                    form.findField('catch_elements_per_page').setDisabled(!checked);
                                },
                                scope: this
                            }
                        },
                            {
                                xtype: 'numberfield',
                                fieldLabel: this.strings.catch_elements_per_page,
                                name: 'catch_elements_per_page',
                                width: 30,
                                allowBlank: false,
                                disabled: true
                            }]
                    },
                    {
                        xtype: 'fieldset',
                        title: Phlexible.elements.Strings.template,
                        autoHeight: true,
                        anchor: '-15',
                        items: [{
                            xtype: 'combo',
                            width: 300,
                            listWidth: 300,
                            fieldLabel: Phlexible.elements.Strings.template,
                            hiddenName: 'catch_template',
                            store: new Ext.data.JsonStore({
                                url: '',//Phlexible.Router.generate('elementfinder_catch_templates'),
                                root: 'templates',
                                fields: ['id', 'template'],
                                listeners: {
                                    beforeload: function () {
                                        ++this.reSetComboCounter;
                                        //Phlexible.console.log('+catch_template');
                                    },
                                    load: function () {
                                        this.reSetComboValue('catch_template');
                                    },
                                    scope: this
                                }
                            }),
                            displayField: 'template',
                            valueField: 'id',
                            mode: 'remote',
                            emptyText: Phlexible.elements.Strings.no_template,
                            listClass: 'x-combo-list-big',
                            editable: false,
                            triggerAction: 'all',
                            selectOnFocus: true,
                            listeners: {
                                beforequery: function (event) {
                                    event.query = this.catchId;
                                },
                                scope: this
                            }
                        }]
                    }]
            }
        ];

        this.tbar = [
            {
                text: '_update',
                iconCls: 'p-element-save-icon',
                handler: this.submitForm,
                scope: this
            },
            {
                text: '_reset',
                iconCls: 'p-element-reload-icon',
                handler: this.onReset,
                scope: this
            }
        ];

        Phlexible.elementfinder.CatchDataPanel.superclass.initComponent.call(this);
    },

    getForm: function () {
        return this.getComponent(0).getForm();
    },

    setValues: function (catchId, catchConfig) {
        this.catchId = catchConfig.id;
        this.catchConfig = catchConfig;

        this.disable();

        // reset form values
        this.getForm().reset();

        // reset Meta Keywords options
        this.getForm().findField('catch_meta_keywords_1').store.removeAll();

        if (this.reloadLinkFieldStore) {
            var field = this.getForm().findField('for_tree_id');
            field.tree.getLoader().load(
                field.tree.getRootNode(),
                this.internalSetValues.createDelegate(this)
            );
        }
        else {
            this.internalSetValues();
        }

    },

    internalSetValues: function () {

        // set new values
        this.getForm().setValues(this.catchConfig);

        // reset all combobox values
        var combos = this.getComponent(0).findByType('combo');
        Ext.each(combos, function (combo, index, allItems) {
            if ('remote' == combo.mode) {
                var value = combo.getValue();
                var rawValue = combo.getRawValue();

                if (value && value == rawValue) {
                    combo.doQuery(this.catchConfig[combo.hiddenField.id], true);
                }
            }

        }, this);

        this.getForm().findField('catch_meta_keywords_1').setOnLoad = this.catchConfig.catch_meta_keywords_1;
        this.getForm().findField('catch_meta_keywords_1').store.load();

        this.enableAfterInitialisation();
    },

    reSetComboValue: function (fieldName) {

        if (this.items && this.items.length) {
            var combo = this.getForm().findField(fieldName);

            var value = combo.getValue();
            var rawValue = combo.getRawValue();

            if (value && value == rawValue) {
                combo.setValue(value);
            }
        }

        --this.reSetComboCounter;
        //Phlexible.console.log('- ' + fieldName);

        this.enableAfterInitialisation();
    },

    onPublish: function () {
        // TODO
    },

    submitForm: function () {
        var form = this.getForm();

        if (form.isValid()) {
            form.submit({
                url: Phlexible.Router.generate('elementfinder_catch_save'),
                params: {
                    id: this.catchId
                },
                success: this.onSuccess,
                failure: this.onFailure,
                scope: this
            });
        }
    },

    onReset: function () {
        this.setValues(this.catchId, this.catchConfig);
    },

    onSuccess: function (form, action) {
        this.fireEvent('save', this, action.result);
    },

    onFailure: function (form, action) {
        if (action.result.msg) {
            Ext.MessageBox.alert('Failure', action.result.msg);
        }
    },

    onGetLock: function () {
        var tb = this.getTopToolbar();
        tb.items.items[0].enable();
        tb.items.items[1].enable();
        tb.items.items[2].enable();
    },

    onIsLocked: function () {
        var tb = this.getTopToolbar();
        tb.items.items[0].disable();
        tb.items.items[1].disable();
        tb.items.items[2].disable();
    },

    onRemoveLock: function () {
        var tb = this.getTopToolbar();
        tb.items.items[0].disable();
        tb.items.items[1].disable();
        tb.items.items[2].disable();
    },

    enableAfterInitialisation: function () {
        if (this.isElementTypeInitialized && this.isLinkInitialized && !this.reSetComboCounter) {
            this.enable();
        }

        //Phlexible.console.log(this.isElementTypeInitialized + " - " + this.isLinkInitialized + " - " + !this.reSetComboCounter  + " - " + this.reSetComboCounter);
    }
});

Ext.reg('elementfinder-catch-panel', Phlexible.elementfinder.CatchDataPanel);