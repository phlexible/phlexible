Phlexible.problems.ProblemsGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.problems.Strings.problems,
    strings: Phlexible.problems.Strings,
    iconCls: 'p-problem-component-icon',

    autoExpandColumn: 1,
    loadMask: true,

    initComponent: function() {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('problems_list'),
            id: 'id',
            fields: Phlexible.problems.model.Problem,
            autoLoad: true
        });

		var expander = new Ext.grid.RowExpander({
			dataIndex: 'hint',
			tpl: new Ext.Template(
				'<p style="padding: 10px;">' + this.strings.solution + ': {hint}</p>'
			)
		});

		this.columns = [
			expander,
		{
            header: this.strings.id,
            dataIndex: 'id',
            hidden: true
        },{
            header: this.strings.problem,
            dataIndex: 'msg',
            width: 400,
            renderer: function(v, md, r) {
                return Phlexible.inlineIcon(r.data.iconCls) + ' ' + v;
            }
        },{
            header: this.strings.severity,
            dataIndex: 'severity',
            width: 80,
            renderer: function(v) {
                return Phlexible.inlineIcon('p-problem-severity_' + v + '-icon') + ' ' + v;
            }
        },{
            header: this.strings.source,
            dataIndex: 'source'
        },{
            header: 'createdAt',
            dataIndex: 'createdAt',
            width: 160
        },{
            header: 'lastCheckedAt',
            dataIndex: 'lastCheckedAt',
            width: 160
        },{
            header: this.strings.link,
            dataIndex: 'link',
            hidden: true
        }];

		this.plugins = [expander];

        Phlexible.problems.ProblemsGrid.superclass.initComponent.call(this);
    }
});
Ext.reg('problems-problemspanel', Phlexible.problems.ProblemsGrid);

