Phlexible.teasers.NewCatchWindow = Ext.extend(Phlexible.gui.util.Dialog, {
    title: Phlexible.teasers.Strings.new_catch,
    width: 530,
    minWidth: 530,
    maxWidth: 530,
    height: 590,
    minHeight: 590,
    maxHeight: 590,
    iconCls: 'p-element-add-icon',

    textHeader: Phlexible.teasers.Strings.new_catch_header,
    textDescription: Phlexible.teasers.Strings.new_catch_description,
    textOk: Phlexible.elements.Strings.save,
    textCancel: Phlexible.elements.Strings.cancel,

    extraCls: 'p-elements-newelement',
    iconClsOk: 'p-element-save-icon',

    getSubmitUrl: function () {
        return Phlexible.Router.generate('teasers_layout_createcatch');
    },

    labelWidth: 180,

    getFormItems: function () {
        return [
            new Phlexible.elements.EidSelector({
                fieldLabel: Phlexible.elements.Strings.eid,
                labelSeparator: '',
                element: {
                    siteroot_id: this.submitParams.siteroot_id
                },
                anchor: '-70',
                listWidth: 233,
                treeWidth: 233,
                menuConfig: {
                    width: 250
                },
                listeners: {
                    render: function (c) {
                        c.hiddenField = c.el.insertSibling({
                            tag: 'input',
                            type: 'hidden',
                            name: 'for_tree_id'
                        }, 'before', true);
                    },
                    change: function (c) {
                        c.hiddenField.value = c.getValue();
                    }

                }
            }), {
                xtype: 'multiselect',
                fieldLabel: Phlexible.elements.Strings.elementtype,
                name: 'catch_element_type_id',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('teasers_catch_elementtypes', {id: this.submitParams.siteroot_id}),
                    root: 'elementtypes',
                    fields: ['id', 'title', 'icon'],
                    autoLoad: true
                }),
                emptyText: Phlexible.elements.Strings.select_elementtype,
                valueField: 'id',
                displayField: 'title',
                tpl: '<tpl for="."><div class="ux-mselect-item' + ((Ext.isIE || Ext.isIE7) ? '" unselectable=on' : ' x-unselectable"') + '><img src="{icon}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                width: 261,
                height: 165,
                allowBlank: false,
                anchor: '-70',
                listeners: {
                    'click': {
                        fn: function (field, event) {
                            var sortField = this.getForm().getForm().findField('catch_sort_field');
                            sortField.clearValue();
                        },
                        scope: this
                    }
                }
            }, {
                xtype: 'checkbox',
                fieldLabel: Phlexible.elements.Strings.elements,
                boxLabel: Phlexible.elements.Strings.in_navigation,
                name: 'catch_in_navigation'
            }, {
                hidden: true
            }, {
                xtype: 'numberfield',
                fieldLabel: Phlexible.teasers.Strings.catch_max_depth,
                name: 'catch_max_depth',
                width: 30,
                allowBlank: true
            }, {
                xtype: 'combo',
                fieldLabel: Phlexible.teasers.Strings.sort_field,
                hiddenName: 'catch_sort_field',
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('teasers_catch_sortfields'),
                    root: 'data',
                    fields: ['ds_id', 'title', 'icon']
                }),
                displayField: 'title',
                valueField: 'ds_id',
                mode: 'remote',
                emptyText: Phlexible.elements.Strings.unsorted,
                listClass: 'x-combo-list-big',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70',
                listeners: {
                    'beforequery': {
                        fn: function (event) {
                            var elementTypesField = this.getForm().getForm().findField('catch_element_type_id');
                            event.query = elementTypesField.getValue();
                        },
                        scope: this
                    }
                }
            }, {
                xtype: 'combo',
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
                allowBlank: false,
                anchor: '-70'
            }, {
                xtype: 'combo',
                fieldLabel: Phlexible.elements.Strings.filter,
                hiddenName: 'catch_filter',
                store: new Ext.data.SimpleStore({
                    fields: ['title', 'value'],
                    data: [
                        [Phlexible.elements.Strings.no_filter, ''],
                        [Phlexible.teasers.Strings.catch_filter_date, 'Phlexible_Teasers_Catch_Filter_Date'],
                        [Phlexible.teasers.Strings.catch_filter_new_product, 'Phlexible_Teasers_Catch_Filter_NewProduct'],
                        [Phlexible.teasers.Strings.catch_filter_next_events, 'Phlexible_Teasers_Catch_Filter_NextEvents'],
                        [Phlexible.teasers.Strings.catch_filter_title_article_number, 'Phlexible_Teasers_Catch_Filter_TitleArticleNumberSort'],
                        [Phlexible.teasers.Strings.catch_filter_color_duration, 'Phlexible_Teasers_Catch_Filter_ColorDuration']
                    ]
                }),
                displayField: 'title',
                valueField: 'value',
                mode: 'local',
                emptyText: Phlexible.elements.Strings.no_filter,
                listClass: 'x-combo-list-big',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true,
                anchor: '-70'
            }, {
                xtype: 'checkbox',
                fieldLabel: Phlexible.elements.Strings.paging,
                boxLabel: Phlexible.elements.Strings.activate,
                name: 'catch_paginator',
                listeners: {
                    'check': {
                        fn: function (field, checked) {
                            Phlexible.console.log('check ' + checked);
                            var form = this.getForm().getForm();
                            form.findField('catch_only_first_page').setDisabled(!checked);
                            form.findField('catch_elements_per_page').setDisabled(!checked);
                        },
                        scope: this
                    }
                }
            }, {
                xtype: 'checkbox',
                fieldLabel: Phlexible.teasers.Strings.catch_only_first_page,
                name: 'catch_only_first_page',
                disabled: true
            }, {
                xtype: 'numberfield',
                fieldLabel: Phlexible.teasers.Strings.catch_elements_per_page,
                name: 'catch_elements_per_page',
                width: 30,
                allowBlank: false,
                disabled: true
            }];

//        this.getComponent(0).getComponent(2).setValue('0');
    }

//        this.getComponent(0).getComponent(1).setValue(this.elementtype_id);
//        this.getComponent(0).getComponent(2).setValue('0');


});
