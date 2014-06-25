Phlexible.elementtypes.RootMappingsPanel = Ext.extend(Ext.Panel, {
    strings: Phlexible.elementtypes.Strings,
    border: false,
    layout: 'border',
    style: 'padding-top: 3px;',

    initComponent: function() {
        this.items = [{
            xtype: 'grid',
            region: 'west',
            width: 100,
            style: 'padding: 3px;',
            store: new Ext.data.SimpleStore({
                fields: ['name'],
                data: [['backend'], ['page'], ['navigation'], ['date'], ['forward'], ['custom1'], ['custom2'], ['custom3'], ['custom4'], ['custom5']]
            }),
            columns: [{
                header: this.strings.field,
                dataIndex: 'name'
            }],
            sm: new Ext.grid.RowSelectionModel({
                singleSelect: true,
                listeners: {
                    selectionchange: function(sm) {
                        var r = sm.getSelected(),
                            name, grid;
                        if (!r) {
                            return;
                        }
                        name = r.get('name');
                        if (name === 'backend' || name === 'page' || name === 'navigation') {
                            this.getComponent(1).layout.setActiveItem(1);
                            grid = this.getMappedTitleGrid();
                            grid.getComponent(0).getView().refresh();
                            if (this.mappings[name]) {
                                grid.getComponent(0).getStore().loadData(this.mappings[name].fields);
                                grid.getComponent(1).setValue(this.mappings[name].pattern);
                            } else {
                                grid.getComponent(0).getStore().removeAll();
                                grid.getComponent(1).setValue('');
                            }
                            this.updatePreview(name);
                        } else if (name === 'date') {
                            this.getComponent(1).layout.setActiveItem(2);
                            grid = this.getMappedDateGrid();
                            grid.getComponent(0).getView().refresh();
                            if (this.mappings[name]) {
                                grid.getComponent(0).getStore().loadData(this.mappings[name].fields);
                            } else {
                                grid.getComponent(0).getStore().removeAll();
                            }
                        } else if (name === 'forward') {
                            this.getComponent(1).layout.setActiveItem(3);
                            grid = this.getMappedLinkGrid();
                            grid.getComponent(0).getView().refresh();
                            if (this.mappings[name]) {
                                grid.getComponent(0).getStore().loadData(this.mappings[name].fields);
                            } else {
                                grid.getComponent(0).getStore().removeAll();
                            }
                        } else {
                            this.getComponent(1).layout.setActiveItem(0);
                        }
                    },
                    scope: this
                }
            })
        },{
            region: 'center',
            layout: 'card',
            activeItem: 0,
            style: 'padding: 3px;',
            border: false,
            items: [{
                html: '_choose',
                border: false
            },{
                xtype: 'form',
                border: false,
                labelAlign: 'top',
                items: [{
                    xtype: 'elementtypes-root-mapped-title',
                    height: 200,
                    listeners: {
                        change: function(fields) {
                            var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                            this.mappings[name] = this.mappings[name] || {};
                            this.mappings[name].fields = fields;
                            this.updatePreview(name);
                        },
                        scope: this
                    }
                },{
                    xtype: 'textfield',
                    fieldLabel: this.strings.pattern,
                    name: 'pattern',
                    anchor: '-10',
                    allowBlank: false,
                    listeners: {
                        change: function(field, value) {
                            var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                            this.mappings[name] = this.mappings[name] || {};
                            this.mappings[name].pattern = value;
                            this.updatePreview(name);
                        },
                        scope: this
                    }
                },{
                    xtype: 'textfield',
                    fieldLabel: this.strings.preview,
                    name: 'preview',
                    anchor: '-10',
                    readOnly: true
                }]
            },{
                border: false,
                items: [{
                    xtype: 'elementtypes-root-mapped-date',
                    height: 200,
                    listeners: {
                        change: function(fields) {
                            var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                            this.mappings[name] = this.mappings[name] || {};
                            this.mappings[name].fields = fields;
                        },
                        scope: this
                    }
                }]
            },{
                border: false,
                items: [{
                    xtype: 'elementtypes-root-mapped-link',
                    height: 200,
                    listeners: {
                        change: function(fields) {
                            var name = this.getComponent(0).getSelectionModel().getSelected().get('name');
                            this.mappings[name] = this.mappings[name] || {};
                            this.mappings[name].fields = fields;
                        },
                        scope: this
                    }
                }]
            }]
        }];

        Phlexible.elementtypes.RootMappingsPanel.superclass.initComponent.call(this);
    },

    getMappedTitleGrid: function() {
        return this.getComponent(1).getComponent(1);
    },

    getMappedDateGrid: function() {
        return this.getComponent(1).getComponent(2);
    },

    getMappedLinkGrid: function() {
        return this.getComponent(1).getComponent(3);
    },

    updatePreview: function(name) {
        var fields = this.mappings[name].fields,
            pattern = Phlexible.clone(this.mappings[name].pattern) || '',
            previewField = this.getMappedTitleGrid().getComponent(2);

        Ext.each(fields, function(field) {
           pattern = pattern.replace('$' + field.index, field.field);
        });

        previewField.setValue(pattern);
    },

    empty: function() {
        this.mappings = {};
    },

    loadData: function(mappings) {
        this.mappings = mappings || {};
    },

    loadRoot: function(properties, node) {
        if (node.attributes.type == 'root' && node.attributes.properties.root.type !== 'layout') {
            this.ownerCt.getTabEl(this).hidden = false;
            this.loadData(properties.mappings, node);
        } else {
            this.ownerCt.getTabEl(this).hidden = true;
            this.empty();
        }
    },

    getSaveValues: function() {
        console.log(this.mappings);
        return this.mappings;

        var data = [];

        for (var i=0; i<this.getComponent(1).store.getCount(); i++) {
            var r = this.getComponent(1).store.getAt(i);
            backend.push([r.get('id'), r.get('field'), r.get('prefix'), r.get('suffix')]);
        }

        if (!this.getComponent(3).getValue()) {
            for (var i=0; i<this.getComponent(4).store.getCount(); i++) {
                var r = this.getComponent(4).store.getAt(i);
                page.push([r.get('id'), r.get('field'), r.get('prefix'), r.get('suffix')]);
            }
        }

        if (!this.getComponent(6).getValue()) {
            for (var i=0; i<this.getComponent(7).store.getCount(); i++) {
                var r = this.getComponent(7).store.getAt(i);
                navigation.push([r.get('id'), r.get('field'), r.get('prefix'), r.get('suffix')]);
            }
        }

        var dateRecord = null, timeRecord = null;
        for (var i=0; i<this.getComponent(9).store.getCount(); i++) {
            var r = this.getComponent(9).store.getAt(i);

            if (r.get('type') == 'date') dateRecord = [r.get('id'), r.get('field'), r.get('type')];
            else if (r.get('type') == 'time') timeRecord = [r.get('id'), r.get('field'), r.get('type')];
        }
        if (dateRecord) date.push(dateRecord);
        if (timeRecord) date.push(timeRecord);

        this.getComponent(1).store.commitChanges();
        this.getComponent(4).store.commitChanges();
        this.getComponent(7).store.commitChanges();
        this.getComponent(9).store.commitChanges();

        return {
            backend: backend,
            page: page,
            navigation: navigation,
            date: date
        };
    },

    isValid: function() {
        if (this.mappings.backend && this.mappings.backend.fields && this.mappings.backend.fields.length && this.mappings.backend.pattern) {
            //this.ownerCt.header.child('span').removeClass('error');
            this.setIconClass('p-elementtype-tab_titles-icon');

            return true;
        }
        else {
            //this.ownerCt.header.child('span').addClass('error');
            this.setIconClass('p-elementtype-tab_error-icon');

            return false;
        }
    }
});

Ext.reg('elementtypes-root-mappings', Phlexible.elementtypes.RootMappingsPanel);
