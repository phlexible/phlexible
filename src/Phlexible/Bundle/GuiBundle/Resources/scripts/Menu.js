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
Phlexible.gui.Menu = function(){
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
        "load"
    );

    this.load();
};

Ext.extend(Phlexible.gui.Menu, Ext.util.Observable, {
    items: [],
    loaded: false,

    /**
     * Load the menu entries
     *
     * Fires the "beforeload" event before loading the menu, can be canceled.
     * After successful load the "load" event is fired.
     */
    load: function() {
        if(this.fireEvent('beforeload', this) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_menu'),
            success: this.onLoadSuccess,
            failure: function(){
				Ext.MessageBox.alert('Load error', 'Error loading menu.');
            },
            scope: this
        });
    },

    /**
     * @see load()
     */
    reload: function() {
        this.load();
    },

    /**
     * Called after successful load
     * @param {Object} XMLHttpResponse object
     */
    onLoadSuccess: function(response) {
        var data = Ext.decode(response.responseText);

        this.items = this.iterate(data);;

        this.loaded = true;

        this.fireEvent('load', this);
    },

    getItems: function() {
        return this.items;
    },

    iterate: function(data) {
        var items = [];

        Ext.each(data, function(dataItem) {
            var handlerCls, handler, config;

            handlerCls = Phlexible.evalClassString(dataItem.xtype);

            if (!handlerCls) {
                console.error('Invalid handler classname', dataItem);
                return;
            }

            handler = new handlerCls();

			if (dataItem.parameters) {
				dataItem.setParameters(dataItem.parameters);
			}

            config = handler.createConfig(dataItem);

            if (Ext.isArray(config)) {
                Ext.each(config, function(configItem) {
                    items.push(configItem);
                }, this);
            } else {
                items.push(config);
            }
        }, this);

        return items;
    }
});
