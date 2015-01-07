Ext.provide('Phlexible.tasks.AssignWindow');

Ext.require('Phlexible.tasks.util.TaskManager');

Phlexible.tasks.AssignWindow = Ext.extend(Ext.Window, {
    title: Phlexible.tasks.Strings.assign,
    strings: Phlexible.tasks.Strings,
    width: 400,
    minWidth: 400,
    height: 270,
    minHeight: 270,
    layout: 'fit',
    modal: true,

    payload: {},
    component_filter: null,

    initComponent: function () {

        this.items = [
            {
                xtype: 'form',
                border: false,
                bodyStyle: 'padding: 5px',
                monitorValid: true,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: this.strings.recipient,
                        hiddenName: 'recipient',
                        anchor: '100%',
                        allowBlank: false,
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
                    },
                    {
                        xtype: 'textarea',
                        anchor: '100%',
                        height: 140,
                        allowBlank: false,
                        fieldLabel: this.strings.comment,
                        name: 'comment'
                    }
                ],
                bindHandler: function () {
                    var valid = true;
                    this.form.items.each(function (f) {
                        if (!f.isValid(true)) {
                            valid = false;
                            return false;
                        }
                    });
                    if (this.ownerCt.buttons) {
                        for (var i = 0, len = this.ownerCt.buttons.length; i < len; i++) {
                            var btn = this.ownerCt.buttons[i];
                            if (btn.formBind === true && btn.disabled === valid) {
                                btn.setDisabled(!valid);
                            }
                        }
                    }
                    this.fireEvent('clientvalidation', this, valid);
                }
            }
        ];

        this.buttons = [
            {
                text: this.strings.cancel,
                handler: this.close,
                scope: this
            },
            {
                text: this.strings.assign,
                handler: this.assign,
                formBind: true,
                scope: this
            }
        ];

        Phlexible.tasks.AssignWindow.superclass.initComponent.call(this);
    },

    assign: function () {
        if (!this.getComponent(0).form.isValid()) {
            return;
        }

        var values = this.getComponent(0).getForm().getValues();
        Phlexible.tasks.util.TaskManager.assign(this.taskId, values.recipient, values.comment, function(success, result) {
            if (success && result.success) {
                this.fireEvent('success');
                this.close();
            }
        });
    }
});
