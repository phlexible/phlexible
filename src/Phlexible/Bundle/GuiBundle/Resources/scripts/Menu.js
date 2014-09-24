/**
 * @class Phlexible.gui.MenuBar
 * @extends Ext.util.Observable
 * This class represents the menu.
 * <br><br>Usage:<pre><code>
 var menu = new Phlexible.gui.Menu();
 // ...
 // reload menu
 menu.reload();
 * </code></pre>
 * @constructor
 */
Phlexible.gui.Menu = function (config) {
    config = config || {};

    this.addEvents(
        /**
         * @event beforeload
         * Fires before menu is loaded.
         * @param {Phlexible.gui.Menu} menu
         */
        "beforeload",
        /**
         * @event load
         * Fires after menu is loaded.
         * @param {Phlexible.gui.Menu} menu
         */
        "load",
        /**
         * @event addTrayItem
         * Fires after a tray item was added.
         * @param {Phlexible.gui.Menu} menu
         * @param {Object} item
         */
        "addTrayItem"
    );

    if (config.listeners) {
        this.on(config.listeners);
    }

    this.load();
};

Ext.extend(Phlexible.gui.Menu, Ext.util.Observable, {
    items: [],
    trayItems: [],

    loaded: false,

    /**
     * Load the menu entries
     *
     * Fires the "beforeload" event before loading the menu, can be canceled.
     * After successful load the "load" event is fired.
     */
    load: function () {
        if (this.fireEvent('beforeload', this) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_menu'),
            success: this.onLoadSuccess,
            failure: function () {
                Ext.MessageBox.alert('Load error', 'Error loading menu.');
            },
            scope: this
        });
    },

    /**
     * @see load()
     */
    reload: function () {
        this.load();
    },

    /**
     * Called after successful load
     * @param {Object} response
     */
    onLoadSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        this.items = this.iterate(data);

        this.loaded = true;

        this.fireEvent('load', this);
    },

    getItems: function () {
        return this.items;
    },

    addTrayItem: function(item) {
        if (!item.trayId) {
            throw new Error('trayId not set');
        }
        this.trayItems.push(item);
        this.fireEvent('addTrayItem', this, item);
    },

    getTrayItem: function(trayId) {
        Ext.each(this.trayItems, function(item) {
            if (item.trayId === trayId) {
                return item;
            }
        })
        throw new Error('Tray item ' + trayId + ' not found.');
    },

    getTrayItems: function() {
        return this.trayItems;
    },

    /**
     * @param {Array} data
     * @returns {Array}
     * @private
     */
    iterate: function (data) {
        var items = [];

        Ext.each(data, function (dataItem) {
            var handlerCls, handler, config;

            handlerCls = Phlexible.evalClassString(dataItem.xtype);

            if (!handlerCls) {
                console.error('Invalid handler classname', dataItem);
                return;
            }

            if (dataItem.resources) {
                var userResources = Phlexible.Config.get('user.resources'),
                    allowed = false;
                Ext.each(dataItem.resources, function(resource) {
                    if (userResources.indexOf(resource) !== -1) {
                        allowed = true;
                        return false;
                    }
                });
                if (!allowed) {
                    return;
                }
            }

            handler = new handlerCls();

            if (dataItem.parameters) {
                dataItem.setParameters(dataItem.parameters);
            }

            config = handler.createConfig(dataItem);

            if (Ext.isArray(config)) {
                Ext.each(config, function (configItem) {
                    items.push(configItem);
                }, this);
            } else {
                items.push(config);
            }
        }, this);

        return items;
    }
});
