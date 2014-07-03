/*jsl:ignoreall*/
/**
 * Ext.ux.SpinnerPlugin
 *
 * @author  Steven Chim
 * @version SpinnerPlugin.js 2008-01-10 v0.1
 *
 * @class Ext.ux.SpinnerPlugin
 * @description: Spinner plugin for textfield component (textfield, numberfield, datefield, timefield)
 */

Ext.ux.SpinnerPlugin = function (config) {
    Ext.apply(this, config);
};

Ext.ux.SpinnerPlugin.prototype = {

    init: function (field) {
        this.field = field;

        if (field.rendered) {
            this.initSpinner();
        } else {
            field.on('render', this.initSpinner, this);
        }

        if (this.strategy == undefined) {
            var xtype = field.getXType();
            var config = Ext.apply({});

            switch (xtype) {
                case "datefield":
                    if (field.format) config.format = field.format;

                    this.strategy = new Ext.ux.form.Spinner.DateStrategy(config);
                    break;
                case "timefield":
                    if (field.format) config.format = field.format;

                    this.strategy = new Ext.ux.form.Spinner.TimeStrategy(config);
                    break;
                case "numberfield":
                    if (field.maxValue) config.maxValue = field.maxValue;
                    if (field.minValue) config.minValue = field.minValue;

                    this.strategy = new Ext.ux.form.Spinner.NumberStrategy(config);
                    break;
                default:
                    this.strategy = new Ext.ux.form.Spinner.NumberStrategy();
            }
        }
    },

    //private
    initSpinner: function () {
        this.keyNav = new Ext.KeyNav(this.field.getEl(), {
            "up": function (e) {
                this.onSpinUp(this.field);
            },

            "down": function (e) {
                this.onSpinDown(this.field);
            },

            "pageUp": function (e) {
                this.onSpinUpAlternate(this.field);
            },

            "pageDown": function (e) {
                this.onSpinDownAlternate(this.field);
            },

            scope: this
        });

        if (this.strategy == undefined) {
            this.strategy = new Ext.ux.form.Spinner.NumberStrategy();
        }
    },

    //private
    onSpinUp: function () {
        if (Ext.EventObject.shiftKey == true) {
            this.onSpinUpAlternate();
            return;
        } else {
            this.strategy.onSpinUp(this.field);
        }
    },

    //private
    onSpinDown: function () {
        if (Ext.EventObject.shiftKey == true) {
            this.onSpinDownAlternate();
            return;
        } else {
            this.strategy.onSpinDown(this.field);
        }
    },

    //private
    onSpinUpAlternate: function () {
        this.strategy.onSpinUpAlternate(this.field);
    },

    //private
    onSpinDownAlternate: function () {
        this.strategy.onSpinDownAlternate(this.field);
    }

};