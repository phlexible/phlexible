Ext.provide('Phlexible.elementtypes.field.SuggestField');

Ext.require('Ext.ux.form.SuperBoxSelect');

Phlexible.elementtypes.field.Suggest = Ext.extend(Ext.ux.form.SuperBoxSelect, {
    onResize: function (w, h, rw, rh) {
        Phlexible.elementtypes.field.Suggest.superclass.onResize.call(this, w, h, rw, rh);

        this.wrap.setWidth(this.width + 20);
    }
});
Ext.reg('elementtypes-field-suggest', Phlexible.elementtypes.field.Suggest);
