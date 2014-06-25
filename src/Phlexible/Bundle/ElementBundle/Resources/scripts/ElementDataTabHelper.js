Phlexible.elements.ElementDataTabHelper = {
    loadItems: function(structureNodes, valueStructure, parentConfig, element, groupPrefix, forceAdd, newGroup){
        if (!groupPrefix) groupPrefix = null;

		if (Ext.isEmpty(structureNodes) || !Ext.isArray(structureNodes)) {
			return [{
				xtype: 'panel',
				plain: true,
				border: false,
				bodyStyle: 'padding: 5px;',
				html: String.format(Phlexible.elements.Strings.empty_tab, parentConfig.title)
			}]
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

		for (var i=0; i<structureNodes.length; i++) {
			var structureNode = structureNodes[i];

			if (structureNode.type === 'reference') {
				structureNode = structureNode.children[0];
			}

			if (structureNode.type === 'referenceroot') {
				structureNode = structureNode.children[0];
			}

			if (!Phlexible.fields.Registry.hasFactory(structureNode.type)) {
				Ext.MessageBox.alert('Failure', 'Missing field: ' + structureNode.type);
				Phlexible.console.warn('unknown type: ' + structureNode.type);
				continue;
			}

//                Phlexible.console.info('factory: [' + structureNode.type + '] ' + structureNode.name);
			var configFactory = Phlexible.fields.Registry.getFactory(structureNode.type),
				config = configFactory({}, structureNode, valueStructure, null, element, groupPrefix, forceAdd, newGroup);

			// count subItems
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

			configs.push(config);
		}

		return configs;
    }
};
