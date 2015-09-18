Ext.provide('Phlexible.elements.tab.Preview');

Phlexible.elements.tab.Preview = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.preview.preview,
    strings: Phlexible.elements.Strings.preview,
    iconCls: 'p-element-preview-icon',
    closable: false,
    layout: 'fit',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.on({
            activate: function () {
                this.isActive = true;
                this.updateIframe();
            },
            deactivate: function () {
                this.isActive = false;
            },
            scope: this
        });

        /*this.items = {
         xtype: 'iframepanel',
         defaultSrc: 'about:blank',
         tbar: [{
         // 0
         xtype: 'textfield',
         width: 500,
         readOnly: true,
         value: 'about:blank'
         },{
         // 1
         iconCls: 'p-element-reload-icon',
         handler: function() {
         this.getComponent(0).setSrc();
         },
         scope: this
         }],
         listeners: {
         render: {
         fn: function(c) {
         c.iframe.dom.onload = this.onIframeLoad.createDelegate(this);
         },
         scope: this
         }
         }
         };*/

        this.mode = null;

        this.initToolbar();

        Phlexible.elements.tab.Preview.superclass.initComponent.call(this);
    },

    initToolbar: function() {
        var langBtns = [];
        Ext.each(Phlexible.Config.get('set.language.frontend'), function(lang) {
            langBtns.push({
                text: lang[1],
                iconCls: lang[2],
                langKey: lang[0],
                checked: lang[0] === this.element.language
            });
        }, this);

        this.btnIndex = {
            language: 0,
            preview: 2,
            online: 3,
            horizontal: 5,
            vertical: 6
        };

        this.tbar = [
            {
                // 0
                xtype: 'cycle',
                showText: true,
                items: langBtns,
                listeners: {
                    change: function (cycle, btn) {
                        if (btn.langKey === this.activeLanguage) {
                            return;
                        }

                        this.activeLanguage = btn.langKey;

                        Ext.Ajax.request({
                            url: Phlexible.Router.generate('elements_data_urls'),
                            params: {
                                tid: this.activeTid,
                                language: this.activeLanguage
                            },
                            success: function (response) {
                                var data = Ext.decode(response.responseText);

                                if (data.success) {
                                    this.setPreviewUrl(data.data.preview);
                                    this.setOnlineUrl(data.data.online);

                                    this.updateIframe();
                                }
                            },
                            scope: this
                        });
                    },
                    scope: this
                }
            },
            '-',
            {
                // 2
                text: this.strings.preview,
                iconCls: 'p-element-preview_preview-icon',
                pressed: true,
                enableToggle: true,
                allowDepress: false,
                toggleGroup: 'preview',
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.updateSrc('preview');
                    }
                },
                scope: this
            },
            {
                // 3
                text: this.strings.online,
                iconCls: 'p-element-preview_online-icon',
                enableToggle: true,
                allowDepress: false,
                toggleGroup: 'preview',
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.updateSrc('online');
                    }
                },
                scope: this
            },
            '-',
            {
                // 5
                text: this.strings.horizontal,
                iconCls: 'p-element-preview_horizontal-icon',
                enableToggle: true,
                allowDepress: false,
                toggleGroup: 'preview',
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.updateSrc('horizontal');
                    }
                },
                scope: this
            },
            {
                // 6
                text: this.strings.vertical,
                iconCls: 'p-element-preview_vertical-icon',
                enableToggle: true,
                allowDepress: false,
                toggleGroup: 'preview',
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.updateSrc('vertical');
                    }
                },
                scope: this
            }
        ];
    },

    getQuery: function() {
        return '';
    },

    setPreviewUrl: function(url) {
        this.preview_url = url;
    },

    getPreviewUrl: function() {
        return this.preview_url + this.getQuery();
    },

    setOnlineUrl: function(url) {
        this.online_url = url;
    },

    getOnlineUrl: function() {
        return this.online_url + this.getQuery();
    },

    onLoadElement: function (element) {
        if (element.properties.et_type == Phlexible.elementtypes.TYPE_FULL ||
            element.properties.et_type == Phlexible.elementtypes.TYPE_PART) {
            this.activeTid = this.element.tid;
            this.activeLanguage = this.element.language;

            this.setPreviewUrl(this.element.data.urls.preview);
            this.setOnlineUrl(this.element.data.urls.online);

            var languageBtn = this.getTopToolbar().items.items[this.btnIndex.language];
            languageBtn.menu.items.each(function (item) {
                if (item.langKey === this.activeLanguage) {
                    languageBtn.setActiveItem(item);
                    return false;
                }
            }, this);


            this.enable();
            this.updateIframe();
        } else {
            this.mode = '';
            this.removeAll(true);
            this.disable();
        }
    },

    updateIframe: function () {
        if (this.isActive &&
            (this.activeTid != this.element.id || this.activeLanguage != this.element.language)) {

            var tb = this.getTopToolbar();
            var newMode = false;
            if (this.getOnlineUrl()) {
                tb.items.items[this.btnIndex.online].enable();
                tb.items.items[this.btnIndex.horizontal].enable();
                tb.items.items[this.btnIndex.vertical].enable();
            }
            else {
                tb.items.items[this.btnIndex.online].disable();
                tb.items.items[this.btnIndex.horizontal].disable();
                tb.items.items[this.btnIndex.vertical].disable();
                if (this.mode === 'horizontal' || this.mode === 'vertical') {
                    newMode = 'preview';
                    tb.items.items[this.btnIndex.preview].toggle(true);
                    return;
                }
            }
            if (!this.mode) {
                newMode = 'preview';
            }

            this.updateSrc(newMode);
        }
    },

    updateSingleSrc: function () {
        var url;
        if (this.mode === 'preview') {
            url = this.getPreviewUrl();
        }
        else if (this.mode === 'online') {
            url = this.getOnlineUrl();
        }
        else {
            return;
        }

        var c = this.getComponent(0);
        c.setSrc(url);
        this.currentSingleUrl = url;
    },

    updateDualSrc: function () {
        var c = this.getComponent(0);
        var c1 = c.getComponent(0);
        var c2 = c.getComponent(1);

        c1.setSrc(this.getPreviewUrl());
        c2.setSrc(this.getOnlineUrl());
        this.currentPreviewUrl = this.getPreviewUrl();
        this.currentOnlineUrl = this.getOnlineUrl();
    },

    updateSrc: function (newMode) {
        if ((!newMode || newMode === 'preview' || newMode === 'online') &&
            (this.mode === 'preview' || this.mode === 'online')) {
            if (newMode) {
                this.mode = newMode;
            }
            this.updateSingleSrc();
            return;
        }
        if (((!newMode || newMode === 'horizontal') && this.mode === 'horizontal') ||
            ((!newMode || newMode === 'vertical') && this.mode === 'vertical')) {
            if (newMode) {
                this.mode = newMode;
            }
            this.updateDualSrc();
            return;
        }

        if (newMode) {
            this.mode = newMode;
        }

        this.removeAll(true);
        delete this.singleIframe;
        switch (this.mode) {
            case 'preview':
            case 'online':
                var url = this.getPreviewUrl();
                if (this.mode === 'online') {
                    url = this.getOnlineUrl();
                }
                this.singleIframe = this.add({
                    xtype: 'iframepanel',
                    defaultSrc: url,
                    tbar: [
                        {
                            // 0
                            iconCls: 'p-element-back-icon',
                            handler: function () {
                                this.singleIframe.iframe.dom.contentWindow.history.back();
                            },
                            scope: this
                        },
                        {
                            // 1
                            iconCls: 'p-element-forward-icon',
                            handler: function () {
                                this.singleIframe.iframe.dom.contentWindow.history.forward();
                            },
                            scope: this
                        },
                        {
                            // 2
                            iconCls: 'p-element-home-icon',
                            handler: function () {
                                this.getComponent(0).setSrc(this.currentSingleUrl);
                            },
                            scope: this
                        },
                        {
                            // 3
                            xtype: 'textfield',
                            width: 500,
                            readOnly: true,
                            value: this.getPreviewUrl()
                        },
                        {
                            // 4
                            iconCls: 'p-element-reload-icon',
                            handler: function () {
                                this.getComponent(0).setSrc();
                            },
                            scope: this
                        }
                    ]
                });
                break;

            case 'horizontal':
                this.add({
                    layout: 'border',
                    border: false,
                    items: [
                        {
                            xtype: 'iframepanel',
                            region: 'west',
                            width: '50%',
                            title: this.strings.preview,
                            defaultSrc: this.getPreviewUrl()
                        },
                        {
                            xtype: 'iframepanel',
                            region: 'center',
                            title: this.strings.online,
                            defaultSrc: this.getOnlineUrl()
                        }
                    ]
                });
                break;

            case 'vertical':
                this.add({
                    layout: 'row-fit',
                    border: false,
                    items: [
                        {
                            xtype: 'iframepanel',
                            height: '50%',
                            title: this.strings.preview,
                            defaultSrc: this.getPreviewUrl()
                        },
                        {
                            xtype: 'iframepanel',
                            height: '50%',
                            title: this.strings.online,
                            defaultSrc: this.getOnlineUrl()
                        }
                    ]
                });
                break;
        }

        this.doLayout();

        if (this.singleIframe) {
            this.singleIframe.iframe.dom.onload = this.onIframeLoad.createDelegate(this, [this.singleIframe], true);
        }
    },

    onIframeLoad: function (e, p) {
        var iframe = e.currentTarget;
        var href = iframe.contentDocument.location.href;

        var tb = p.getTopToolbar();
        tb.items.items[3].setValue(href);
    }
});

Ext.reg('elements-tab-preview', Phlexible.elements.tab.Preview);
