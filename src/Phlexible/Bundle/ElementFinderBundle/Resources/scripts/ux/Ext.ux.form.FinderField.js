Ext.ux.form.FinderField = Ext.extend(Ext.form.TwinTriggerField, {
    trigger1Class: 'x-form-clear-trigger',
    trigger2Class: 'p-elementfinder-catch-icon',
    hiddenValue: '',
    hideTrigger1: true,

    initComponent: function () {
        if (this.element) {
            this.siteroot_id = this.element.siteroot_id;
            this.language = this.element.language;
        }

        if (this.readonly) {
            this.editable = false;
            this.readOnly = true;
            this.hideTrigger = true;
            this.ctCls = 'x-item-disabled';
        }

        Ext.ux.form.FinderField.superclass.initComponent.call(this);
    },
    reset: Ext.form.Field.prototype.reset.createSequence(function () {
        this.triggers[0].hide();
    }),

    onViewClick: Ext.form.ComboBox.prototype.onViewClick.createSequence(function () {
        this.triggers[0].show();
    }),

    onTrigger2Click: function () {
        this.onTriggerClick();
    },

    onTrigger1Click: function () {
        if (this.disabled) {
            return;
        }
        this.clearValue();
        this.triggers[0].hide();
        this.onClear();
        this.fireEvent('clear', this);
    },

    initValue: function() {
        Ext.ux.form.FinderField.superclass.initValue.call(this);

        this.setHiddenFieldValue(this.hiddenValue);
    },

    setHiddenValue: function(value) {
        this.hiddenValue = value;
        this.setHiddenFieldValue(value);
    },

    setHiddenFieldValue: function(value) {
        if (this.hiddenField) {
            if (value) {
                this.hiddenField.value = Ext.encode(value);
            } else {
                this.hiddenField.value = '';
            }
        }
    },

    setValue: function(v) {
        if (v && !this.readOnly) {
            this.hideTrigger1 = false;

            if (this.triggers) {
                this.triggers[0].show();
            }
        }

        this.setHiddenFieldValue(this.hiddenValue);
    },

    validateValue: function (value) {
        return true;
    },

    onSelect: function (record, index) {
        Ext.ux.form.LinkField.superclass.onSelect.call(this, record, index);

        this.setHiddenValue({
            type: record.data.type,
            tid: record.data.tid,
            eid: record.data.eid
        });
    },

    onClear: function () {
        Ext.ux.form.LinkField.superclass.onClear.call(this);

        this.hiddenValue = this.getValue();
    },

    onTriggerClick: function (e, el) {
        if (this.disabled || this.readonly) {
            return;
        }

        var w = new Phlexible.elementfinder.ElementFinderConfigWindow({
            siterootId: this.siterootId,
            elementtypeIds: this.elementtypeIds,
            inNavigation: this.inNavigation,
            maxDepth: this.maxDepth,
            filter: this.filter,
            listeners: {
                set: function(w, values) {
                    this.setValue('configured');
                    this.hiddenValue = values;
                },
                scope: this
            }
        });
        w.show(el);
    }
});

Ext.reg('finderfield', Ext.ux.form.FinderField);