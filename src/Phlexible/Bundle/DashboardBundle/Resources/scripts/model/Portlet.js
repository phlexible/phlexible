Ext.provide('Phlexible.dashboard.model.Portlet');

Phlexible.dashboard.model.Portlet = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'title', type: 'string'},
    {name: 'class', type: 'string'},
    {name: 'mode', type: 'string'},
    {name: 'col', type: 'integer'},
    {name: 'pos', type: 'integer'},
    {name: 'iconCls', type: 'string'},
    {name: 'data'},
    {name: 'settings'},
]);
