Phlexible.tasks.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.tasks.Strings.tasks,
    cls: 'p-tasks-main-panel',
    iconCls: 'p-task-component-icon',
    layout: 'border',
	border: false,

    params: {},

    loadParams: function(params) {
        if (params.id) {
            this.getComponent(1).taskId = params.id;
            this.getComponent(0).onReset();
            this.getComponent(0).updateFilter();
        }
    },

    initComponent: function() {
        this.items = [{
			xtype: 'tasks-filterpanel',
            region: 'west',
            width: 200,
            collapsible: true,
            listeners: {
                updateFilter: {
                    fn: function(values) {
                        this.getComponent(1).store.baseParams = values;
                        this.getComponent(1).store.reload();
                    },
                    scope: this
                }
            }
        },{
			xtype: 'tasks-tasksgrid',
			region: 'center',
			taskId: this.params.id || false
		}];

        Phlexible.tasks.MainPanel.superclass.initComponent.call(this);
    }
});

Ext.reg('tasks-mainpanel', Phlexible.tasks.MainPanel)