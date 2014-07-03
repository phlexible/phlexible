Phlexible.translations.TranslationFilterPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.translations.Strings.filter,
    strings: Phlexible.translations.Strings,
    cls: 'p-translations-filter-panel',
    iconCls: 'p-translation-filter-icon',
    labelWidth: 100,
    bodyStyle: 'padding: 5px; background: white;',
    defaultType: 'textfield',

    initComponent: function () {
        this.addEvents(
            'search'
        );

        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.items = [
            {
                xtype: 'panel',
                title: this.strings.keys,
                layout: 'form',
                frame: true,
                collapsible: true,
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'textfield',
                        anchor: '100%',
                        fieldLabel: this.strings.key,
                        name: 'key',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                title: this.strings.content,
                layout: 'form',
                frame: true,
                collapsible: true,
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.gui.Strings.de,
                        name: 'de_value',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        },
                        append: [
                            {
                                xtype: 'checkbox',
                                boxLabel: this.strings.empty,
                                name: 'de_empty',
                                listeners: {
                                    check: {
                                        fn: function (cb, checked) {
                                            if (checked) {
                                                cb.ownerCt.disable();
                                            } else {
                                                cb.ownerCt.enable();
                                            }

                                            this.updateFilter();
                                        },
                                        scope: this
                                    }
                                }
                            }
                        ]
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: Phlexible.gui.Strings.en,
                        name: 'en_value',
                        enableKeyEvents: true,
                        listeners: {
                            keyup: {
                                fn: function (field, event) {
                                    if (event.getKey() == event.ENTER) {
                                        this.task.cancel();
                                        this.updateFilter();
                                        return;
                                    }

                                    this.task.delay(500);
                                },
                                scope: this
                            }
                        },
                        append: [
                            {
                                xtype: 'checkbox',
                                boxLabel: this.strings.empty,
                                name: 'en_empty',
                                listeners: {
                                    check: {
                                        fn: function (cb, checked) {
                                            if (checked) {
                                                cb.ownerCt.disable();
                                            } else {
                                                cb.ownerCt.enable();
                                            }

                                            this.updateFilter();
                                        },
                                        scope: this
                                    }
                                }
                            }
                        ]
                    }
                ]
            }
        ];

        this.tbar = ['->', {
            text: this.strings.reset,
            iconCls: 'p-translation-reset-icon',
            disabled: true,
            handler: this.resetFilter,
            scope: this
        }];

        Phlexible.translations.TranslationFilterPanel.superclass.initComponent.call(this);
    },

    resetFilter: function (btn) {
        this.form.reset();
        this.updateFilter();
        btn.disable();
    },

    updateFilter: function () {
        this.getTopToolbar().items.items[1].enable();

        var values = this.form.getValues();

        if (!values.de_empty) {
            values.de_empty = '';
        }
        if (!values.en_empty) {
            values.en_empty = '';
        }

        // checkbox states must be reset
        this.fireEvent('search', values || {});
    }
});

Ext.reg('translations-filterpanel', Phlexible.translations.TranslationFilterPanel);