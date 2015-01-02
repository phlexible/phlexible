Ext.ns('Phlexible.gui.menuhandle.handle');

Phlexible.gui.menuhandle.handle.Handle = function(config) {
    Ext.apply(this, config);
};

Ext.extend(Phlexible.gui.menuhandle.handle.Handle, Ext.util.Observable, {
    /**
     * @cfg {String} text Display text
     */
    text: '',

    /**
     * @cfg {String} iconCls Icon class
     */
    iconCls: '',

    /**
     * @cfg {String} component Component
     */
    component: '',

    /**
     * Return text
     * @return {String}
     */
    getText: function () {
        return this.text;
    },

    /**
     * Return iconCls
     * @return {String}
     */
    getIconCls: function () {
        return this.iconCls;
    },

    /**
     * Return iconCls
     * @return {String}
     */
    getComponent: function () {
        return this.component;
    },

    /**
     * Handle menu item
     */
    handle: function () {
    },

    /**
     * Create and return config
     *
     * @private
     * @param {Object} data
     * @return {Object}
     */
    createConfig: function (data) {
        var btnConfig = this.createBasicConfig();

        btnConfig.handler = function () {
            this.handle();
        };
        btnConfig.scope = this;

        return btnConfig;
    },

    /**
     * Create and return basic config
     *
     * @private
     * @return {Object}
     */
    createBasicConfig: function () {
        return btnConfig = {
            text: this.getText(),
            iconCls: this.getIconCls()
        };
    }
});