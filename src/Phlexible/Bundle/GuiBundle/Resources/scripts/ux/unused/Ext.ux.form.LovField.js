Ext.namespace('Ext.ux.form');

Ext.ux.form.LovField = Ext.extend(Ext.form.TriggerField, {
    defaultAutoCreate: {tag: "input", type: "text", size: "16", style: "cursor:default;", autocomplete: "off"},
    triggerClass: 'x-form-search-trigger',
    validateOnBlur: false,

    // LOV window width
    lovWidth: 300,
    // LOV window height
    lovHeight: 300,
    // LOV window title
    lovTitle: '',
    // Multiple selection is possible?
    multiSelect: false,

    // If this option is true, data store reloads each time the LOV opens
    alwaysLoadStore: false,
    // LOV data provider, intance of Ext.grid.GridPanel or Ext.DataView
    view: {},

    // Which data store field will use for return
    valueField: 'id',
    // Which data store field will use for display
    displayField: 'id',
    // If multiple items are selected, they are joined with this character
    valueSeparator: ',',
    displaySeparator: ',',

    // LOV window configurations
    // autoScroll, layout, bbar and items configurations are not changed by this option
    windowConfig: {},

    showOnFocus: false,

    minItem: 0,
    minItemText: 'The minimum selected item number for this field is {0}',

    maxItem: Number.MAX_VALUE,
    maxItemText: 'The maximum selected item number for this field is {0}',

    // Private
    isStoreLoaded: false,
    // Private
    selections: [],
    // Private
    selectedRecords: [],

    initComponent: function () {
        if ((this.view.xtype != 'grid' && this.view.xtype != 'dataview') &&
            (!(this.view instanceof Ext.grid.GridPanel) && !(this.view instanceof Ext.DataView))) {
            throw "Ext.ux.form.LovField.view option must be instance of Ext.grid.GridPanel or Ext.DataView!";
        }

        Ext.ux.form.LovField.superclass.initComponent.call(this);

        this.viewType = this.view.getXType();
        if (this.viewType == 'grid' && !this.view.sm) {
            this.view.sm = this.view.getSelectionModel();
        }

        if (this.viewType == 'grid') {
            this.view.sm.singleSelect = !this.multiSelect;
        } else {
            this.view.singleSelect = !this.multiSelect;
            this.view.multiSelect = this.multiSelect;
        }

        if (Ext.type(this.displayField) == 'array') {
            this.displayField = this.displayField.join('');
        }
        if (/<tpl(.*)<\/tpl>/.test(this.displayField) && !(this.displayFieldTpl instanceof Ext.XTemplate)) {
            this.displayFieldTpl = new Ext.XTemplate(this.displayField).compile();
        }

        if (Ext.type(this.qtipTpl) == 'array') {
            this.qtipTpl = this.qtipTpl.join('');
        }
        if (/<tpl(.*)<\/tpl>/.test(this.qtipTpl) && !(this.qtipTpl instanceof Ext.XTemplate)) {
            this.qtipTpl = new Ext.XTemplate(this.qtipTpl).compile();
        }

        // If store was auto loaded mark it as loaded
        if (this.view.store.autoLoad) {
            this.isStoreLoaded = true;
        }
    },

    onRender: function (ct, position) {
        if (this.isRendered) {
            return;
        }

        this.readOnly = true;
        if (this.textarea) {
            this.defaultAutoCreate = {tag: "textarea", style: "cursor:default;width:124px;height:65px;", autocomplete: "off"};
            this.displaySeparator = '\n';
        }

        Ext.ux.form.LovField.superclass.onRender.call(this, ct, position);

        this.hiddenField = this.el.insertSibling({tag: 'input', type: 'hidden',
            name: this.el.dom.getAttribute('name'), id: this.id + '-hidden'}, 'before', true);

        // prevent input submission
        this.el.dom.removeAttribute('name');

        if (this.showOnFocus) {
            this.on('focus', this.onTriggerClick, this);
        }

        this.isRendered = true;
    },

    validateValue: function (value) {
        if (Ext.ux.form.LovField.superclass.validateValue.call(this, value)) {
            if (this.selectedRecords.length < this.minItem) {
                this.markInvalid(String.format(this.minItemText, this.minItem));
                return false;
            }
            if (this.selectedRecords.length > this.maxItem) {
                this.markInvalid(String.format(this.maxItemText, this.maxItem));
                return false;
            }
        } else {
            return false;
        }
        return true;
    },

    getSelectedRecords: function () {
        if (this.viewType == 'grid') {
            this.selections = this.selectedRecords = this.view.sm.getSelections();
        } else {
            this.selections = this.view.getSelectedIndexes();
            this.selectedRecords = this.view.getSelectedRecords();
        }

        return this.selectedRecords;
    },

    clearSelections: function () {
        return (this.viewType == 'grid') ? this.view.sm.clearSelections() : this.view.clearSelections();
    },

    select: function (selections) {
        if (this.viewType == 'grid') {
            if (selections[0] instanceof Ext.data.Record) {
                this.view.sm.selectRecords(selections);
            } else {
                this.view.sm.selectRows(selections);

            }
        } else {
            this.view.select(selections);
        }
    },

    onSelect: function () {
        var d = this.prepareValue(this.getSelectedRecords());
        var returnValue = d.hv ? d.hv.join(this.valueSeparator) : '';
        var displayValue = d.dv ? d.dv.join(this.displaySeparator) : '';

        Ext.form.ComboBox.superclass.setValue.call(this, displayValue);

        this.hiddenField.setAttribute('value', returnValue);

        if (Ext.QuickTips) { // fix for floating editors interacting with DND
            Ext.QuickTips.enable();
        }
        this.window.hide();
    },

    prepareValue: function (sRec) {
        this.el.dom.qtip = '';
        if (sRec.length > 0) {
            var vals = {"hv": [], "dv": []};
            Ext.each(sRec, function (i) {
                vals.hv.push(i.get(this.valueField));
                if (this.displayFieldTpl) {
                    vals.dv.push(this.displayFieldTpl.apply(i.data));
                } else {
                    vals.dv.push(i.get(this.displayField));
                }

                if (this.qtipTpl) {
                    this.el.dom.qtip += this.qtipTpl.apply(i.data);
                }

            }, this);
            return vals;
        }
        return false;
    },

    getValue: function () {
        var v = this.hiddenField.value;
        if (v === this.emptyText || v === undefined) {
            v = '';
        }
        return v;
    },

    onTriggerClick: function (e) {
        // Store Load
        if (!this.isStoreLoaded) {
            this.view.store.load();
            this.isStoreLoaded = true;
        } else if (this.alwaysLoadStore === true) {
            this.view.store.reload();
        }

        this.windowConfig = Ext.apply(this.windowConfig,
            {
                title: this.lovTitle,
                width: this.lovWidth,
                height: this.lovHeight,
                autoScroll: true,
                layout: 'fit',
                bbar: [
                    {text: 'Cancel', handler: function () {
                        this.select(this.selections);
                        this.window.hide();
                    }, scope: this},
                    {text: 'Clear', handler: function () {
                        this.clearSelections();
                    }, scope: this},
                    '->',
                    {text: 'Select', handler: this.onSelect, scope: this}
                ],
                items: this.view
            }, {shadow: false, frame: true});

        if (!this.window) {
            this.window = new Ext.Window(this.windowConfig);

            this.window.setPagePosition(e.xy[0] + 16, e.xy[1] + 16);

            this.window.on('beforeclose', function () {
                this.window.hide();
                return false;
            }, this);

            this.window.on('hide', this.validate, this);
            this.view.on('dblclick', this.onSelect, this);
        }

        this.window.show();
    }
});

Ext.reg('xlovfield', Ext.ux.form.LovField);