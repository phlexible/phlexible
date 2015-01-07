Ext.provide('Phlexible.gui.model.Bundle');

Phlexible.gui.model.Bundle = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'classname', type: 'string'},
    {name: 'path', type: 'string'},
    {name: 'package', type: 'string'},
    {name: 'icon', type: 'string'},
]);
