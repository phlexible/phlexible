Phlexible.elements.DeleteInstancesWindow = Ext.extend(Ext.Window, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.delete_instances_header,
    iconCls: 'p-element-delete-icon',
    width: 600,
    height: 300,
    border: true,
    modal: true,
    bodyStyle: 'padding: 10px; background: #DFE8F6;',

    data: [],
    parentId: '',

    initComponent: function(){
        var checkColumn = Ext.grid.CheckboxColumn({
            xheader: this.strings.delete_element,
            dataIndex: 'checked',
            width: 50
        });

        this.items = [{
            height: 50,
            border: false,
            html: this.strings.delete_instances_text
        },{
            xtype: 'grid',
            height: 160,
            border: true,
            autoExpandColumn: 2,
            store: new Ext.data.SimpleStore({
                fields: ['id', 'siteroot', 'parent_title', 'modify_time', 'instance_master', 'checked'],
                data: this.data
            }),
            plugins: [checkColumn],
            columns: [{
                header: this.strings.tid,
                dataIndex: 'id',
                width: 50,
                renderer: function(s, meta, r) {
                    if(r.data.instance_master) {
                        return '<b>' + s + '</b>';
                    }

                    return s;
                }
            },{
                header: this.strings.siteroot_id,
                dataIndex: 'siteroot',
                width: 130
            },{
                header: this.strings.parent_title,
                dataIndex: 'parent_title',
                width: 200
            },{
                header: this.strings.modify_time,
                dataIndex: 'modify_time',
                width: 130
            },
                checkColumn
            ]
        }];

        this.buttons = [{
            text: this.strings.delete_cancel,
            handler: function() {
                this.close();
            },
            scope: this
        },{
            text: this.strings.delete_element,
            iconCls: 'p-element-delete-icon',
            handler: function() {
                var targets = {}, i = 0;

                this.getComponent(1).getStore().each(function(r) {
                    if (r.get('checked')) {
                        targets['id['+i+']'] = r.data.id;
                        i++;
                    }
                }, this);

                this.buttons[0].setText(this.strings.close);
                this.buttons[1].disable();
                this.buttons[1].hide();

                Ext.Ajax.request({
                    method: 'POST',
                    url: Phlexible.Router.generate('elements_tree_delete'),
                    params: targets,
                    success: function(response) {
                        var data = Ext.decode(response.responseText);

                        if (data.success) {
                            this.fireEvent('listReloadNode', this.parentId);
                            this.close();

                            Phlexible.success(data.msg);
                        }
                        else {
                            Ext.MessageBox.alert('Failure', data.msg);
                        }
                    },
                    scope: this
                });
            },
            scope: this
        }];

        Phlexible.elements.DeleteInstancesWindow.superclass.initComponent.call(this);
    }
});
