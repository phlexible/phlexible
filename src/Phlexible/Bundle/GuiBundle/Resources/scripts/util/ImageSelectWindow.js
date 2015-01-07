Ext.provide('Phlexible.gui.util.ImageSelectWindowTemplate');
Ext.provide('Phlexible.gui.util.ImageSelectWindow');

Phlexible.gui.util.ImageSelectWindowTemplate = new Ext.XTemplate(
    '<tpl for=".">',
    '<div class="thumb-wrap" id="image_select_{title}">',
    '<div class="thumb">',
    '<img src="{url}" width="18" height="18">',
    '<span>{title}</span>',
    '</div>',
    '</div>',
    '</tpl>',
    '<div class="x-clear"></div>'
);

Phlexible.gui.util.ImageSelectWindow = Ext.extend(Ext.Window, {

    title: 'Select image',
    cls: 'p-imageselect',
    width: 600,
    minWidth: 600,
    height: 300,
    minHeight: 300,
    autoScroll: true,
    modal: true,
    bodyStyle: 'padding: 3px',

    storeUrl: '',
    storeRoot: 'images',

    value: '',

    initComponent: function () {

        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.thumb-wrap',
                overClass: 'thumb-wrap-over',
                singleSelect: true,
                emptyText: 'No images found',
                deferEmptyText: false,
                autoHeight: true,
                tpl: Phlexible.gui.util.ImageSelectWindowTemplate,
                store: new Ext.data.JsonStore({
                    url: this.storeUrl,
                    root: this.storeRoot,
                    id: 'title',
                    fields: ['title', 'url'],
                    autoLoad: true,
                    listeners: {
                        load: {
                            fn: function (store) {
                                if (this.value) {
                                    var r = store.getById(this.value);

                                    if (r) {
                                        this.getComponent(0).select('image_select_' + r.get('title'));
                                    }
                                }
                            },
                            scope: this
                        }
                    }
                }),
                listeners: {
                    dblclick: {
                        fn: function (view, index) {
                            var r = view.store.getAt(index);

                            this.fireEvent('imageSelect', r.get('title'));

                            this.close();
                        },
                        scope: this
                    },
                    mouseenter: {
                        fn: function (view, index, node) {
                            var r = view.store.getAt(index);
                            Ext.get(this.getTopToolbar().items.items[2].id).update(r.data.title);
                        },
                        scope: this
                    },
                    mouseleave: {
                        fn: function (view, index, node) {
                            Ext.get(this.getTopToolbar().items.items[2].id).update('&nbsp;');
                        },
                        scope: this
                    }
                }
            }
        ];

        this.tbar = [
            {
                xtype: 'textfield',
                emptyText: 'Type to filter...',
                enableKeyEvents: true,
                listeners: {
                    keyup: {
                        buffer: 200,
                        fn: function (field) {
                            var val = field.getValue();

                            this.getComponent(0).store.filterBy(function (record, id) {
                                var regex = new RegExp('.*' + val + '.*');
                                if (record.data.title.match(regex) !== null) {
                                    return(true);
                                }
                                else {
                                    return(false);
                                }
                            });
                        },
                        scope: this
                    }
                }

            },
            '-',
            '&nbsp;'
        ];

        this.buttons = [
            {
                text: 'Cancel',
                handler: this.close,
                scope: this
            },
            {
                text: 'Select',
                handler: function () {
                    var sel = this.getComponent(0).getSelectedRecords();

                    if (sel.length) {
                        var r = sel[0];

                        this.fireEvent('imageSelect', r.get('title'));
                    }

                    this.close();
                },
                scope: this
            }
        ];

        Phlexible.gui.util.ImageSelectWindow.superclass.initComponent.call(this);
    }
});
