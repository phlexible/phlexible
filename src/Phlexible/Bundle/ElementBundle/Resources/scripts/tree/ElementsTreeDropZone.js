Ext.provide('Phlexible.elements.ElementsTreeDropZone');

Phlexible.elements.ElementsTreeDropZone = Ext.extend(Ext.tree.TreeDropZone, {
    processDrop: function (targetNode, data, point, dd, e, dropNode) {
        //Phlexible.console.info(targetNode);
        //Phlexible.console.info(data);
        //Phlexible.console.info(dropNode);
        //Phlexible.console.info(e);

        var targetId = targetNode.id;
        var tree = targetNode.getOwnerTree();
        var sourceId, grid;

        if (dropNode) {
            grid = false;
            sourceId = dropNode.id;
        } else if (data.selections) {
            grid = data.grid;
            sourceId = data.selections[0].data.tid;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('tree_move'),
            params: {
                id: sourceId,
                target: targetId
            },
            success: function (response, options) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Failure', data.msg);
                    return;
                }

                var node = tree.getNodeById(data.data.id);
                if (node) {
                    node.parentNode.removeChild(node);
                }

                var newParentNode = tree.getNodeById(data.data.parent_id);

                if (!newParentNode)
                    alert("no new parent node!");

                newParentNode.attributes.children = false;
                newParentNode.reload();

                if (grid) {
                    grid.store.reload();
                }

                //newParentNode.expand();
                //newParentNode.appendChild(node);
            },
            scope: this
        });

        return true;
    },

    onNodeOver: function (n, dd, e, data) {
        // slightly modified from TreeDropZone
        // this just highlights the node currently hovering over.
        // without regard to leaf or not.  See original TreeDropZone
        // version to change to leaf only.

        var pt = this.getDropPoint(e, n, dd);
        var node = n.node;

        // auto node expand check
        if (!this.expandProcId && pt == "append" && node.hasChildNodes() && !n.node.isExpanded()) {
            this.queueExpand(node);
        } else if (pt != "append") {
            this.cancelExpand();
        }

        // set the insert point style on the target node
        var returnCls = this.dropNotAllowed;
        if (this.isValidDropPoint(n, pt, dd, e, data)) {
            if (pt) {
                var el = n.ddel;
                var cls;
                returnCls = "x-tree-drop-ok-append";
                cls = "x-tree-drag-append";
                if (this.lastInsertClass != cls) {
                    Ext.fly(el).replaceClass(this.lastInsertClass, cls);
                    this.lastInsertClass = cls;
                }
            }
        }
        return returnCls;
    },

    isValidDropPoint: function (n, pt, dd, e, data) {
        if (!n || !data) {
            return false;
        }

//        Phlexible.console.log(n);
//        Phlexible.console.log(data);
//        Phlexible.console.log(pt);

        var result2 = false;
        var targetNode = n.node;

        if (data.selections) {
            // grid drag
            var r = data.selections[0];

            //Phlexible.console.debug(r.get('_type'));
            if (r.get('_type') == 'teaser') {
                // from layoutarea
                if (pt != 'append') {
                    return false;
                }

                return false;
                if (targetNode.attributes.areas && targetNode.attributes.areas.indexOf(r.get('_parent')) != -1) {
                    result2 = true;
                }
            } else if (r.get('_type') == 'element') {
                // from element list
                var dropNode = r;

                var sourceTid = r.get('tid');
                if (sourceTid === targetNode.id) {
                    return false;
                }

                var sourceNode = targetNode.getOwnerTree().getNodeById(sourceTid);
                if (sourceNode) {
                    if (targetNode.isAncestor(sourceNode)) {
                        return false;
                    }
                }

                var sourceETID = r.get('element_type_id') + '';
                if (Ext.isArray(targetNode.attributes.allowed_et) && targetNode.attributes.allowed_et.indexOf(sourceETID) !== -1) {
                    result2 = true;
                }
                else {
                    return false;
                }
            }
        } else {
            // tree drag
            var sourceNode = data.node;

            if (sourceNode.id === targetNode.id) {
                return false;
            }
            if (targetNode.isAncestor(sourceNode)) {
                return false;
            }

            var sourceETID = sourceNode.attributes.element_type_id;
            //Phlexible.console.debug(sourceETID);
            //Phlexible.console.debug(targetNode);
            if (Ext.isArray(targetNode.attributes.allowed_et) && targetNode.attributes.allowed_et.indexOf(sourceETID) !== -1) {
                result2 = true;
            }
            else {
                return false;
            }
        }

        // reuse the object
        var overEvent = this.dragOverData;
        overEvent.tree = this.tree;
        overEvent.target = targetNode;
        overEvent.data = data;
        overEvent.point = pt;
        overEvent.source = dd;
        overEvent.rawEvent = e;
        overEvent.dropNode = dropNode;
        overEvent.cancel = false;
        var result = this.tree.fireEvent("nodedragover", overEvent);
        return overEvent.cancel === false && result !== false && result2 !== false;

        return result;
    }
});
