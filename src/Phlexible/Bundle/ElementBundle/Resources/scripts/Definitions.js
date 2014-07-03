Ext.namespace(
    'Phlexible.elements.menuhandle',
    'Phlexible.elements.accordion',
    'Phlexible.elements.portlet',
    'Phlexible.tasks.Strings'
);

Phlexible.EntryManager.register('elements', function (params) {
    return {
        handler: Phlexible.LoadHandler.handlePanel,
        identifier: 'Phlexible_elements_MainPanel_' + params.title,
        handleTarget: Phlexible.elements.MainPanel,
        params: params
    };
});

Phlexible.elements.STATUS_ONLINE = 'online';
Phlexible.elements.STATUS_ASYNC = 'async';
Phlexible.elements.STATUS_OFFLINE = '';

Phlexible.elements.Format = {
    elementIcon: function (icon, meta, record) {
        return '<img src="' + icon + '" width="18" height="18" border="0" title="' + record.get('element_type') + '" alt="' + record.get('element_type') + '" />';
    },

    status: function (status, meta, record) {
        return '';
    }
};

Phlexible.elements.ElementListRecord = Ext.data.Record.create([
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

Phlexible.elements.ElementHistoryRecord = Ext.data.Record.create([
    {name: 'type', type: 'string'},
    {name: 'id', type: 'string'},
    {name: 'tid', type: 'string'},
    {name: 'eid', type: 'string'},
    {name: 'version', type: 'string'},
    {name: 'language', type: 'string'},
    {name: 'comment', type: 'string'},
    {name: 'action', type: 'string'},
    {name: 'username', type: 'string'},
    {name: 'create_time', type: 'string'}
]);
