Ext.provide('Ext.ux.TwinIconCombo');

/**
 * Ext.ux.IconCombo Extension Class
 *
 * @author  Jozef Sakalos
 * @version 1.0
 *
 * @class Ext.ux.IconCombo
 * @extends Ext.form.ComboBox
 * @constructor
 * @param {Object} config Configuration options
 */
Ext.ux.TwinIconCombo = Ext.extend(Ext.ux.TwinComboBox, {
    iconClsValue: '',

    initComponent: function () {
        if (!this.tpl) {
            this.tpl = '<tpl for="."><div class="x-combo-list-item x-icon-combo-item {' +
                this.iconClsField +
                '}">{' + this.displayField + '}</div></tpl>';
        }

        Ext.ux.TwinIconCombo.superclass.initComponent.call(this);
    },

    onRender: function (ct, position) {
        Ext.ux.TwinIconCombo.superclass.onRender.call(this, ct, position);

        var wrap = this.el.up('div.x-form-field-wrap');
        this.wrap.applyStyles({position: 'relative'});
        this.el.addClass('x-icon-combo-input');
        this.icon = Ext.DomHelper.append(wrap, {
            tag: 'div',
            style: 'position:absolute',
            cls: 'x-icon-combo-icon ' + this.iconClsValue
        });
    },

    setIconCls: function () {
        var index = this.store.find(this.valueField, new RegExp('^' + this.getValue() + '$'));
        var rec = this.store.getAt(index);
//        var rec = this.store.query(this.valueField, this.getValue()).itemAt(0);

        this.iconClsValue = 'x-icon-combo-icon';
        if (rec) {
            this.iconClsValue += ' ' + rec.get(this.iconClsField);
        }

        if (rec && this.icon) {
            this.icon.className = this.iconClsValue;
        }
    },

    clearIconCls: function () {
        this.iconClsValue = '';

        if (this.icon) {
            this.icon.className = this.iconClsValue;
        }
    },
    onTrigger1Click: function () {
        if (this.disabled) {
            return;
        }
        this.clearIconCls();
        Ext.ux.TwinIconCombo.superclass.onTrigger1Click.call(this);
    },
    setValue: function (value) {
        Ext.ux.TwinIconCombo.superclass.setValue.call(this, value);
        if (value) {
            this.setIconCls();
        } else {
            this.clearIconCls();
        }
    }

}); // end of extend

// Register component
Ext.reg('twiniconcombo', Ext.ux.TwinIconCombo);

// end of file
