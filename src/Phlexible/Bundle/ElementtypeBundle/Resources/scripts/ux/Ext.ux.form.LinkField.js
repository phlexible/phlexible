Ext.ux.form.LinkField = Ext.extend(Ext.ux.TwinComboBox, {
    minChars: 2,
    //hideTrigger: true,
    trigger2Class: 'p-form-link-trigger',
    displayField: 'title',
    valueField: 'id',
    typeAhead: false,
    listWidth: 300,
    queryDelay: 1000,
    hiddenValue: '',

    allowed: {
        tid: true,
        intrasiteroot: false,
        url: true,
        mailto: true
    },
    elementTypeIds: '',
    language: '',
    siteroot_id: '',
    hideNoLink: false,
    hideNewWindow: false,
    hideLanguage: false,

    initComponent: function () {
        if (this.element) {
            this.siteroot_id = this.element.siteroot_id;
            this.language = this.element.language;
        }

        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('elements_links_search'),
            baseParams: {
                language: this.language,
                siteroot_id: this.siteroot_id,
                allow_tid: this.allowed.tid ? 1 : 0,
                allow_intrasiteroot: this.allowed.intrasiteroot ? 1 : 0,
                element_type_ids: this.elementTypeIds || this.elementTypeIds
            },
            root: 'results',
            totalProperty: 'totalCount',
            id: 'id',
            fields: ['id', 'eid', 'tid', 'title']
        });

        this.addListener({
            beforequery: function (e) {
                if (!this.allowed.tid && !this.allowed.intrasiteroot) {
                    return false;
                }
                if (e.query.match(/^http[s]{0,1}:/) || e.query.match(/^mailto:/)) {
                    e.combo.setValue(e.combo.getRawValue());
                    this.hiddenValue = e.combo.getRawValue();
                    return false;
                }
            },
            scope: this
        });

        if (this.readonly) {
            this.editable = false;
            this.readOnly = true;
            this.hideTrigger = true;
            this.ctCls = 'x-item-disabled';
        }

        Ext.ux.form.LinkField.superclass.initComponent.call(this);
    },

    setElementTypeIds: function (elementTypeIds) {
        this.elementTypeIds = elementTypeIds;
        this.store.baseParams.element_type_ids = elementTypeIds;
    },

    validateValue: function (value) {
        var hiddenValue = this.hiddenValue;

        if (!hiddenValue && (value.match(/^http[s]{0,1}:/) || value.match(/^mailto:(.*)$/))) {
            hiddenValue = value;
        }

        //Phlexible.console.log('value: ' + value);
        //Phlexible.console.log('hiddenValue: ' + hiddenValue);

        if (value && !hiddenValue) {
            this.markInvalid(this.invalidText);
            //Phlexible.console.log('value + !hiddenValue');
            return false;
        }

        if (hiddenValue.length < 1 || hiddenValue === this.emptyText) { // if it's blank
            if (this.allowBlank) {
                this.clearInvalid();
                return true;
            } else {
                this.markInvalid(this.blankText);
                return false;
            }
        }

        var dummy;
        if (hiddenValue.match(/^id:\d+(,\d+)?(;newWindow)?$/)) {
            return this.allowed.tid;
        }
        if (hiddenValue.match(/^sr:\d+(,\d+)?(;newWindow)?$/)) {
            return this.allowed.intrasiteroot;
        }
        dummy = hiddenValue.match(/^(newWindow;)?([a-z][a-z];)?(http[s]{0,1}:\/\/.*)$/);
        if (dummy) {
            return Ext.form.VTypes.url(dummy[3]) && this.allowed.url;
        }
        dummy = hiddenValue.match(/^mailto:(.*)$/);
        if (dummy) {
            return Ext.form.VTypes.email(dummy[1]) && this.allowed.mailto;
        }

        return false;
    },

    onSelect: function (record, index) {
        Ext.ux.form.LinkField.superclass.onSelect.call(this, record, index);

        this.hiddenValue = this.getValue();
    },

    onClear: function () {
        Ext.ux.form.LinkField.superclass.onClear.call(this);

        this.hiddenValue = this.getValue();
    },

    onTriggerClick: function (e, el) {
        if (this.disabled || this.readonly) {
            return;
        }

        var w = new Phlexible.elements.LinkWindow({
            siteroot_id: this.siteroot_id,
            value: this.hiddenValue,
            allowed: this.allowed,
            elementTypeIds: this.elementTypeIds || '',
            hideNoLink: this.hideNoLink,
            hideNewWindow: this.hideNewWindow,
            hideLanguage: this.hideLanguage,
            language: this.language,
            listeners: {
                set: function (value, display) {
                    if (!value && !display) {
                        this.onTrigger1Click();
                        return;
                    }

                    this.hiddenValue = value;
                    this.setValue(value);
                    this.setRawValue(display);

                    this.fireEvent('set', value, display);
                },
                scope: this
            }
        });
        w.show(el);
    }
});

Ext.reg('linkfield', Ext.ux.form.LinkField);