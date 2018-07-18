Ext.provide('Phlexible.frontendmedia.FieldHelper');

Phlexible.frontendmedia.FieldHelper = {

    diffFn: function (diff, panel) {
        var content_from;
        if (diff.content_from) {
            var split = diff.content_from.split(';');
            var id = split[0];

            content_from = '<img src="' + Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '_mm_large'}) + '" width="96" height="96" style="border: 1px solid #99BBE8;" />';
        }
        else {
            content_from = '(empty)';
        }
        var split = diff.content_to.split(';');
        var id = split[0];
        var content_to = '<img src="' + Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '_mm_large'}) + '" width="96" height="96" style="border: 1px solid #99BBE8;" />';

        var html = '<label>' + Phlexible.elements.Strings.version + ' ' + panel.diff.version_from + '</label>' +
            '<div>' + content_from + '</div>' +
            '<label>' + Phlexible.elements.Strings.version + ' ' + panel.diff.version_to + '</label>' +
            '<div>' + content_to + '</div>';

        panel.body.update(html);
    },

    inlineDiff: function (targetEl, clickEl) {
        if (!this.element || !this.diff || (this.diff['type'] && !this.diff.content_from)) return;

        if (!targetEl) targetEl = this.el;
        if (!clickEl) clickEl = targetEl;

        targetEl.on('click', function () {
            if (this.element.activeDiffEl && this.element.activeDiffEl.isVisible() && !e.within(targetEl.dom, false, true) && !e.within(this.element.activeDiffEl.dom, false, true)) {
                this.element.activeDiffEl.hide();
                this.element.activeDiffEl = null;
            }

            if (!this.diffEl) {
                var height = (targetEl.getHeight && targetEl.getHeight() > 32) ? targetEl.getHeight() : 32;
                var html = '';
                if (this.diff['type'] == 'change') {
                    var split = this.diff.content_from.split(';');
                    var id = split[0];

                    html = '<img src="' + Phlexible.Router.generate('mediamanager_media', {file_id: id, template_key: '_mm_large'}) + '" width="96" height="96" style="border: 1px solid #99BBE8;" />';
                }
                else {
                    html = Phlexible.fields.Strings.diff_new_field;
                    height = 14;
                }

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

    unlink: function () {
        if (this.isSynchronized) {
            if (this.isMaster) {
                this.el.addClass('p-fields-synchronized-master');
            }
            else {
                if (this.isUnlinked) {
                    this.el.addClass('p-fields-synchronized-unlinked');
                }
                else {
                    this.el.addClass('p-fields-synchronized-synched');
                    this.disable();
                }

                if (this.hasUnlink && !this.isDiff) {
                    this.hidden_unlink = this.el.parent().last().insertSibling({
                        tag: 'input',
                        type: 'hidden',
                        name: 'unlink_' + this.name,
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
                            this.el.removeClass('p-fields-synchronized-unlinked');
                            this.el.addClass('p-fields-synchronized-synched');
                            this.disable();
                            if (this.masterValue) {
                                this.setFile(this.masterValue.file_id, this.masterValue.file_version, this.masterValue.name, this.masterValue.folder_id);
                            }
                            else {
                                this.reset();
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
                            this.el.addClass('p-fields-synchronized-unlinked');
                            this.el.removeClass('p-fields-synchronized-synched');
                            this.enable();
                        }
                    }, this);
                }
            }
        }
    }
};
