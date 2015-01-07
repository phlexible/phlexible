Ext.provide('Phlexible.elementtypes.ElementtypeStructureTreeDropZone');

Phlexible.elementtypes.ElementtypeStructureTreeDropZone = Ext.extend(Ext.tree.TreeDropZone, {
    debug: true,

    // private
    getDropPoint: function (e, n, dd) {
        var targetNode = n.node,
            position;

        if (targetNode.isRoot) {
            position = targetNode.allowChildren !== false ? "append" : false; // always append for root
            if (this.debug) Phlexible.console.log('getDropPoint.position: ' + position + ' (isRoot)');
            return position;
        }

        var dragEl = n.ddel;
        var t = Ext.lib.Dom.getY(dragEl), b = t + dragEl.offsetHeight;
        var y = Ext.lib.Event.getPageY(e);
        var noAppend = targetNode.allowChildren === false;

        if (this.debug) Phlexible.console.log('getDropPoint.noAppend: ', noAppend);
        if (this.debug) Phlexible.console.log('getDropPoint.allowChildren: ', targetNode.allowChildren);
        if (this.debug) Phlexible.console.log('getDropPoint.appendOnly: ', this.appendOnly);

        if (this.appendOnly) {
            position = noAppend ? false : "append";
            if (this.debug) Phlexible.console.log('getDropPoint.position: ' + position + ' (appendOnly)');
            return position;
        }

        if (targetNode.parentNode.allowChildren === false) {
            position = noAppend ? false : "append";
            if (this.debug) Phlexible.console.log('getDropPoint.position: ' + position + ' (!allowChildren)');
            return position;
        }

        var noBelow = false;
        if (!this.allowParentInsert) {
            noBelow = targetNode.hasChildNodes() && targetNode.isExpanded();
        }
        if (this.debug) Phlexible.console.log('getDropPoint.noBelow: ', noBelow);

        var q = (b - t) / (noAppend ? 2 : 3);

        if (y >= t && y < (t + q)) {
            position = "above";
        } else if (!noBelow && (noAppend || y >= b - q && y <= b)) {
            position = "below";
        } else {
            position = "append";
        }

        if (this.debug) Phlexible.console.log('getDropPoint.position: ' + position);
        return position;
    },

    isValidDropPoint: function (n, position, dd, e, data) {
        if (position === false) {
            return false;
        }

        var targetNode = n.node;
        var sourceNode = data.node;

        if (this.debug) Phlexible.console.log('isValidDropPoint.position: ' + position);

        if (position !== 'append' && targetNode.parentNode.attributes.type == 'referenceroot' && targetNode.parentNode.firstChild) {
            if (this.debug) Phlexible.console.warn('reference root node already has a child');
            return false;
        }
        if (position === 'append' && targetNode.attributes.type == 'referenceroot' && targetNode.parentNode.firstChild) {
            if (this.debug) Phlexible.console.warn('reference root node already has a child');
            return false;
        }

        if (sourceNode.ownerTree !== targetNode.ownerTree && targetNode.ownerTree.root.firstChild.attributes.type == 'referenceroot' && sourceNode.attributes.type == 'referenceroot') {
            if (this.debug) Phlexible.console.warn('target is already a reference');
            return false;
        }

        if (targetNode.isAncestor(sourceNode)) {
            if (this.debug) Phlexible.console.warn('targetNode is ancestor of sourceNode');
            return false;
        }
        if (targetNode === sourceNode) {
            if (this.debug) Phlexible.console.warn('targetNode === sourceNode => false');
            return false;
        }
        if (targetNode.attributes.reference && targetNode.attributes.type != 'reference') {
            if (this.debug) Phlexible.console.warn('targetNode is reference => false');
            return false;
        }
        if (targetNode.attributes.reference && targetNode.attributes.type != 'referenceroot' && position == 'append') {
            if (this.debug) Phlexible.console.warn('targetNode is reference root node, append => false');
            return false;
        }
        if (sourceNode.attributes.reference && sourceNode.attributes.type != 'reference') {
            if (this.debug) Phlexible.console.warn('sourceNode is reference => false');
            return false;
        }
//        if(sourceNode.attributes.reference && sourceNode.attributes.type != 'referenceroot' && position == 'append') {
//            if (this.debug) Phlexible.console.warn('sourceNode is referenceRootNode, append => false');
//            return false;
//        }

        var sourceType = sourceNode.attributes.type,
            targetType = targetNode.attributes.type;;

        if (targetType === 'reference') {
            if (this.debug) Phlexible.console.warn('targetType is reference => false');
            return false;

            if (!targetNode.childNodes || !targetNode.childNodes[0]) {
                if (this.debug) Phlexible.console.warn('targetNode is referenceNode and empositiony => false');
                return false;
            }

            targetType = targetNode.childNodes[0].attributes.type;
        }

        if (sourceType === 'reference') {
            if (!sourceNode.childNodes || !sourceNode.childNodes[0]) {
                if (this.debug) Phlexible.console.warn('sourceNode is referenceNode and empositiony => false');
                return false;
            }

            sourceType = sourceNode.childNodes[0].attributes.type;
        }

        if (sourceType === 'referenceroot' && sourceNode.ownerTree !== targetNode.ownerTree) {
            if (!sourceNode.childNodes || !sourceNode.childNodes[0]) {
                if (this.debug) Phlexible.console.warn('from template, sourceNode is referenceRootNode and empty => false');
                return false;
            }

            // TODO: only disallow for siblings
            /*
            var searchDsId = sourceNode.childNodes[0].attributes.ds_id;
            var found = false;
            targetNode.ownerTree.root.cascade(function (node) {
                if (node.attributes.ds_id == searchDsId) {
                    found = true;
                    return false;
                }
            });

            if (found) {
                if (this.debug) Phlexible.console.warn('sourceType is referenceroot && ds_id already present => false');
                return false;
            }
            */

            sourceType = sourceNode.childNodes[0].attributes.type;
        }

        var sourceField = Phlexible.fields.FieldTypes.getField(sourceType),
            targetParentType = targetNode.parentNode.attributes.type || null;

        if (position === 'append') {
            if (sourceField.allowedIn.indexOf(targetType) !== -1) {
                if (this.debug) Phlexible.console.info('sourceType "' + sourceType + '" allowed in targetType ' + targetType + ' => true');
                return true;
            }
            if (this.debug) Phlexible.console.warn('sourceType ' + sourceType + ' not allowed in targetType ' + targetType + ' => false');
            return false;
        } else {
            if (sourceField.allowedIn.indexOf(targetParentType) !== -1) {
                if (this.debug) Phlexible.console.info('sourceType ' + sourceType + ' allowed in targetParentType ' + targetParentType + ' => true');
                return true;
            }
            if (this.debug) Phlexible.console.warn('sourceType ' + sourceType + ' not allowed in targetParentType ' + targetParentType + ' => false');
            return false;
        }

        return false;

        /*
        var targetIsContainer = false;
        var sourceIsContainer = false;

        switch (targetType) {
            case 'accordion':
            case 'tab':
            case 'group':
            case 'referenceroot':
            case 'root':
                targetIsContainer = true;
                break;
        }

//        Phlexible.console.log(position);
        if (this.debug) Phlexible.console.log('targetType: ' + targetType);

        // Target is item
        if (!targetIsContainer) {
            // Append mode - not allowed
            if (position == 'append') {
                if (this.debug) Phlexible.console.warn('!targetIsContainer && position == append => false');
                return false;
            }
            //
            //return true;
        }

        switch (sourceType) {
            case 'accordion':
            case 'tab':
            case 'group':
                sourceIsContainer = true;
                break;
        }

        if (this.debug) Phlexible.console.log('sourceType: ' + sourceType);
//        Phlexible.console.log('---');

        if ((targetType == 'root' || targetType == 'referenceroot') && position != 'append') {
            if (this.debug) Phlexible.console.warn('targetType == (reference)root && !append => false');
            return false;
        }

        if (targetType == 'referenceroot' && position == 'append') {
            if (this.debug) Phlexible.console.info('targetType == referenceroot && append => true');
            return true;
        }

        if (sourceType == 'tab') {
            if (targetType == 'root') {
                if (this.debug) Phlexible.console.info('targetType == root => true');
                return true;
            }
            if (targetType == 'tab' && position != 'append') {
                if (this.debug) Phlexible.console.info('targetType == tab && !append => true');
                return true;
            }

            if (this.debug) Phlexible.console.warn('sourceType == tab => false');
            return false;
        }

        if (sourceType == 'accordion') {
            if (targetType == 'tab' && position == 'append') {
                if (this.debug) Phlexible.console.info('sourceType == accordion && targetType == tab => true');
                return true;
            }
            if (targetType == 'accordion' && position != 'append') {
                if (this.debug) Phlexible.console.info('sourceType == accordion && targetType == accordion => true');
                return true;
            }
            if (targetNode.parentNode.attributes.type == 'tab' && position != 'append') {
                if (this.debug) Phlexible.console.info('sourceType == accordion && targetType == tab => true');
                return true;
            }

            if (this.debug) Phlexible.console.warn('sourceType == accordion => false');
            return false;
        }

        if (sourceType == 'group') {
            if (targetType != 'root' && targetType != 'tab') {
                if (this.debug) Phlexible.console.info('sourceType == group && targetType != root => true');
                return true;
            }
            if (targetType == 'tab' && position == 'append') {
                if (this.debug) Phlexible.console.info('sourceType == group && targetType == tab && position == append => true');
                return true;
            }

            if (this.debug) Phlexible.console.warn('sourceType == group => false');
            return false;
        }

        if (!sourceIsContainer) {
            if (targetType == 'tab') {
                if (position == 'append') {
                    if (this.debug) Phlexible.console.info('!sourceIsContainer && targetType == tab && append => true');
                    return true;
                }
                if (this.debug) Phlexible.console.warn('!sourceIsContainer && targetType == tab && !append => true');
                return false;
            }
            if (targetType != 'root') {
                if (this.debug) Phlexible.console.info('!sourceIsContainer && targetType != root => true');
                return true;
            }

            if (this.debug) Phlexible.console.warn('!sourceIsContainer => false');
            return false;
        }

        if (this.debug) Phlexible.console.info('=> true');
        return true;
        */
    }
});
