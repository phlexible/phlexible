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
        var result,
            selected = 'no',
            tidValue = null,
            eidValue = null,
            intrasiterootValue = null,
            urlValue = null,
            mailtoValue = null,
            newWindowValue = null,
            targetLanguageValue = null,
            legacy = true;

        if (this.value) {
            if (result = this.value.match(/^id\:(\d+)(,\d+)?(;newWindow)?$/)) {
                selected = 'tid';
                tidValue = result[1];
                if (result[2]) {
                    eidValue = result[2].substr(1);
                }
                if (result[3]) {
                    newWindowValue = true;
                }
                legacy = false;
                Phlexible.console.info('tid: ' + tidValue);
                Phlexible.console.info('eid: ' + eidValue);
                Phlexible.console.info('newWindow: ' + newWindowValue);
            }
            else if (result = this.value.match(/^sr:(\d+)(,\d+)?(;newWindow)?$/)) {
                selected = 'intrasiteroot';
                intrasiterootValue = result[1];
                if (result[2]) {
                    eidValue = result[2].substr(1);
                }
                if (result[3]) {
                    newWindowValue = true;
                }
                legacy = false;
                Phlexible.console.info('intrasiteroot: ' + intrasiterootValue);
                Phlexible.console.info('eid: ' + eidValue);
                Phlexible.console.info('newWindow: ' + newWindowValue);
            }
            else if (result = this.value.match(/^(newWindow;)?([a-z][a-z];)?(http[s]{0,1}:\/\/.*?)$/)) {
                selected = 'url';
                urlValue = result[3];
                if (result[1]) {
                    newWindowValue = true;
                }
                if (result[2]) {
                    targetLanguageValue = result[2].substr(0, 2);
                }
                legacy = false;
                Phlexible.console.info('url: ' + urlValue);
                Phlexible.console.info('newWindow: ' + newWindowValue);
                Phlexible.console.info('target: ' + targetLanguageValue);
            }
            else if (result = this.value.match(/^mailto:(.*)$/)) {
                selected = 'mailto';
                mailtoValue = result[1];
                Phlexible.console.info(mailtoValue);
            }

            if (legacy) {
                // legacy
                if (result = this.value.match(/^id\:(\d+)(;1)?$/)) {
                    selected = 'tid';
                    tidValue = result[1];
                    if (result[2]) {
                        newWindowValue = true;
                    }
                    Phlexible.console.info('[LEGACY] tid: ' + tidValue);
                    Phlexible.console.info('[LEGACY] eid: ' + eidValue);
                    Phlexible.console.info('[LEGACY] newWindow: ' + newWindowValue);
                }
                else if (result = this.value.match(/^sr:(\d+)(;1)?$/)) {
                    selected = 'intrasiteroot';
                    intrasiterootValue = result[1];
                    if (result[2]) {
                        newWindowValue = true;
                    }
                    Phlexible.console.info('[LEGACY] intrasiteroot: ' + intrasiterootValue);
                    Phlexible.console.info('[LEGACY] eid: ' + eidValue);
                    Phlexible.console.info('[LEGACY] newWindow: ' + newWindowValue);
                }
                else if (result = this.value.match(/^(http[s]{0,1}:\/\/.*?)(;1)?(;[a-z][a-z])?$/)) {
                    selected = 'url';
                    urlValue = result[1];
                    if (result[2]) {
                        newWindowValue = true;
                    }
                    if (result[3]) {
                        targetLanguageValue = result[3].substr(1, 2);
                    }
                    Phlexible.console.info('[LEGACY] url: ' + urlValue);
                    Phlexible.console.info('[LEGACY] newWindow: ' + newWindowValue);
                    Phlexible.console.info('[LEGACY] target: ' + targetLanguageValue);
                }
                else if (result = this.value.match(/^mailto:(.*)$/)) {
                    selected = 'mailto';
                    mailtoValue = result[1];
                    Phlexible.console.info('[LEGACY] ' + mailtoValue);
                }
            }
        }

        var typeValues = [];

        if (!this.hideNoLink) {
            typeValues.push(['no', this.strings.link_no_link]);
        }
        if (this.allowed.tid) {
            typeValues.push(['tid', this.strings.link_eid]);
        }
        if (this.allowed.intrasiteroot) {
            typeValues.push(['intrasiteroot', this.strings.link_intrasiteroot]);
        }
        if (this.allowed.url) {
            typeValues.push(['url', this.strings.link_url]);
        }
        if (this.allowed.mailto) {
            typeValues.push(['mailto', this.strings.link_mailto]);
        }

        if (typeValues.length < 2) {
            Ext.MessageBox.alert('Error', 'You have no permission');
        }
        else if (typeValues.length === 2) {
            if (selected === 'no') {
                selected = typeValues[1][0];
            }
        }

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
                            select: {
                                fn: function (c, r, rowIndex) {
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

                                        case 'tid':
                                            formPanel.getComponent(1).show();
                                            formPanel.getComponent(2).hide();
                                            formPanel.getComponent(3).hide();
                                            if (!this.hideNewWindow) formPanel.getComponent(4).show();
                                            formPanel.getComponent(5).hide();
                                            formPanel.getComponent(6).hide();
                                            break;

                                        case 'intrasiteroot':
                                            formPanel.getComponent(1).hide();
                                            formPanel.getComponent(2).show();
                                            formPanel.getComponent(3).hide();
                                            if (!this.hideNewWindow) formPanel.getComponent(4).show();
                                            formPanel.getComponent(5).hide();
                                            formPanel.getComponent(6).hide();
                                            break;

                                        case 'url':
                                            formPanel.getComponent(1).hide();
                                            formPanel.getComponent(2).hide();
                                            formPanel.getComponent(3).show();
                                            if (!this.hideNewWindow) formPanel.getComponent(4).show();
                                            if (!this.hideLanguage) formPanel.getComponent(5).show();
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
                        }
                    },
                    {
                        xtype: 'tidselector',
                        name: 'tid',
                        fieldLabel: this.strings.tid,
                        siteroot_id: this.siteroot_id,
                        hidden: selected !== 'tid',
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
                        hidden: selected !== 'url',
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
                        hidden: selected !== 'tid' && selected !== 'intrasiteroot' && selected !== 'url',
                        width: 300,
                        checked: newWindowValue,
                        hidden: this.hideNewWindow
                    },
                    {
                        xtype: 'iconcombo',
                        hiddenName: 'target_language',
                        fieldLabel: this.strings.language,
                        helpText: this.strings.link_language_help,
                        emptyText: this.strings.linkl_language_empty,
                        hidden: selected !== 'url',
                        width: 300,
                        store: new Ext.data.SimpleStore({
                            fields: ['key', 'title', 'icon'],
                            data: Phlexible.Config.get('set.language.frontend')
                        }),
                        valueField: 'key',
                        displayField: 'title',
                        iconClsField: 'icon',
                        mode: 'local',
                        editable: false,
                        triggerAction: 'all',
                        selectOnFocus: true,
                        value: targetLanguageValue,
                        hidden: this.hideLanguage
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
                handler: function () {
                    this.close();
                },
                scope: this
            },
            {
                text: this.strings.set,
                handler: function () {
                    var formPanel = this.getComponent(0);
                    var values = formPanel.getForm().getValues();
                    var display = '', value = '';

                    if (values.type === 'no') {
                        display = '';
                        value = '';
                    } else if (values.type === 'tid') {
                        var field = formPanel.getComponent(1);
                        if (!field.isValid()) {
                            return;
                        }
                        display = field.getRawValue();
                        value = 'id:' + field.getValue();
                        var n = field.tree.getNodeById(field.getValue());
                        if (n) {
                            value += ',' + n.attributes.eid;
                        }
                        if (formPanel.getComponent(4).getValue()) {
                            value += ';newWindow';
                        }
                    } else if (values.type === 'intrasiteroot') {
                        var field = formPanel.getComponent(2);
                        if (!field.isValid()) {
                            return;
                        }
                        display = field.getRawValue();
                        value = 'sr:' + field.getValue();
                        var n = field.tree.getNodeById(field.getValue());
                        if (n) {
                            value += ',' + n.attributes.eid;
                        }
                        if (formPanel.getComponent(4).getValue()) {
                            value += ';newWindow';
                        }
                    } else if (values.type === 'url') {
                        var field = formPanel.getComponent(3);
                        if (!field.isValid()) {
                            return;
                        }
                        display = value = field.getValue();
                        value = '';
                        if (formPanel.getComponent(4).getValue()) {
                            value += 'newWindow;';
                        }
                        if (formPanel.getComponent(5).getValue()) {
                            value += formPanel.getComponent(5).getValue() + ';';
                        }
                        value += field.getValue();
                    } else if (values.type === 'mailto') {
                        var field = formPanel.getComponent(6);
                        if (!field.isValid()) {
                            return;
                        }
                        display = value = 'mailto:' + field.getValue();
                    } else {
                        return;
                    }

                    Phlexible.console.info(display);
                    Phlexible.console.info(value);
                    this.fireEvent('set', value, display);
                    this.close();
                },
                scope: this
            }
        ];

        Phlexible.elements.LinkWindow.superclass.initComponent.call(this);

        if (selected === 'tid') {
            this.getComponent(0).getComponent(1).tree.loader.addListener({
                load: {
                    fn: function (loader, node) {
                        this.enable();
                    },
                    scope: this
                },
                loadexception: {
                    fn: function (loader, node) {
                        this.enable();
                    },
                    scope: this
                }
            });
        } else if (selected === 'intrasiteroot') {
            this.getComponent(0).getComponent(2).tree.loader.addListener({
                load: {
                    fn: function (loader, node) {
                        this.enable();
                    },
                    scope: this
                },
                loadexception: {
                    fn: function (loader, node) {
                        this.enable();
                    },
                    scope: this
                }
            });
        } else {
            this.enable();
        }

    }
});
