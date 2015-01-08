Ext.provide('Phlexible.search.field.SearchBox');

Ext.require('Phlexible.search.model.Result');

Phlexible.search.field.SearchBox = Ext.extend(Ext.form.ComboBox, {
    displayField: 'title',
    cls: 'p-searchbox',
    typeAhead: false,
    loadingText: Phlexible.search.Strings.searching,
    width: 150,
    growWidth: false,
    listWidth: 500,
    maxHeight: 500,
    pageSize: 8,
    minChars: 2,
    typeAheadDelay: 500,
    //hideTrigger: true,
    triggerClass: 'x-form-search-trigger',
    itemSelector: 'div.search-item',
    listeners: {
        focus: function (c) {
            if (this.growWidth) {
                this.setWidth(this.growWidth);
            }
        },
        blur: function (c) {
            if (this.growWidth) {
                this.setWidth(this.origWidth);
            }
        },
        beforeselect: function (combo, record) {
            var menu = record.get('menu');

            if (menu && menu.xtype) {
                var xtype = Phlexible.evalClassString(menu.xtype),
                    handler = new xtype();

                if (menu.parameters) {
                    handler.setParameters(menu.parameters);
                }

                handler.handle();
            }

            combo.collapse();
            this.setWidth(this.origWidth);

            return false;
        }
    },
    initComponent: function () {
        this.origWidth = this.width;
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('search_search'),
            root: 'results',
            totalProperty: 'totalCount',
//                id: 'id'
            fields: Phlexible.search.model.Result
        });

        // Custom rendering Template
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="search-item">',
            '<div class="search-result-image">',
            '<img src="{image}" alt="{title}" />',
            '</div>',
            '<div class="search-result-text">',
            '<h3><span>{date:date("Y-m-d H:i:s"}<br />by {author}</span>{title}</h3>',
            '{component}<br />&nbsp;',
            '</div>',
            '<div class="x-clear"">',
            '</div>',
            '</div>',
            '</tpl>'
        );

        Phlexible.search.field.SearchBox.superclass.initComponent.call(this);

        this.on('render', function () {
            Phlexible.globalKeyMap.accessKey({key: 'f', alt: true}, this.focus, this);
        }, this);
    }
});

Ext.reg('searchbox', Phlexible.search.field.SearchBox);
