Ext.provide('Phlexible.elements.UrlPanel');

Phlexible.elements.UrlPanel = Ext.extend(Ext.Panel, {
    title: '_urls',
    iconCls: 'p-element-url-icon',

    initComponent: function () {
        this.store = new Ext.data.JsonStore({
            fields: ['bla'],
            data: [['bla']]
        });

        this.items = [{
            xtype: 'form',
            border: false,
            bodyStyle: 'padding: 5px',
            items: [{
                xtype: 'textfield',
                fieldLabel: 'url',
                width: 200
            },{
                xtype: 'textfield',
                fieldLabel: 'slug',
                width: 200
            }]
        },{
            xtype: 'panel',
            bodyStyle: 'margin: 5px',
            border: false,
            items: [{
                xtype: 'grid',
                title: '_old_urls',
                store: this.store,
                columns: [{
                    header: 'bla',
                    dataIndex: 'bla'
                }]
            }]
        }];

        Phlexible.elements.UrlPanel.superclass.initComponent.call(this);

        this.element.on('load', this.onLoadElement, this);

        this.store.baseParams = {
            filter_tid: null,
            filter_teaser_id: null
        };

        this.on('show', function () {
            if ((this.store.baseParams.tid != this.element.tid) || (this.store.baseParams.teaser_id != this.element.properties.teaser_id)) {
                this.onRealLoad();
            }
        }, this);
    },

    onLoadElement: function (element) {
        if (element.properties.et_type !== 'full') {
            this.disable();
            //this.hide();
            return;
        }

        this.enable();
        //this.show();

        if (!this.hidden) {
            this.onRealLoad();
        }
    },

    onRealLoad: function () {
        //this.store.load();
        //this.getComponent(0).getComponent(0).setValue(this.element.urls.preview);
        this.getComponent(0).getComponent(1).setValue(this.element.properties.page_title);
    }
});

Ext.reg('elements-urlpanel', Phlexible.elements.UrlPanel);
