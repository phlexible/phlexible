Phlexible.elementtypes.ElementtypeStructureTreeDropZone = Ext.extend(Ext.tree.TreeDropZone, {
    // private
    getDropPoint : function(e, n, dd){
        var tn = n.node;
        if(tn.isRoot){
            return tn.allowChildren !== false ? "append" : false; // always append for root
        }
        var dragEl = n.ddel;
        var t = Ext.lib.Dom.getY(dragEl), b = t + dragEl.offsetHeight;
        var y = Ext.lib.Event.getPageY(e);
        var noAppend = tn.allowChildren === false;
//        Phlexible.console.log(tn);
//        Phlexible.console.log('allowChildren: ');
//        Phlexible.console.info(tn.allowChildren);
//        Phlexible.console.log('appendOnly: ');
//        Phlexible.console.info(this.appendOnly);
        if(this.appendOnly || tn.parentNode.allowChildren === false){
            return noAppend ? false : "append";
        }
        var noBelow = false;
        if(!this.allowParentInsert){
            noBelow = tn.hasChildNodes() && tn.isExpanded();
        }
        var q = (b - t) / (noAppend ? 2 : 3);
        if(y >= t && y < (t + q)){
            return "above";
        }else if(!noBelow && (noAppend || y >= b-q && y <= b)){
            return "below";
        }else{
            return "append";
        }
    },

    isValidDropPoint: function(n, pt, dd, e, data) {
        var targetNode = n.node;
        var sourceNode = data.node;

        var debug = true;

        if(pt !== 'append' && targetNode.parentNode.attributes.type == 'referenceroot' && targetNode.parentNode.firstChild) {
            if (debug) Phlexible.console.log('referenceRootNode has already a child');
            return false;
        }

        if(sourceNode.ownerTree !== targetNode.ownerTree && targetNode.ownerTree.root.firstChild.attributes.type == 'referenceroot' && sourceNode.attributes.type == 'referenceroot') {
            if (debug) Phlexible.console.log('target is already a reference');
            return false;
        }

        if(targetNode.isAncestor(sourceNode)) {
            if (debug) Phlexible.console.info('isAncestor');
            return false;
        }
        if(targetNode === sourceNode) {
            if (debug) Phlexible.console.info('targetNode===sourceNode => false');
            return false;
        }
        if(targetNode.attributes.reference && targetNode.attributes.type != 'reference') {
            if (debug) Phlexible.console.info('targetNode is referenceNode => false');
            return false;
        }
        if(targetNode.attributes.reference && targetNode.attributes.type != 'referenceroot' && pt == 'append') {
            if (debug) Phlexible.console.info('targetNode is referenceRootNode, append => false');
            return false;
        }
        if(sourceNode.attributes.reference && sourceNode.attributes.type != 'reference') {
            if (debug) Phlexible.console.info('sourceNode is referenceNode => false');
            return false;
        }
//        if(sourceNode.attributes.reference && sourceNode.attributes.type != 'referenceroot' && pt == 'append') {
//            if (debug) Phlexible.console.info('sourceNode is referenceRootNode, append => false');
//            return false;
//        }

        var targetType = targetNode.attributes.type;
        var sourceType = sourceNode.attributes.type;

        if(targetType === 'reference') {
            if(!targetNode.childNodes || ! targetNode.childNodes[0]) {
                if (debug) Phlexible.console.info('targetNode is referenceNode and empty => false');
                return false;
            }

            targetType = targetNode.childNodes[0].attributes.type;
        }

        if(sourceType === 'reference') {
            if(!sourceNode.childNodes || ! sourceNode.childNodes[0]) {
                if (debug) Phlexible.console.info('sourceNode is referenceNode and empty => false');
                return false;
            }

            sourceType = sourceNode.childNodes[0].attributes.type;
        }

        if (sourceType == 'referenceroot' && sourceNode.ownerTree !== targetNode.ownerTree) {
            if(!sourceNode.childNodes || ! sourceNode.childNodes[0]) {
                if (debug) Phlexible.console.info('from template, sourceNode is referenceRootNode and empty => false');
                return false;
            }

            var searchDsId = sourceNode.childNodes[0].attributes.ds_id;
            var found = false;
            targetNode.ownerTree.root.cascade(function(node) {
                if(node.attributes.ds_id == searchDsId) {
                    found = true;
                    return false;
                }
            });

            if (found) {
                if (debug) Phlexible.console.info('sourceNode is referenceRootNode && ds_id already present => false');
                return false;
            }

            sourceType = sourceNode.childNodes[0].attributes.type;
        }

        var targetIsContainer = false;
        var sourceIsContainer = false;

        switch(targetType) {
            case 'accordion':
                targetType = 'accordion';
            case 'tab':
            case 'group':
            case 'referenceroot':
            case 'root':
                targetIsContainer = true;
                break;
        }

//        Phlexible.console.log(pt);
        if (debug) Phlexible.console.log('targetType: ' + targetType);

        // Target is item
        if(!targetIsContainer) {
            // Append mode - not allowed
            if(pt=='append') {
                if (debug) Phlexible.console.info('!targetIsContainer && pt==append => false');
                return false;
            }
            //
            //return true;
        }

        switch(sourceType) {
            case 'accordion':
                sourceType = 'accordion';
            case 'tab':
            case 'group':
                sourceIsContainer = true;
                break;
        }

        if (debug) Phlexible.console.log('sourceType: ' + sourceType);
//        Phlexible.console.log('---');

        if(targetType=='referenceroot' && pt == 'append') {
            if (debug) Phlexible.console.info('targetType == referenceroot && append => true');
            return true;
        }

        if(sourceType=='tab') {
            if(targetType=='root') {
                if (debug) Phlexible.console.info('targetType == root => true');
                return true;
            }
            if(targetType=='tab' && pt!='append') {
                if (debug) Phlexible.console.info('targetType == tab => true');
                return true;
            }

            if (debug) Phlexible.console.info('sourceType == tab => false');
            return false;
        }

        if(sourceType=='accordion') {
            if(targetType=='tab' && pt=='append') {
                if (debug) Phlexible.console.info('sourceType == accordion && targetType == tab => true');
                return true;
            }
            if(targetType=='accordion' && pt!='append') {
                if (debug) Phlexible.console.info('sourceType == accordion && targetType == accordion => true');
                return true;
            }
            if(targetNode.parentNode.attributes.type == 'tab' && pt!='append') {
                if (debug) Phlexible.console.info('sourceType == accordion && targetType == tab => true');
                return true;
            }

            if (debug) Phlexible.console.info('sourceType == accordion => false');
            return false;
        }

        if(sourceType=='group') {
            if(targetType!='root' && targetType!='tab') {
                if (debug) Phlexible.console.info('sourceType == group && targetType != root => true');
                return true;
            }
            if(targetType=='tab' && pt == 'append') {
                if (debug) Phlexible.console.info('sourceType == group && targetType == tab && pt == append => true');
                return true;
            }

            if (debug) Phlexible.console.info('sourceType == group => false');
            return false;
        }

        if(!sourceIsContainer) {
            if(targetType=='tab') {
                if(pt=='append') {
                    if (debug) Phlexible.console.info('!sourceIsContainer && targetType == tab => true');
                    return true;
                }
                return false;
            }
            if(targetType!='root') {
                if (debug) Phlexible.console.info('!sourceIsContainer && targetType != root => true');
//                Phlexible.console.log(pt);
                return true;
            }

            if (debug) Phlexible.console.info('!sourceIsContainer => false');
            return false;
        }

        if (debug) Phlexible.console.info('=> true');

        return true;
    }
});
