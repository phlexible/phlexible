Ext.provide('Phlexible.gui.util.Dialog');

Phlexible.gui.util.Dialog = Ext.extend(Ext.Window, {
    title: 'Dialog',
    width: 400,
    height: 250,
    minWidth: 400,
    minHeight: 250,
    layout: 'fit',
    modal: true,
    cls: 'p-dialog',
    constrainHeader: true,

    textHeader: 'Header',
    textDescription: 'Description',
    textOk: 'Ok',
    textCancel: 'Cancel',

    extraCls: '',
    iconClsOk: '',
    iconClsCancel: '',

    noFocus: false,

    formItems: {},

    submitUrl: '',
    submitParams: {},

    labelWidth: 60,
    labelAlign: 'left',

    values: {},
    formConfig: {},

    initComponent: function () {
        this.addEvents(
            'beforeSubmit',
            'submit',
            'submitSuccess',
            'submitFailure',
            'success',
            'failure'
        );

        var formItems = [
            {
                xtype: 'panel',
                cls: 'p-header',
                frame: false,
                border: false,
                plain: true,
                header: false,
                html: '<div class="p-header-image"></div>' +
                    '<div class="p-header-title">' + this.textHeader + '</div>' +
                    '<div class="p-header-description">' + this.textDescription + '</div>'


            }
        ];

        var additionalFormItems = this.getFormItems();

        for (var i = 0; i < additionalFormItems.length; i++) {
            if (this.values[additionalFormItems[i].name]) {
                additionalFormItems[i].value = this.values[additionalFormItems[i].name];
            } else if (this.values[additionalFormItems[i].hiddenName]) {
                additionalFormItems[i].value = this.values[additionalFormItems[i].hiddenName];
            }
            formItems.push(additionalFormItems[i]);
        }

        this.items = {
            xtype: 'form',
            frame: false,
            border: false,
            monitorValid: true,
            labelWidth: this.labelWidth,
            labelAlign: this.labelAlign,
            defaultType: 'textfield',
            cls: 'p-dialog-form',
            items: formItems,
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
        };

        Ext.apply(this.items, this.formConfig);

        this.buttons = [
            {
                text: this.textCancel,
                iconCls: this.iconClsCancel,
                handler: function () {
                    this.close();
                },
                scope: this
            },
            {
                text: this.textOk,
                iconCls: this.iconClsOk,
                handler: this.submitForm,
                formBind: true,
                scope: this
            }
        ];

        if (this.extraCls) {
            this.cls += ' ' + this.extraCls;
        }

        this.on('show', function () {
            if (!this.noFocus && this.getComponent(0) && this.getComponent(0).getComponent(1)) {
                this.getComponent(0).getComponent(1).focus(false, 10);
            }
        }, this);

        this.on('move', function () {
            if (!this.noFocus && this.getComponent(0) && this.getComponent(0).getComponent(1)) {
                this.getComponent(0).getComponent(1).focus(false, 10);
            }
        }, this);

        this.on('resize', function () {
            if (!this.noFocus && this.getComponent(0) && this.getComponent(0).getComponent(1)) {
                this.getComponent(0).getComponent(1).focus(false, 10);
            }
        }, this);

        Phlexible.gui.util.Dialog.superclass.initComponent.call(this);

        this.addListener('render', function () {
            var keyMap = this.getKeyMap();
            keyMap.addBinding({
                key: Ext.EventObject.ENTER,
                fn: function (a, e) {
                    e.stopEvent();
                    this.submitForm();
                },
                scope: this
            });
        }, this);
    },

    /**
     * Return the FormPanel Component
     * @return Ext.form.FormPanel
     */
    getForm: function () {
        return this.getComponent(0);
    },

    /**
     * Return the BasicForm Component
     * @return Ext.form.BasicForm
     */
    getBasicForm: function () {
        return this.getComponent(0).getForm();
    },

    /**
     * Return all Form Items for this Dialog
     * @return array
     */
    getFormItems: function () {
        return this.formItems;
    },

    /**
     * Return the Submit URL
     * @return string
     */
    getSubmitUrl: function () {
        return this.submitUrl;
    },

    /**
     * Return the Submit Parameters
     * @return object
     */
    getSubmitParams: function () {
        return this.submitParams;
    },

    submitForm: function () {
        if (!this.getBasicForm().isValid()) {
            return;
        }

        var values = this.getBasicForm().getValues();
        if (!this.fireEvent('beforeSubmit', values, this)) {
            return;
        }

        this.fireEvent('submit', values, this);

        var submitUrl = this.getSubmitUrl();
        if (submitUrl) {
            this.getBasicForm().submit({
                url: submitUrl,
                params: this.getSubmitParams(),
                success: this.onSuccess,
                failure: this.onFailure,
                scope: this
            });
        }
        else {
            this.close();
        }
    },

    onSuccess: function (form, action) {
        if (action.result.success) {
            this.fireEvent('success', this, action.result);
            this.fireEvent('submitSuccess', this, action.result);

            this.close();
        }
        else {
            this.onFailure(form, action);
        }
    },

    onFailure: function (form, action) {
        this.fireEvent('failure', this, action);
        this.fireEvent('submitFailure', this, action);

        if (action.result && action.result.msg) {
            Ext.MessageBox.alert('Failure', action.result.msg);
        }
    }
});
