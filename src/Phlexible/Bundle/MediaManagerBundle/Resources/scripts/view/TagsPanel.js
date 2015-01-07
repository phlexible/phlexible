Ext.provide('Phlexible.mediamanager.TagsPanel');

Phlexible.mediamanager.TagsPanel = Ext.extend(Ext.Panel, {
    strings: {},//Phlexible.tags.Strings,
    title: 'Tags', //Phlexible.tags.Strings.tags,
    iconCls: 'p-tags-component-icon',
    bodyStyle: 'padding: 5px;',

    initComponent: function () {
        this.items = [
            {
                xtype: 'superboxselect',
                hiddenName: 'tags',
                emptyText: 'No tag selected',
                width: 260,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value'],
                    data: [
                        ['test1', 'Test 1'],
                        ['test2', 'Test 2']
                    ]
                }),
                //store: new Ext.data.JsonStore({
                //url: Phlexible.Router.generate('tags_list'),
                //fields: ['key', 'value'],
                //root: 'tags'
                //autoLoad: true
                //}),
                displayField: 'value',
                valueField: 'key',
                mode: 'local',
                //minLength: 2,
                //maxLength: 4,
                allowAddNewData: true,
                stackItems: true,
                extraItemCls: 'x-tag x-tag-red',
                listeners: {
                    newitem: function (bs, v) {
                        var key = v.toLowerCase();
                        var value = v;
                        var newObj = {
                            key: key,
                            value: value
                        };
                        bs.addNewItem(newObj);
                    }
                }
            }
        ];

        this.tbar = [
            {
                text: 'save',
                iconCls: 'p-mediamanager-meta_save-icon',
                handler: function () {
                    var sbs = this.getComponent(0);

                    var data = {
                        list: {},
                        tags: []
                    };

                    var range = sbs.store.getRange();
                    for (var i = 0; i < range.length; i++) {
                        data.list[range[i].data.key] = range[i].data.value;
                    }

                    data.tags = sbs.getValue();

                    Phlexible.console.log(data);
                },
                scope: this
            },
            '->',
            {
                text: 'Tags',
                iconCls: 'p-tags-component-icon',
                disabled: true,
                handler: function () {

                },
                scope: this
            }
        ];

        Phlexible.mediamanager.TagsPanel.superclass.initComponent.call(this);
    },

    loadTags: function (file_id, file_version) {

    },

    empty: function () {

    }
});
