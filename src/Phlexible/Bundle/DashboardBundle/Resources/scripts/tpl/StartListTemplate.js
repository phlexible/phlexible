Ext.provide('Phlexible.dashboard.tpl.StartListTemplate');

Phlexible.dashboard.tpl.StartListTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="portlets-wrap" id="{title}">',
    '<div class="x-panel-tl">',
    '<div class="x-panel-tr">',
    '<div class="x-panel-tc">',
    '<div class="x-panel-header x-panel-icon {iconCls}" style="cursor: pointer;" title="Click to add.">',
    '<span class="x-panel-header-text">{title}</span>',
    '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</div>',
    '</tpl>'
);
