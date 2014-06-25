Phlexible.users.options.Theme = Ext.extend(Ext.form.FormPanel, {
    strings: Phlexible.users.Strings,
    title: Phlexible.users.Strings.theme,
    bodyStyle: 'padding: 15px',
    border: false,

    initComponent: function() {
        this.items = new Ext.DataView({
            store: new Ext.data.SimpleStore({
                id: 0,
                fields: ['id', 'name', 'preview'],
                data : Phlexible.Config.get('set.themes')
            }),
            tpl: Phlexible.users.OptionsWindowThemeTemplate,
            autoHeight:true,
            singleSelect: true,
            overClass:'x-view-over',
            itemSelector:'div.thumb-wrap',
            emptyText: 'No themes to display',
            listeners: {
                selectionchange: function(view, nodes) {
					var records = view.getSelectedRecords();
					if (records[0]) {
						var r = records[0];
						this.changeTheme(r.id);
					}
				},
				scope: this
            }
        });

        this.addListener({
            show: function(panel) {
                panel.getComponent(0).select(Phlexible.Config.get('user.property.theme', 'default'), false, true);
            }
        });

        this.buttons = [{
            text: this.strings.save,
            handler: function() {
                var view = this.getComponent(0);
                var records = view.getSelectedRecords();
                if(records[0])
                {
                    var r = records[0];
                    if (Phlexible.Config.get('user.property.theme', 'default') != r.id) {
                        Ext.Ajax.request({
                            url: Phlexible.Router.generate('users_options_savedetails', {theme: r.id}),
                            success: function(response){
                                var data = Ext.decode(response.responseText);
                                if (data.success) {
                                    Phlexible.Config.set('user.property.theme', this.getComponent(0).getSelectedRecords()[0].id);

                                    this.fireEvent('cancel');
                                }
                                else {
                                    Ext.MessageBox.alert('Failure', data.msg);
                                }
                            },
                            scope: this
                        });
                    }
                }
            },
            scope: this
        },{
            text: this.strings.cancel,
            handler: function() {
                var view = this.getComponent(0);
                var records = view.getSelectedRecords();
                if (records[0]) {
                    var r = records[0];
                    if (Phlexible.Config.get('user.property.theme', 'default') != r.id) {
                        this.changeTheme(Phlexible.Config.get('user.property.theme', 'default'));
                    }
                }
                this.fireEvent('cancel');
            },
            scope: this
        }];

        Phlexible.users.options.Theme.superclass.initComponent.call(this);
    },

    changeTheme: function(themeName) {
                    // cleanup changed theme
//                    var values = this.layout.activeItem.form.getValues();
//                    if(values.theme != Phlexible.Config.get('user.property.theme', 'default)) {
//                        this.changeTheme(Phlexible.Config.get('user.property.theme', 'default));
//                    }

        Phlexible.console.log(themeName);
        Ext.util.CSS.removeStyleSheet('theme');
        if (themeName != 'default') {
            var theme = Phlexible.component('/phlexiblegui/scripts/ext-2.3.0/resources/css/xtheme-' + themeName + '.css');
            Ext.util.CSS.swapStyleSheet('theme', theme);
        }
    }
});

Ext.reg('usersoptionstheme', Phlexible.users.options.Theme);
