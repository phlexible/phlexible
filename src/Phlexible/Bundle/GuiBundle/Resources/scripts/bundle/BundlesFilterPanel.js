Phlexible.gui.BundlesFilterPanel = Ext.extend(Ext.form.FormPanel, {
    title: Phlexible.gui.Strings.filter,
    strings: Phlexible.gui.Strings,
    bodyStyle: 'padding: 5px;',
    cls: 'p-gui-componentfilter-panel',
    iconCls: 'p-gui-filter-icon',
    autoScroll: true,

    initComponent: function() {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);

        this.items = [{
            xtype: 'panel',
            title: this.strings.filter,
            layout: 'form',
            frame: true,
            collapsible: true,
            defaults: {
                hideLabel: true
            },
            items: [{
                xtype: 'textfield',
                name: 'filter',
                anchor: '-10',
                enableKeyEvents: true,
                listeners: {
                    keyup: function(field, event) {
						if(event.getKey() == event.ENTER){
							this.task.cancel();
							this.updateFilter();
							return;
						}

						this.task.delay(500);
					},
					scope: this
                }
            }]
        },{
            xtype: 'panel',
            title: this.strings['package'],
            layout: 'form',
            frame: true,
            collapsible: true,
            defaults: {
                hideLabel: true
            },
            html: '<div class="loading-indicator">Loading...</div>'
        }];

        this.tbar = ['->',{
            text: this.strings.reset,
            iconCls: 'p-gui-reset-icon',
            disabled: true,
            handler: this.resetFilter,
            scope: this
        }];

        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_bundles_filtervalues'),
            success: this.onLoadFilterValues,
            scope: this
        });

        Phlexible.gui.BundlesFilterPanel.superclass.initComponent.call(this);
    },

    onLoadFilterValues: function(response) {
        var data = Ext.decode(response.responseText);

        if(data.packages && data.packages.length && Ext.isArray(data.packages)) {
            this.getComponent(1).body.update('');
            Ext.each(data.packages, function(item) {
                this.getComponent(1).add({
                    xtype: 'checkbox',
                    name: 'package_' + item.id,
                    boxLabel: item.title,
                    checked: item.checked,
                    listeners: {
                        check: function(cb, checked) {
							this.updateFilter();
						},
						scope: this
                    }
                });
            }, this);
            this.getComponent(1).items.each(function(item) {
                this.form.add(item);
            }, this);
        }

        this.doLayout();
    },

    resetFilter: function(btn) {
        this.form.reset();
        this.updateFilter();
        btn.disable();
    },

    updateFilter: function() {
        this.getTopToolbar().items.items[1].enable();

        var values = this.form.getValues();

        var data = {
            status: [],
            packages: [],
            filter: ''
        };

        for(var key in values) {
            //if (values[key] !== 'on') continue;

            if(key.substr(0,8) == 'package_' && values[key] === 'on') {
                data.packages.push(key.substr(8));
            }
            else if(key === 'filter')
            {
                data.filter = values[key];
            }
        }

        this.fireEvent('updateFilter', data);
    }
});

Ext.reg('gui-bundles-filter', Phlexible.gui.BundlesFilterPanel);