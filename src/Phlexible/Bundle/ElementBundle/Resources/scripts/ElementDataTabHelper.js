Ext.ns('Phlexible.elements');

Phlexible.elements.ElementDataTabHelper = {
    /**
     * Fix structure, removes all root, reference root and reference nodes
     * @param {Array} structureNodes
     * @param {Array} valueStructure
     * @returns {Array}
     */
    fixStructure: function(structureNodes, valueStructure) {
        if (structureNodes[0].type === 'root' || structureNodes[0].type === 'referenceroot') {
            structureNodes = structureNodes[0].children;
        }

        var fixDsIds = {}, needFix = false;

        for (var i=0; i<structureNodes.length; i++) {
            if (structureNodes[i].type === 'reference') {
                var toParentDsId = structureNodes[i].parentDsId;

                structureNodes[i] = structureNodes[i].children[0];

                if (structureNodes[i].type === 'referenceroot') {
                    structureNodes[i] = structureNodes[i].children[0];

                    needFix = true;
                    fixDsIds[structureNodes[i].parentDsId] = toParentDsId;
                }
            }

            if (structureNodes[i].children && structureNodes[i].children.length) {
                structureNodes[i].children = Phlexible.elements.ElementDataTabHelper.fixStructure(structureNodes[i].children, valueStructure);
            }
        }

        if (needFix) {
            var fromDsId, toDsId;
            for (fromDsId in fixDsIds) {
                if (fixDsIds.hasOwnProperty(fromDsId)) {
                    toDsId = fixDsIds[fromDsId];
                    Phlexible.elements.ElementDataTabHelper.fixValueStructure(valueStructure, fromDsId, toDsId);
                }
            }
        }

        return structureNodes;
    },

    fixValueStructure: function(valueStructure, fromDsId, toDsId) {
        Ext.each(valueStructure.structures, function(structure) {
            if (structure.parentDsId === fromDsId) {
                structure.parentDsId = toDsId;
            }
            if (structure.structures) {
                Phlexible.elements.ElementDataTabHelper.fixValueStructure(structure, fromDsId, toDsId);
            }
        });
    },

    /**
     * Import all field/group prototypes
     *
     * @param {Array} structureNodes
     * @param {Phlexible.elements.Element} element
     */
    importPrototypes: function(structureNodes, element) {
        Ext.each(structureNodes, function(structureNode) {
            if (structureNode.type === 'group') {
                element.prototypes.addGroupPrototype(structureNode);
            } else {
                element.prototypes.addFieldPrototype(structureNode);
            }

            if (structureNode.children && structureNode.children.length) {
                Phlexible.elements.ElementDataTabHelper.importPrototypes(structureNode.children, element);
            }
        });
    },

    loadItems: function (structureNodes, valueStructure, parentConfig, element, groupPrefix) {
        if (!groupPrefix) groupPrefix = null;

        if (Ext.isEmpty(structureNodes) || !Ext.isArray(structureNodes)) {
            return [
                {
                    xtype: 'panel',
                    plain: true,
                    border: false,
                    bodyStyle: 'padding: 5px;',
                    html: String.format(Phlexible.elements.Strings.empty_tab, parentConfig.title)
                }
            ]
        }

        var configs = [];

        /*
         if (parentConfig.isSortable) {
         Ext.each(valueStructure.structues, function(subValueStructure) {
         Ext.each(structureNodes, function(structureNode) {
         if (structureNode.dsId === subValueStructure.dsId) {
         var configFactory = Phlexible.fields.Registry.getFactory(structureNode.type),
         config = configFactory({}, structureNode, subValueStructure, null, element, groupPrefix, forceAdd, newGroup);

         configs.push(config);
         }
         });
         });

         return configs;
         }
         */

        var currentStructureNodes = [];
        var repeatableDsIds = [];

        Ext.each(structureNodes, function(structureNode) {
            if (structureNode.type === 'group') {
                var minRepeat = parseInt(structureNode.configuration.repeat_min, 10) || 0;
                var maxRepeat = parseInt(structureNode.configuration.repeat_max, 10) || 0;
                var isRepeatable = minRepeat != maxRepeat || maxRepeat > 1;
                var isOptional = minRepeat == 0 && maxRepeat;
                if (!isRepeatable && !isOptional) {
                    currentStructureNodes.push(structureNode);
                } else if (!isRepeatable && isOptional) {
                    Ext.each(valueStructure.structures, function(valueStructureNode) {
                        if (valueStructureNode.dsId === structureNode.dsId) {
                            var clonedStructureNode = Phlexible.clone(structureNode);
                            clonedStructureNode.id = valueStructureNode.id;
                            clonedStructureNode.valueStructure = valueStructureNode;
                            currentStructureNodes.push(clonedStructureNode);
                            return false;
                        }
                    });
                } else {
                    repeatableDsIds.push(structureNode.dsId);
                }
            } else {
                currentStructureNodes.push(structureNode);
            }
        });

        if (repeatableDsIds.length) {
            if (valueStructure.structures.length) {
                Ext.each(valueStructure.structures, function(valueStructureNode) {
                    Ext.each(repeatableDsIds, function(dsId) {
                        if (valueStructureNode.dsId === dsId) {
                            var pt = element.prototypes.getPrototype(valueStructureNode.dsId);
                            var clonedStructureNode = Phlexible.clone(pt);
                            clonedStructureNode.id = valueStructureNode.id;
                            clonedStructureNode.valueStructure = valueStructureNode;
                            currentStructureNodes.push(clonedStructureNode);
                        }
                    });
                });
            } else {
                Ext.each(repeatableDsIds, function(dsId) {
                    var pt = element.prototypes.getPrototype(dsId);
                    var defaultRepeat = parseInt(pt.configuration.repeat_default, 10) || 0;
                    if (defaultRepeat > 0) {
                        for (var i=0; i<defaultRepeat; i++) {
                            var clonedStructureNode = Phlexible.clone(pt);
                            currentStructureNodes.push(clonedStructureNode);
                        }
                    }
                });
            }
        }

        /*
        if (parentConfig) {
            Ext.each(valueStructure.structures, function(valueStructureNode) {
                if (valueStructureNode.parentDsId === parentConfig.dsId) {
                    var pt = element.prototypes.getPrototype(valueStructureNode.dsId);
                    var structureNode = Phlexible.clone(pt);
                    structureNode.valueStructure = valueStructureNode;
                    currentStructureNodes.push(structureNode);
                }
            });
            if (!currentStructureNodes.length) {
                currentStructureNodes = structureNodes;
            }
        }
        */

        Ext.each(currentStructureNodes, function(currentStructureNode) {
            if (!currentStructureNode.valueStructure) {
                currentStructureNode.valueStructure = valueStructure;
            }
        });

        for (var i = 0; i < currentStructureNodes.length; i++) {
            var structureNode = currentStructureNodes[i];

            if (!Phlexible.fields.Registry.hasFactory(structureNode.type)) {
                Ext.MessageBox.alert('Failure', 'Missing field: ' + structureNode.type);
                Phlexible.console.warn('unknown type: ' + structureNode.type, structureNode);
                continue;
            }

            /*
            if (element.type === 'group') {
                var minRepeat = parseInt(structureNode.configuration.repeat_min, 10) || 0;
                var maxRepeat = parseInt(structureNode.configuration.repeat_max, 10) || 0;
                var isRepeatable = minRepeat != maxRepeat || maxRepeat > 1;
                var isOptional = minRepeat == 0 && maxRepeat;
                var defaultRepeat = parseInt(structureNode.configuration.repeat_default, 10) || 0;
                if (element.version > 1 && minRepeat === 0) {
                    defaultRepeat = 0;
                }

                if (isRepeatable || isOptional) {
                    Ext.each(valueStructure.structures, function(structure) {

                    });
                }
            }
            */

//                Phlexible.console.info('factory: [' + structureNode.type + '] ' + structureNode.name);
            var configFactory = Phlexible.fields.Registry.getFactory(structureNode.type),
                config = configFactory(parentConfig, structureNode, structureNode.valueStructure, element, groupPrefix);

            // count subItems
            /*
            if (structureNode.configuration.repeat_max != structureNode.configuration.repeat_min) {
                if (!config.allowedItems) {
                    config.allowedItems = {};
                }
                if (!config.allowedItems[structureNode.dsId]) {
                    config.allowedItems[structureNode.dsId] = {
                        title: structureNode.name,
                        type: structureNode.type,
                        pos: 0, // pos
                        max: structureNode.configuration.repeat_max || 1
                    };

                    Phlexible.console.warn('tools');
                    //if (formItem.tools && formItem.tools.right) formItem.tools.right.show();
                }
            }
            */

            configs.push(config);
        }

        return configs;
    }
};
