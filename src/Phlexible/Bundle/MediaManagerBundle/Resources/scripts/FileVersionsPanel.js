/**
 * Create a DragZone instance for our JsonView
 */
ImageDragZone = function(view, config){
    this.view = view;
    ImageDragZone.superclass.constructor.call(this, view.getEl(), config);
};
Ext.extend(ImageDragZone, Ext.dd.DragZone, {
    // We don't want to register our image elements, so let's
    // override the default registry lookup to fetch the image
    // from the event instead
    getDragData : function(e){
        var target = e.getTarget('.version-wrap');
        if(target){
            var view = this.view;
            if(!view.isSelected(target)){
                view.onClick(e);
            }
            var selNodes = view.getSelectedNodes();
            var dragData = {
                nodes: selNodes
            };
            if(selNodes.length == 1){
                dragData.ddel = target;
                dragData.single = true;
            }else{
                var div = document.createElement('div'); // create the multi element drag "ghost"
                div.className = 'multi-proxy';
                for(var i = 0, len = selNodes.length; i < len; i++){
                    div.appendChild(selNodes[i].firstChild.firstChild.cloneNode(true)); // image nodes only
                    if((i+1) % 3 === 0){
                        div.appendChild(document.createElement('br'));
                    }
                }
                var count = document.createElement('div'); // selected image count
                count.innerHTML = i + ' images selected';
                div.appendChild(count);

                dragData.ddel = div;
                dragData.multi = true;
            }
            return dragData;
        }
        return false;
    },

    // this method is called by the TreeDropZone after a node drop
    // to get the new tree node (there are also other way, but this is easiest)
    getTreeNode : function(){
        var treeNodes = [];
        var nodeData = this.view.getRecords(this.dragData.nodes);
        for(var i = 0, len = nodeData.length; i < len; i++){
            var data = nodeData[i].data;
            treeNodes.push(new Ext.tree.TreeNode({
                text: data.name,
                icon: '../view/'+data.url,
                data: data,
                leaf:true,
                cls: 'image-node'
            }));
        }
        return treeNodes;
    },

    // the default action is to "highlight" after a bad drop
    // but since an image can't be highlighted, let's frame it
    afterRepair:function(){
        for(var i = 0, len = this.dragData.nodes.length; i < len; i++){
            Ext.fly(this.dragData.nodes[i]).frame('#8db2e3', 1);
        }
        this.dragging = false;
    },

    // override the default repairXY with one offset for the margins and padding
    getRepairXY : function(e){
        if(!this.dragData.multi){
            var xy = Ext.Element.fly(this.dragData.ddel).getXY();
            xy[0]+=3;xy[1]+=3;
            return xy;
        }
        return false;
    }
});

Phlexible.mediamanager.FileVersionsTemplate = new Ext.XTemplate(
    '<tpl for=".">',
        '<div class="version-wrap" id="version-{version}">',
            '<div class="thumb"><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.id, template_key: \"_mm_medium\", file_version: values.version})]}" width="48" height="48"></div>',
            '<div class="text">',
                '<span><b qtip="{name}">{[values.name.shorten(25)]}</b></span><br />',
                '<span>[v{version}] {[Phlexible.documenttypes.DocumentTypes.getText(values.document_type_key)]}, {[Phlexible.Format.size(values.size)]}</span><br />',
                //'<span>Create User: {create_user_id}</span><br />',
                '<span>{create_time}</span><br />',
            '</div>',
            '<div class="x-clear"></div>',
        '</div>',
    '</tpl>'
);

Phlexible.mediamanager.FileVersionsPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.mediamanager.Strings.versions,
    strings: Phlexible.mediamanager.Strings,
    iconCls: 'p-mediamanager-version-icon',
    layout: 'fit',
    autoScroll: true,

    file_id: null,
    file_version: null,

    initComponent: function() {
        this.addEvents(
            'versionChange',
            'versionSelect'
        );

        this.contextMenu = new Ext.menu.Menu({
            items: [{
                text: this.strings.download_file_version,
                iconCls: 'p-mediamanager-download-icon',
                handler: function(btn) {
                    this.fireEvent('versionDownload', btn.parentMenu.file_id, btn.parentMenu.file_version);
                },
                scope: this
            }]
        });

        this.items = [{
            xtype: 'dataview',
            cls: 'p-mediamanager-versions-view',
            store: new Ext.data.JsonStore({
                url: Phlexible.Router.generate('mediamanager_file_detail'),
                baseParams: {
                    id: this.file_id
                },
                root: 'detail',
                fields: [
                    'id',
                    'folder_id',
                    'name',
                    'size',
                    'version',
                    'document_type_key',
                    'asset_type',
                    'create_user_id',
                    'create_time'
                ],
                listeners: {
                    load: function(store, records){
                        if (!records.length) {
                            this.setTitle(this.strings.versions);
                        }
                        else {
                            this.setTitle(this.strings.versions + ' [' + records.length + ']');
                            if (this.file_version) {
                                var index = store.find('version', this.file_version);
                                this.getComponent(0).select(index);
                            } else {
                                this.getComponent(0).select(0);
                            }
                        }
                    },
                    scope: this
                }
            }),
            tpl: Phlexible.mediamanager.FileVersionsTemplate,
            autoHeight: true,
            singleSelect: true,
            overClass: 'x-view-over',
            itemSelector: 'div.version-wrap',
            emptyText: 'No versions to display',
            listeners: {
                render: function(c){
                    var dragZone = new ImageDragZone(c, {
                        containerScroll: true,
                        ddGroup: 'versions'
                    });
                },
                click: this.versionSelect,
                dblclick: this.versionSelect,
                contextmenu: this.onContextMenu,
                scope: this.contextMenu
            }
        }];

        this.on({
            destroy: function() {
                this.contextMenu.destroy();
                delete this.contextMenu;
            },
            scope: this
        });

        Phlexible.mediamanager.FileVersionsPanel.superclass.initComponent.call(this);
    },

    versionSelect: function(view, rowIndex, node, e){
        e.stopEvent();

        var r = view.store.getAt(rowIndex);

        var file_id = r.data.id;
        var file_version = r.data.version;
        var file_name = r.data.name;
        var folder_id = r.data.folder_id;
        var document_type_key = r.data.document_type_key;
        var asset_type = r.data.asset_type;

        this.fireEvent('versionSelect', file_id, file_version, file_name, folder_id, document_type_key, asset_type);
    },

    loadFile: function(file_id, file_version) {
        this.file_id = file_id;

        if (file_version) {
            this.file_version = file_version;
        } else {
            this.file_version = null;
        }

        this.getComponent(0).store.baseParams.id = this.file_id;
        this.getComponent(0).store.load();
    },

    empty: function() {
        this.getComponent(0).store.removeAll();
    },

    onContextMenu: function(view, rowIndex, node, event){
        event.stopEvent();

        this.file_id = view.store.getAt(rowIndex).data.id;
        this.file_version = view.store.getAt(rowIndex).data.version;

        var coords = event.getXY();
        this.showAt([coords[0], coords[1]]);
    }
});

Ext.reg('fileversionspanel', Phlexible.mediamanager.FileVersionsPanel);
