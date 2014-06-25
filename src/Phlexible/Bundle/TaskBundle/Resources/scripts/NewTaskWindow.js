Phlexible.tasks.NewTaskWindow = Ext.extend(Ext.Window, {
    title: Phlexible.tasks.Strings.new_task,
    strings: Phlexible.tasks.Strings,
    iconCls: 'p-task-component-icon',
    width: 400,
    minWidth: 400,
    height: 270,
    minHeight: 270,
    layout: 'fit',
    modal: true,

    payload: {},
    component_filter: null,

    initComponent: function() {

        this.items = [{
            xtype: 'form',
            border: false,
            bodyStyle: 'padding: 5px',
            monitorValid: true,
            items: [{
                xtype: 'combo',
                fieldLabel: this.strings.task,
                hiddenName: 'task',
                anchor: '100%',
                allowBlank: false,
                emptyText: this.strings.please_choose,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('tasks_types'),
                    baseParams: {
                        component: this.component_filter
                    },
                    fields: ['id', 'task'],
                    id: 'id',
                    root: 'tasks'
                }),
                editable: false,
                displayField: 'task',
                valueField: 'id',
                mode: 'remote',
                triggerAction: 'all',
                selectOnFocus: true,
                listeners: {
                    select: {
                        fn: function(combo, r) {
                            if (r) {
                                var c = this.getComponent(0).getComponent(1);
                                c.lastQuery = null;
                                c.store.baseParams.task_class = r.data.id;
                                c.setValue('');
                                c.enable();
                            }
                        },
                        scope: this
                    }
                }
            },{
                xtype: 'combo',
                fieldLabel: this.strings.recipient,
                hiddenName: 'recipient',
                anchor: '100%',
                allowBlank: false,
                disabled: true,
                emptyText: this.strings.please_choose,
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('tasks_recipients'),
                    baseParams: {
                        task_class: false
                    },
                    fields: ['uid', 'username'],
                    id: 'uid',
                    root: 'users'
                }),
                editable: false,
                displayField: 'username',
                valueField: 'uid',
                mode: 'remote',
                triggerAction: 'all',
                selectOnFocus: true
            },{
                xtype: 'textarea',
                anchor: '100%',
                height: 140,
                allowBlank: false,
                fieldLabel: this.strings.comment,
                name: 'comment'
            }],
            bindHandler : function(){
                var valid = true;
                this.form.items.each(function(f){
                    if(!f.isValid(true)){
                        valid = false;
                        return false;
                    }
                });
                if(this.ownerCt.buttons){
                    for(var i = 0, len = this.ownerCt.buttons.length; i < len; i++){
                        var btn = this.ownerCt.buttons[i];
                        if(btn.formBind === true && btn.disabled === valid){
                            btn.setDisabled(!valid);
                        }
                    }
                }
                this.fireEvent('clientvalidation', this, valid);
            }
        }];

        this.buttons = [{
            text: this.strings.send,
            handler: this.onSend,
            formBind: true,
            scope: this
        },{
            text: this.strings.cancel,
            handler: this.close,
            scope: this
        }];

		Phlexible.tasks.NewTaskWindow.superclass.initComponent.call(this);
    },

    onSend: function() {
        if (!this.getComponent(0).form.isValid()) {
            return;
        }

        this.getComponent(0).form.submit({
            url: Phlexible.Router.generate('tasks_create_task'),
            params: {
                payload: Ext.encode(this.payload)
            },
            failure: function(form, action) {
                var result = action.result;

                Ext.MessageBox.alert('Failure', result.msg);
            },
            success: function(form, action) {
                var result = action.result;

                if(result.success) {
                    Phlexible.success(result.msg);
                    this.fireEvent('create', this);
                    this.close();
                }
                else {
                    Ext.MessageBox.alert('Failure', result.msg);
                }
            },
            scope: this
        });
    }
});
