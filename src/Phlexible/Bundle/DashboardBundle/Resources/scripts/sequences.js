Ext.require('Phlexible.dashboard.MainPanel');
Ext.require('Phlexible.gui.util.Frame');

Phlexible.gui.util.Frame.prototype.initStartItems =
    Phlexible.gui.util.Frame.prototype.initStartItems.createSequence(function() {
        this.startItems.push({
            xtype: 'dashboard-main-panel',
            id: 'Phlexible_dashboard_MainPanel',
            header: false
        });
    });
