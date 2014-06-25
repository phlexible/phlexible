Ext.namespace(
    'Phlexible.gui.menuhandle.handle',
    'Phlexible.gui.util'
);

Phlexible.gui.ComponentRecord = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'classname', type: 'string'},
    {name: 'path', type: 'string'},
    {name: 'package', type: 'string'},
    {name: 'icon', type: 'string'},
]);

Phlexible.gui.Actions = new Ext.Window({
    title: Phlexible.gui.Strings.menu,
    width: 400,
    height: 72,
    resizable: false,
    modal: true,
    bodyStyle: 'padding: 10px;',
    closeAction: 'hide',
    items: [{
        xtype: 'combo',
        width: 360,
        store: new Ext.data.SimpleStore({
            fields: ['text', 'iconCls', 'handler', 'menu'],
            sortInfo: {field: 'text', direction: 'ASC'}
        }),
        editable: true,
        typeAhead: true,
        displayField: 'text',
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus: false,
        forceSelection: true,
        anchor: '-10',
        tpl: '<tpl for="."><div class="x-combo-list-item">{[Phlexible.inlineIcon(values.iconCls)]} {text}</div></tpl>',
        listeners: {
            select: function(c, r) {
                if (!r || !r.data.handler || !r.data.menu) {
                    return;
                }

                r.data.handler(r.data.menu);
                Phlexible.gui.Actions.hide();
            }
        }
    }],
    listeners: {
        hide: function(c) {
            c.getComponent(0).reset();
        },
        show: function(c) {
            c.getComponent(0).focus();
        }
    }
});

Phlexible.globalKeyMap.accessKey({key:'y', alt: true}, function() {
    Phlexible.gui.Actions.show();
});
