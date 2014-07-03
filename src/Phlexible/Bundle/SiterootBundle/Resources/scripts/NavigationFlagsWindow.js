Phlexible.siteroots.NavigationFlagsWindow = Ext.extend(Ext.Window, {
    title: Phlexible.siteroots.Strings.flags,
    strings: Phlexible.siteroots.Strings,
    iconCls: 'p-siteroot-flag-icon',
    width: 400,
    height: 360,
    resizable: false,
    modal: true,
    layout: 'fit',

    flags: 0,
    supports: 0,

    initComponent: function () {
        this.flags = parseInt(this.record.get('flags'), 10);
        this.supports = parseInt(this.record.get('supports'), 10);

        this.items = [
            {
                xtype: 'form',
                bodyStyle: 'padding: 10px',
                border: false,
                items: [
                    {
                        xtype: 'checkboxgroup',
                        fieldLabel: this.strings.flags,
                        hideLabel: true,
                        columns: 1,
                        items: [
                            {
                                name: 'flag_1',
                                boxLabel: this.strings.flag_no_prepend_home,
                                flag: 1,
                                checked: this.flags & 1,
                                disabled: !(this.supports & 1)
                            },
                            {
                                name: 'flag_2',
                                boxLabel: this.strings.flag_append_active,
                                flag: 2,
                                checked: this.flags & 2,
                                disabled: !(this.supports & 2)
                            },
                            {
                                name: 'flag_4',
                                boxLabel: this.strings.flag_include_no_navigation,
                                flag: 4,
                                checked: this.flags & 4,
                                disabled: !(this.supports & 4)
                            },
                            {
                                name: 'flag_8',
                                boxLabel: this.strings.flag_include_restricted,
                                flag: 8,
                                checked: this.flags & 8,
                                disabled: !(this.supports & 8)
                            },
                            {
                                name: 'flag_16',
                                boxLabel: this.strings.flag_include_not_published,
                                flag: 16,
                                checked: this.flags & 16,
                                disabled: !(this.supports & 16)
                            },
                            {
                                name: 'flag_32',
                                boxLabel: this.strings.flag_include_type_full,
                                flag: 32,
                                checked: this.flags & 32,
                                disabled: !(this.supports & 32)
                            },
                            {
                                name: 'flag_64',
                                boxLabel: this.strings.flag_include_type_structure,
                                flag: 64,
                                checked: this.flags & 64,
                                disabled: !(this.supports & 64)
                            },
                            {
                                name: 'flag_128',
                                boxLabel: this.strings.flag_include_type_layout,
                                flag: 128,
                                checked: this.flags & 128,
                                disabled: !(this.supports & 128)
                            },
                            {
                                name: 'flag_256',
                                boxLabel: this.strings.flag_include_type_teaser,
                                flag: 256,
                                checked: this.flags & 256,
                                disabled: !(this.supports & 256)
                            },
                            {
                                name: 'flag_512',
                                boxLabel: this.strings.flag_include_unique_id,
                                flag: 512,
                                checked: this.flags & 512,
                                disabled: !(this.supports & 512)
                            }
                        ]
                    }
                ]
            }
        ];

        this.buttons = [
            {
                text: this.strings.store,
                handler: function () {
                    var flags = 0;
                    this.getComponent(0).getComponent(0).items.each(function (cb) {
                        if (cb.checked) {
                            flags = flags | cb.flag;
                        }
                    }, this);

                    this.record.set('flags', flags);

                    this.close();
                },
                scope: this
            },
            {
                text: this.strings.cancel,
                handler: function () {
                    this.close();
                },
                scope: this
            }
        ];

        Phlexible.siteroots.NavigationFlagsWindow.superclass.initComponent.call(this);
    }
});
