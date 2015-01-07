Ext.provide('Phlexible.problems.model.Problem');

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