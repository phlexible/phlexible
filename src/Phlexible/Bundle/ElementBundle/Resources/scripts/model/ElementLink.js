Ext.provide('Phlexible.elements.model.ElementLink');

Phlexible.elements.model.ElementLink = Ext.data.Record.create([
    {name: 'id', type: 'int'},
    {name: 'language', type: 'string'},
    {name: 'iconCls', type: 'string'},
    {name: 'type', type: 'string'},
    {name: 'title', type: 'string'},
    {name: 'content', type: 'string'},
    {name: 'link'},
    {name: 'raw', type: 'string'}
]);
