Phlexible.elementfinder.ElementFinderConfigPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elementfinder.Strings,
    title: Phlexible.elementfinder.Strings.finder,
    iconCls: 'p-elementfinder-finder-icon',
    cls: 'p-elements-catch-panel',
    autoScroll: true,
    //autoWidth: false,
    bodyStyle: 'padding-top: 5px',
    //hideMode: 'offsets',

    reloadLinkFieldStore: false,

    isLinkInitialized: false,
    isElementTypeInitialized: false,

    siteroot_id: '',

    //disabled: true,

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
                    title: Phlexible.elements.Strings.elements,
                    autoHeight: true,
                    anchor: '-15',
                    items: [{
                        xtype: 'hidden',
                        name: 'for_tree_id_hidden'
                    },{
                        xtype: 'tidselector',
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
                                }
                            },
                            scope: this
                        }
                    },{
                        xtype: 'checkbox',
                        fieldLabel: Phlexible.elements.Strings.elements,
                        boxLabel: Phlexible.elements.Strings.in_navigation,
                        name: 'in_navigation',
                        checked: this.inNavigation
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: this.strings.max_depth,
                        name: 'max_depth',
                        width: 30,
                        value: this.maxDepth,
                        allowBlank: true
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
                            hiddenName: 'sort_field',
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_sortfields'),
                                root: 'data',
                                fields: ['ds_id', 'title', 'icon']
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
                                    event.query = this.elementtypeIds;
                                },
                                scope: this
                            }
                        },
                            {
                                xtype: 'combo',
                                width: 300,
                                listWidth: 300,
                                fieldLabel: Phlexible.elements.Strings.sort_order,
                                hiddenName: 'sort_order',
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
                            fieldLabel: Phlexible.elements.Strings.meta_filter,
                            hiddenName: 'meta_key_1',
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_metakey'),
                                root: 'metakeys',
                                fields: ['key', 'value']
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
                            name: 'meta_keywords_1',
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_metakeywords', {id: this.siteroot_id}),
                                root: 'meta_keywords',
                                fields: ['keyword'],
                                listeners: {
                                    beforeload: function (store, options) {
                                        options['params'] = {
                                            language: 'de',
                                            key: ''
                                        };

                                        if (this.getForm()) {
                                            var keyField = this.getForm().findField('meta_key_1');
                                            if (keyField && keyField.isFormField) {
                                                options['params']['key'] = keyField.getValue();
                                            }
                                        }
                                    },
                                    load: function () {
                                        var onLoad = this.getForm().findField('meta_keywords_1').setOnLoad;
                                        if (onLoad) {
                                            this.getForm().findField('meta_keywords_1').setValue(onLoad);
                                            this.getForm().findField('meta_keywords_1').setOnLoad = false;
                                        }
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
                    }]
            }
        ];

        Phlexible.elementfinder.ElementFinderConfigPanel.superclass.initComponent.call(this);
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
        this.getForm().findField('meta_keywords_1').store.removeAll();

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

        this.getForm().findField('meta_keywords_1').setOnLoad = this.catchConfig.meta_keywords_1;
        this.getForm().findField('meta_keywords_1').store.load();
    },

    isValid: function() {
        var form = this.getForm();

        return form.isValid();
    },

    getValues: function () {
        if (!this.isValid()) {
            return null;
        }

        var form = this.getForm(),
            values = form.getValues();

        values.element_type_ids = this.elementtypeIds;
        values.filter = this.filter;

        return values;
    }
});

Ext.reg('elementfinder-finder-config-panel', Phlexible.elementfinder.ElementFinderConfigPanel);