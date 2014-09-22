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

    //disabled: true,

    initComponent: function () {
        this.addEvents(
            'save',
            'values'
        );

        if (!this.values) {
            this.values = {};
        }
        if (this.values.inNavigation === undefined) {
            this.values.inNavigation = this.baseValues.inNavigation;
        }
        if (this.values.maxDepth === undefined) {
            this.values.maxDepth = this.baseValues.maxDepth;
        }
        if (this.values.sortField === undefined) {
            this.values.sortField = this.baseValues.sortField;
        }
        if (this.values.sortDir === undefined) {
            this.values.sortDir = this.baseValues.sortDir;
        }

        if (!this.siterootId) {
            this.siterootId = '';
        }

        this.fireEvent('values', this, this.values);

        this.items = [
            {
                xtype: 'form',
                labelWidth: 150,
                border: false,
                items: [{
                    xtype: 'fieldset',
                    title: this.strings.elements,
                    autoHeight: true,
                    anchor: '-15',
                    items: [{
                        xtype: 'hidden',
                        name: 'startTreeId',
                        value: this.values.startTreeId
                    },{
                        xtype: 'tidselector',
                        name: 'for_tree_id',
                        fieldLabel: this.strings.start_node,
                        labelSeparator: '',
                        element: {
                            siteroot_id: this.siterootId
                        },
                        recursive: true,
                        width: 300,
                        listWidth: 300,
                        treeWidth: 292,
                        value: this.values.startTreeId,
                        listeners: {
                            change: function (field) {
                                this.getComponent(0).getComponent(0).getComponent(0).setValue(
                                    this.getComponent(0).getComponent(0).getComponent(1).getValue()
                                );
                            },
                            load: function () {
                                this.getComponent(0).getComponent(0).getComponent(1).setValue(
                                    this.getComponent(0).getComponent(0).getComponent(1).getValue()
                                );
                            },
                            scope: this
                        }
                    },{
                        xtype: 'checkbox',
                        fieldLabel: this.strings.elements,
                        boxLabel: this.strings.in_navigation,
                        name: 'inNavigation',
                        checked: this.values.inNavigation
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: this.strings.max_depth,
                        name: 'maxDepth',
                        width: 30,
                        value: this.values.maxDepth,
                        allowBlank: true
                    }]
                },
                    {
                        xtype: 'fieldset',
                        title: this.strings.sort,
                        autoHeight: true,
                        anchor: '-15',
                        items: [{
                            xtype: 'twincombobox',
                            width: 300,
                            listWidth: 300,
                            fieldLabel: this.strings.sort_field,
                            hiddenName: 'sortField',
                            value: this.values.sortField,
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_sortfields', {query: this.baseValues.elementtypeIds}),
                                root: 'data',
                                fields: ['ds_id', 'title', 'icon'],
                                autoLoad: true,
                                listeners: {
                                    load: function() {
                                        this.getComponent(0).getComponent(1).getComponent(0).setValue(
                                            this.getComponent(0).getComponent(1).getComponent(0).getValue()
                                        );
                                    },
                                    scope: this
                                }
                            }),
                            //tpl: '<tpl for="."><div class="x-combo-list-item {icon}">{title}</div></tpl>',
                            displayField: 'title',
                            valueField: 'ds_id',
                            mode: 'remote',
                            emptyText: this.strings.unsorted,
                            listClass: 'x-combo-list-big',
                            editable: false,
                            triggerAction: 'all',
                            selectOnFocus: true,
                            listeners: {
                                beforequery: function (event) {
                                    event.query = this.baseValues.elementtypeIds;
                                },
                                scope: this
                            }
                        },
                            {
                                xtype: 'combo',
                                width: 300,
                                listWidth: 300,
                                fieldLabel: this.strings.sort_dir,
                                hiddenName: 'sortDir',
                                value: this.values.sortDir || 'ASC',
                                store: new Ext.data.SimpleStore({
                                    fields: ['title', 'value'],
                                    data: [
                                        [this.strings.sort_ascending, 'ASC'],
                                        [this.strings.sort_descending, 'DESC']
                                    ]
                                }),
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
                        title: this.strings.filter,
                        autoHeight: true,
                        anchor: '-15',
                        items: [{
                            xtype: 'twincombobox',
                            width: 300,
                            listWidth: 300,
                            fieldLabel: this.strings.meta_filter,
                            hiddenName: 'metaKey',
                            value: this.values.metaKey,
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_metakeys'),
                                root: 'metakeys',
                                fields: ['id', 'name'],
                                autoLoad: true,
                                listeners: {
                                    load: function() {
                                        this.getComponent(0).getComponent(2).getComponent(0).setValue(
                                            this.getComponent(0).getComponent(2).getComponent(0).getValue()
                                        );
                                    },
                                    scope: this
                                }
                            }),
                            listeners: {
                                select: function (combo, record, index) {
                                    var sortField = this.getForm().findField('metaKeywords');
                                    sortField.store.load();
                                },
                                clear: function() {
                                    this.getComponent(0).getComponent(2).getComponent(1).getStore().removeAll();
                                },
                                scope: this
                            },
                            displayField: 'name',
                            valueField: 'id',
                            mode: 'remote',
                            emptyText: this.strings.no_filter,
                            listClass: 'x-combo-list-big',
                            editable: false,
                            triggerAction: 'all',
                            selectOnFocus: true
                        },
                        {
                            xtype: 'multiselect',
                            fieldLabel: this.strings.meta_keywords,
                            name: 'metaKeywords',
                            value: this.values.metaKeywords,
                            store: new Ext.data.JsonStore({
                                url: Phlexible.Router.generate('elementfinder_catch_metakeywords', {siteroot_id: this.siteroot_id}),
                                root: 'meta_keywords',
                                fields: ['keyword'],
                                listeners: {
                                    beforeload: function (store, options) {
                                        options['params'] = {
                                            language: 'de',
                                            id: ''
                                        };

                                        if (this.getForm()) {
                                            var keyField = this.getForm().findField('metaKey');
                                            if (keyField && keyField.isFormField) {
                                                options['params']['id'] = keyField.getValue();
                                            }
                                        }
                                    },
                                    load: function () {
                                        var onLoad = this.getForm().findField('metaKeywords').setOnLoad;
                                        if (onLoad) {
                                            this.getForm().findField('metaKeywords').setValue(onLoad);
                                            this.getForm().findField('metaKeywords').setOnLoad = false;
                                        }
                                    },
                                    scope: this
                                }
                            }),
                            width: 300,
                            emptyText: this.strings.select_meta_keywords,
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

        values.elementtypeIds = this.baseValues.elementtypeIds;
        values.filter = this.baseValues.filter;
        values.template = this.baseValues.template;

        return values;
    }
});

Ext.reg('elementfinder-finder-config-panel', Phlexible.elementfinder.ElementFinderConfigPanel);