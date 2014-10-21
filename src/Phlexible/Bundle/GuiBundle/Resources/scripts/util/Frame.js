Phlexible.gui.util.Frame = function () {
    this.addEvents({
        frameready: true
    });
};

Ext.extend(Phlexible.gui.util.Frame, Ext.util.Observable, {
    /**
     * @returns {Phlexible.gui.Menu}
     */
    getMenu: function () {
        return this.menu;
    },

    /**
     * @returns {Phlexible.gui.util.SystemMessage}
     */
    getSystemMessage: function () {
        return this.systemMessage;
    },

    /**
     * @returns {Ext.Viewport}
     */
    getViewport: function () {
        return this.viewport;
    },

    /**
     * @returns {Ext.Toolbar}
     */
    getToolbar: function() {
        return this.getViewport().getComponent(0);
    },

    /**
     * @returns {Ext.TabPanel}
     */
    getMainPanel: function() {
        return this.getViewport().getComponent(1);
    },

    /**
     * @param {String} trayId
     * @returns {Ext.Toolbar.Item}
     */
    getTrayButton: function(trayId) {
        var button = null;
        this.viewport.getComponent(0).items.each(function(item) {
            if (item.trayId === trayId) {
                button = item;
                return false;
            }
        });
        if (button) {
            return button;
        }
        throw new Error('Tray Button ' + trayId + ' not found.');
    },

    /**
     * @returns {Ext.Component}
     */
    getActive: function () {
        return this.getMainPanel().getActiveTab();
    },

    loadConfig: function () {
        // load config
        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_config'),
            success: function(response) {
                var config = Ext.decode(response.responseText);
                Phlexible.Config = new Phlexible.gui.util.Config(config);
                Phlexible.User = new Phlexible.gui.util.User(
                    config['user.id'],
                    config['user.username'],
                    config['user.email'],
                    config['user.firstname'],
                    config['user.lastname'],
                    config['user.properties'],
                    config['user.roles']
                );
            },
            failure: function () {
                Ext.MessageBox.alert('Load error', 'Error loading config.');
            },
            scope: this
        });
    },

    handleConfig: Ext.emptyFn,

    /**
     * @private
     */
    initBody: function () {
        /* remove in 0.8 */
        Ext.isGecko10 = Ext.isGecko && /rv:10\./.test(navigator.userAgent.toLowerCase());

        // find the body element
        var bd = document.body || document.getElementsByTagName('body')[0];
        if (!bd) return;
        if (!Ext.isGecko && !Ext.isGecko10) return;

        bd.className += ' ext-gecko10';
        /*/remove in 0.8 */
    },

    /**
     * @private
     */
    removeSplash: function () {
        Ext.get("loading").fadeOut({remove: true});
    },

    /**
     * @private
     */
    initFrame: function () {
        // init config
        this.initConfig();

        // init body
        this.initBody();

        // initialize menu
        this.initMenu();

        // initialize system message
        this.initSystemMessage();

        // generate viewport
        this.initViewport();

        // do browser check
        this.checkBrowser();

        // remove splashscreen
        this.removeSplash();

        this.fireEvent('frameready', this);
    },

    /**
     * @private
     */
    initConfig: function() {
        var config = Phlexible.config;
        console.log(config);
        delete Phlexible.config;
        Phlexible.Config = new Phlexible.gui.util.Config(config);
        Phlexible.User = new Phlexible.gui.util.User(
            config['user.id'],
            config['user.username'],
            config['user.email'],
            config['user.firstname'],
            config['user.lastname'],
            config['user.properties'],
            config['user.roles']
        );
    },

    /**
     * @private
     */
    checkBrowser: function () {
        var ok = true;
        if (Ext.isGecko) {
            if (Ext.isGecko1 || Ext.isGecko2) {
                ok = false;
            }
        }
        else if (!Ext.isIE && !Ext.isWebKit && !Ext.isOpera) {
            ok = false;
        }

        if (!ok) {
            Ext.MessageBox.alert(Phlexible.gui.Strings.warning, String.format(Phlexible.gui.Strings.check_not_suported, navigator.appName + ' ' + navigator.appVersion));
        }
        else {
            if (Ext.isOpera) {
                Ext.MessageBox.alert(Phlexible.gui.Strings.warning, String.format(Phlexible.gui.Strings.check_no_right_click, navigator.appName + ' ' + navigator.appVersion));
            }
        }
    },

    /**
     * @private
     */
    initMenu: function () {
        this.menu = new Phlexible.gui.Menu({
            listeners: {
                load: function (menu, items) {
                    this.getToolbar().items.each(function(item) {
                        if (item.edge) {
                            return false;
                        }
                        item.destroy();
                    });

                    var edge = 0;
                    Ext.each(items, function (item) {
                        if (item) {
                            if (item === '->') {
                                item = new Ext.Toolbar.Fill();
                            }
                            if (item === '-') {
                                item = new Ext.Toolbar.Separator();
                            }
                            if (item === ' ') {
                                item = new Ext.Toolbar.Spacer();
                            }
                            if (item.bla) {
                                var tb = this.getToolbar(),
                                    td = document.createElement("td");
                                tb.tr.insertBefore(td, tb.tr.childNodes[edge]);
                                item.render(td);
                                tb.items.insert(edge, item);
                                edge++;
                                return;
                            }
                            this.getToolbar().insertButton(edge++, item);
                        }
                    }, this);
                },
                addTrayItem: function(menu, item) {
                    this.getToolbar().add(item);
                },
                scope: this
            }
        });
    },

    /**
     * @private
     */
    initSystemMessage: function () {
        this.systemMessage = new Phlexible.gui.util.SystemMessage();
        this.systemMessage.on('message', function (e) {
            if (e.msg) {
                Phlexible.msg('Event', e.msg);
            }
        });

        // initialize system messages
        this.systemMessage.deactivate.defer(2000, this.systemMessage);

        Phlexible.globalKeyMap.accessKey({key: 'j', alt: true}, this.systemMessage.poll, this.systemMessage);
    },

    /**
     * @private
     */
    initViewport: function () {
        var mainItems = [{
            xtype: 'dashboard-main-panel',
            id: 'Phlexible_dashboard_MainPanel',
            header: false
        }];

        if (Phlexible.entry) {
            var e = Phlexible.EntryManager.get(Phlexible.entry.e);
            if (e) {
                var i = e(Phlexible.entry.p);
                mainItems.push({
                    id: i.identifier,
                    xtype: i.handleTarget,
                    header: false,
                    params: i.params || {}
                })
            }
        }

        this.viewport = new Ext.Viewport({
            layout: 'border',
            items: [
                {
                    xtype: 'toolbar',
                    region: 'north',
                    height: 34,
                    items: [{
                        xtype: 'tbedge'
                    }],
                    listeners: {
                        render: function (c) {
                            c.el.first().setStyle('padding', '4px');
                        }
                    }
                },
                {
                    xtype: 'tabpanel',
                    region: 'center',
                    border: false,
                    tabPosition: 'bottom',
                    items: mainItems,
                    activeTab: 0
                }
            ]
        });

        this.menu.addTrayItem({
            trayId: 'load',
            cls: 'x-btn-icon',
            iconCls: 'p-gui-msg_inactive-icon'
        });

        Ext.Ajax.on("requestcomplete", function (conn, response) {
            this.getTrayButton('load').setIconClass('p-gui-conn_wait-icon');
        }, this);
        Ext.Ajax.on("beforerequest", function (conn) {
            this.getTrayButton('load').setIconClass('p-gui-conn_load-icon');
        }, this);
        Ext.Ajax.on("requestexception", function (conn, response) {
            var title = 'Communication Error';
            var text = 'Status Code: ' + response.status;
            if (response.argument) {
                text += '<br />Request URL: ' + response.argument.url;
            }
            Phlexible.msg(title, text, {
                iconCls: 'p-gui-msg_error-icon',
                autoDestroy: false,
                bodyStyle: 'text-align: left;',
                width: 300
            });
            this.getTrayButton('load').setIconClass('p-gui-conn_error-icon');
            Phlexible.console.log('Status Code: ' + response.status);
            if (response.argument) {
                Phlexible.console.log('Request URL: ' + response.argument.url);
            }
        }, this);
    },

    logout: function () {
        var close = Phlexible.Frame.checkClose();

        if (close !== false) {
            Ext.Msg.alert(close);
            return;
        }

        document.location.href = Phlexible.Router.generate('security_logout');
    },

    /**
     * @private
     */
    checkClose: function () {
        return false;
        var count = Phlexible.Frame.getMainPanel().dirtyTitles.getCount();

        if (count > 1) {
            return "Can't log out, you have several unchanged modifications.";
        } else if (count == 1) {
            var title = Phlexible.Frame.getMainPanel().dirtyTitles.items[0];
            var key = Phlexible.Frame.getMainPanel().dirtyTitles.keys[0];
//                Phlexible.Frame.getMainPanel().activate(key);
            return 'Can\'t log out, you have unchanged modifications in Panel "' + title + '".';
        }

        return false;
    },

    /**
     * @private
     */
    unloadPanel: function (id) {
        var panel = Phlexible.Frame.getMainPanel().getComponent(id);
        if (panel) {
            panel.ownerCt.remove(panel);
            panel.destroy();
        }
        this.getMainPanel().setActiveTab(0);
    },

    /**
     * @private
     */
    loadPanel: function (id, cls, params) {
        if (!params) {
            params = {};
        }

        id = id.replace(/[-.]/g, '_');

        var panel = Phlexible.Frame.getMainPanel().getComponent(id),
            config;

        if (!panel) {
            config = {
                id: id,
                closable: true,
                header: false,
                params: params,
                scope: this
            };

            if (typeof(cls) === 'string') {
                config.xtype = cls;
                panel = this.getMainPanel().add(config);
                this.getMainPanel().setActiveTab(panel);
            } else if (typeof(cls) === 'function') {
                panel = new cls(config);
                this.mainPanel.add(panel);
                this.getMainPanel().setActiveTab(panel);
            } else {
                Phlexible.console.error('loadPanel() received type ' + typeof(cls));
                return;
            }
        } else {
            if (!panel.dirty && panel.loadParams) {
                panel.loadParams(params);
            }
            this.getMainPanel().setActiveTab(id);
        }

        this.viewport.doLayout();

        return panel;
    }
});

Phlexible.gui.util.ToolbarEdge = function () {
    var s = document.createElement("div");
    s.className = "ytb-edge";
    Phlexible.gui.util.ToolbarEdge.superclass.constructor.call(this, s);
    this.edge = true;
};
Ext.extend(Phlexible.gui.util.ToolbarEdge, Ext.Toolbar.Item, {
    enable: Ext.emptyFn,
    disable: Ext.emptyFn,
    focus: Ext.emptyFn
});

Ext.reg('tbedge', Phlexible.gui.util.ToolbarEdge);
