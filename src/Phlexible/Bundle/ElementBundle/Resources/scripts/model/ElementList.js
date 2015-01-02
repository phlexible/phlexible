Ext.ns('Phlexible.elements.model');

Phlexible.elements.model.ElementList = Ext.data.Record.create([
    {name: 'tid', type: 'int'},
    {name: 'teaser_id', type: 'int'},
    {name: '_type', type: 'string'},
    {name: 'element_type', type: 'string'},
    {name: 'element_type_id', type: 'int'},
    {name: 'navigation', type: 'bool'},
    {name: 'restricted', type: 'bool'},
    {name: 'icon', type: 'string'},
    {name: 'eid', type: 'int'},
    {name: 'title', type: 'string'},
    {name: 'create_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'publish_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'custom_date', type: 'string'},//, dateFormat: 'Y-m-d H:i:s'},
    {name: 'sort', type: 'int'},
    {name: 'version_latest', type: 'int'},
    {name: 'version_online', type: 'int'},
    {name: 'status', type: 'string'},
    {name: 'rights'},
    {name: 'qtip', type: 'string'}
]);
