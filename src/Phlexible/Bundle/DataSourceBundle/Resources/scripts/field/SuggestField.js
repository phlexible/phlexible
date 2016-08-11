Ext.provide('Phlexible.datasources.field.SuggestField');

Ext.require('Ext.ux.form.SuperBoxSelect');

Phlexible.datasources.field.SuggestField = Ext.extend(Ext.ux.form.SuperBoxSelect, {
    onResize: function (w, h, rw, rh) {
        Phlexible.datasources.field.SuggestField.superclass.onResize.call(this, w, h, rw, rh);

        this.wrap.setWidth(this.width + 20);
    }
});
Ext.reg('datasources-field-suggest', Phlexible.datasources.field.SuggestField);
