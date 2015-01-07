Ext.provide('Phlexible.elements.DiffVersionComboTemplate');
Ext.provide('Phlexible.elements.ElementDataPanel');

/*Phlexible.elements.xElementDataTabs = Ext.extend(Ext.TabPanel, {
 // private
 xadjustBodyWidth : function(w){
 if(this.header){
 this.header.setWidth(w);
 }
 if(this.footer){
 this.footer.setWidth(w);
 }
 return w;
 }
 });*/

Phlexible.elements.DiffVersionComboTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="x-combo-list-item">',
    '<tpl if="values.is_published"><b></tpl>',
    '<tpl if="values.was_published"><i></tpl>',
    '{version} [{date}]',
    '<tpl if="values.was_published"></i></tpl>',
    '<tpl if="values.is_published"></b></tpl>',
    '</div>',
    '</tpl>'
);

Phlexible.elements.ElementDataPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.data,
    iconCls: 'p-element-tab_data-icon',
    cls: 'p-elements-data-panel',
    autoScroll: false,
    autoWidth: false,
    bodyStyle: 'padding-top: 5px',
    layout: 'border',
    hideMode: 'offsets',

    currentETID: null,
    currentActive: null,

    accordionCollapsed: false,

    initComponent: function () {

        this.addEvents(
            'save'
        );

        this.element.on({
            load: this.onLoadElement,
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            internalSave: this.onSave,
            scope: this
        });

        this.compareElement = new Phlexible.elements.Element({
            siteroot_id: this.element.siteroot_id,
            language: this.element.language
        });

        this.items = [
            {
                xtype: 'form',
                region: 'center',
                layout: 'fit',
                hideMode: 'offsets',
                items: {
                    xtype: 'elements-elementcontentpanel',
                    element: this.element,
                    listeners: {
                        tabchange: function (tabPanel, panel) {
                            var index = tabPanel.items.items.indexOf(panel);
                            var targetPanel = this.getComponent(1).getComponent(1).getComponent(index);
                            if (targetPanel) {
                                this.getComponent(1).getComponent(1).setActiveTab(targetPanel);
                            }
                        },
                        scope: this
                    }
                }
            },
            {
                border: true,
                region: 'east',
                layout: 'card',
                split: true,
                collapsible: true,
                collapseMode: 'mini',
                collapsed: this.accordionCollapsed,
                activeItem: 0,
                listeners: {
                    expand: function () {
                        if (this.isDiffMode) {
                            this.getComponent(1).setWidth(this.getInnerWidth() / 2);
                            this.getComponent(1).ownerCt.doLayout();
                        }
                    },
                    scope: this
                },
                items: [
                    {
                        border: false,
                        width: 220,
                        minWidth: 220,
                        title: this.strings.properties,
                        autoScroll: true,
                        items: [
                            {
                                xtype: 'elements-accordion-quickinfo',
                                region: 'north',
                                //height: 300
                                autoHeight: true
                            },
                            {
                                xtype: 'elements-elementaccordion',
                                region: 'center',
                                border: false,
                                element: this.element
                            }
                        ]
                    },
                    {
                        xtype: 'elements-elementcontentpanel',
                        element: this.compareElement,
                        listeners: {
                            tabchange: function (tabPanel, panel) {
                                var index = tabPanel.items.items.indexOf(panel);
                                this.getContentPanel().setActiveTab(this.getContentPanel().getComponent(index));
                            },
                            scope: this
                        }
                    }
                ]
            }
        ];

        this.tbar = [
            {
                // 0
                text: this.strings.save_element,
                iconCls: 'p-element-save-icon',
                disabled: true,
                hidden: true,
                handler: this.onSave,
                scope: this
            },
            {
                // 1
                xtype: 'checkbox',
                hideLabel: true,
                hidden: true,
                boxLabel: this.strings.minor_update
            },
            {
                // 2
                text: this.strings.reset,
                iconCls: 'p-element-reload-icon',
                disabled: true,
                hidden: true,
                handler: this.onReset,
                scope: this
            },
            {
                // 3
                text: this.strings.publish_element,
                iconCls: 'p-element-publish-icon',
                disabled: true,
                hidden: true,
                handler: this.onPublish,
                scope: this
            },
            '->',
            {
                // 5
                xtype: 'tbtext',
                text: this.strings.diff.show,
                hidden: true
            },
            ' ',
            {
                // 7
                xtype: 'iconcombo',
                hidden: true,
                width: 140,
                value: this.element.language,
                emptyText: this.strings.diff.language,
                store: new Ext.data.JsonStore({
                    fields: ['langKey', 'text', 'iconCls'],
                    data: this.element.languages
                }),
                valueField: 'langKey',
                displayField: 'text',
                iconClsField: 'iconCls',
                mode: 'local',
                forceSelection: true,
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },
            ' ',
            {
                // 9
                xtype: 'combo',
                hidden: true,
                width: 80,
                listWidth: 200,
                emptyText: this.strings.diff.from_version,
                tpl: Phlexible.elements.DiffVersionComboTemplate,
                //value: (parseInt(this.element.version, 10) - 1),
                store: new Ext.data.JsonStore({
                    fields: [
                        {name: 'version'},
                        {name: 'format'},
                        {name: 'create_date'},
                        {name: 'was_published', type: 'boolean'},
                        {name: 'is_published', type: 'boolean'}
                    ],
                    id: 0,
                    data: []
                }),
                displayField: 'version',
                mode: 'local',
                forceSelection: true,
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },
            {
                // 10
                text: this.strings.diff.compare_to,
                enableToggle: true,
                hidden: true,
                toggleHandler: function (btn, state) {
                    this.getTopToolbar().items.items[this.btnIndex.version_to].setDisabled(!state);
                },
                scope: this
            },
            {
                // 11
                xtype: 'combo',
                width: 80,
                hidden: true,
                disabled: true,
                listWidth: 200,
                emptyText: this.strings.diff.to_version,
                tpl: Phlexible.elements.DiffVersionComboTemplate,
                //value: (parseInt(this.element.version, 10) - 1),
                store: new Ext.data.JsonStore({
                    fields: [
                        {name: 'version'},
                        {name: 'format'},
                        {name: 'create_date'},
                        {name: 'was_published', type: 'boolean'},
                        {name: 'is_published', type: 'boolean'}
                    ],
                    id: 0,
                    data: []
                }),
                displayField: 'version',
                mode: 'local',
                forceSelection: true,
                typeAhead: false,
                editable: false,
                triggerAction: 'all',
                selectOnFocus: true
            },
            ' ',
            {
                // 13
                text: this.strings.diff.load,
                hidden: true,
                handler: this.loadDiff,
                scope: this
            },
            '-',
            {
                // 15
                text: this.strings.diff.compare,
                iconCls: 'p-element-diff-icon',
                enableToggle: true,
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.showDiff();
                    } else {
                        this.hideDiff();

                        if (this.element.data.diff && this.element.data.diff.enabled) {
                            this.element.reload();
                        }
                    }
                },
                scope: this,
                listeners: {
                    render: function () {
                        var tb = this.getTopToolbar();
                        tb.items.items[this.btnIndex.show].hide();
                        tb.items.items[this.btnIndex.sep].hide();
                    },
                    scope: this
                }
            }
        ];

        this.btnIndex = {
            save: 0,
            minor: 1,
            reset: 2,
            show: 5,
            language: 7,
            version_from: 9,
            compare_to: 10,
            version_to: 11,
            load: 13,
            sep: 14,
            compare: 15
        };

        this.keys = [
            {
                key: 's',
                alt: true,
                stopEvent: true,
                fn: this.onSave,
                scope: this
            }
        ];

        /*
         this.getContentPanel().on({
         render: {
         fn: function(c) {
         c.body.on({
         scroll: {
         fn: function(e) {
         Phlexible.console.log(e);

         this.getComponent(1).getComponent(1).el.scrollTo('top', 100, false);
         },
         scope: this
         }
         });
         },
         scope: this
         }
         })
         */

        this.diffParams = (this.element.startParams && this.element.startParams.diff)
            ? this.element.startParams.diff
            : {};

        Phlexible.elements.ElementDataPanel.superclass.initComponent.call(this);
    },

    getFormPanel: function () {
        return this.getComponent(0);
    },

    getContentPanel: function () {
        return this.getFormPanel().getComponent(0);
    },

    getCardPanel: function () {
        return this.getComponent(1);
    },

    getSidebarPanel: function () {
        return this.getCardPanel().getComponent(0);
    },

    getQuickInfoPanel: function () {
        return this.getSidebarPanel().getComponent(0);
    },

    getAccordionPanel: function () {
        return this.getSidebarPanel().getComponent(1);
    },

    onLoadElement: function (element) {
        this.getQuickInfoPanel().load(element);

        // load diff data
        var tb = this.getTopToolbar();
        var v = [];
        for (var i = 0; i < element.data.versions.length; i++) {
            if (element.data.versions[i]['format'] > 1) {
                v.push(element.data.versions[i]);
            }
        }

        var toVersion;
        if (this.diffParams.version) {
            toVersion = this.diffParams.version;
        }
        else {
            toVersion = element.version > 1 ? element.version - 1 : element.version;
        }

        tb.items.items[this.btnIndex.version_from].store.loadData(v);
        tb.items.items[this.btnIndex.version_to].store.loadData(v);
        tb.items.items[this.btnIndex.version_from].setValue(toVersion);
        tb.items.items[this.btnIndex.version_to].setValue(element.version);

        if (this.diffParams.enabled) {
            tb.items.items[this.btnIndex.compare_to].toggle(true);
            tb.items.items[this.btnIndex.compare].toggle(true);
        }

        if (tb.items.items[this.btnIndex.compare].pressed) {
            this.loadDiff();
        }

        this.diffParams = {};
    },

    loadDiff: function () {
        var tb = this.getTopToolbar();

        var lang = tb.items.items[this.btnIndex.language];
        var langValue = lang.getValue();

        var from = tb.items.items[this.btnIndex.version_from];
        var fromValue = from.getValue();

        if (!fromValue) {
            from.markInvalid();
            return;
        }

        var toValue;
        if (tb.items.items[this.btnIndex.compare_to].pressed) {
            var to = tb.items.items[this.btnIndex.version_to];
            toValue = to.getValue();

            if (!toValue) {
                to.markInvalid();
                return;
            }
        } else {
            toValue = null;//this.element.version;
        }

        //var versionValue = toValue > fromValue ? toValue : fromValue;

        this.compareElement.reload({
            id: this.element.tid,
            teaser_id: this.element.data.properties.teaser_id || null,
            version: this.element.version,
            language: langValue,
            lock: 0,
            diff: 1,
            diff_language: langValue,
            diff_version_from: fromValue,
            diff_version_to: toValue
        });
    },

    toggleDiff: function (diff) {
        var tb = this.getTopToolbar();

        if (diff.enabled) {
            tb.items.items[this.btnIndex.compare].toggle(true, true);
            tb.items.items[this.btnIndex.language].setValue(diff.language);
            tb.items.items[this.btnIndex.version_from].setValue(diff.version_from);
            tb.items.items[this.btnIndex.version_to].setValue(diff.version_to);

            this.showDiff();
        } else {
            tb.items.items[this.btnIndex.compare].toggle(false, true);
            this.hideDiff();
        }
    },

    showDiff: function () {
        this.isDiffMode = true;
        var tb = this.getTopToolbar();

        tb.items.items[this.btnIndex.compare].toggle(true);

        tb.items.items[this.btnIndex.show].show();
        tb.items.items[this.btnIndex.language].show();
        tb.items.items[this.btnIndex.version_from].show();
        tb.items.items[this.btnIndex.compare_to].show();
        tb.items.items[this.btnIndex.version_to].show();
        tb.items.items[this.btnIndex.sep].show();
        tb.items.items[this.btnIndex.load].show();

        this.getComponent(1).layout.setActiveItem(1);

        if (this.accordionCollapsed) {
            this.getComponent(1).expand();
        } else {
            this.getComponent(1).setWidth(this.getInnerWidth() / 2);
            this.getComponent(1).ownerCt.doLayout();
        }
    },

    hideDiff: function () {
        this.isDiffMode = false;
        var tb = this.getTopToolbar();

        tb.items.items[this.btnIndex.compare].toggle(false);

        tb.items.items[this.btnIndex.show].hide();
        tb.items.items[this.btnIndex.language].hide();
        tb.items.items[this.btnIndex.version_from].hide();
        tb.items.items[this.btnIndex.compare_to].hide();
        tb.items.items[this.btnIndex.version_to].hide();
        tb.items.items[this.btnIndex.sep].hide();
        tb.items.items[this.btnIndex.load].hide();

        this.getComponent(1).layout.setActiveItem(0);
        this.getComponent(1).setWidth(220);
        if (this.accordionCollapsed) {
            this.getComponent(1).collapse();
        }
        this.getComponent(1).ownerCt.doLayout();
    },

    onReset: function () {
        this.element.reload();
    },

    onSave: function (publishComment, isPublish, sendNotifications) {
        if (!this.element.tid) {
            Ext.MessageBox.alert('Failure', 'Save not possible, no element loaded.');
            return;
        }

        if (!this.getAccordionPanel().isValid()) {
            Ext.MessageBox.alert('Failure', 'Save not possible, required fields inside the marked accordions are missing.');
            return;
        }

        var data = this.getAccordionPanel().getData();

        this.getContentPanel().syncData(this.getContentPanel());

        if (!this.getContentPanel().isValid()) {
            Ext.MessageBox.alert('Failure', 'Save not possible, required fields are missing.');
            return;
        }

        if (this.element.fireEvent('beforeSave', this.element) === false) {
            return;
        }

        var minor = this.getTopToolbar().items.items[1].getValue() ? 1 : 0;

        this.getAccordionPanel().saveData();

        this.getFormPanel().getForm().submit({
            url: Phlexible.Router.generate('elements_data_save'),
            params: {
                tid: this.element.tid,
                teaser_id: this.element.properties.teaser_id,
                eid: this.element.eid,
                language: this.element.language,
                version: this.element.version,
                minor: minor,
                comment: publishComment,
                publish: isPublish,
                data: Ext.encode(data),
                notifications: sendNotifications
            },
            success: function (form, action) {
                var result = action.result;

                if (!result) {
                    result = {
                        success: false,
                        msg: 'An unexpected error occured',
                        data: {}
                    };
                }

                if (result.success) {
                    Phlexible.success(result.msg);
                    this.getTopToolbar().items.items[1].setValue(false);

                    this.fireEvent('save', this);
                    this.element.fireEvent('save', this.element, result);

                    if (isPublish) {
                        this.element.fireEvent('publish', this.element, result);
                    }

                    this.element.reload({
                        version: null
                    });

                    if (result.data.publish_other && result.data.publish_other.length) {
                        var w = new Phlexible.elements.PublishSlaveWindow({
                            data: result.data.publish_other
                        });
                        w.show();
                    }
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                    this.element.fireEvent('saveFailure', this.element, result);
                }
            },
            failure: function (action, result) {
                Phlexible.console.log('failure');
                var data = result.result;

                if (!data) {
                    data = {
                        success: false,
                        msg: 'Error occured'
                    };
                }

                Ext.MessageBox.alert('Failure', data.msg);
                this.element.fireEvent('saveFailure', this.element, result);
            },
            scope: this
        });
        return;
    },

    onGetLock: function () {
        Phlexible.console.log('ElementDataPanel: GET LOCK');

        var tb = this.getTopToolbar();

        /*
         var x = function() {
         this.getTopToolbar().items.items[this.btnIndex.save].enable();
         };
         x.defer(5000, this);

         tb.items.items[this.btnIndex.save].disable();
         tb.items.items[this.btnIndex.minor].enable();
         tb.items.items[this.btnIndex.reset].enable();
         */
        tb.items.items[this.btnIndex.compare].enable();
    },

    onIsLocked: function () {
        Phlexible.console.log('ElementDataPanel: IS LOCKED');

        var tb = this.getTopToolbar();

        /*
         tb.items.items[this.btnIndex.save].disable();
         tb.items.items[this.btnIndex.minor].disable();
         tb.items.items[this.btnIndex.reset].disable();
         */
        tb.items.items[this.btnIndex.compare].disable();
        this.hideDiff();
    },

    onRemoveLock: function () {
        Phlexible.console.log('ElementDataPanel: DROP LOCK');

        var tb = this.getTopToolbar();

        /*
         tb.items.items[this.btnIndex.save].disable();
         tb.items.items[this.btnIndex.minor].disable();
         tb.items.items[this.btnIndex.reset].disable();
         */
        tb.items.items[this.btnIndex.compare].disable();
        this.hideDiff();
    }
});

Ext.reg('elements-elementdatapanel', Phlexible.elements.ElementDataPanel);
