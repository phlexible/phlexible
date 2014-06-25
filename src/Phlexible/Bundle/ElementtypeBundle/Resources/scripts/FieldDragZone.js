Phlexible.elementtypes.FieldDragZone = Ext.extend(Ext.tree.TreeDragZone, {
    xgetDragData: function(e){
        var target = Ext.get(e.getTarget());
//        Phlexible.console.log(target);
        return false;
        e = Ext.EventObject.setEvent(e);
        var target = e.getTarget('.field');
        if(target){
            var view = this.view;
            if(!view.isSelected(target)){
                view.select(target, e.ctrlKey);
            }
            var selNodes = view.getSelectedNodes();
            var dragData = {
                nodes: selNodes
            };
            if(selNodes.length == 1){
                dragData.ddel = target; // the img element
                dragData.single = true;
            }else{
                var div = document.createElement('div'); // create the multi element drag "ghost"
                div.className = 'multi-proxy';
                for(var i = 0, len = selNodes.length; i < len; i++){
                    div.appendChild(selNodes[i].cloneNode(true));
                    if((i+1) % 3 === 0){
                        div.appendChild(document.createElement('br'));
                    }
                }
                dragData.ddel = div;
                dragData.multi = true;
            }
            return dragData;
        }
        return false;
    },

    // this method is called by the TreeDropZone after a node drop
    // to get the new tree node (there are also other way, but this is easiest)
    xgetTreeNode: function(){
        var treeNodes = [];
        var nodeData = this.view.getNodeData(this.dragData.nodes);
        for(var i = 0, len = nodeData.length; i < len; i++){
            var data = nodeData[i];
            treeNodes.push(new Ext.tree.TreeNode({
                text: data.title,
                icon: data.icon,
                data: data,
                leaf: true,
                cls:  data.cls,
                qtip: data.qtip
            }));
        }
        return treeNodes;
    },

    // the default action is to "highlight" after a bad drop
    // but since an image can't be highlighted, let's frame it
    xafterRepair: function(){
        for(var i = 0, len = this.dragData.nodes.length; i < len; i++){
            Ext.fly(this.dragData.nodes[i]).frame('#8db2e3', 1);
        }
        this.dragging = false;
    },

    // override the default repairXY with one offset for the margins and padding
    xgetRepairXY: function(e){
        if(!this.dragData.multi){
            var xy = Ext.Element.fly(this.dragData.ddel).getXY();
//        Phlexible.console.log(xy);
            xy[0]+=3;xy[1]+=3;
            return xy;
        }
        return false;
    }
});
