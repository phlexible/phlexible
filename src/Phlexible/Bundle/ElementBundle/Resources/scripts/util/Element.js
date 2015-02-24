Ext.provide('Phlexible.elements.Element');
Ext.provide('Phlexible.elements.Teaser');

Ext.require('Phlexible.fields.Prototypes');
Ext.require('Phlexible.elements.NewElementInstanceWindow');
Ext.require('Phlexible.elements.NewElementWindow');

Phlexible.elements.Element = function (config) {
    this.addEvents(
        'beforeLoad',
        'load',
        'createElement',
        'beforeSave',
        'save',
        'saveFailure',
        'enableSave',
        'disableSave',
        'internalSave',
        'beforePublish',
        'publish',
        'publishFailure',
        'beforPublishAdvanced',
        'publishAdvanced',
        'publishAdvancedFailure',
        'beforeSetOffline',
        'setOffline',
        'setOfflineFailure',
        'beforeSetOfflineAdvanced',
        'setOfflineAdvanced',
        'setOfflineAdvancedFailure',
        'beforeLock',
        'beforeUnlock',
        'getlock',
        'islocked',
        'removelock'
    );

    if (config.language) {
        this.language = config.language;
    }
    if (config.siteroot_id) {
        this.siteroot_id = config.siteroot_id;

        var checkedLanguage = this.language || Phlexible.Config.get('language.frontend');
        var siterootLanguages = Phlexible.Config.get('user.siteroot.languages')[this.siteroot_id];
        var langBtns = [], hasChecked = false;
        for (var i = 0; i < Phlexible.Config.get('set.language.frontend').length; i++) {
            var languageRow = Phlexible.Config.get('set.language.frontend')[i];
            if (siterootLanguages.indexOf(languageRow[0]) === -1) {
                continue;
            }
            if (languageRow[0] === Phlexible.Config.get('language.frontend')) {
                hasChecked = true;
            }
            langBtns.push({
                text: languageRow[1],
                iconCls: languageRow[2],
                langKey: languageRow[0],
                checked: languageRow[0] === checkedLanguage
            });
        }
        if (!hasChecked) {
            langBtns[0].checked = true;
            this.language = langBtns[0].langKey;
        }

        this.languages = langBtns;
    }
    if (config.startParams) {
        this.startParams = config.startParams;
    }

    Ext.getDoc().on('mousedown', function (e) {
        if (this.activeDiffEl && this.activeDiffEl.isVisible()) {
            this.activeDiffEl.hide();
            this.activeDiffEl = null;
        }
    }, this);

    this.prototypes = new Phlexible.fields.Prototypes();

    this.history = new Ext.util.MixedCollection();
};

Ext.extend(Phlexible.elements.Element, Ext.util.Observable, {
    siteroot_id: '',
    tid: '',
    eid: '',
    language: '',
    version: '',
    master: false,
    title: '',
    treeNode: null,
    properties: null,
    preview_url: null,
    loaded: false,

    loadEid: function (eid, version, language, doLock) {
        var loadParams = {
            id: null,
            eid: eid || null,
            siteroot_id: this.siteroot_id || null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        this.reload(loadParams);
    },

    loadTeaser: function (teaser_id, version, language, doLock) {
        var loadParams = {
            id: null,
            teaser_id: teaser_id,
            siteroot_id: this.siteroot_id || null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        this.reload(loadParams);
    },

    load: function (id, version, language, doLock) {
        var loadParams = {
            id: id || null,
            teaser_id: null,
            eid: null,
            siteroot_id: null,
            language: language || this.language || null,
            version: version || null,
            lock: doLock ? 1 : 0
        };

        //this.mask.msg = 'Loading Element ' + eid;
        //this.mask.show();

        this.reload(loadParams);

        //return;
    },

    reload: function (params) {
        if (!params) {
            params = {};
        }

        if (params.id === undefined) {
            params.id = this.tid;
        }

        if (params.teaser_id === undefined && this.properties && this.properties.teaser_id && this.properties.teaser_id !== undefined) {
            delete params.id;
            params.teaser_id = this.properties.teaser_id;
        }

        if (params.eid === undefined) {
            params.eid = this.eid;
        }

        if (params.siteroot_id === undefined) {
            params.siteroot_id = this.siteroot_id;
        }

        if (params.language === undefined) {
            params.language = this.language;
        }

        if (params.version !== null && params.version === undefined && this.version) {
            params.version = this.version;
        }

        if (((this.tid && this.tid != params.id) || (this.language && this.language != params.language)) &&
            this.lockinfo && this.lockinfo.status == 'edit') {
            params.unlock = this.lockinfo.id;
        }

        if (!params.lock && this.lockinfo) {
            params.lock = 1;
        }

        if (this.fireEvent('beforeLoad', params) === false) {
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_data_load'),
            params: params,
            success: this.onLoadSuccess,
            failure: this.onLoadFailure,
            scope: this
        });
    },

    onLoadSuccess: function (response) {
        var elementData = Ext.decode(response.responseText);

        if (elementData.success) {
            var properties = elementData.properties;

            this.data = elementData;
            this.properties = properties;

            this.master = properties.master;
            this.tid = properties.tid;
            this.eid = properties.eid;
            this.language = properties.language;
            this.version = properties.version;
            this.title = properties.backend_title;
            this.preview_url = properties.preview_url;
            this.masterlanguage = properties.masterlanguage;
            this.icon = properties.icon;

            this.setLockStatus(elementData.lockinfo);

            this.loaded = true;

            this.fireEvent('load', this);

            this.fireEvent('afterload', this);
        } else {
            Ext.MessageBox.alert('Error', elementData.msg);

            this.setStatusLocked();
        }

        var historyKey = this.tid + '_' + this.version + '_' + this.language;
        if (this.history.indexOfKey(historyKey) !== false) {
            this.history.removeKey(historyKey);
        }
        this.history.add(historyKey, [this.tid, this.version, this.language, this.title, properties.icon, new Date().getTime()]);
        this.fireEvent('historychange', this, this.history);
    },

    onLoadFailure: function () {
        Ext.MessageBox.alert('Error connecting to server.');
        this.setStatusLocked();
    },

    save: function(parameters) {
        parameters = parameters || {};

        if (!this.tid) {
            Ext.MessageBox.alert('Failure', 'Save not possible, no element loaded.');
            return;
        }

        parameters.tid = this.tid;
        parameters.teaser_id = this.properties.teaser_id;
        parameters.eid = this.eid;
        parameters.language = this.language;
        parameters.version = this.version;

        var errors = [];

        this.fireEvent('internalSave', parameters, errors);

        console.log(errors);
        if (errors.length) {
            Ext.MessageBox.alert('Failure', 'Save not possible. ' + errors.join(' '));
            return;
        }

        console.log(parameters);

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_data_save'),
            params: parameters,
            success: this.onSaveSuccess,
            failure: function (response) {
                var result = Ext.decode(response.responseText);

                if (!result) {
                    result = {
                        success: false,
                        msg: 'Error occured'
                    };
                }

                Ext.MessageBox.alert('Failure', result.msg);
                this.fireEvent('saveFailure', this, result);
            },
            scope: this
        });
    },

    onSaveSuccess: function(response) {
        var result = Ext.decode(response.responseText);

        if (!result) {
            result = {
                success: false,
                msg: 'An unexpected error occured',
                data: {}
            };
        }

        if (result.success) {
            Phlexible.success(result.msg);

            //this.fireEvent('save', this);
            this.fireEvent('save', this, result);

            if (result.data.publish) {
                this.fireEvent('publish', this, result);
            }

            this.reload({
                version: null
            });

            if (result.data.publish_other && result.data.publish_other.length) {
                var w = new Phlexible.elements.PublishSlaveWindow({
                    data: result.data.publish_other
                });
                w.show();
            }
        } else {
            Ext.MessageBox.alert('Failure', data.msg);
            this.fireEvent('saveFailure', this, result);
        }
    },

    getLanguage: function () {
        return this.language;
    },

    setLanguage: function (language, noReload) {
        if (language !== this.language) {
            this.fireEvent('setLanguage', this, language);
            if (!noReload && this.loaded) {
                this.reload({
                    language: language
                });
            }
            this.language = language;
        }
    },

    getRights: function () {
        if (!this.data || !this.data.rights) {
            return [];
        }

        return this.data.rights;
    },

    isAllowed: function (right) {
        var rights = this.getRights();

        return rights.indexOf(right) !== -1;
    },

    setTreeNode: function (node) {
        this.treeNode = node;
        this.teaserNode = null;
    },

    getTreeNode: function () {
        return this.treeNode;
    },

    setTeaserNode: function (node) {
        this.teaserNode = node;
    },

    getTeaserNode: function () {
        return this.teaserNode;
    },

    showNewElementWindow: function (node, element_type_id) {
        if (!node) {
            node = this.getTreeNode();
        }

        if (node) {
            sort_mode = node.attributes.sort_mode;
        } else {
            sort_mode = null;
        }

        var w = new Phlexible.elements.NewElementWindow({
            element_type_id: element_type_id,
            sort_mode: sort_mode,
            language: this.language,
            submitParams: {
                siteroot_id: this.siteroot_id,
                eid: node ? node.attributes.eid : this.eid,
                id: node ? node.id : this.tid
            },
            listeners: {
                success: function (dialog, result, node) {
                    this.fireEvent('createElement', this, result.data, node);
                    this.load(result.data.tid, null, result.data.master_language, true);
                }.createDelegate(this, [node], true)
            }
        });
        w.show();
    },

    showNewAliasWindow: function (node, element_type_id) {
        if (!node) {
            node = this.getTreeNode();
        }
        var w = new Phlexible.elements.NewElementInstanceWindow({
            element_type_id: element_type_id,
            sort_mode: node.attributes.sort_mode,
            language: this.language,
            submitParams: {
                siteroot_id: this.siteroot_id,
                eid: node.attributes.eid,
                id: node.id
            },
            listeners: {
                success: function (node) {
                    node.attributes.children = false;
                    node.reload();
                }.createDelegate(this, [node], false)
            }
        });
        w.show();
    },

    diff: function (diff, cb, scope) {
        this.fireEvent('diff', diff, cb, scope);
    },

    clearDiff: function () {
        this.fireEvent('clearDiff');
    },

    getLockStatus: function () {
        if (this.lockinfo && (this.lockinfo.status == 'edit' || this.lockinfo.status == 'locked' || this.lockinfo.status == 'locked_permanently')) {
            return this.lockinfo.status;
        }

        return 'idle';
    },

    // protected
    setLockStatus: function (lockinfo) {
        if (!lockinfo) {
            this.setStatusIdle();
            return;
        }

        this.lockinfo = lockinfo;

        switch (lockinfo.status) {
            case 'edit':
                this.setStatusEdit();
                break;

            case 'locked':
                this.setStatusLocked();
                break;

            case 'locked_permanently':
                this.setStatusLockedPermanently();
                break;

            default:
                this.setStatusIdle();
        }
    },

    // protected
    setStatusLocked: function () {
        if (!this.lockinfo) {
            this.lockinfo = {};
        }

        this.lockinfo.status = 'locked';

        this.fireEvent('islocked', this);
    },

    // protected
    setStatusLockedPermanently: function () {
        if (!this.lockinfo) {
            this.lockinfo = {};
        }

        this.lockinfo.status = 'locked_permanently';

        this.fireEvent('islocked', this);
    },

    // protected
    setStatusEdit: function () {
        if (!this.lockinfo) {
            this.lockinfo = {};
        }

        this.lockinfo.status = 'edit';

        this.fireEvent('getlock', this);
    },

    // protected
    setStatusIdle: function () {
        this.lockinfo = null;

        this.fireEvent('removelock', this);
    },

    lock: function (callback) {
        if (this.lockinfo) {
            return false;
        }

        if (this.fireEvent('beforeLock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onLockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_lock'),
            params: {
                eid: this.eid,
                language: this.language
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onLockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            //this.setStatusEdit();
            this.reload();
            Phlexible.success('Lock acquired.');
        }
        else {
            this.setStatusLocked();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    },

    unlock: function (callback) {
        if (!this.lockinfo || this.lockinfo.status != 'edit') {
            return false;
        }

        if (this.fireEvent('beforeUnlock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onUnlockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_unlock'),
            params: {
                id: this.lockinfo.id
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onUnlockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setStatusIdle();
            Phlexible.success('Lock released.');
        }
        else {
            this.setStatusIdle();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    }
});

Phlexible.elements.Teaser = function (config) {
    this.addEvents(
        'beforeLoad',
        'load'
    );

    if (config.siteroot_id) {
        this.siteroot_id = config.siteroot_id;
    }
    if (config.language) {
        this.language = config.language;
    }
};
Ext.extend(Phlexible.elements.Teaser, Ext.util.Observable, {
    siteroot_id: '',
    id: '',
    eid: '',
    language: '',
    version: '',
    master: false,
    title: '',
    properties: null,
    loaded: false,

    load: function (eid, version, language) {
        if (!language) {
            language = this.language;
        }

        var loadParams = {
            siteroot_id: this.siteroot_id,
            eid: eid,
            language: language
        };
        if (version) {
            loadParams.version = version;
        } else {
            loadParams.version = null;
        }

        this.reload(loadParams);
    },

    reload: function (params) {
        if (!params) {
            params = {};
        }

        if (!params.siteroot_id) {
            params.siteroot_id = this.siteroot_id;
        }
        if (params.eid === undefined) {
            params.eid = this.eid;
        }
        if (params.language === undefined) {
            params.language = this.language;
        }
        if (params.version !== null && params.version === undefined && this.version) {
            params.version = this.version;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_data_load'),
            params: params,
            success: this.onLoadSuccess,
            failure: function () {
                alert("failure");
            },
            scope: this
        });
    },

    onLoadSuccess: function (response) {
        var elementData = Ext.decode(response.responseText);
        var properties = elementData.properties;

        this.data = elementData;
        this.properties = properties;

        this.master = properties.master;
        this.tid = properties.tid;
        this.eid = properties.eid;
        this.language = properties.language;
        this.version = properties.version;
        this.title = properties.title;
        this.masterlanguage = properties.masterlanguage;

        this.setLockStatus(elementData.lockinfo);

        this.loaded = true;

        this.fireEvent('load', this);
    },

    showNewTeaserWindow: function (node, element_type_id) {
        /*
         if(!node) {
         node = this.getTreeNode();
         }
         var w = new Phlexible.elements.NewElementWindow({
         element_type_id: element_type_id,
         submitParams: {
         siteroot_id: this.siteroot_id,
         eid: node.attributes.eid,
         id: node.id
         },
         listeners: {
         success: function(node) {
         node.attributes.children = false;
         node.reload();
         }.createDelegate(this, [node], false)
         }
         });
         w.show();
         */
    },

    getLockStatus: function () {
        if (this.lockinfo && (this.lockinfo.status == 'edit' || this.lockinfo.status == 'locked')) {
            return this.lockinfo.status;
        }

        return 'idle';
    },

    // protected
    setLockStatus: function (lockinfo) {
        if (!lockinfo) {
            this.setStatusIdle();
            return;
        }

        this.lockinfo = lockinfo;

        switch (lockinfo.status) {
            case 'edit':
                this.setStatusEdit();
                break;

            case 'locked':
                this.setStatusLocked();
                break;

            case 'locked_permanently':
                this.setStatusLockedPermanently();
                break;

            default:
                this.setStatusIdle();
        }
    },

    // protected
    setStatusLocked: function () {
        if (!this.lockinfo) this.lockinfo = {};

        this.lockinfo.status = 'locked';

        this.fireEvent('islocked', this);
    },

    // protected
    setStatusEdit: function () {
        if (!this.lockinfo) this.lockinfo = {};

        this.lockinfo.status = 'edit';

        this.fireEvent('getlock', this);
    },

    // protected
    setStatusIdle: function () {
        this.lockinfo = null;

        this.fireEvent('removelock', this);
    },

    lock: function (callback) {
        if (this.lockinfo) {
            return false;
        }

        if (this.fireEvent('beforeLock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onLockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_lock'),
            params: {
                eid: this.eid,
                language: this.language
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onLockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setStatusEdit();
            Phlexible.success('Lock acquired.');
        }
        else {
            this.setStatusLocked();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    },

    unlock: function (callback) {
        if (!this.lockinfo || this.lockinfo.status != 'edit') {
            return false;
        }

        if (this.fireEvent('beforeUnlock', this) === false) {
            return false;
        }

        if (!callback) callback = this.onUnlockSuccess;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elements_locks_unlock'),
            params: {
                id: this.lockinfo.id
            },
            success: callback,
            scope: this
        });

        return true;
    },

    // private
    onUnlockSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setStatusIdle();
            Phlexible.success('Lock released.');
        }
        else {
            this.setStatusIdle();
            Ext.MessageBox.alert('Failure', data.msg);
        }
    }
});