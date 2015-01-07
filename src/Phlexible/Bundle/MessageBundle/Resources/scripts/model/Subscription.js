Ext.provide('Phlexible.messages.model.Subscription');

Phlexible.messages.model.Subscription = Ext.data.Record.create([
    {name: 'id'},
    {name: 'filter'},
    {name: 'filterId'},
    {name: 'handler'}
]);
