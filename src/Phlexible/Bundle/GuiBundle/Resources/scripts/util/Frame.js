Phlexible.gui.util.Frame = function() {
    this.addEvents({
        frameready: true
    });

    this.initConfig();
};

Ext.extend(Phlexible.gui.util.Frame, Ext.util.Observable, {
    initStage2: function(){
        this.initBody();

        // initialize menu
        this.initMenu();

        // initialize system message
        this.initSystemMessage();

        // initialize main panel
        this.initMainPanel();

        // generate viewport
        this.initViewport();

        // do browser check
        this.checkBrowser();

        // remove splashscreen
        this.removeSplash();

        this.fireEvent('frameready', this);
    },

    initBody: function() {
        /* remove in 0.8 */
        Ext.isGecko10 = Ext.isGecko && /rv:10\./.test(navigator.userAgent.toLowerCase());

        // find the body element
        var bd = document.body || document.getElementsByTagName('body')[0];
        if (!bd) return;
        if (!Ext.isGecko && !Ext.isGecko10) return;

        bd.className += ' ext-gecko10';
        /*/remove in 0.8 */
    },

    checkBrowser: function() {
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

    removeSplash: function() {
        Ext.get("loading").fadeOut({remove: true});
    },

    initConfig: function() {
        // load config
        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_config'),
            success: this.onLoadConfigSuccess,
			failure: function() {
				Ext.MessageBox.alert('Load error', 'Error loading config.');
			},
            scope: this
        });
    },

    onLoadConfigSuccess: function(response) {
        var config = Ext.decode(response.responseText);
        Phlexible.Config = new Phlexible.gui.util.Config(config);
        Phlexible.User = new Phlexible.gui.util.User(config['user.resources']);

        this.initStage2();

        this.handleConfig(Phlexible.Config);


        this.loadFirstPanel();
    },

    handleConfig: Ext.emptyFn,

    getMainPanel: function() {
        return this.mainPanel;
    },

    initMainPanel: function() {
        // generate main panel
        this.mainPanel = new Ext.Panel({
            region: 'center',
            border: false,
            layout: 'card'
        });
    },

    getMenu: function() {
        return this.menu;
    },

    initMenu: function() {
        this.menu = new Phlexible.gui.Menu();
        /*
        this.menu.on({
            load: function() {
                function getItems(menu, items) {
                    Ext.each(menu, function(item) {
                        if (!item.menu && item.handler) {
                            items.push([item.text, item.iconCls, item.handler, item]);
                        }
                        else if (item.menu){
                            getItems(item.menu.items, items);
                        }
                    });
                }

                var items = [];
                getItems(Phlexible.Frame.getMenu().items, items);

                Phlexible.gui.Actions.getComponent(0).getStore().removeAll();
                Phlexible.gui.Actions.getComponent(0).getStore().loadData(items);
            }
        });
        */
    },

    getSystemMessage: function() {
        return this.systemMessage;
    },

    initSystemMessage: function() {
        this.systemMessage = new Phlexible.gui.util.SystemMessage();
        this.systemMessage.on('message', function(e){
            if(e.msg) {
                Phlexible.msg('Event', e.msg);
            }
        });

        Phlexible.globalKeyMap.accessKey({key:'j',alt:true}, this.systemMessage.poll, this.systemMessage);
    },

    getViewport: function() {
        return this.viewport;
    },

    initViewport: function() {
		// initialize system messages
		this.systemMessage.deactivate.defer(2000, this.systemMessage);

		/*
		this.taskbar = new Ext.ux.TaskBar({
			region: 'south',
			height: 30,
			xstartButtonConfig: {
				text: Phlexible.gui.Strings.menu
			},
			xlisteners: {
				render: function(c) {
					Phlexible.globalKeyMap.accessKey({key:'m',alt:true}, function() {
						c.startMenu.show(c.startButton.el, 'bl-tl');
					}, c);
				}
			}
		});
		*/
		this.taskbar = new Ext.Toolbar({
			region: 'south',
			height: 34,
			tasks: {
				add: function(o) {
					var edge = 0;
					Phlexible.Frame.taskbar.items.each(function(item, key) {
						if (item.el && item.el.className && item.el.className.search('ytb-spacer') !== -1) {
							edge = key;
							return false;
						}
						if (item.toggle) {
							item.toggle(false);
						}
					});
					Phlexible.Frame.taskbar.insertButton(edge, {
						text: o.title,
						iconCls: o.iconCls,
						minWidth: 100,
						enableToggle: true,
						allowDepress: false,
						toggleGroup: 'viewport-tasks',
						pressed: true,
						targetPanel: o,
						style: 'padding-right: 10px;',
						handler: function (btn) {
							Phlexible.Frame.taskbar.tasks.setActive(btn.targetPanel.id);

						}
					});
					Phlexible.Frame.mainPanel.getLayout().setActiveItem(o);
					return Phlexible.Frame.taskbar.items.get(0);
				},
				setActive: function(id) {
					Phlexible.Frame.taskbar.items.each(function(item) {
						if (item.targetPanel && item.targetPanel.id === id) {
							item.toggle(true);
							Phlexible.Frame.mainPanel.getLayout().setActiveItem(item.targetPanel);
							return false;
						}
					})
				}
			},
			trayPanel: {
				add: function(o) {
					Phlexible.Frame.taskbar.add(o);
					return Phlexible.Frame.taskbar.items.last();
				}
			},
			items: [
				'->',
			{
				xtype: 'searchbox',
				width: 150
			},
			    '-'
			],
			listeners: {
				render: function(c) {
					c.el.first().setStyle('padding', '4px');
				}
			}
		})

        this.menu.on('load', function(menu) {
            //this.loadMenu(menu);
        }, this.menuPanel);

		this.viewport = new Ext.Viewport({
			layout: 'border',
			items: [{
				xtype: 'toolbar',
				region: 'north',
				height: 34,
				listeners: {
					render: function(c) {
						c.el.first().setStyle('padding', '4px');
					}
				}
			},
				this.mainPanel,
				this.taskbar
			]
		});

		this.loadButton = this.taskbar.trayPanel.add({
			cls: 'x-btn-icon',
			iconCls: 'p-gui-msg_inactive-icon'
		});

		Ext.Ajax.on("requestcomplete", function (conn, response) {
			this.loadButton.setIconClass('p-gui-conn_wait-icon');
		}, this);
		Ext.Ajax.on("beforerequest", function (conn) {
			this.loadButton.setIconClass('p-gui-conn_load-icon');
		}, this);
		Ext.Ajax.on("requestexception", function (conn, response) {
			var title = 'Communication Error';
			var text = 'Status Code: ' + response.status;
			if (response.argument) {
				text += '<br />Request URL: ' + response.argument.url;
			}
			Phlexible.msg(title, text, {
				iconCls:     'p-gui-msg_error-icon',
				autoDestroy: false,
				bodyStyle:   'text-align: left;',
				width:       300
			});
			this.loadButton.setIconClass('p-gui-conn_error-icon');
			Phlexible.console.log('Status Code: ' + response.status);
			if (response.argument) {
				Phlexible.console.log('Request URL: ' + response.argument.url);
			}
		}, this);

		this.menu.on('load', function(menu) {
			var items = [];
			Ext.each(menu.getItems(), function(item) {
				if (item) {
					this.viewport.getComponent(0).add(item);
				}
			}, this)

			return;


			getItems(Phlexible.Frame.getMenu().getItems(), items);

			//this.viewport.getComponent(0).removeAll();
//        this.taskbar.startMenu.clean();

			Ext.each(menu.getItems(), function(item) {
				this.viewport.getComponent(0).add(item);
			}, this);
		}, this);
    },

    loadFirstPanel: function() {
        Phlexible.Frame.loadPanel('Phlexible_dashboard_MainPanel', Phlexible.dashboard.MainPanel, false, false);

        if (Phlexible.entry) {
            var e = Phlexible.EntryManager.get(Phlexible.entry.e);
            if (e) {
                var i = e(Phlexible.entry.p);
                Phlexible.Frame.loadPanel(i.identifier, i.handleTarget, i.params || {}, false);
            }
        }
    },

    getActive: function(){
        return this.mainPanel.getActiveTab();
    },

    logout: function() {
        var close = Phlexible.Frame.checkClose();

        if (close !== false) {
            Ext.Msg.alert(close);
            return;
        }

        document.location.href = Phlexible.Router.generate('security_logout');
    },

    checkClose: function() {
        return false;
        var count = Phlexible.Frame.mainPanel.dirtyTitles.getCount();

        if(count > 1) {
            return "Can't log out, you have several unchanged modifications.";
        } else if(count == 1) {
            var title = Phlexible.Frame.mainPanel.dirtyTitles.items[0];
            var key = Phlexible.Frame.mainPanel.dirtyTitles.keys[0];
//                Phlexible.Frame.getMainPanel().activate(key);
            return 'Can\'t log out, you have unchanged modifications in Panel "' + title + '".';
        }

        return false;
    },

    unloadPanel: function(id){
		var panel = Phlexible.Frame.mainPanel.getComponent(id);
		this.taskbar.taskButtonPanel.removeWin(panel.id);
		this.taskbar.taskButtonPanel.setActiveButton(this.taskbar.taskButtonPanel.items[0].id);
		if(panel) {
			panel.ownerCt.remove(panel);
			panel.destroy();
		}
		var npanel = Phlexible.Frame.mainPanel.getComponent(this.taskbar.taskButtonPanel.items[0].win.id);
		this.mainPanel.getLayout().setActiveItem(npanel);
    },

    loadPanel: function(id, cls, params, noTaskPanelButton) {
		if (!params) {
			params = {};
		}

		id = id.replace(/[-.]/g, '_');

		var panel = Phlexible.Frame.mainPanel.getComponent(id),
			config;

		if (!panel) {
			config = {
				id: id,
				closable: true,
				tools: [{
					id: 'close',
					handler: function(id) {
						Phlexible.Frame.unloadPanel(id);
					}.createDelegate(this, [id], false)
				}],
				header: false,
				params: params,
				/*
				 listeners: {
				 dirty: {
				 fn: function(panel){
				 return;
				 this.dirtyTitles.add(panel.id, panel.title);

				 var domEl = panel.ownerCt.getTabEl(panel);
				 var el = Ext.get(domEl);

				 el.addClass('p-tab-dirty');
				 el.removeClass('x-tab-strip-closable');
				 },
				 scope: this
				 },
				 clean: {
				 fn: function(panel){
				 return;
				 this.dirtyTitles.remove(panel.id);

				 var domEl = panel.ownerCt.getTabEl(panel);
				 var el = Ext.get(domEl);

				 el.removeClass('p-tab-dirty');
				 el.addClass('x-tab-strip-closable');
				 },
				 scope: this
				 }
				 },*/
				scope: this
			};

			if (typeof(cls) === 'string') {
				config.xtype = cls;
				panel = this.mainPanel.add(config);
			} else if (typeof(cls) === 'function') {
				panel = new cls(config);
				this.mainPanel.add(panel);
			} else {
				Phlexible.console.error('loadPanel() received type ' + typeof(cls));
				return;
			}

			if (!noTaskPanelButton) {
				this.taskbar.tasks.add(panel);
			}
		} else {
			if(!panel.dirty && panel.loadParams) {
				panel.loadParams(params);
			}
			this.taskbar.tasks.setActive(id);
		}

		//this.mainPanel.getLayout().setActiveItem(panel);

		this.viewport.doLayout();

		return panel;
    }
});
