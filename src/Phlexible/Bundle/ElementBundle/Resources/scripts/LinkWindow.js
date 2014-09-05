Phlexible.elements.LinkWindow = Ext.extend(Ext.Window, {
    title: Phlexible.elements.Strings.link,
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-link_edit-icon',
    width: 450,
    height: 200,
    modal: true,
    layout: 'fit',
    closable: false,
    disabled: true,

    value: '',

    allowed: {
        tid: true,
        intrasiteroot: true,
        url: true,
        mailto: true
    },
    elementTypeIds: '',
    siteroot_id: '',
    hideNewWindow: false,
    hideNoLink: false,
    hideLanguage: false,
    language: '',

    initComponent: function () {
        if (this.element) {
            this.siteroot_id = this.element.siteroot_id;
        }

        this.addEvents(
            'set'
        );

        // legacy
        if (this.allowed.eid !== undefined) {
            this.allowed.tid = this.allowed.eid;

        }
        var selected = 'no',
            tidValue = null,
            eidValue = null,
            intrasiterootValue = null,
            urlValue = null,
            mailtoValue = null,
            newWindowValue = null,
            targetLanguageValue = null;

        if (this.value) {
            if (this.value.type === 'internal') {
                selected = 'internal';
                tidValue = this.value.tid;
                if (this.value.eid) {
                    eidValue = this.value.eid;
                }
                if (this.value.language) {
                    targetLanguageValue = this.value.language;
                }
                if (this.value.newWindow) {
                    newWindowValue = true;
                }
                Phlexible.console.info('tid: ' + tidValue);
                Phlexible.console.info('eid: ' + eidValue);
                Phlexible.console.info('language: ' + targetLanguageValue);
                Phlexible.console.info('newWindow: ' + newWindowValue);
            }
            else if (this.value.type === 'intrasiteroot') {
                selected = 'intrasiteroot';
                intrasiterootValue = this.value.tid;
                if (this.value.eid) {
                    eidValue = this.value.eid;
                }
                if (this.value.language) {
                    targetLanguageValue = this.value.language;
                }
                if (this.value.newWindow) {
                    newWindowValue = true;
                }
                Phlexible.console.info('intrasiteroot: ' + intrasiterootValue);
                Phlexible.console.info('eid: ' + eidValue);
                Phlexible.console.info('language: ' + targetLanguageValue);
                Phlexible.console.info('newWindow: ' + newWindowValue);
            }
            else if (this.value.type === 'external') {
                selected = 'external';
                urlValue = this.value.url;
                if (this.value.newWindow) {
                    newWindowValue = true;
                }
                if (this.value.language) {
                    targetLanguageValue = this.value.language;
                }
                Phlexible.console.info('url: ' + urlValue);
                Phlexible.console.info('language: ' + targetLanguageValue);
                Phlexible.console.info('newWindow: ' + newWindowValue);
            }
            else if (this.value.type === 'mailto') {
                selected = 'mailto';
                mailtoValue = this.value.recipient;
                Phlexible.console.info('recipient', mailtoValue);
            }
        }

        var typeValues = [];

        if (!this.hideNoLink) {
            typeValues.push(['no', this.strings.link_no_link]);
        }
        if (this.allowed.tid) {
            typeValues.push(['internal', this.strings.link_tid]);
        }
        if (this.allowed.intrasiteroot) {
            typeValues.push(['intrasiteroot', this.strings.link_intrasiteroot]);
        }
        if (this.allowed.url) {
            typeValues.push(['external', this.strings.link_url]);
        }
        if (this.allowed.mailto) {
            typeValues.push(['mailto', this.strings.link_mailto]);
        }

        if (typeValues.length < 2) {
            Ext.MessageBox.alert('Error', 'No link types available.');
            this.close();
        }
        else if (typeValues.length === 2) {
            if (selected === 'no') {
                selected = typeValues[1][0];
            }
        }

        var languages = Phlexible.Config.get('set.language.frontend');

        this.items = [
            {
                xtype: 'form',
                bodyStyle: 'padding: 10px;',
                border: false,
                labelWidth: 100,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: this.strings.type,
                        hiddenName: 'type',
                        width: 300,
                        listWidth: 283,
                        value: selected,
                        store: new Ext.data.SimpleStore({
                            fields: ['key', 'title'],
                            data: typeValues
                        }),
                        valueField: 'key',
                        displayField: 'title',
                        mode: 'local',
                        editable: false,
                        triggerAction: 'all',
                        selectOnFocus: true,
                        listeners: {
                            select: function (c, r, rowIndex) {
                                // update visibility
                                var formPanel = this.getComponent(0);

                                switch (r.data.key) {
                                    case 'no':
                                        formPanel.getComponent(1).hide();
                                        formPanel.getComponent(2).hide();
                                        formPanel.getComponent(3).hide();
                                        formPanel.getComponent(4).hide();
                                        formPanel.getComponent(5).hide();
                                        formPanel.getComponent(6).hide();
                                        break;

                                    case 'internal':
                                        formPanel.getComponent(1).show();
                                        formPanel.getComponent(2).hide();
                                        formPanel.getComponent(3).hide();
                                        formPanel.getComponent(4).setVisible(!this.hideNewWindow);
                                        formPanel.getComponent(5).show();
                                        formPanel.getComponent(6).hide();
                                        break;

                                    case 'intrasiteroot':
                                        formPanel.getComponent(1).hide();
                                        formPanel.getComponent(2).show();
                                        formPanel.getComponent(3).hide();
                                        formPanel.getComponent(4).setVisible(!this.hideNewWindow);
                                        formPanel.getComponent(5).show();
                                        formPanel.getComponent(6).hide();
                                        break;

                                    case 'external':
                                        formPanel.getComponent(1).hide();
                                        formPanel.getComponent(2).hide();
                                        formPanel.getComponent(3).show();
                                        formPanel.getComponent(4).setVisible(!this.hideNewWindow);
                                        formPanel.getComponent(5).setVisible(!this.hideLanguage);
                                        formPanel.getComponent(6).hide();
                                        break;

                                    case 'mailto':
                                        formPanel.getComponent(1).hide();
                                        formPanel.getComponent(2).hide();
                                        formPanel.getComponent(3).hide();
                                        formPanel.getComponent(4).hide();
                                        formPanel.getComponent(5).hide();
                                        formPanel.getComponent(6).show();
                                        break;
                                }
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'tidselector',
                        name: 'tid',
                        fieldLabel: this.strings.tid,
                        siteroot_id: this.siteroot_id,
                        hidden: selected !== 'internal',
                        width: 300,
                        listWidth: 283,
                        treeWidth: 283,
                        elementTypeIds: this.elementTypeIds,
                        language: this.language,
                        value: tidValue
                    },
                    {
                        xtype: 'tidselector',
                        name: 'intrasiteroot',
                        fieldLabel: this.strings.tid,
                        siteroot_id: this.siteroot_id,
                        hidden: selected !== 'intrasiteroot',
                        width: 300,
                        listWidth: 283,
                        treeWidth: 283,
                        elementTypeIds: this.elementTypeIds,
                        intrasiteroot: true,
                        language: this.language,
                        value: intrasiterootValue
                    },
                    {
                        xtype: 'textfield',
                        name: 'url',
                        fieldLabel: this.strings.url,
                        helpText: this.strings.link_url_help,
                        vtype: 'url',
                        hidden: selected !== 'external',
                        width: 300,
                        value: urlValue
                    },
                    {
                        xtype: 'checkbox',
                        name: 'new_window',
                        fieldLabel: '',
                        labelSeparator: '',
                        boxLabel: this.strings.link_new_window,
                        vtype: 'url',
                        width: 300,
                        checked: newWindowValue,
                        hidden: this.hideNewWindow || selected === 'mailto'
                    },
                    {
                        xtype: 'iconcombo',
                        hiddenName: 'target_language',
                        fieldLabel: this.strings.language,
                        helpText: this.strings.link_language_help,
                        emptyText: this.strings.link_language_empty,
                        width: 300,
                        store: new Ext.data.SimpleStore({
                            fields: ['key', 'title', 'icon'],
                            data: languages
                        }),
                        valueField: 'key',
                        displayField: 'title',
                        iconClsField: 'icon',
                        mode: 'local',
                        editable: false,
                        triggerAction: 'all',
                        selectOnFocus: true,
                        value: targetLanguageValue,
                        hidden: this.hideLanguage || selected === 'mailto'
                    },
                    {
                        xtype: 'textfield',
                        name: 'mailto',
                        fieldLabel: this.strings.mailto,
                        helpText: this.strings.link_mailto_help,
                        vtype: 'email',
                        hidden: selected !== 'mailto',
                        width: 300,
                        value: mailtoValue
                    }
                ]
            }
        ];

        this.buttons = [
            {
                text: this.strings.cancel,
                handler: this.close,
                scope: this
            },
            {
                text: this.strings.set,
                handler: function () {
                    var formPanel = this.getComponent(0),
                        values = formPanel.getForm().getValues(),
                        display = '',
                        value = {},
                        field, node;

                    if (values.type === 'no') {
                        display = '';
                        value = '';
                    } else if (values.type === 'internal') {
                        field = formPanel.getComponent(1);
                        if (!field.isValid()) {
                            return;
                        }
                        display = field.getRawValue();
                        value.type = 'internal';
                        value.tid = field.getValue();
                        node = field.tree.getNodeById(field.getValue());
                        if (node) {
                            value.eid = node.attributes.eid;
                        }
                        if (formPanel.getComponent(5).getValue()) {
                            value.language = formPanel.getComponent(5).getValue();
                        }
                        if (formPanel.getComponent(4).getValue()) {
                            value.newWindow = true;
                        }
                    } else if (values.type === 'intrasiteroot') {
                        field = formPanel.getComponent(2);
                        if (!field.isValid()) {
                            return;
                        }
                        display = field.getRawValue();
                        value.type = 'intrasiteroot';
                        value.tid = field.getValue();
                        node = field.tree.getNodeById(field.getValue());
                        if (node) {
                            value.eid = node.attributes.eid;
                        }
                        if (formPanel.getComponent(5).getValue()) {
                            value.language = formPanel.getComponent(5).getValue();
                        }
                        if (formPanel.getComponent(4).getValue()) {
                            value.newWindow = true;
                        }
                    } else if (values.type === 'external') {
                        field = formPanel.getComponent(3);
                        if (!field.isValid()) {
                            return;
                        }
                        display = field.getValue();
                        value.type = 'external';
                        value.url = display;
                        if (formPanel.getComponent(5).getValue()) {
                            value.language = formPanel.getComponent(5).getValue();
                        }
                        if (formPanel.getComponent(4).getValue()) {
                            value.newWindow = true;
                        }
                    } else if (values.type === 'mailto') {
                        field = formPanel.getComponent(6);
                        if (!field.isValid()) {
                            return;
                        }
                        display = 'mailto:' + field.getValue();
                        value.type = 'mailto';
                        value.recipient = field.getValue();
                    } else {
                        return;
                    }

                    Phlexible.console.info('display', display);
                    Phlexible.console.info('value', value);
                    this.fireEvent('set', value, display);
                    this.close();
                },
                scope: this
            }
        ];

        Phlexible.elements.LinkWindow.superclass.initComponent.call(this);

        if (selected === 'tid') {
            this.getComponent(0).getComponent(1).tree.loader.addListener({
                load: function (loader, node) {
                    this.enable();
                },
                loadexception: function (loader, node) {
                    this.enable();
                },
                scope: this
            });
        } else if (selected === 'intrasiteroot') {
            this.getComponent(0).getComponent(2).tree.loader.addListener({
                load: function (loader, node) {
                    this.enable();
                },
                loadexception: function (loader, node) {
                    this.enable();
                },
                scope: this
            });
        } else {
            this.enable();
        }

    }
});
