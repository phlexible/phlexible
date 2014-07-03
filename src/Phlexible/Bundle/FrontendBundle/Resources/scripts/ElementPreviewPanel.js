Phlexible.frontend.ElementPreviewPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.frontend.Strings.preview,
    strings: Phlexible.frontend.Strings,
    iconCls: 'p-frontend-preview-icon',
    closable: false,
    layout: 'fit',

    initComponent: function () {
        this.element.on({
            load: {
                fn: this.onLoadElement,
                scope: this
            }
        });

        this.on({
            activate: {
                fn: function () {
                    this.isActive = true;
                    this.updateIframe();
                },
                scope: this
            },
            deactivate: {
                fn: function () {
                    this.isActive = false;
                },
                scope: this
            }
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

        var langBtns = [];
        for (var i = 0; i < Phlexible.Config.get('set.language.frontend').length; i++) {
            langBtns.push({
                text: Phlexible.Config.get('set.language.frontend')[i][1],
                iconCls: Phlexible.Config.get('set.language.frontend')[i][2],
                langKey: Phlexible.Config.get('set.language.frontend')[i][0],
                checked: Phlexible.Config.get('set.language.frontend')[i][0] === this.element.language
            });
        }

        this.btnIndex = {
            language: 0,
            preview: 2,
            online: 3,
            debug: 4,
            horizontal: 6,
            vertical: 7
        };

        this.tbar = [
            {
                // 0
                xtype: 'cycle',
                showText: true,
                items: langBtns,
                listeners: {
                    change: {
                        fn: function (cycle, btn) {
                            this.activeLanguage = btn.langKey;

                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('frontend_preview_urls'),
                                params: {
                                    tid: this.activeTid,
                                    language: this.activeLanguage
                                },
                                success: function (response) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        this.preview_url = data.data.preview;
                                        this.online_url = data.data.online;
                                        this.debug_url = data.data.debug;

                                        this.updateIframe();
                                    }
                                },
                                scope: this
                            });
                        },
                        scope: this
                    }
                }
            },
            '-',
            {
                // 2
                text: this.strings.preview,
                iconCls: 'p-frontend-preview_preview-icon',
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
                text: this.strings.preview_online,
                iconCls: 'p-frontend-preview_online-icon',
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
            {
                // 4
                text: this.strings.preview_debug,
                iconCls: 'p-frontend-preview_debug-icon',
                enableToggle: true,
                allowDepress: false,
                toggleGroup: 'preview',
                hidden: Phlexible.User.isGranted('debug'),
                toggleHandler: function (btn, state) {
                    if (state) {
                        this.updateSrc('debug');
                    }
                },
                scope: this
            },
            '-',
            {
                // 6
                text: this.strings.preview_horizontal,
                iconCls: 'p-frontend-preview_horizontal-icon',
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
                // 7
                text: this.strings.preview_vertical,
                iconCls: 'p-frontend-preview_vertical-icon',
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

        Phlexible.frontend.ElementPreviewPanel.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        if (element.properties.et_type == Phlexible.elementtypes.TYPE_FULL ||
            element.properties.et_type == Phlexible.elementtypes.TYPE_PART) {
            this.activeTid = this.element.tid;
            this.activeLanguage = this.element.language;

            this.preview_url = this.element.data.urls.preview;
            this.debug_url = this.element.data.urls.debug;
            this.online_url = this.element.data.urls.online;

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
            if (this.online_url) {
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
            url = this.preview_url;
        }
        else if (this.mode === 'online') {
            url = this.online_url;
        }
        else if (this.mode === 'debug') {
            url = this.debug_url;
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

        c1.setSrc(this.preview_url);
        c2.setSrc(this.online_url);
        this.currentPreviewUrl = this.preview_url;
        this.currentOnlineUrl = this.online_url;
    },

    updateSrc: function (newMode) {
        if ((!newMode || newMode === 'preview' || newMode === 'online' || newMode === 'debug') &&
            (this.mode === 'preview' || this.mode === 'online' || this.mode === 'debug')) {
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
            case 'debug':
                this.singleIframe = this.add({
                    xtype: 'iframepanel',
                    defaultSrc: this[this.mode + '_url'],
                    tbar: [
                        {
                            // 0
                            iconCls: 'p-frontend-back-icon',
                            handler: function () {
                                this.singleIframe.iframe.dom.contentWindow.history.back();
                            },
                            scope: this
                        },
                        {
                            // 1
                            iconCls: 'p-frontend-forward-icon',
                            handler: function () {
                                this.singleIframe.iframe.dom.contentWindow.history.forward();
                            },
                            scope: this
                        },
                        {
                            // 2
                            iconCls: 'p-frontend-home-icon',
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
                            value: this.preview_url
                        },
                        {
                            // 4
                            iconCls: 'p-frontend-reload-icon',
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
                            defaultSrc: this.preview_url
                        },
                        {
                            xtype: 'iframepanel',
                            region: 'center',
                            title: this.strings.preview_online,
                            defaultSrc: this.online_url
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
                            defaultSrc: this.preview_url
                        },
                        {
                            xtype: 'iframepanel',
                            height: '50%',
                            title: this.strings.preview_online,
                            defaultSrc: this.online_url
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

Ext.reg('elements-elementpreviewpanel', Phlexible.frontend.ElementPreviewPanel);
