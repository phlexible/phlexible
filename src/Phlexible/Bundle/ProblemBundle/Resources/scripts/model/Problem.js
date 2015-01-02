Ext.namespace('Phlexible.problems.model');

Phlexible.problems.model.Problem = Ext.data.Record.create([
    'id',
    'iconCls',
    'msg',
    'hint',
    'severity',
    'link',
    'source',
    'createdAt',
    'lastCheckedAt'
]);