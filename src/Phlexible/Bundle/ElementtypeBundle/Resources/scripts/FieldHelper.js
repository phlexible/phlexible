Phlexible.fields.FieldHelper = {
    /**
     *
     * @param {Object} parentConfig
     * @param {Object} item
     * @param {Array} valueStructure
     * @param {Phlexible.elements.Element} element
     * @param {Number} repeatableId
     * @returns {Object}
     */
    defaults: function (parentConfig, item, valueStructure, element, repeatableId) {
        // labels
        var hideLabel,
            label,
            labelSeparator = ':',
            language = Phlexible.Config.get('user.property.interfaceLanguage', 'en');

        if (parentConfig.singleLineLabel) {
            label = parentConfig.singleLineLabel;
            parentConfig.singleLineLabel = '';
            hideLabel = false;
        } else if (parentConfig.singleLine) {
            hideLabel = true;
            label = item.labels.fieldlabel[language];
        } else if (item.configuration.hide_label) {
            hideLabel = false;
            label = '';
            labelSeparator = '';
        } else {
            hideLabel = false;
            label = item.labels.fieldlabel[language];
        }

        var itemValue = null;
        Ext.each(valueStructure.values, function (value) {
            if (value.dsId === item.dsId) {
                itemValue = value;
            }
        });

        // name
        var name = 'field-' + item.dsId + '-';
        if (itemValue) {
            name += 'id-' + itemValue.id;
        } else {
            name += Ext.id(null, 'new-');
        }
        if (repeatableId) {
            name += '__' + repeatableId;
        }
        console.info(name);

        var config = {
            name: name,
            dsId: item.dsId,

            fieldLabel: label,
            helpText: (item.labels.context_help[language] || ''),
            prefix: (item.labels.prefix[language] || ''),
            suffix: (item.labels.suffix[language] || ''),
            labelSeparator: labelSeparator,
            hideLabel: hideLabel,

            value: itemValue ? itemValue.content : '',
            masterValue: '', // TODO
            attributes: itemValue ? itemValue.attributes : {},

            isMaster: element.master,
            isDiff: !!element.data.diff,

            isSynchronized: (item.configuration['synchronized'] === 'synchronized' || item.configuration['synchronized'] === 'synchronized_unlink'),
            hasUnlink: item.configuration['synchronized'] === 'synchronized_unlink',
            isUnlinked: item.data_options && item.data_options.unlinked,

//            isRepeatable: (item.configuration.repeat_max > 1 ? true : false),
            minRepeat: (item.configuration.repeat_min ? parseInt(item.configuration.repeat_min, 10) : 0),
            maxRepeat: (item.configuration.repeat_max ? parseInt(item.configuration.repeat_max, 10) : 0),
            defaultRepeat: (item.configuration.repeat_default ? parseInt(item.configuration.repeat_default, 10) : 0),

            width: (parseInt(item.configuration.width, 10) || 100),

            allowBlank: item.validation.required != 'always',

            element: element,

            listeners: {
                render: function (c) {
                    if (c.supportsPrefix) {
                        Phlexible.fields.FieldHelper.prefix.call(c);
                    }
                    if (c.supportsSuffix) {
                        Phlexible.fields.FieldHelper.suffix.call(c);
                    }
                    if (c.supportsDiff) {
                        Phlexible.fields.FieldHelper.diff.call(c);
                    }
                    if (c.supportsInlineDiff) {
                        Phlexible.fields.FieldHelper.inlineDiff.call(c);
                    }
                    if (c.supportsUnlink) {
                        var styleEl = false;
                        var unlinkEl = false;
                        if (c.supportsUnlink.styleEl && this[c.supportsUnlink.styleEl]) {
                            styleEl = this[c.supportsUnlink.styleEl];
                        }
                        if (c.supportsUnlink.unlinkEl && this[c.supportsUnlink.unlinkEl]) {
                            unlinkEl = this[c.supportsUnlink.unlinkEl];
                        }
                        Phlexible.fields.FieldHelper.unlink.call(c, styleEl, unlinkEl);
                    }
                    if (c.supportsRepeatable) {
                        Phlexible.fields.FieldHelper.repeatable.call(c);
                    }
                }
            }
        };

        if (item.configuration.readonly) {
            config.readOnly = true;
            config.ctCls = 'x-item-disabled';
        }

        if (config.isDiff) {
            config.readOnly = true;
        }

        return config;
    },

    prefix: function () {
        if (this.prefix) {
            this.el.insertSibling({
                tag: 'span',
                cls: 'p-form-item-prefix',
                html: this.prefix
            }, 'before');
        }
    },

    suffix: function () {
        if (this.suffix) {
            this.el.insertSibling({
                tag: 'span',
                cls: 'p-form-item-suffix',
                html: this.suffix
            }, 'after');
        }
    },

    diff: function (styleEl) {
        if (!this.attributes || !this.attributes.diff) {
            return
        }

        if (!styleEl) {
            styleEl = this.el;
        }

        switch (this.attributes.diff) {
            case 'modified':
                styleEl.addClass('p-fields-diff-modified');
                break;

            case 'added':
                styleEl.addClass('p-fields-diff-added');
                break;

            case 'removed':
                styleEl.addClass('p-fields-diff-removed');
                break;
        }
    },

    inlineDiff: function (targetEl, clickEl) {
        if (!this.element || !this.attributes || !this.attributes.diff || this.attributes.diff !== 'modified') {
            return;
        }

        if (!targetEl) {
            targetEl = this.el;
        }
        if (!clickEl) {
            clickEl = targetEl;
        }

        targetEl.on('click', function () {
            if (this.element.activeDiffEl && this.element.activeDiffEl.isVisible() && !e.within(targetEl.dom, false, true) && !e.within(this.element.activeDiffEl.dom, false, true)) {
                this.element.activeDiffEl.hide();
                this.element.activeDiffEl = null;
            }

            if (!this.diffEl) {
                var height = (targetEl.getHeight && targetEl.getHeight() > 32) ? targetEl.getHeight() : 32;
                var html = '_old_value: ' + this.attributes.oldValue + '<br/>_diff: ' + this.attributes.diffValue;

                this.diffEl = targetEl.insertSibling({
                    tag: 'div',
                    html: html,
                    cls: 'p-fields-diff-inline',
                    style: 'height: ' + height + 'px;'
                }, 'after');
                this.diffEl.setVisibilityMode(Ext.Element.DISPLAY);
                this.element.activeDiffEl = this.diffEl;
            }
            else {
                if (!this.diffEl.isVisible()) {
                    this.diffEl.show();
                    this.element.activeDiffEl = this.diffEl;
                }
            }
        }, this);
    },

    unlink: function (styleEl, unlinkEl) {
        if (this.isSynchronized) {
            if (!styleEl) styleEl = this.el;
            if (!unlinkEl) unlinkEl = this.el;
            if (this.isMaster) {
                styleEl.addClass('p-fields-synchronized-master');
            }
            else {
                if (this.isUnlinked) {
                    styleEl.addClass('p-fields-synchronized-unlinked');
                } else {
                    styleEl.addClass('p-fields-synchronized-synched');
                    this.disable();
                }

                if (this.hasUnlink && !this.isDiff) {
                    if (this.el) {
                        this.el.setWidth(this.el.getWidth() - 19);
                    }
                    this.hidden_unlink = unlinkEl.insertSibling({
                        tag: 'input',
                        type: 'hidden',
                        name: 'unlink_' + (this.hiddenName || this.name),
                        value: this.isUnlinked ? '1' : ''
                    }, 'after');
                    this.button_unlink = this.hidden_unlink.insertSibling({
                        tag: 'img',
                        cls: 'p-inline-icon p-fields-' + (this.isUnlinked ? 'unlink' : 'link'),
                        src: Ext.BLANK_IMAGE_URL,
                        width: 16,
                        height: 16,
                        qtip: this.isUnlinked ? 'Link' : 'Unlink'
                    }, 'after');
                    this.button_unlink.on('click', function () {
                        if (this.isUnlinked) {
                            this.isUnlinked = false;
                            this.hidden_unlink.set({
                                value: ''
                            });
                            this.button_unlink.removeClass('p-fields-unlink');
                            this.button_unlink.addClass('p-fields-link');
                            this.button_unlink.set({
                                qtip: 'Unlink'
                            });
                            styleEl.removeClass('p-fields-synchronized-unlinked');
                            styleEl.addClass('p-fields-synchronized-synched');
                            this.disable();
                            if (this.masterValue) {
                                this.setValue(this.masterValue);
                            }
                            else {
                                this.setValue('');
                            }
                        }
                        else {
                            this.isUnlinked = true;
                            this.hidden_unlink.set({
                                value: '1'
                            });
                            this.button_unlink.removeClass('p-fields-link');
                            this.button_unlink.addClass('p-fields-unlink');
                            this.button_unlink.set({
                                qtip: 'Link'
                            });
                            styleEl.addClass('p-fields-synchronized-unlinked');
                            styleEl.removeClass('p-fields-synchronized-synched');
                            this.enable();
                        }
                    }, this);
                }
            }
        }
    },

    repeatable: function () {
        if (this.isRepeatable) {
            var button_remove = this.el.insertSibling({tag: 'div', cls: 'x-tool x-tool-minus p-repeat-minus'}, 'before');
            var button_add = this.el.insertSibling({tag: 'div', cls: 'x-tool x-tool-plus p-repeat-plus'}, 'before');
            //var button_remove = this.el.insertSibling({tag: 'img', src: Phlexible.component('/elements/asset/image/name/field_delete.png'), name: this.id+'-remove'}, 'after');
            //var button_add = this.el.insertSibling({tag: 'img', src: Phlexible.component('/elements/asset/image/name/field_add.png'), name: this.id+'-add'}, 'after');

            button_remove.on('click', function () {
                if (this.element.prototypes.getCount(this.ds_id) > this.minRepeat) {
                    this.element.prototypes.decCount(this.ds_id);

                    if (!this.element.prototypes.getCount(this.ds_id)) {
                        this.setValue('');
                        this.el.addClass('x-item-disabled');
                        this.el.parent().prev().addClass('x-item-disabled');
                        this.el.prev().prev().addClass('x-item-disabled');
                    }
                    else {
                        this.el.up('.x-form-item').remove();
                        this.ownerCt.remove(this);
                    }
                }
            }, this);
            button_add.on('click', function () {
                if (!this.element.prototypes.getCount(this.ds_id)) {
                    this.element.prototypes.incCount(this.ds_id);
                    this.el.removeClass('x-item-disabled');
                    this.el.parent().prev().removeClass('x-item-disabled');
                    this.el.prev().prev().removeClass('x-item-disabled');
                }
                else {
                    if (!this.maxRepeat || this.element.prototypes.getCount(this.ds_id) < this.maxRepeat) {
                        var pt = this.element.prototypes.getPrototype(this.ds_id);
//                        Phlexible.console.log(this.ownerCt.items.items);
                        var pos = this.ownerCt.items.items.indexOf(this);
//                        Phlexible.console.log(pos);
                        this.addField(this.ownerCt, pt, pos + 1);
                        //this.owner.doLayout();
                    }
                }
            }, this);
        }
    }
};
