/**
 *
 */
Phlexible.elements.TopToolbar = Ext.extend(Ext.Toolbar, {
    strings: Phlexible.elements.Strings,
    cls: 'p-elements-main-panel',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            beforeload: this.onBeforeLoadElement,
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            enableSave: this.onEnableSave,
            disableSave: this.onDisableSave,
            scope: this
        });

        this.populateTbar();
        this.items = this.tbarIndex.getRange();

        Phlexible.elements.TopToolbar.superclass.initComponent.call(this);
    },

    populateTbar: function () {
        var langBtns = this.element.languages;

        var saveBtn = {
            text: this.strings.save,
            iconCls: 'p-element-save-icon',
            xtype: 'button',
            handler: this.onSave,
            disabled: true,
            scope: this
        };

        var publishBtn = {
            xtype: 'button',
            text: this.strings.publish_element,
            iconCls: 'p-element-publish-icon',
            disabled: true,
            handler: this.onExtendedPublish,
            scope: this
        };

        var setOfflineBtn = {
            xtype: 'button',
            text: this.strings.set_element_offline,
            iconCls: 'p-element-set_offline-icon',
            disabled: true,
            handler: this.onExtendedSetOffline,
            scope: this
        };

        this.populateExtendedMenu();
        var extendedMenuBtn = {
            xtype: 'button',
            text: this.strings.more,
            //iconCls: 'p-element-more-icon',
            disabled: false,
            menu: this.extendedMenuIndex.getRange()
        };

        if (!Phlexible.Config.get('elements.publish.comment_required')) {
            publishBtn.xtype = 'tbsplit';
            publishBtn.handler = this.onQuickPublish;
            publishBtn.menu = [
                {
                    text: this.strings.publish_element,
                    iconCls: 'p-element-publish-icon',
                    handler: this.onQuickPublish,
                    scope: this
                },
                {
                    text: this.strings.publish_element_with_notification,
                    iconCls: 'p-element-publish-icon',
                    handler: this.onPublishWithNotification,
                    scope: this
                },
                {
                    text: this.strings.publish_element_advanced,
                    iconCls: 'p-element-publish-icon',
                    handler: this.onExtendedPublish,
                    scope: this
                }
            ];

            setOfflineBtn.xtype = 'tbsplit';
            setOfflineBtn.handler = this.onQuickSetOffline;
            setOfflineBtn.menu = [
                {
                    text: this.strings.set_element_offline,
                    iconCls: 'p-element-set_offline-icon',
                    handler: this.onQuickSetOffline,
                    scope: this
                },
                {
                    text: this.strings.set_element_offline_advanced,
                    iconCls: 'p-element-set_offline-icon',
                    handler: this.onExtendedSetOffline,
                    scope: this
                }
            ];
        }

        this.tbarIndex = new Ext.util.MixedCollection();
        this.tbarIndex.add('lang', {
            // items[0]
            xtype: 'cycle',
            showText: true,
            items: langBtns,
            listeners: {
                change: function (cycle, btn) {
                    this.element.setLanguage(btn.langKey);
                },
                scope: this
            }
        });

        this.element.on('setLanguage', function (element, language) {
            var cycle = this.items.items[this.tbarIndex.indexOfKey('lang')];
            if (cycle.activeItem.langKey == language) return;

            var foundItem = null;
            cycle.menu.items.each(function (item) {
                if (item.langKey == language) {
                    foundItem = item;
                    return false;
                }
            }, this);

            if (foundItem) cycle.setActiveItem(foundItem, true);
        }, this);

        this.tbarIndex.add('add_sep', '-');
        this.tbarIndex.add('add', {
            // items[2]
            text: this.strings.add_element,
            iconCls: 'p-element-add-icon',
            disabled: true,
            handler: function () {
                this.element.showNewElementWindow();
            },
            scope: this
        });
        this.tbarIndex.add('edit_sep', '-');
        this.tbarIndex.add('edit', {
            // items[4]
            text: this.strings.edit_element,
            iconCls: 'p-element-edit-icon',
            disabled: true,
            handler: function (btn) {
                var lockStatus = this.element.getLockStatus();

                if (lockStatus == 'idle') {
                    this.element.lock();
                }
                else if (lockStatus == 'edit') {
                    this.element.unlock();
                }
            },
            scope: this
        });

        this.tbarIndex.add('publish_sep', '-');
        this.tbarIndex.add('save', saveBtn);

        this.tbarIndex.add('publish', publishBtn);
        this.tbarIndex.add('setOffline', setOfflineBtn);
        this.tbarIndex.add('delete', {
            // items[10]
            text: this.strings.delete_element,
            iconCls: 'p-element-delete-icon',
            handler: function () {
                Ext.MessageBox.alert('Oh, wait...', 'To be done!');
            },
            scope: this,
            disabled: true,
            hidden: true
        });

        this.tbarIndex.add('extended_sep', '-');
        this.tbarIndex.add('extended', extendedMenuBtn);

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.tbarIndex.add('debug_sep', '-');
            this.tbarIndex.add('debug', {
                text: 'Debug',
                iconCls: 'p-element-debug-icon',
                menu: [
                    {
                        text: 'Dump element',
                        handler: function () {
                            Phlexible.console.log(this.element);
                            var dump = Phlexible.dumpHtml(this.element, true, ['events', 'scope', 'treeNode', 'teaserNode', 'structure']);
                            var w = new Ext.Window({
                                title: 'Element Debug',
                                width: 800,
                                height: 500,
                                layout: 'fit',
                                maximizable: true,
                                items: {
                                    border: false,
                                    layout: 'fit',
                                    bodyStyle: 'padding: 5px;',
                                    autoScroll: true,
                                    html: '<pre>' + dump + '</pre>'
                                }
                            });
                            w.show();
                        },
                        scope: this
                    },
                    {
                        text: 'Link to this element',
                        handler: function () {
                            var link = document.location.href;
                            link += '?&e=elements';
                            link += '&p[id]=' + this.element.tid;
                            link += '&p[siteroot_id]=' + this.element.siteroot_id;
                            Ext.MessageBox.prompt('Link', 'Link to this element', null, null, true, link);
                        },
                        scope: this
                    }
                ]
            });
        }

        this.tbarIndex.add('fill_lock', '->');
        this.tbarIndex.add('locks', {
            iconCls: 'p-element-lock-icon',
            handler: function () {
                if (Phlexible.User.isGranted('ROLE_ELEMENT_LOCKS')) {
                    return;
                }

                Phlexible.LoadHandler.handleDialog('Phlexible.elements.LocksWindow');
            }
        });
        this.tbarIndex.add('lock', {
            text: ' ',
            hidden: true,
            handler: function () {
                if (this.element.getLockStatus() !== 'locked') {
                    return;
                }

                if (Phlexible.User.isGranted('ROLE_ELEMENT_LOCKS')) {
                    return;
                }

                Ext.MessageBox.confirm('Warning', String.format(this.strings.unlock_warning, Phlexible.Format.age(this.element.data.lockinfo.age)), function (btn) {
                    if (btn != 'yes') {
                        return;
                    }

                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('elements_locks_forceunlock'),
                        params: {
                            eid: this.element.eid,
                            force: 1,
                            language: this.element.language
                        },
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                this.element.reload({
                                    lock: 1
                                });
                            } else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                }, this);
            },
            scope: this
        });

        this.tbarIndex.add('pager_separator', '-');
        this.tbarIndex.add('pager_first', {
            iconCls: 'p-element-resultset_first-icon',
            hidden: true,
            handler: function (btn) {
                this.element.load(btn.tid, null, this.element.language, 1);
            },
            scope: this
        });
        this.tbarIndex.add('pager_prev', {
            iconCls: 'p-element-resultset_previous-icon',
            hidden: true,
            handler: function (btn) {
                this.element.reload({
                    id: btn.tid,
                    version: null,
                    lock: 1
                });
            },
            scope: this
        });
        this.tbarIndex.add('pager_reload', {
            iconCls: 'p-element-reload-icon',
            hidden: true,
            handler: function (btn) {
                this.element.reload({
                    lock: 1,
                    version: null
                });
            },
            scope: this
        });
        this.tbarIndex.add('pager_next', {
            iconCls: 'p-element-resultset_next-icon',
            hidden: true,
            handler: function (btn) {
                this.element.reload({
                    id: btn.tid,
                    version: null,
                    lock: 1
                });
            },
            scope: this
        });
        this.tbarIndex.add('pager_last', {
            iconCls: 'p-element-resultset_last-icon',
            hidden: true,
            handler: function (btn) {
                this.element.reload({
                    id: btn.tid,
                    version: null,
                    lock: 1
                });
            },
            scope: this
        });
    },

    populateExtendedMenu: function () {
        this.extendedMenuIndex = new Ext.util.MixedCollection();

        this.extendedMenuIndex.add('preview', {
            // items[6]
            xtype: 'tbsplit',
            text: Phlexible.elements.Strings.preview.preview,
            iconCls: 'p-element-preview_page-icon',
            disabled: true,
            handler: function () {
                var src = this.element.data.urls.preview;
                window.open(src, 'latest_preview'); //, 'width=1000,height=700,scrollbars=yes');
            },
            scope: this,
            menu: [
                {
                    text: Phlexible.elements.Strings.preview.preview,
                    iconCls: 'p-element-preview_preview-icon',
                    handler: function () {
                        var src = this.element.data.urls.preview;
                        window.open(src, 'preview'); //, 'width=1000,height=700,scrollbars=yes');
                    },
                    scope: this
                },
                {
                    text: Phlexible.elements.Strings.preview.online,
                    iconCls: 'p-element-preview_online-icon',
                    disabled: true,
                    handler: function () {
                        var src = this.element.data.urls.online;
                        window.open(src, 'preview_live'); //, 'width=1000,height=700,scrollbars=yes');
                    },
                    scope: this
                }
            ]
        });

        this.extendedMenuIndex.add('history_sep', '-');
        this.extendedMenuIndex.add('history', {
            // items[13]
            text: this.strings.history,
            iconCls: 'p-element-tab_history-icon',
            handler: function () {
                var w = new Phlexible.elements.HistoryWindow();
                w.show();
            },
            scope: this
        });

        if (Phlexible.tasks.Strings && Phlexible.User.isGranted('ROLE_ELEMENT_TASKS')) {
            this.extendedMenuIndex.add('task_sep', '-');
            this.extendedMenuIndex.add('task', {
                text: Phlexible.tasks.Strings.new_task,
                iconCls: 'p-tasks-component-icon',
                disabled: true,
                handler: function () {
                    var payload = {
                        tid: this.element.tid
                        //language: this.element.language
                    };

                    if (this.element.teaser_id) {
                        payload.teaser_id = this.element.teaser_id;
                    }

                    var w = new Phlexible.tasks.NewTaskWindow({
                        payload: payload,
                        component_filter: 'elements',
                        listeners: {
                            create: function () {
                                this.element.reload({
                                    unlock: this.element.eid
                                });
                            },
                            scope: this
                        }
                    });

                    w.show();
                },
                scope: this
            });
        }
    },

    updateLockInfo: function () {
        var targetItem1 = this.items.items[this.tbarIndex.indexOfKey('lock')];

        var lockStatus = this.element.getLockStatus();

        switch (lockStatus) {
            case 'edit':
                targetItem1.setText('<span style="font-weight: bold; color: green;">' + this.strings.locked_by_me + '</span>');
                targetItem1.show();
                break;

            case 'locked':
                targetItem1.setText('<span style="font-weight: bold; color: red;">' + String.format(this.strings.locked_by_user, this.element.data.lockinfo.username) + '</span>');
                targetItem1.show();
                break;

            case 'locked_permanently':
                targetItem1.setText('<span style="font-weight: bold; color: red;">'
                    + String.format(this.strings.locked_by_user_permanently, this.element.data.lockinfo.username)
                    + '</span>');
                targetItem1.show();
                break;

            default:
                targetItem1.setText('');
                targetItem1.hide();
        }
    },

    onLoadElement: function () {
        // add button
        if (Phlexible.User.isGranted('ROLE_ELEMENT_CREATE') && this.element.data.rights.indexOf('CREATE') !== -1) {
            this.items.items[this.tbarIndex.indexOfKey('add')].enable();
        } else {
            this.items.items[this.tbarIndex.indexOfKey('add')].disable();
        }

        if (this.element.data.pager && (this.element.data.pager.next || this.element.data.pager.prev)) {
            this.items.items[this.tbarIndex.indexOfKey('pager_separator')].show();
            this.items.items[this.tbarIndex.indexOfKey('pager_first')].show();
            this.items.items[this.tbarIndex.indexOfKey('pager_prev')].show();
            this.items.items[this.tbarIndex.indexOfKey('pager_reload')].show();
            this.items.items[this.tbarIndex.indexOfKey('pager_next')].show();
            this.items.items[this.tbarIndex.indexOfKey('pager_last')].show();

            if (this.element.data.pager.first != this.element.tid) {
                this.items.items[this.tbarIndex.indexOfKey('pager_first')].tid = this.element.data.pager.first;
                this.items.items[this.tbarIndex.indexOfKey('pager_first')].enable();
            }
            else {
                this.items.items[this.tbarIndex.indexOfKey('pager_first')].disable();
            }

            if (this.element.data.pager.last != this.element.tid) {
                this.items.items[this.tbarIndex.indexOfKey('pager_last')].tid = this.element.data.pager.last;
                this.items.items[this.tbarIndex.indexOfKey('pager_last')].enable();
            }
            else {
                this.items.items[this.tbarIndex.indexOfKey('pager_last')].disable();
            }

            if (this.element.data.pager.prev) {
                this.items.items[this.tbarIndex.indexOfKey('pager_prev')].tid = this.element.data.pager.prev;
                this.items.items[this.tbarIndex.indexOfKey('pager_prev')].enable();
            }
            else {
                this.items.items[this.tbarIndex.indexOfKey('pager_prev')].disable();
            }

            if (this.element.data.pager.next) {
                this.items.items[this.tbarIndex.indexOfKey('pager_next')].tid = this.element.data.pager.next;
                this.items.items[this.tbarIndex.indexOfKey('pager_next')].enable();
            }
            else {
                this.items.items[this.tbarIndex.indexOfKey('pager_next')].disable();
            }
        }
        else {
            this.items.items[this.tbarIndex.indexOfKey('pager_separator')].hide();
            this.items.items[this.tbarIndex.indexOfKey('pager_first')].hide();
            this.items.items[this.tbarIndex.indexOfKey('pager_prev')].hide();
            this.items.items[this.tbarIndex.indexOfKey('pager_reload')].hide();
            this.items.items[this.tbarIndex.indexOfKey('pager_next')].hide();
            this.items.items[this.tbarIndex.indexOfKey('pager_last')].hide();
        }

        var extendedItem = this.items.items[this.tbarIndex.indexOfKey('extended')];
        var previewItem = extendedItem.menu.items.items[this.extendedMenuIndex.indexOfKey('preview')];
        if (this.element.properties.et_type == Phlexible.elementtypes.TYPE_FULL) {
            previewItem.enable();
            if (this.element.properties.is_published) {
                previewItem.menu.items.items[1].enable();
            }
            else {
                previewItem.menu.items.items[1].disable();
            }
        }
        else {
            previewItem.disable();
        }

        this.updateLockInfo();
    },

    onBeforeLoadElement: function (params) {
        var langBtn = this.items.items[this.tbarIndex.indexOfKey('lang')];
        if (this.element.language != params.language) {
            items = langBtn.items;
            for (var i = 0; i < items.length; i++) {
                if (items[i].langKey == params.language) {
                    langBtn.setActiveItem(langBtn.menu.items.items[i], true);
                }
            }
        }
    },

    onGetLock: function () {
        Phlexible.console.log('TopToolbar: GET LOCK');

        this.updateLockInfo();

        this.items.items[this.tbarIndex.indexOfKey('edit')].enable();
        this.items.items[this.tbarIndex.indexOfKey('edit')].toggle(true);

        // edit button
        if (this.element.isAllowed('EDIT')) {
            this.items.items[this.tbarIndex.indexOfKey('edit')].enable();
            this.items.items[this.tbarIndex.indexOfKey('locks')].enable();
            this.items.items[this.tbarIndex.indexOfKey('lock')].enable();
            this.items.items[this.tbarIndex.indexOfKey('save')].enable();
        } else {
            this.items.items[this.tbarIndex.indexOfKey('edit')].disable();
            this.items.items[this.tbarIndex.indexOfKey('locks')].disable();
            this.items.items[this.tbarIndex.indexOfKey('lock')].disable();
            this.items.items[this.tbarIndex.indexOfKey('save')].disable();
        }

        // publish/set offline button
        if (Phlexible.User.isGranted('ROLE_ELEMENT_PUBLISH') && this.element.isAllowed('PUBLISH')) {
            this.items.items[this.tbarIndex.indexOfKey('publish')].enable();
            if (this.element.properties.is_published) {
                this.items.items[this.tbarIndex.indexOfKey('setOffline')].enable();
            }
            else {
                this.items.items[this.tbarIndex.indexOfKey('setOffline')].disable();
            }
        } else {
            Phlexible.console.log('else');
            this.items.items[this.tbarIndex.indexOfKey('publish')].disable();
            this.items.items[this.tbarIndex.indexOfKey('setOffline')].disable();
        }

        // delete button
        if (Phlexible.User.isGranted('ROLE_ELEMENT_DELETE') && this.element.isAllowed('DELETE')) {
            if (!this.element.properties.is_published ||
                (Phlexible.User.isGranted('ROLE_ELEMENT_PUBLISH') &&
                    this.element.data.rights.indexOf('PUBLISH') !== -1)) {
                this.items.items[this.tbarIndex.indexOfKey('delete')].enable();
            } else {
                this.items.items[this.tbarIndex.indexOfKey('delete')].disable();
            }
        } else {
            this.items.items[this.tbarIndex.indexOfKey('delete')].disable();
        }

        // task button
        var extendedItem = this.items.items[this.tbarIndex.indexOfKey('extended')];
        if (this.extendedMenuIndex.indexOfKey('task') !== -1) {
            extendedItem.menu.items.items[this.extendedMenuIndex.indexOfKey('task')].enable();
        }
    },

    onIsLocked: function () {
        Phlexible.console.log('TopToolbar: IS LOCKED');

        this.updateLockInfo();

        this.items.items[this.tbarIndex.indexOfKey('edit')].disable();
        this.items.items[this.tbarIndex.indexOfKey('edit')].toggle(true);
        this.items.items[this.tbarIndex.indexOfKey('save')].disable();
        this.items.items[this.tbarIndex.indexOfKey('publish')].disable();
        this.items.items[this.tbarIndex.indexOfKey('setOffline')].disable();
        this.items.items[this.tbarIndex.indexOfKey('delete')].disable();
        // task button
        var extendedItem = this.items.items[this.tbarIndex.indexOfKey('extended')];
        extendedItem.menu.items.items[this.extendedMenuIndex.indexOfKey('task')].disable();
    },

    onRemoveLock: function () {
        Phlexible.console.log('TopToolbar: REMOVE LOCKED');

        this.updateLockInfo();

        if (this.element.isAllowed('EDIT')) {
            this.items.items[this.tbarIndex.indexOfKey('edit')].enable();
            this.items.items[this.tbarIndex.indexOfKey('edit')].toggle(false);
            this.items.items[this.tbarIndex.indexOfKey('lock')].enable();
            this.items.items[this.tbarIndex.indexOfKey('locks')].enable();
        } else {
            this.items.items[this.tbarIndex.indexOfKey('edit')].disable();
            this.items.items[this.tbarIndex.indexOfKey('edit')].toggle(false);
            this.items.items[this.tbarIndex.indexOfKey('lock')].disable();
            this.items.items[this.tbarIndex.indexOfKey('locks')].disable();
        }
        this.items.items[this.tbarIndex.indexOfKey('save')].disable();
        this.items.items[this.tbarIndex.indexOfKey('publish')].disable();
        this.items.items[this.tbarIndex.indexOfKey('setOffline')].disable();
        this.items.items[this.tbarIndex.indexOfKey('delete')].disable();
        // task button
        var extendedItem = this.items.items[this.tbarIndex.indexOfKey('extended')];
        extendedItem.menu.items.items[this.extendedMenuIndex.indexOfKey('task')].disable();
    },

    onQuickPublish: function () {
        if (Phlexible.Config.get('elements.publish.confirm_required')) {
            Ext.MessageBox.confirm(this.strings.warning, this.strings.confirm_publish_dialog, function (btn) {
                if (btn == 'yes') {
                    this.onPublish();
                }
            }, this);
        } else {
            this.onPublish();
        }
    },

    onPublishWithNotification: function () {
        this.onUpdatePublish();
    },

    onSave: function () {
        this.element.fireEvent('internalSave');
    },

    onSaveMinor: function () {
        alert('To be implemented!');
    },

    onSavePublish: function () {
        alert('To be implemented!');
    },

    onEnableSave: function () {
        this.items.items[this.tbarIndex.indexOfKey('save')].enable();
    },

    onDisableSave: function () {
        this.items.items[this.tbarIndex.indexOfKey('save')].disable();
    },

    onExtendedPublish: function () {
        var windowClass;
        if (this.element.properties.teaser_id) {
            windowClass = Phlexible.elements.PublishTeaserWindow;
        }
        else {
            windowClass = Phlexible.elements.PublishTreeNodeWindow;
        }

        var w = new windowClass({
            comment_required: Phlexible.Config.get('elements.publish.comment_required'),
            tid: this.element.tid,
            teaser_id: this.element.properties.teaser_id,
            language: this.element.language,
            version: this.element.version,
            listeners: {
                submit: function (values) {
                    var comment = values.comment || false;
                    if (values.include_sub_elements) {
                        this.onPublishRecursive(comment);
                    } else {
                        this.onPublish(comment);
                    }

                    this.element.reload();
                },
                success: function () {
                    if (this.element.treeNode) {
                        var iconEl = this.element.treeNode.getUI().getIconEl();
                        // TODO: repair
                        /*
                        if (iconEl.src.match(/\/status\/[a-z]+/)) {
                            iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '/status/online');
                        } else {
                            iconEl.src = iconEl.src + '/status/online';
                        }
                        */

                        if (this.element.treeNode.hasChildNodes()) {
                            this.element.treeNode.attributes.children = false;
                            this.element.treeNode.reload(function (node) {
                                var tree = this.element.treeNode.getOwnerTree();
                                var n = tree.getNodeById(this.element.tid);
                                if (n) {
                                    tree.getSelectionModel().select(n);
                                }
                            }.createDelegate(this));
                        }
                    }

                    this.element.reload();
                },
                scope: this
            }
        });
        w.show();
    },

    onPublish: function (comment) {
        if (this.element.fireEvent('beforePublish', this.element, comment) === false) {
            return;
        }

        this.element.fireEvent('internalSave', comment, true);
    },

    onUpdatePublish: function (comment) {
        if (this.element.fireEvent('beforePublish', this.element, comment) === false) {
            return;
        }

        this.element.fireEvent('internalSave', comment, true, true);
    },

    onPublishRecursive: function (comment) {
        if (this.element.fireEvent('beforePublishAdvanced', this.element, comment) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_publish_advancedpublish'),
            params: {
                tid: this.element.tid,
                teaser_id: this.element.properties.teaser_id,
                language: this.element.language,
                comment: comment
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data) {
                    data = {
                        success: false,
                        msg: 'An unexpected error occured'
                    };
                }

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.element.fireEvent('publishAdvanced', this.element, result);
                }
                else {
                    Ext.MessageBox.alert('Failure', data.msg);
                    this.element.fireEvent('publishAdvancedFailure', this.element, result);
                }

            },
            failure: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data) {
                    data = {
                        success: false,
                        msg: 'An unexpected error occured'
                    };
                }

                Ext.MessageBox.alert('Failure', data.msg);
                this.element.fireEvent('publishFailure', this.element, result);
            },
            scope: this
        });
    },

    xonPublishRecursive: function (comment) {
        Ext.MessageBox.show({
            title: this.strings.confirm_publish,
            msg: this.strings.confirm_publish_text,
            buttons: Ext.Msg.OKCANCEL,
            icon: Ext.MessageBox.QUESTION,
            fn: function (btn) {
                if (btn == 'ok') {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('elements_element_publishrecursive'),
                        params: {
                            tid: this.element.tid,
                            teaser_id: this.element.properties.teaser_id,
                            language: this.element.language,
                            comment: comment
                        },
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                Phlexible.success(data.msg);
                            } else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                }
            },
            scope: this
        });
    },

    onQuickSetOffline: function () {
        this.onSetOffline();
    },

    onExtendedSetOffline: function () {
        var windowClass;
        if (this.element.properties.teaser_id) {
            windowClass = Phlexible.elements.SetTeaserOfflineWindow;
        } else {
            windowClass = Phlexible.elements.SetTreeNodeOfflineWindow;
        }

        var w = new windowClass({
            comment_required: Phlexible.Config.get('elements.publish.comment_required'),
            listeners: {
                submit: function (values) {
                    var comment = values.comment || false;
                    if (values.include_sub_elements) {
                        this.onSetOfflineRecursive(comment);
                    } else {
                        this.onSetOffline(comment);
                    }
                },
                scope: this
            }
        });
        w.show();
    },

    onSetOffline: function (comment) {
        if (this.element.fireEvent('beforeSetOffline', this.element, comment) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_publish_setoffline'),
            params: {
                tid: this.element.tid,
                teaser_id: this.element.properties.teaser_id,
                language: this.element.language,
                comment: comment
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data) {
                    data = {
                        success: false,
                        msg: 'An unexpected error occured'
                    };
                }

                if (data.success) {
                    Phlexible.success(data.msg);
                    this.element.fireEvent('setOffline', this.element, response);
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                    this.element.fireEvent('setOfflineFailure', this.element, response);
                }

                this.element.reload();
            },
            failure: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data) {
                    data = {
                        success: false,
                        msg: 'An unexpected error occured'
                    };
                }

                Ext.MessageBox.alert('Failure', data.msg);
                this.element.fireEvent('setOfflineFailure', this.element, response);
            },
            scope: this
        });
    },

    onSetOfflineRecursive: function (comment) {
        if (this.element.fireEvent('beforeSetOfflineAdvanced', this.element, comment) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_publish_setofflinerecursive'),
            params: {
                tid: this.element.tid,
                teaser_id: this.element.properties.teaser_id,
                language: this.element.language,
                comment: comment
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data) {
                    data = {
                        success: false,
                        msg: 'An unexpected error occured'
                    };
                }

                if (data.success) {
                    Phlexible.success(data.msg);
                    this.element.fireEvent('setOfflineAdvanced', this.element, response);
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                    this.element.fireEvent('setOfflineAdvancedFailure', this.element, response);
                }

                this.element.reload();
            },
            failure: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data) {
                    data = {
                        success: false,
                        msg: 'An unexpected error occured'
                    };
                }

                Ext.MessageBox.alert('Failure', data.msg);
                this.element.fireEvent('setOfflineAdvancedFailure', this.element, response);
            },
            scope: this
        });
    },

    xonSetOfflineRecursive: function (comment) {
        Ext.MessageBox.show({
            title: this.strings.confirm_set_offline,
            msg: this.strings.confirm_set_offline_text,
            buttons: Ext.Msg.OKCANCEL,
            icon: Ext.MessageBox.QUESTION,
            fn: function (btn) {
                if (btn == 'ok') {
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('elements_element_setofflinerecursive'),
                        params: {
                            tid: this.element.tid,
                            teaser_id: this.element.properties.teaser_id,
                            language: this.element.language,
                            comment: comment
                        },
                        success: function (response) {
                            var data = Ext.decode(response.responseText);

                            if (data.success) {
                                Phlexible.success(data.msg);
                            } else {
                                Ext.MessageBox.alert('Failure', data.msg);
                            }
                        },
                        scope: this
                    });
                }
            },
            scope: this
        });
    }
});

Ext.reg('elements-toptoolbar', Phlexible.elements.TopToolbar);
