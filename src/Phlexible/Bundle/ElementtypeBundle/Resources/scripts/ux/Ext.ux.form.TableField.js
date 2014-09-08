Ext.ux.form.TableField = Ext.extend(Ext.grid.EditorGridPanel, {
    width: 400,
    height: 200,
    isFormField: true,
    //autoHeight: true,

//    viewConfig: {
//        forceFit: true
//    },
    hideHeaders: true,
    cls: 'p-field-table',

    tableRow: {},
    tableNextRow: 0,
    tableNumCols: 0,
    tableDefaultCols: 0,
    tableHasColHeader: true,
    tableHasRowHeader: true,

    addRowAboveText: 'add_row_above',
    addRowBelowText: 'add_row_below',
    deleteRowText: 'delete_row',
    addColumnBeforeText: 'add_column_before',
    addColumnAfterText: 'add_column_after',
    deleteColumnText: 'delete_column',
    headersText: 'headers',
    firstRowIsHeaderText: 'first_row_is_header',
    firstColumnIsHeaderText: 'first_column_is_header',

    initComponent: function () {

        this.border = !this.isSynchronized;

        this.value = this.value || {};

        this.tableHasRowHeader = this.value.hasRowHeader ? true : false;
        this.tableHasColHeader = this.value.hasColHeader ? true : false;

        var meta = this.createMeta(this.value);
        delete this.value;

        this.store = meta.store;
        this.cm = meta.cm;

        this.tbar = [
            {
                qtip: this.addRowAboveText,
                iconCls: 'p-elementtype-table_row_add_before-icon',
                disabled: true,
                handler: this.addRow,
                scope: this
            },
            {
                qtip: this.addRowBelowText,
                iconCls: 'p-elementtype-table_row_add_after-icon',
                handler: this.addRowAfter,
                scope: this
            },
            {
                qtip: this.deleteRowText,
                iconCls: 'p-elementtype-table_row_delete-icon',
                disabled: true,
                handler: this.removeRow,
                disabled: true,
                scope: this
            },
            '-',
            {
                qtip: this.addColumnBeforeText,
                iconCls: 'p-elementtype-table_col_add_before-icon',
                disabled: true,
                handler: this.addCol,
                scope: this
            },
            {
                qtip: this.addColumnAfterText,
                iconCls: 'p-elementtype-table_col_add_after-icon',
                disabled: true,
                handler: this.addColAfter,
                scope: this
            },
            {
                qtip: this.deleteColumnText,
                iconCls: 'p-elementtype-table_col_delete-icon',
                disabled: true,
                handler: this.removeCol,
                scope: this
            },
            '-',
            {
                text: this.headersText,
                menu: {
                    items: [
                        {
                            text: this.firstRowIsHeaderText,
                            checked: this.tableHasColHeader ? true : false,
                            checkHandler: function () {
                                this.tableHasColHeader = !this.tableHasColHeader;

                                var cnt = this.tableNumCols;

                                for (var i = 0; i < cnt; i++) {
                                    var cell = Ext.get(this.view.getCell(0, i));

                                    if (i === 0 && this.tableHasRowHeader) continue;

                                    if (!this.tableHasColHeader) {
                                        cell.removeClass('x-grid3-td-rowheader');
                                    }
                                    else {
                                        cell.addClass('x-grid3-td-rowheader');
                                    }
                                }

                                this.syncHiddenField();
                            },
                            scope: this
                        },
                        {
                            text: this.firstColumnIsHeaderText,
                            checked: this.tableHasRowHeader ? true : false,
                            checkHandler: function () {
                                this.tableHasRowHeader = !this.tableHasRowHeader;

                                var cnt = this.store.getCount();

                                for (var i = 0; i < cnt; i++) {
                                    var cell = Ext.get(this.view.getCell(i, 0));

                                    if (i === 0 && this.tableHasColHeader) continue;

                                    if (!this.tableHasRowHeader) {
                                        cell.removeClass('x-grid3-td-rowheader');
                                    }
                                    else {
                                        cell.addClass('x-grid3-td-rowheader');
                                    }
                                }

                                this.syncHiddenField();
                            },
                            scope: this
                        }
                    ]
                }
            }
        ];

        this.sm = new Ext.grid.CellSelectionModel({
            listeners: {
                selectionchange: function (sm, sel) {
                    var tb = this.getTopToolbar();

                    if (!sel) {
                        tb.items.items[0].disable();
                        tb.items.items[1].enable();
                        tb.items.items[2].disable();

                        tb.items.items[4].disable();
                        tb.items.items[5].enable();
                        tb.items.items[6].disable();

                        return;
                    }

                    tb.items.items[0].enable();
                    tb.items.items[1].enable();
                    tb.items.items[2].setDisabled(this.store.getCount() <= 1);

                    tb.items.items[4].enable();
                    tb.items.items[5].enable();
                    tb.items.items[6].setDisabled(this.tableNumCols <= 1);
                },
                scope: this
            }
        });

        Ext.ux.form.TableField.superclass.initComponent.call(this);
    },

    buildDiffTable: function (input) {
        var data = input.data;
        var hasRowHeaders = input.hasRowHeaders;
        var hasColHeaders = input.hasColHeaders;

        var t = '';

        for (var i = 0; i < data.length; i++) {
            t += '<tr>';
            for (var j = 0; j < data[i].length; j++) {
                if (i === 0 && hasColHeaders || j === 0 && hasRowHeaders) {
                    t += '<th>' + data[i][j] + '</th>';
                }
                else {
                    t += '<td>' + data[i][j] + '</td>';
                }
            }
            t += '</tr>';
        }

        return t;
    },

    onRender: function (ct, position) {
        Ext.ux.form.TableField.superclass.onRender.call(this, ct, position);

        Phlexible.fields.FieldHelper.diff.call(this);

        if (this.isSynchronized) {
            this.el.setStyle('border-width', '1px');
            this.el.setStyle('border-style', 'solid');
            if (this.isMaster) {
                this.el.setStyle('border-color', 'green');
            }
            else {
                this.el.setStyle('border-color', 'red');
                this.disable();
            }
        }

        this.hiddenField = Ext.DomHelper.insertAfter(this.el, {
            tag: 'input',
            name: this.name,
            type: 'hidden'
        }, true);

        this.syncHiddenField();
    },

    addRowAfter: function () {
        this.addRow('after');
    },

    addRow: function (where) {
        var value = this.getValue();
        var tr = Phlexible.clone(this.tableRow);
        var r = new Ext.data.Record();
        var cell = this.getSelectionModel().getSelectedCell();

        if (cell) {
            var rowIndex = cell[0];
            var colIndex = cell[1];
            var mod = 0;
            var jump = 1;
            if (where == 'after') {
                mod = 1;
                jump = 0;
            }

            value.data.splice(rowIndex + mod, 0, tr);
        } else {
            value.data.push(tr);
        }

        var config = this.createMeta(value);
        this.reconfigure(config.store, config.cm);

        if (cell) {
            this.getSelectionModel().select(rowIndex + jump, colIndex);
        }

        this.syncHiddenField();
    },

    removeRow: function () {
        var value = this.getValue();
        var cell = this.getSelectionModel().getSelectedCell();
        if (!cell) return;
        var rowIndex = cell[0];
        var colIndex = cell[1];

        value.data.splice(rowIndex, 1);

        var config = this.createMeta(value);
        this.reconfigure(config.store, config.cm);

        if (cell) {
            var selectRow = rowIndex > 0 ? rowIndex - 1 : 0;
            this.getSelectionModel().select(selectRow, colIndex);
        }

        this.syncHiddenField();
    },

    addColAfter: function () {
        this.addCol('after');
    },

    addCol: function (where) {
        var value = this.getValue();
        var cell = this.getSelectionModel().getSelectedCell();

        if (cell) {
            var rowIndex = cell[0];
            var colIndex = cell[1];

            var mod = 0;
            var jump = 1;
            if (where == 'after') {
                mod = 1;
                jump = 0;
            }

            for (var x = 0; x < value.data.length; x++) {
                value.data[x].splice(colIndex + mod, 0, '');
            }
        } else {
            for (var x = 0; x < value.data.length; x++) {
                value.data[x].push('');
            }
        }

        var config = this.createMeta(value);
        this.reconfigure(config.store, config.cm);

        if (cell) {
            this.getSelectionModel().select(rowIndex, colIndex + jump);
        }

        this.syncHiddenField();
    },

    removeCol: function () {
        var value = this.getValue();
        var cell = this.getSelectionModel().getSelectedCell();
        if (!cell) return;

        var rowIndex = cell[0];
        var colIndex = cell[1];

        for (var x = 0; x <= value.data.length - 1; x++) {
            value.data[x].splice(colIndex, 1);
        }

        var config = this.createMeta(value);
        this.reconfigure(config.store, config.cm);

        if (cell) {
            var selectCol = colIndex > 0 ? colIndex - 1 : 0;
            this.getSelectionModel().select(rowIndex, selectCol);
        }

        this.syncHiddenField();
    },

    createMeta: function (value) {
        if (!value) value = {};

        var data = value.data || [];

        var rowdata = [];
        var numCols = this.tableDefaultCols;

        var fields = [];
        var columns = [];

        if (data && Ext.isArray(data) && data[0] && data[0].length) {
            numCols = data[0].length;
        }

        this.tableRow = [];

        // build cols
        for (var i = 0; i < numCols; i++) {
            fields.push('col' + i);
            this.tableRow.push('');
            columns.push({
                dataIndex: 'col' + i,
                header: 'x',
                editor: new Ext.form.TextField(),
                menuDisabled: true,
                width: 100,
                renderer: function (s, md, r, rowIndex, colIndex) {
                    if (rowIndex === 0 && this.tableHasColHeader) {
                        md.css = 'x-grid3-td-rowheader';
                    }
                    else if (colIndex === 0 && this.tableHasRowHeader) {
                        md.css = 'x-grid3-td-rowheader';
                    }
                    return s;
                }.createDelegate(this)
            });
        }

        var row;

        // build data
        for (var j = 0; j < data.length; j++) {
            row = [];
            this.tableNextRow++;
            for (var i = 0; i < numCols; i++) {
                row.push(data[j][i]);
            }
            rowdata.push(row);
        }

        var store = new Ext.data.SimpleStore({
            fields: fields,
            data: rowdata,
            listeners: {
                update: this.syncHiddenField,
                scope: this
            }
        });

        var cm = new Ext.grid.ColumnModel(columns);

        this.tableNumCols = numCols;

        return {
            store: store,
            cm: cm
        };
    },

    isValid: function () {
        return true;
    },

    getName: function () {
        return this.name;
    },

    validate: function () {
        return true;
    },

    getValue: function () {
        return Ext.decode(this.getRawValue());
    },

    getRawValue: function () {
        return this.hiddenField.dom.value;
    },

    setValue: function (v) {
        this.setRawValue(v);
    },

    setRawValue: function (v) {
        this.store.loadData(v);
    },

    syncHiddenField: function () {
        var range = this.store.getRange();

        var values = [];

        var row;
        for (var i = 0; i < range.length; i++) {
            row = [];
            for (var j in range[i].data) {
                var m = j.match(/col(\d+)/);
                if (m && m[1]) {
                    row[m[1]] = range[i].data[j];
                }
            }
            values.push(row);
        }

        var data = {
            hasRowHeader: this.tableHasRowHeader ? true : false,
            hasColHeader: this.tableHasColHeader ? true : false,
            data: values
        };

        this.hiddenField.dom.value = Ext.encode(data);
    },

    createConfig: function (parentConfig, item, pos, element, repeatableId) {
        if (element.master) {
            element.prototypes.addFieldPrototype(item);
        }

        element.prototypes.incCount(item.ds_id);

        var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

        Ext.apply(config, {
            xtype: 'elementtypes-field-table',
            width: (parseInt(item.configuration.width, 10) || 400),
            height: (parseInt(item.configuration.height, 10) || 200),
            value: item.content,
            tableDefaultCols: 2
        });

        return config;
    }
});
Ext.reg('tablefield', Ext.ux.form.TableField);