Ext.ns('Ext.ux');

Ext.provide('Ext.ux.PortalColumn');

Ext.ux.PortalColumn = Ext.extend(Ext.Container, {
    layout: 'anchor',
    autoEl: 'div',
    defaultType: 'portlet',
    cls: 'x-portal-column'
});
Ext.reg('portalcolumn', Ext.ux.PortalColumn);
